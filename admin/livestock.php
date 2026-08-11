<?php
/**
 * Livestock & Batch Records — poultry batches or individual goat/dairy
 * animals, one flexible model distinguished by `sector`. Reference module
 * for the farm-management suite: demonstrates list+detail view, a linked
 * sub-resource (health records), sector-conditional form fields, and the
 * standard RBAC/CSRF/PRG pattern used across every admin module here.
 *
 * ob_start() below buffers header.php's output so a later POST-handler
 * redirect (header('Location: ...')) doesn't fail with "headers already
 * sent" once the page's HTML has started streaming.
 */
ob_start();
$pageModule = 'livestock';
$pageTitle = 'Livestock & Batches';
$pageSub = 'Poultry batches, goats, and dairy herd records';
require_once __DIR__ . '/includes/header.php';

$canEdit = cg_can('livestock', 'full');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'save') {
      $id = (int) ($_POST['id'] ?? 0);
      $sector = $_POST['sector'] ?? '';
      $identifier = trim($_POST['identifier'] ?? '');
      $breed = trim($_POST['breed'] ?? '');
      $sex = $_POST['sex'] ?? '';
      $sex = in_array($sex, ['Male', 'Female'], true) ? $sex : null;
      $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
      $dateAcquired = $_POST['date_acquired'] ?? '';
      $source = $_POST['source'] ?? 'Purchased';
      $status = $_POST['status'] ?? 'Active';
      $notes = trim($_POST['notes'] ?? '');

      if (in_array($sector, ['Poultry', 'Goats', 'Dairy'], true) && $identifier !== '' && $dateAcquired !== '') {
         if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE livestock_records SET sector=?, identifier=?, breed=?, sex=?, quantity=?, date_acquired=?, source=?, status=?, notes=? WHERE id=?');
            $stmt->execute([$sector, $identifier, $breed ?: null, $sex, $quantity, $dateAcquired, $source, $status, $notes ?: null, $id]);
         } else {
            $stmt = $pdo->prepare('INSERT INTO livestock_records (sector, identifier, breed, sex, quantity, date_acquired, source, status, notes, created_by) VALUES (?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$sector, $identifier, $breed ?: null, $sex, $quantity, $dateAcquired, $source, $status, $notes ?: null, $user['id']]);
         }
         header('Location: livestock.php?saved=1');
         exit;
      }
      $message = 'Please fill in Sector, Identifier, and Date Acquired.';
   } elseif ($action === 'delete') {
      $id = (int) ($_POST['id'] ?? 0);
      $pdo->prepare('DELETE FROM livestock_records WHERE id=?')->execute([$id]);
      header('Location: livestock.php?deleted=1');
      exit;
   } elseif ($action === 'add_health') {
      $livestockId = (int) ($_POST['livestock_id'] ?? 0);
      $recordType = $_POST['record_type'] ?? '';
      $description = trim($_POST['description'] ?? '');
      $recordDate = $_POST['record_date'] ?? '';
      $nextDue = trim($_POST['next_due_date'] ?? '') ?: null;
      $cost = trim($_POST['cost'] ?? '') !== '' ? (float) $_POST['cost'] : null;
      $performedBy = trim($_POST['performed_by'] ?? '') ?: null;

      if ($livestockId > 0 && in_array($recordType, ['Vaccination', 'Treatment', 'Checkup', 'Incident'], true) && $description !== '' && $recordDate !== '') {
         $stmt = $pdo->prepare('INSERT INTO livestock_health_records (livestock_id, record_type, description, record_date, next_due_date, cost, performed_by) VALUES (?,?,?,?,?,?,?)');
         $stmt->execute([$livestockId, $recordType, $description, $recordDate, $nextDue, $cost, $performedBy]);
      }
      header('Location: livestock.php?id=' . $livestockId . '&saved=1');
      exit;
   }
}

if (isset($_GET['saved'])) $message = 'Saved.';
if (isset($_GET['deleted'])) $message = 'Record deleted.';

$viewId = (int) ($_GET['id'] ?? 0);
$editId = (int) ($_GET['edit'] ?? 0);

