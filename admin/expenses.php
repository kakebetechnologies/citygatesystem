<?php
/**
 * Expense Tracking — records farm operating costs (feed, labor, vet,
 * utilities, repairs, transport, other) and pairs with the Sales module
 * to give a simple month-to-date Net (Revenue − Expenses) view.
 *
 * ob_start(): the shared header.php streams its nav HTML immediately, and
 * this environment's php.ini has output_buffering=4096 — the nav alone
 * exceeds that, so PHP auto-flushes before our later header('Location')
 * redirects run, breaking the PRG pattern with a "headers already sent"
 * warning. Wrapping this page's own output in an explicit buffer (which,
 * unlike the implicit one, only flushes at script end) sidesteps it
 * without touching the shared header.php.
 */
ob_start();
$pageModule = 'expenses';
$pageTitle = 'Expenses';
$pageSub = 'Track farm expenses and monitor spending against revenue';
require_once __DIR__ . '/includes/header.php';

$canEdit = cg_can('expenses', 'full');
$message = '';
$errors = [];

const CG_EXPENSE_CATEGORIES = ['Feed', 'Labor', 'Veterinary', 'Utilities', 'Repairs', 'Transport', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'save') {
      $category = $_POST['category'] ?? '';
      $description = trim($_POST['description'] ?? '');
      $amount = $_POST['amount'] ?? '';
      $expenseDate = $_POST['expense_date'] ?? '';
      $supplierId = (int) ($_POST['supplier_id'] ?? 0);

      if (!in_array($category, CG_EXPENSE_CATEGORIES, true)) $errors[] = 'Please choose a valid category.';
      if ($description === '') $errors[] = 'Description is required.';
      if ($amount === '' || !is_numeric($amount) || (float) $amount < 0) $errors[] = 'Amount must be a valid number.';
      if ($expenseDate === '' || !DateTime::createFromFormat('Y-m-d', $expenseDate)) $errors[] = 'A valid expense date is required.';

      if (!$errors) {
         $stmt = $pdo->prepare('INSERT INTO expenses (category, description, amount, expense_date, supplier_id, recorded_by) VALUES (?,?,?,?,?,?)');
         $stmt->execute([$category, $description, $amount, $expenseDate, $supplierId > 0 ? $supplierId : null, $user['id']]);
         header('Location: expenses.php?saved=1');
         exit;
      }
   } elseif ($action === 'delete') {
      $id = (int) ($_POST['id'] ?? 0);
      $pdo->prepare('DELETE FROM expenses WHERE id=?')->execute([$id]);
      header('Location: expenses.php?deleted=1');
      exit;
   }
}

if (isset($_GET['saved'])) $message = 'Expense recorded.';
if (isset($_GET['deleted'])) $message = 'Expense deleted.';

function fmt_ugx($n) { return 'UGX ' . number_format((float) $n); }

$suppliers = $pdo->query('SELECT id, name FROM suppliers_buyers ORDER BY name')->fetchAll();

