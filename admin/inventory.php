<?php
/**
 * Inventory & Stock — feed / medicine / equipment stock levels, with a
 * low-stock alert panel and stock in/out transaction ledger. Follows the
 * conventions established in admin/livestock.php (reference module).
 *
 * ob_start() here (this file only) guards the PRG header('Location: ...')
 * redirects below against this environment's php.ini output_buffering=4096:
 * header.php's sidebar/topbar HTML alone is close to that chunk size, so
 * without an explicit buffer PHP can auto-flush mid-request and lock
 * headers before our POST handlers get a chance to redirect.
 */
ob_start();
$pageModule = 'inventory';
$pageTitle = 'Inventory & Stock';
$pageSub = 'Feed, medicine, and equipment stock levels';
require_once __DIR__ . '/includes/header.php';

$canEdit = cg_can('inventory', 'full');
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'add_item') {
      $name = trim($_POST['name'] ?? '');
      $category = $_POST['category'] ?? '';
      $unit = trim($_POST['unit'] ?? '');
      $quantityOnHand = trim($_POST['quantity_on_hand'] ?? '');
      $reorderLevel = trim($_POST['reorder_level'] ?? '');
      $unitCost = trim($_POST['unit_cost'] ?? '');
      $notes = trim($_POST['notes'] ?? '');

      $validCategory = in_array($category, ['Feed', 'Medicine', 'Equipment', 'Other'], true);

      if ($name !== '' && $validCategory && $unit !== '' && is_numeric($quantityOnHand) && is_numeric($reorderLevel)) {
         $stmt = $pdo->prepare('INSERT INTO inventory_items (name, category, unit, quantity_on_hand, reorder_level, unit_cost, notes) VALUES (?,?,?,?,?,?,?)');
         $stmt->execute([
            $name,
            $category,
            $unit,
            (float) $quantityOnHand,
            (float) $reorderLevel,
            $unitCost !== '' && is_numeric($unitCost) ? (float) $unitCost : null,
            $notes ?: null,
         ]);
         header('Location: inventory.php?saved=1');
         exit;
      }
      $message = 'Please fill in Name, Category, Unit, Quantity on Hand, and Reorder Level (numbers must be valid).';
      $messageType = 'danger';
   } elseif ($action === 'transaction') {
      $itemId = (int) ($_POST['item_id'] ?? 0);
      $type = $_POST['type'] ?? '';
      $quantity = trim($_POST['quantity'] ?? '');
      $reason = trim($_POST['reason'] ?? '');
      $relatedLivestockId = (int) ($_POST['related_livestock_id'] ?? 0);

      $validType = in_array($type, ['In', 'Out'], true);

      if ($itemId > 0 && $validType && is_numeric($quantity) && (float) $quantity > 0) {
         $quantity = (float) $quantity;
         try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT name, unit, quantity_on_hand FROM inventory_items WHERE id=? FOR UPDATE');
            $stmt->execute([$itemId]);
            $item = $stmt->fetch();

            if (!$item) {
               $pdo->rollBack();
               $message = 'Selected item was not found.';
               $messageType = 'danger';
            } elseif ($type === 'Out' && $quantity > (float) $item['quantity_on_hand']) {
               $pdo->rollBack();
               $message = 'Cannot record this "Out" transaction: ' . htmlspecialchars($quantity . ' ' . $item['unit'])
                  . ' exceeds current stock on hand of ' . htmlspecialchars(rtrim(rtrim(number_format((float) $item['quantity_on_hand'], 2), '0'), '.') . ' ' . $item['unit'])
                  . ' for "' . htmlspecialchars($item['name']) . '".';
               $messageType = 'danger';
            } else {
               $delta = $type === 'In' ? $quantity : -$quantity;
               $ins = $pdo->prepare('INSERT INTO inventory_transactions (item_id, type, quantity, reason, related_livestock_id, performed_by) VALUES (?,?,?,?,?,?)');
               $ins->execute([$itemId, $type, $quantity, $reason ?: null, $relatedLivestockId > 0 ? $relatedLivestockId : null, $user['id']]);

               $upd = $pdo->prepare('UPDATE inventory_items SET quantity_on_hand = quantity_on_hand + ? WHERE id=?');
               $upd->execute([$delta, $itemId]);

               $pdo->commit();
               header('Location: inventory.php?saved=1');
               exit;
            }
         } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
               $pdo->rollBack();
            }
            $message = 'Something went wrong recording the transaction. Please try again.';
            $messageType = 'danger';
         }
      } else {
         $message = 'Please choose a valid Type and a Quantity greater than zero.';
         $messageType = 'danger';
      }
   }
}

