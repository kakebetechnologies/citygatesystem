<?php
/**
 * Production Records — daily/periodic yield logging (eggs, milk, weight
 * gain, harvests, ...) against production_records, optionally linked to a
 * specific livestock_records row. Manager/SuperAdmin get the log form;
 * Finance is read-only per the RBAC matrix.
 *
 * ob_start() here (this file only) guards the PRG header('Location: ...')
 * redirect below against this environment's php.ini output_buffering=4096:
 * header.php's sidebar/topbar HTML alone is close to that chunk size, so
 * without an explicit buffer PHP can auto-flush mid-request and lock
 * headers before our POST handler gets a chance to redirect.
 */
ob_start();
$pageModule = 'production';
$pageTitle = 'Production Records';
$pageSub = 'Egg, milk, growth, and harvest yields across the farm';
require_once __DIR__ . '/includes/header.php';

$canEdit = cg_can('production', 'full');
$message = '';

$validSectors = ['Poultry', 'Dairy', 'Crops', 'Goats'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canEdit) {
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'save') {
      $sector = $_POST['sector'] ?? '';
      $metric = trim($_POST['metric'] ?? '');
      $quantity = trim($_POST['quantity'] ?? '');
      $unit = trim($_POST['unit'] ?? '');
      $recordDate = $_POST['record_date'] ?? '';
      $livestockId = (int) ($_POST['livestock_id'] ?? 0);
      $notes = trim($_POST['notes'] ?? '');

      if (in_array($sector, $validSectors, true) && $metric !== '' && is_numeric($quantity) && $unit !== '' && $recordDate !== '') {
         $stmt = $pdo->prepare('INSERT INTO production_records (sector, metric, quantity, unit, record_date, livestock_id, notes, recorded_by) VALUES (?,?,?,?,?,?,?,?)');
         $stmt->execute([
            $sector,
            $metric,
            (float) $quantity,
            $unit,
            $recordDate,
            $livestockId > 0 ? $livestockId : null,
            $notes ?: null,
            $user['id'],
         ]);
         header('Location: production.php?saved=1');
         exit;
      }
      $message = 'Please fill in Sector, Metric, Quantity, Unit, and Record Date.';
   }
}

if (isset($_GET['saved'])) $message = 'Production record logged.';

$sectorFilter = $_GET['sector'] ?? '';