$sectorFilter = $_GET['sector'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$where = [];
$params = [];
if (in_array($sectorFilter, ['Poultry', 'Goats', 'Dairy'], true)) { $where[] = 'sector = ?'; $params[] = $sectorFilter; }
if (in_array($statusFilter, ['Active', 'Sold', 'Deceased'], true)) { $where[] = 'status = ?'; $params[] = $statusFilter; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM livestock_records $whereSql ORDER BY date_acquired DESC, id DESC");
$stmt->execute($params);
$records = $stmt->fetchAll();

$editRecord = null;
if ($editId > 0) {
   $s = $pdo->prepare('SELECT * FROM livestock_records WHERE id=?');
   $s->execute([$editId]);
   $editRecord = $s->fetch() ?: null;
}

$viewRecord = null;
$healthRecords = [];
if ($viewId > 0) {
   $s = $pdo->prepare('SELECT * FROM livestock_records WHERE id=?');
   $s->execute([$viewId]);
   $viewRecord = $s->fetch() ?: null;
   if ($viewRecord) {
      $hs = $pdo->prepare('SELECT * FROM livestock_health_records WHERE livestock_id=? ORDER BY record_date DESC');
      $hs->execute([$viewId]);
      $healthRecords = $hs->fetchAll();
   }
}

function cg_status_badge_class(string $status): string {
   return match ($status) {
      'Active' => 'cg-badge-active',
      'Sold' => 'cg-badge-approved',
      'Deceased' => 'cg-badge-rejected',
      default => 'cg-badge-draft',
   };
}
?>

<?php if ($message): ?>
<div class="alert alert-success" style="border-radius:10px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($viewRecord): ?>
<!-- ============ Detail view: one record + its health history ============ -->
<div class="cg-panel">
   <div class="cg-panel-head">
      <h2><?php echo htmlspecialchars($viewRecord['identifier']); ?> — <?php echo htmlspecialchars($viewRecord['sector']); ?></h2>
      <a href="livestock.php" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-arrow-left"></i> Back to List</a>
   </div>
   <div class="row g-3 mb-2">
      <div class="col-md-3"><strong>Breed:</strong> <?php echo htmlspecialchars($viewRecord['breed'] ?: '—'); ?></div>
      <div class="col-md-3"><strong>Sex:</strong> <?php echo htmlspecialchars($viewRecord['sex'] ?: '—'); ?></div>
      <div class="col-md-3"><strong>Quantity:</strong> <?php echo (int) $viewRecord['quantity']; ?></div>
      <div class="col-md-3"><strong>Status:</strong> <span class="cg-badge <?php echo cg_status_badge_class($viewRecord['status']); ?>"><?php echo htmlspecialchars($viewRecord['status']); ?></span></div>
      <div class="col-md-3"><strong>Acquired:</strong> <?php echo htmlspecialchars($viewRecord['date_acquired']); ?> (<?php echo htmlspecialchars($viewRecord['source']); ?>)</div>
      <div class="col-md-9"><strong>Notes:</strong> <?php echo htmlspecialchars($viewRecord['notes'] ?: '—'); ?></div>
   </div>
</div>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>Health & Veterinary History</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Next Due</th><th>Cost</th><th>Performed By</th></tr></thead>
         <tbody>
         <?php if (!$healthRecords): ?>
            <tr><td colspan="6" class="text-muted">No health records yet.</td></tr>
         <?php else: foreach ($healthRecords as $h): ?>
            <tr>
               <td><?php echo htmlspecialchars($h['record_date']); ?></td>
               <td><?php echo htmlspecialchars($h['record_type']); ?></td>
               <td><?php echo htmlspecialchars($h['description']); ?></td>
               <td><?php echo htmlspecialchars($h['next_due_date'] ?: '—'); ?></td>
               <td><?php echo $h['cost'] !== null ? 'UGX ' . number_format((float) $h['cost']) : '—'; ?></td>
               <td><?php echo htmlspecialchars($h['performed_by'] ?: '—'); ?></td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>

   <?php if ($canEdit): ?>
   <hr class="my-4">
   <h6 class="mb-3">Add Health Record</h6>
   <form method="post">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="add_health">
      <input type="hidden" name="livestock_id" value="<?php echo (int) $viewRecord['id']; ?>">
      <div class="row g-3">
         <div class="col-md-3">
            <label class="form-label">Type</label>
            <select name="record_type" class="form-select" required>
               <option value="Vaccination">Vaccination</option>
               <option value="Treatment">Treatment</option>
               <option value="Checkup">Checkup</option>
               <option value="Incident">Incident</option>
            </select>
         </div>
         <div class="col-md-5">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Date</label>
            <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Next Due</label>
            <input type="date" name="next_due_date" class="form-control">
         </div>
         <div class="col-md-3">
            <label class="form-label">Cost (UGX)</label>
            <input type="number" step="0.01" name="cost" class="form-control">
         </div>
         <div class="col-md-4">
            <label class="form-label">Performed By</label>
            <input type="text" name="performed_by" class="form-control" placeholder="Vet / staff name">
         </div>
         <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="cg-btn cg-btn-primary w-100">Add Record</button>
         </div>
      </div>
   </form>
   <?php endif; ?>
</div>

<?php else: ?>
<!-- ============ List view ============ -->
<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2><?php echo $editRecord ? 'Edit Record' : 'Add Livestock Record'; ?></h2></div>
   <form method="post">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?php echo $editRecord ? (int) $editRecord['id'] : 0; ?>">
      <div class="row g-3">
         <div class="col-md-2">
            <label class="form-label">Sector</label>
            <select name="sector" class="form-select" required>
               <?php foreach (['Poultry', 'Goats', 'Dairy'] as $s): ?>
               <option value="<?php echo $s; ?>" <?php echo ($editRecord['sector'] ?? '') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-2">
            <label class="form-label">Identifier</label>
            <input type="text" name="identifier" class="form-control" placeholder="e.g. GT-005 or PLT-2026-09-01" value="<?php echo htmlspecialchars($editRecord['identifier'] ?? ''); ?>" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Breed</label>
            <input type="text" name="breed" class="form-control" value="<?php echo htmlspecialchars($editRecord['breed'] ?? ''); ?>">
         </div>
         <div class="col-md-1">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-select">
               <option value="">—</option>
               <option value="Male" <?php echo ($editRecord['sex'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
               <option value="Female" <?php echo ($editRecord['sex'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>
         </div>
         <div class="col-md-1">
            <label class="form-label">Qty</label>
            <input type="number" min="1" name="quantity" class="form-control" value="<?php echo (int) ($editRecord['quantity'] ?? 1); ?>" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Date Acquired</label>
            <input type="date" name="date_acquired" class="form-control" value="<?php echo htmlspecialchars($editRecord['date_acquired'] ?? date('Y-m-d')); ?>" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Source</label>
            <select name="source" class="form-select">
               <?php foreach (['Purchased', 'Hatched', 'Born'] as $s): ?>
               <option value="<?php echo $s; ?>" <?php echo ($editRecord['source'] ?? 'Purchased') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
               <?php foreach (['Active', 'Sold', 'Deceased'] as $s): ?>
               <option value="<?php echo $s; ?>" <?php echo ($editRecord['status'] ?? 'Active') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-8">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($editRecord['notes'] ?? ''); ?>">
         </div>
         <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="cg-btn cg-btn-primary w-100"><?php echo $editRecord ? 'Save Changes' : 'Add Record'; ?></button>
         </div>
         <?php if ($editRecord): ?>
         <div class="col-md-2 d-flex align-items-end">
            <a href="livestock.php" class="cg-btn cg-btn-outline w-100">Cancel</a>
         </div>
         <?php endif; ?>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Livestock Records</h2>
      <form method="get" class="d-flex gap-2">
         <select name="sector" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Sectors</option>
            <?php foreach (['Poultry', 'Goats', 'Dairy'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $sectorFilter === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
         </select>
         <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <?php foreach (['Active', 'Sold', 'Deceased'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
         </select>
      </form>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Identifier</th><th>Sector</th><th>Breed</th><th>Qty</th><th>Acquired</th><th>Status</th><th></th></tr></thead>
         <tbody>
         <?php if (!$records): ?>
            <tr><td colspan="7" class="text-muted">No livestock records match this filter.</td></tr>
         <?php else: foreach ($records as $r): ?>
            <tr>
               <td><a href="livestock.php?id=<?php echo (int) $r['id']; ?>"><?php echo htmlspecialchars($r['identifier']); ?></a></td>
               <td><?php echo htmlspecialchars($r['sector']); ?></td>
               <td><?php echo htmlspecialchars($r['breed'] ?: '—'); ?></td>
               <td><?php echo (int) $r['quantity']; ?></td>
               <td><?php echo htmlspecialchars($r['date_acquired']); ?></td>
               <td><span class="cg-badge <?php echo cg_status_badge_class($r['status']); ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
               <td class="text-nowrap">
                  <a href="livestock.php?id=<?php echo (int) $r['id']; ?>" class="cg-btn cg-btn-outline cg-btn-sm">View</a>
                  <?php if ($canEdit): ?>
                  <a href="livestock.php?edit=<?php echo (int) $r['id']; ?>" class="cg-btn cg-btn-outline cg-btn-sm">Edit</a>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete this record?');">
                     <?php echo cg_csrf_field(); ?>
                     <input type="hidden" name="action" value="delete">
                     <input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>">
                     <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm">Delete</button>
                  </form>
                  <?php endif; ?>
               </td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