if (isset($_GET['saved']) && $message === '') {
   $message = 'Saved.';
   $messageType = 'success';
}

$lowStockItems = $pdo->query('SELECT * FROM inventory_items WHERE quantity_on_hand <= reorder_level ORDER BY name')->fetchAll();
$items = $pdo->query('SELECT * FROM inventory_items ORDER BY category, name')->fetchAll();
$activeLivestock = $pdo->query("SELECT id, identifier FROM livestock_records WHERE status='Active' ORDER BY identifier")->fetchAll();

$transactions = $pdo->query(
   'SELECT t.*, i.name AS item_name, i.unit AS item_unit, u.name AS performed_by_name, lr.identifier AS livestock_identifier
    FROM inventory_transactions t
    JOIN inventory_items i ON t.item_id = i.id
    LEFT JOIN users u ON t.performed_by = u.id
    LEFT JOIN livestock_records lr ON t.related_livestock_id = lr.id
    ORDER BY t.created_at DESC, t.id DESC
    LIMIT 30'
)->fetchAll();

function cg_fmt_qty($val): string {
   return rtrim(rtrim(number_format((float) $val, 2), '0'), '.');
}

function cg_txn_badge_class(string $type): string {
   return $type === 'In' ? 'cg-badge-approved' : 'cg-badge-pending';
}
?>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType === 'danger' ? 'danger' : 'success'; ?>" style="border-radius:10px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<!-- ============ Low stock alert ============ -->
<div class="cg-panel" style="border:1.5px solid <?php echo $lowStockItems ? '#A32E2E' : '#eee'; ?>;">
   <div class="cg-panel-head">
      <h2><i class="fa fa-exclamation-triangle" style="color:#A32E2E;"></i> Low Stock Alert
         <?php if ($lowStockItems): ?><span class="cg-badge cg-badge-rejected"><?php echo count($lowStockItems); ?></span><?php endif; ?>
      </h2>
   </div>
   <?php if (!$lowStockItems): ?>
   <p class="text-muted mb-0">All items are above their reorder level. Nothing needs restocking right now.</p>
   <?php else: ?>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Item</th><th>Category</th><th>Quantity on Hand</th><th>Reorder Level</th><th>Shortfall</th></tr></thead>
         <tbody>
         <?php foreach ($lowStockItems as $li): ?>
            <tr style="background:#FBE2E2;">
               <td><strong><?php echo htmlspecialchars($li['name']); ?></strong></td>
               <td><?php echo htmlspecialchars($li['category']); ?></td>
               <td><?php echo cg_fmt_qty($li['quantity_on_hand']); ?> <?php echo htmlspecialchars($li['unit']); ?></td>
               <td><?php echo cg_fmt_qty($li['reorder_level']); ?> <?php echo htmlspecialchars($li['unit']); ?></td>
               <td><span class="cg-badge cg-badge-rejected">-<?php echo cg_fmt_qty($li['reorder_level'] - $li['quantity_on_hand']); ?> <?php echo htmlspecialchars($li['unit']); ?></span></td>
            </tr>
         <?php endforeach; ?>
         </tbody>
      </table>
   </div>
   <?php endif; ?>
</div>

