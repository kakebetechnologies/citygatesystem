<?php
/**
 * Suppliers & Buyers — directory of feed suppliers, vets, and produce/
 * livestock buyers. Follows the conventions established in
 * admin/livestock.php (reference module), including its create/edit form
 * pattern via ?edit=ID.
 *
 * ob_start() here (this file only) guards the PRG header('Location: ...')
 * redirects below against this environment's php.ini output_buffering=4096;
 * see admin/inventory.php for the full explanation.
 */
ob_start();
$pageModule = 'suppliers';
$pageTitle = 'Suppliers & Buyers';
$pageSub = 'Directory of feed suppliers, vets, and produce/livestock buyers';
require_once __DIR__ . '/includes/header.php';

$canEdit = cg_can('suppliers', 'full');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'save') {
      $id = (int) ($_POST['id'] ?? 0);
      $name = trim($_POST['name'] ?? '');
      $type = $_POST['type'] ?? '';
      $contactPerson = trim($_POST['contact_person'] ?? '');
      $phone = trim($_POST['phone'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $address = trim($_POST['address'] ?? '');
      $category = trim($_POST['category'] ?? '');
      $notes = trim($_POST['notes'] ?? '');

      if ($name !== '' && in_array($type, ['Supplier', 'Buyer', 'Both'], true)) {
         if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE suppliers_buyers SET name=?, type=?, contact_person=?, phone=?, email=?, address=?, category=?, notes=? WHERE id=?');
            $stmt->execute([$name, $type, $contactPerson ?: null, $phone ?: null, $email ?: null, $address ?: null, $category ?: null, $notes ?: null, $id]);
         } else {
            $stmt = $pdo->prepare('INSERT INTO suppliers_buyers (name, type, contact_person, phone, email, address, category, notes) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$name, $type, $contactPerson ?: null, $phone ?: null, $email ?: null, $address ?: null, $category ?: null, $notes ?: null]);
         }
         header('Location: suppliers.php?saved=1');
         exit;
      }
      $message = 'Please fill in Name and Type.';
   } elseif ($action === 'delete') {
      $id = (int) ($_POST['id'] ?? 0);
      $pdo->prepare('DELETE FROM suppliers_buyers WHERE id=?')->execute([$id]);
      header('Location: suppliers.php?deleted=1');
      exit;
   }
}

if (isset($_GET['saved'])) $message = 'Saved.';
if (isset($_GET['deleted'])) $message = 'Record deleted.';

$editId = (int) ($_GET['edit'] ?? 0);
$typeFilter = $_GET['type'] ?? '';

$where = [];
$params = [];
if (in_array($typeFilter, ['Supplier', 'Buyer', 'Both'], true)) { $where[] = 'type = ?'; $params[] = $typeFilter; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM suppliers_buyers $whereSql ORDER BY name");
$stmt->execute($params);
$records = $stmt->fetchAll();

$editRecord = null;
if ($editId > 0) {
   $s = $pdo->prepare('SELECT * FROM suppliers_buyers WHERE id=?');
   $s->execute([$editId]);
   $editRecord = $s->fetch() ?: null;
}

function cg_supplier_badge_class(string $type): string {
   return match ($type) {
      'Supplier' => 'cg-badge-scheduled',
      'Buyer' => 'cg-badge-approved',
      'Both' => 'cg-badge-pending',
      default => 'cg-badge-draft',
   };
}
?>

<?php if ($message): ?>
<div class="alert alert-success" style="border-radius:10px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2><?php echo $editRecord ? 'Edit Supplier / Buyer' : 'Add Supplier / Buyer'; ?></h2></div>
   <form method="post">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?php echo $editRecord ? (int) $editRecord['id'] : 0; ?>">
      <div class="row g-3">
         <div class="col-md-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($editRecord['name'] ?? ''); ?>" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
               <?php foreach (['Supplier', 'Buyer', 'Both'] as $t): ?>
               <option value="<?php echo $t; ?>" <?php echo ($editRecord['type'] ?? '') === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-3">
            <label class="form-label">Contact Person</label>
            <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($editRecord['contact_person'] ?? ''); ?>">
         </div>
         <div class="col-md-2">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($editRecord['phone'] ?? ''); ?>">
         </div>
         <div class="col-md-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($editRecord['email'] ?? ''); ?>">
         </div>
         <div class="col-md-5">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($editRecord['address'] ?? ''); ?>">
         </div>
         <div class="col-md-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" placeholder="e.g. Feed Supplier" value="<?php echo htmlspecialchars($editRecord['category'] ?? ''); ?>">
         </div>
         <div class="col-md-4">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control" value="<?php echo htmlspecialchars($editRecord['notes'] ?? ''); ?>">
         </div>
         <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="cg-btn cg-btn-primary w-100"><?php echo $editRecord ? 'Save Changes' : 'Add'; ?></button>
         </div>
         <?php if ($editRecord): ?>
         <div class="col-md-2 d-flex align-items-end">
            <a href="suppliers.php" class="cg-btn cg-btn-outline w-100">Cancel</a>
         </div>
         <?php endif; ?>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Suppliers & Buyers</h2>
      <form method="get" class="d-flex gap-2">
         <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Types</option>
            <?php foreach (['Supplier', 'Buyer', 'Both'] as $t): ?>
            <option value="<?php echo $t; ?>" <?php echo $typeFilter === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
            <?php endforeach; ?>
         </select>
      </form>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Name</th><th>Type</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Category</th><?php if ($canEdit): ?><th></th><?php endif; ?></tr></thead>
         <tbody>
         <?php if (!$records): ?>
            <tr><td colspan="<?php echo $canEdit ? 7 : 6; ?>" class="text-muted">No suppliers or buyers match this filter.</td></tr>
         <?php else: foreach ($records as $r): ?>
            <tr>
               <td><?php echo htmlspecialchars($r['name']); ?></td>
               <td><span class="cg-badge <?php echo cg_supplier_badge_class($r['type']); ?>"><?php echo htmlspecialchars($r['type']); ?></span></td>
               <td><?php echo htmlspecialchars($r['contact_person'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($r['phone'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($r['email'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($r['category'] ?: '—'); ?></td>
               <?php if ($canEdit): ?>
               <td class="text-nowrap">
                  <a href="suppliers.php?edit=<?php echo (int) $r['id']; ?>" class="cg-btn cg-btn-outline cg-btn-sm">Edit</a>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete this supplier/buyer?');">
                     <?php echo cg_csrf_field(); ?>
                     <input type="hidden" name="action" value="delete">
                     <input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>">
                     <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm">Delete</button>
                  </form>
               </td>
               <?php endif; ?>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