$where = [];
$params = [];
if (in_array($sectorFilter, $validSectors, true)) {
   $where[] = 'p.sector = ?';
   $params[] = $sectorFilter;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
   SELECT p.*, l.identifier AS livestock_identifier
   FROM production_records p
   LEFT JOIN livestock_records l ON l.id = p.livestock_id
   $whereSql
   ORDER BY p.record_date DESC, p.id DESC
");
$stmt->execute($params);
$records = $stmt->fetchAll();

$activeLivestock = $pdo->query("SELECT id, identifier, sector FROM livestock_records WHERE status='Active' ORDER BY sector, identifier")->fetchAll();

$eggsThisMonth = (float) $pdo->query("
   SELECT COALESCE(SUM(quantity),0) FROM production_records
   WHERE metric='Eggs Collected' AND MONTH(record_date)=MONTH(CURDATE()) AND YEAR(record_date)=YEAR(CURDATE())
")->fetchColumn();

$milkThisMonth = (float) $pdo->query("
   SELECT COALESCE(SUM(quantity),0) FROM production_records
   WHERE metric='Milk Yield' AND MONTH(record_date)=MONTH(CURDATE()) AND YEAR(record_date)=YEAR(CURDATE())
")->fetchColumn();

$harvestThisMonth = (float) $pdo->query("
   SELECT COALESCE(SUM(quantity),0) FROM production_records
   WHERE sector='Crops' AND MONTH(record_date)=MONTH(CURDATE()) AND YEAR(record_date)=YEAR(CURDATE())
")->fetchColumn();

$entriesThisMonth = (int) $pdo->query("
   SELECT COUNT(*) FROM production_records
   WHERE MONTH(record_date)=MONTH(CURDATE()) AND YEAR(record_date)=YEAR(CURDATE())
")->fetchColumn();
?>

<?php if ($message): ?>
<div class="alert alert-success" style="border-radius:10px;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon"><i class="fa fa-circle-o"></i></div><div><div class="cg-stat-card-num"><?php echo number_format($eggsThisMonth); ?></div><div class="cg-stat-card-label">Egg Trays This Month</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon blue"><i class="fa fa-tint"></i></div><div><div class="cg-stat-card-num"><?php echo number_format($milkThisMonth, 1); ?> L</div><div class="cg-stat-card-label">Milk Yield This Month</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon gold"><i class="fa fa-leaf"></i></div><div><div class="cg-stat-card-num"><?php echo number_format($harvestThisMonth); ?></div><div class="cg-stat-card-label">Crop Harvest This Month</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon rose"><i class="fa fa-line-chart"></i></div><div><div class="cg-stat-card-num"><?php echo $entriesThisMonth; ?></div><div class="cg-stat-card-label">Entries Logged This Month</div></div></div>
</div>

<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2>Log Production</h2></div>
   <form method="post">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <div class="row g-3">
         <div class="col-md-2">
            <label class="form-label">Sector</label>
            <select name="sector" id="prodSector" class="form-select" required>
               <?php foreach ($validSectors as $s): ?>
               <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-2">
            <label class="form-label">Metric</label>
            <input type="text" name="metric" class="form-control" placeholder="e.g. Eggs Collected / Milk Yield" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Quantity</label>
            <input type="number" step="0.01" name="quantity" class="form-control" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Unit</label>
            <input type="text" name="unit" class="form-control" placeholder="Trays / Litres / Kg" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Record Date</label>
            <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
         </div>
         <div class="col-md-2">
            <label class="form-label">Livestock (optional)</label>
            <select name="livestock_id" id="prodLivestock" class="form-select">
               <option value="">—</option>
               <?php foreach ($activeLivestock as $l): ?>
               <option value="<?php echo (int) $l['id']; ?>" data-sector="<?php echo htmlspecialchars($l['sector']); ?>"><?php echo htmlspecialchars($l['identifier']); ?> (<?php echo htmlspecialchars($l['sector']); ?>)</option>
               <?php endforeach; ?>
            </select>
         </div>
         <div class="col-md-8">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control">
         </div>
         <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="cg-btn cg-btn-primary w-100">Log Production</button>
         </div>
      </div>
   </form>
</div>
<script>
(function () {
   var sectorSel = document.getElementById('prodSector');
   var livestockSel = document.getElementById('prodLivestock');
   var options = Array.prototype.slice.call(livestockSel.options);
   function filterLivestock() {
      var sector = sectorSel.value;
      livestockSel.value = '';
      options.forEach(function (opt) {
         if (!opt.value) { opt.hidden = false; return; }
         opt.hidden = opt.getAttribute('data-sector') !== sector;
      });
   }
   sectorSel.addEventListener('change', filterLivestock);
   filterLivestock();
})();
</script>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Production Records</h2>
      <form method="get" class="d-flex gap-2">
         <select name="sector" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Sectors</option>
            <?php foreach ($validSectors as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $sectorFilter === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
         </select>
      </form>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Date</th><th>Sector</th><th>Metric</th><th>Quantity</th><th>Livestock</th><th>Notes</th></tr></thead>
         <tbody>
         <?php if (!$records): ?>
            <tr><td colspan="6" class="text-muted">No production records match this filter.</td></tr>
         <?php else: foreach ($records as $r): ?>
            <tr>
               <td><?php echo htmlspecialchars($r['record_date']); ?></td>
               <td><?php echo htmlspecialchars($r['sector']); ?></td>
               <td><?php echo htmlspecialchars($r['metric']); ?></td>
               <td><?php echo htmlspecialchars(rtrim(rtrim(number_format((float) $r['quantity'], 2), '0'), '.')); ?> <?php echo htmlspecialchars($r['unit']); ?></td>
               <td><?php echo $r['livestock_id'] ? '<a href="livestock.php?id=' . (int) $r['livestock_id'] . '">' . htmlspecialchars($r['livestock_identifier']) . '</a>' : '—'; ?></td>
               <td><?php echo htmlspecialchars($r['notes'] ?: '—'); ?></td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