<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2>Add Item</h2></div>
   <form method="post">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="add_item">
      <div class="row g-3">
         <div class="col-md-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
               <?php foreach (['Feed', 'Medicine', 'Equipment', 'Other'] as $c): ?>
               <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-1">
            <label class="form-label">Unit</label>
            <input type="text" name="unit" class="form-control" placeholder="Kg" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Qty on Hand</label>
            <input type="number" step="0.01" min="0" name="quantity_on_hand" class="form-control" value="0" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Reorder Level</label>
            <input type="number" step="0.01" min="0" name="reorder_level" class="form-control" value="0" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Unit Cost (UGX)</label>
            <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
         </div>
         <div class="col-md-10">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control">
         </div>
         <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="cg-btn cg-btn-primary w-100">Add Item</button>
         </div>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>All Inventory Items</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead>
         <tr>
            <th>Name</th><th>Category</th><th>Unit</th><th>Quantity on Hand</th><th>Reorder Level</th><th>Unit Cost</th><th>Updated At</th>
            <?php if ($canEdit): ?><th></th><?php endif; ?>
         </tr>
         </thead>
         <tbody>
         <?php if (!$items): ?>
            <tr><td colspan="<?php echo $canEdit ? 8 : 7; ?>" class="text-muted">No inventory items yet.</td></tr>
         <?php else: foreach ($items as $it):
            $isLow = (float) $it['quantity_on_hand'] <= (float) $it['reorder_level'];
         ?>
            <tr<?php echo $isLow ? ' style="background:#FBE2E2;"' : ''; ?>>
               <td><?php echo htmlspecialchars($it['name']); ?></td>
               <td><?php echo htmlspecialchars($it['category']); ?></td>
               <td><?php echo htmlspecialchars($it['unit']); ?></td>
               <td>
                  <?php echo cg_fmt_qty($it['quantity_on_hand']); ?>
                  <?php if ($isLow): ?><span class="cg-badge cg-badge-rejected">Low Stock</span><?php endif; ?>
               </td>
               <td><?php echo cg_fmt_qty($it['reorder_level']); ?></td>
               <td><?php echo $it['unit_cost'] !== null ? 'UGX ' . number_format((float) $it['unit_cost']) : '—'; ?></td>
               <td><?php echo htmlspecialchars($it['updated_at']); ?></td>
               <?php if ($canEdit): ?>
               <td class="text-nowrap">
                  <details>
                     <summary class="cg-btn cg-btn-outline cg-btn-sm" style="cursor:pointer;">Record Txn</summary>
                     <form method="post" class="mt-2" style="min-width:260px;">
                        <?php echo cg_csrf_field(); ?>
                        <input type="hidden" name="action" value="transaction">
                        <input type="hidden" name="item_id" value="<?php echo (int) $it['id']; ?>">
                        <div class="row g-2">
                           <div class="col-6">
                              <select name="type" class="form-select form-select-sm" required>
                                 <option value="In">In</option>
                                 <option value="Out">Out</option>
                              </select>
                           </div>
                           <div class="col-6">
                              <input type="number" step="0.01" min="0.01" name="quantity" class="form-control form-control-sm" placeholder="Qty" required>
                           </div>
                           <div class="col-12">
                              <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason">
                           </div>
                           <div class="col-12">
                              <select name="related_livestock_id" class="form-select form-select-sm">
                                 <option value="">Related livestock (optional)</option>
                                 <?php foreach ($activeLivestock as $lv): ?>
                                 <option value="<?php echo (int) $lv['id']; ?>"><?php echo htmlspecialchars($lv['identifier']); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                           <div class="col-12">
                              <button type="submit" class="cg-btn cg-btn-primary cg-btn-sm w-100">Save Transaction</button>
                           </div>
                        </div>
                     </form>
                  </details>
               </td>
               <?php endif; ?>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>Recent Transactions</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Date</th><th>Item</th><th>Type</th><th>Quantity</th><th>Reason</th><th>Related Livestock</th><th>Performed By</th></tr></thead>
         <tbody>
         <?php if (!$transactions): ?>
            <tr><td colspan="7" class="text-muted">No transactions recorded yet.</td></tr>
         <?php else: foreach ($transactions as $t): ?>
            <tr>
               <td><?php echo htmlspecialchars($t['created_at']); ?></td>
               <td><?php echo htmlspecialchars($t['item_name']); ?></td>
               <td><span class="cg-badge <?php echo cg_txn_badge_class($t['type']); ?>"><?php echo htmlspecialchars($t['type']); ?></span></td>
               <td><?php echo cg_fmt_qty($t['quantity']); ?> <?php echo htmlspecialchars($t['item_unit']); ?></td>
               <td><?php echo htmlspecialchars($t['reason'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($t['livestock_identifier'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($t['performed_by_name'] ?: '—'); ?></td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