$categoryFilter = $_GET['category'] ?? '';
$supplierFilter = (int) ($_GET['supplier_id'] ?? 0);
$fromFilter = $_GET['from'] ?? '';
$toFilter = $_GET['to'] ?? '';
$searchFilter = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if (in_array($categoryFilter, CG_EXPENSE_CATEGORIES, true)) { $where[] = 'e.category = ?'; $params[] = $categoryFilter; }
if ($supplierFilter > 0) { $where[] = 'e.supplier_id = ?'; $params[] = $supplierFilter; }
if ($fromFilter !== '' && DateTime::createFromFormat('Y-m-d', $fromFilter)) { $where[] = 'e.expense_date >= ?'; $params[] = $fromFilter; }
if ($toFilter !== '' && DateTime::createFromFormat('Y-m-d', $toFilter)) { $where[] = 'e.expense_date <= ?'; $params[] = $toFilter; }
if ($searchFilter !== '') { $where[] = 'e.description LIKE ?'; $params[] = '%' . $searchFilter . '%'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$queryStringNoPage = http_build_query(array_filter([
   'category' => $categoryFilter, 'supplier_id' => $supplierFilter ?: '', 'from' => $fromFilter, 'to' => $toFilter, 'q' => $searchFilter,
]));

$stmt = $pdo->prepare("SELECT e.*, s.name AS supplier_name FROM expenses e LEFT JOIN suppliers_buyers s ON e.supplier_id = s.id $whereSql ORDER BY e.expense_date DESC, e.id DESC");
$stmt->execute($params);
$expenses = $stmt->fetchAll();
$filteredTotal = array_sum(array_map(fn($e) => (float) $e['amount'], $expenses));

$expensesThisMonth = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE MONTH(expense_date)=MONTH(CURDATE()) AND YEAR(expense_date)=YEAR(CURDATE())")->fetchColumn();
$revenueThisMonth = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM sales WHERE status='Paid' AND MONTH(sale_date)=MONTH(CURDATE()) AND YEAR(sale_date)=YEAR(CURDATE())")->fetchColumn();
$netThisMonth = $revenueThisMonth - $expensesThisMonth;
$totalRecords = (int) $pdo->query('SELECT COUNT(*) FROM expenses')->fetchColumn();

$today = date('Y-m-d');
?>

<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon rose"><i class="fa fa-credit-card"></i></div><div><div class="cg-stat-card-num"><?php echo fmt_ugx($expensesThisMonth); ?></div><div class="cg-stat-card-label">Total Expenses This Month</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon <?php echo $netThisMonth >= 0 ? '' : 'rose'; ?>"><i class="fa fa-balance-scale"></i></div><div><div class="cg-stat-card-num"><?php echo fmt_ugx($netThisMonth); ?></div><div class="cg-stat-card-label">Net This Month (Revenue &minus; Expenses)</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon gold"><i class="fa fa-list-alt"></i></div><div><div class="cg-stat-card-num"><?php echo $totalRecords; ?></div><div class="cg-stat-card-label">Total Expense Records</div></div></div>
</div>

<?php if ($message): ?>
<div class="alert alert-success" style="border-radius:10px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger" style="border-radius:10px;">
   <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
</div>
<?php endif; ?>

<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2>Record Expense</h2></div>
   <form method="post" class="row g-3">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <div class="col-md-2">
         <label class="form-label">Category</label>
         <select name="category" class="form-select" required>
            <?php foreach (CG_EXPENSE_CATEGORIES as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo (($_POST['category'] ?? '') === $c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-md-4">
         <label class="form-label">Description</label>
         <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($_POST['description'] ?? ''); ?>" required>
      </div>
      <div class="col-md-2">
         <label class="form-label">Amount (UGX)</label>
         <input type="number" min="0" step="any" name="amount" class="form-control" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" required>
      </div>
      <div class="col-md-2">
         <label class="form-label">Expense Date</label>
         <input type="date" name="expense_date" class="form-control" value="<?php echo htmlspecialchars($_POST['expense_date'] ?? $today); ?>" required>
      </div>
      <div class="col-md-2">
         <label class="form-label">Supplier</label>
         <select name="supplier_id" class="form-select">
            <option value="0">— None —</option>
            <?php foreach ($suppliers as $s): ?>
            <option value="<?php echo (int) $s['id']; ?>" <?php echo ((int) ($_POST['supplier_id'] ?? 0) === (int) $s['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
         <button type="submit" class="cg-btn cg-btn-primary w-100"><i class="fa fa-plus"></i> Record Expense</button>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Expenses</h2>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
         <a href="expenses-print.php?<?php echo htmlspecialchars($queryStringNoPage); ?>" target="_blank" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-print"></i> Print / PDF</a>
         <a href="expenses-export.php?<?php echo htmlspecialchars($queryStringNoPage); ?>" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-file-excel-o"></i> Export to Excel</a>
      </div>
   </div>

   <form method="get" class="row g-2 align-items-end mb-3">
      <div class="col-md-2">
         <label class="form-label small text-muted mb-1">Category</label>
         <select name="category" class="form-select form-select-sm">
            <option value="">All Categories</option>
            <?php foreach (CG_EXPENSE_CATEGORIES as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $categoryFilter === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-md-2">
         <label class="form-label small text-muted mb-1">Supplier</label>
         <select name="supplier_id" class="form-select form-select-sm">
            <option value="">All Suppliers</option>
            <?php foreach ($suppliers as $s): ?>
            <option value="<?php echo (int) $s['id']; ?>" <?php echo $supplierFilter === (int) $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-md-2">
         <label class="form-label small text-muted mb-1">From</label>
         <input type="date" name="from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fromFilter); ?>">
      </div>
      <div class="col-md-2">
         <label class="form-label small text-muted mb-1">To</label>
         <input type="date" name="to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($toFilter); ?>">
      </div>
      <div class="col-md-2">
         <label class="form-label small text-muted mb-1">Search Description</label>
         <input type="text" name="q" class="form-control form-control-sm" placeholder="e.g. feed" value="<?php echo htmlspecialchars($searchFilter); ?>">
      </div>
      <div class="col-md-2 d-flex gap-2">
         <button type="submit" class="cg-btn cg-btn-primary cg-btn-sm w-100"><i class="fa fa-filter"></i> Filter</button>
         <?php if ($categoryFilter || $supplierFilter || $fromFilter || $toFilter || $searchFilter): ?>
         <a href="expenses.php" class="cg-btn cg-btn-outline cg-btn-sm">Clear</a>
         <?php endif; ?>
      </div>
   </form>

   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th><th>Supplier</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
         <tbody>
         <?php if (!$expenses): ?>
            <tr><td colspan="6" class="text-muted">No expenses match this filter.</td></tr>
         <?php else: foreach ($expenses as $e): ?>
            <tr>
               <td><?php echo htmlspecialchars($e['expense_date']); ?></td>
               <td><?php echo htmlspecialchars($e['category']); ?></td>
               <td><?php echo htmlspecialchars($e['description']); ?></td>
               <td><?php echo fmt_ugx($e['amount']); ?></td>
               <td><?php echo htmlspecialchars($e['supplier_name'] ?: '—'); ?></td>
               <?php if ($canEdit): ?>
               <td class="text-nowrap">
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete this expense?');">
                     <?php echo cg_csrf_field(); ?>
                     <input type="hidden" name="action" value="delete">
                     <input type="hidden" name="id" value="<?php echo (int) $e['id']; ?>">
                     <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm">Delete</button>
                  </form>
               </td>
               <?php endif; ?>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
         <?php if ($expenses): ?>
         <tfoot>
            <tr style="font-weight:700;background:#FAFAF8;">
               <td colspan="3" style="text-align:right;">Filtered Total (<?php echo count($expenses); ?> records)</td>
               <td colspan="<?php echo $canEdit ? 3 : 2; ?>"><?php echo fmt_ugx($filteredTotal); ?></td>
            </tr>
         </tfoot>
         <?php endif; ?>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
