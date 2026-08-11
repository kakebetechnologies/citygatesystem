<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
/**
 * Health & Veterinary — site-wide view of livestock_health_records across
 * every animal/batch. The per-animal "Add Health Record" form already lives
 * on livestock.php?id=X; this page is the cross-farm read + filter view:
 * a Due Soon / Overdue panel plus the full filterable history table.
 */
$pageModule = 'health';
$pageTitle = 'Health & Veterinary';
$pageSub = 'Vaccinations, treatments, checkups, and incidents across the farm';
require_once __DIR__ . '/includes/header.php';

$today = date('Y-m-d');

$typeFilter = $_GET['type'] ?? '';
$validTypes = ['Vaccination', 'Treatment', 'Checkup', 'Incident'];

$where = [];
$params = [];
if (in_array($typeFilter, $validTypes, true)) {
   $where[] = 'h.record_type = ?';
   $params[] = $typeFilter;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
   SELECT h.*, l.identifier AS livestock_identifier, l.sector AS livestock_sector
   FROM livestock_health_records h
   JOIN livestock_records l ON l.id = h.livestock_id
   $whereSql
   ORDER BY h.record_date DESC, h.id DESC
");
$stmt->execute($params);
$records = $stmt->fetchAll();

$dueStmt = $pdo->prepare("
   SELECT h.*, l.identifier AS livestock_identifier, l.sector AS livestock_sector
   FROM livestock_health_records h
   JOIN livestock_records l ON l.id = h.livestock_id
   WHERE h.next_due_date IS NOT NULL AND h.next_due_date <= DATE_ADD(?, INTERVAL 30 DAY)
   ORDER BY h.next_due_date ASC
");
$dueStmt->execute([$today]);
$dueSoon = $dueStmt->fetchAll();

function cg_health_type_badge_class(string $type): string {
   return match ($type) {
      'Vaccination' => 'cg-badge-active',
      'Treatment' => 'cg-badge-scheduled',
      'Checkup' => 'cg-badge-approved',
      'Incident' => 'cg-badge-rejected',
      default => 'cg-badge-draft',
   };
}
?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>Due Soon / Overdue</h2>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Livestock</th><th>Type</th><th>Description</th><th>Next Due</th><th>Status</th></tr></thead>
         <tbody>
         <?php if (!$dueSoon): ?>
            <tr><td colspan="5" class="text-muted">Nothing due in the next 30 days.</td></tr>
         <?php else: foreach ($dueSoon as $d): $isOverdue = $d['next_due_date'] < $today; ?>
            <tr>
               <td><a href="livestock.php?id=<?php echo (int) $d['livestock_id']; ?>"><?php echo htmlspecialchars($d['livestock_identifier']); ?></a> <span class="text-muted">(<?php echo htmlspecialchars($d['livestock_sector']); ?>)</span></td>
               <td><span class="cg-badge <?php echo cg_health_type_badge_class($d['record_type']); ?>"><?php echo htmlspecialchars($d['record_type']); ?></span></td>
               <td><?php echo htmlspecialchars($d['description']); ?></td>
               <td><?php echo htmlspecialchars($d['next_due_date']); ?></td>
               <td>
                  <?php if ($isOverdue): ?>
                     <span class="cg-badge cg-badge-rejected">Overdue</span>
                  <?php else: ?>
                     <span class="cg-badge cg-badge-pending">Due Soon</span>
                  <?php endif; ?>
               </td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Health Records</h2>
      <form method="get" class="d-flex gap-2">
         <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All Types</option>
            <?php foreach ($validTypes as $t): ?>
            <option value="<?php echo $t; ?>" <?php echo $typeFilter === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
            <?php endforeach; ?>
         </select>
      </form>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Livestock</th><th>Type</th><th>Description</th><th>Record Date</th><th>Next Due Date</th><th>Cost</th><th>Performed By</th></tr></thead>
         <tbody>
         <?php if (!$records): ?>
            <tr><td colspan="7" class="text-muted">No health records match this filter.</td></tr>
         <?php else: foreach ($records as $r): ?>
            <tr>
               <td><a href="livestock.php?id=<?php echo (int) $r['livestock_id']; ?>"><?php echo htmlspecialchars($r['livestock_identifier']); ?></a> <span class="text-muted">(<?php echo htmlspecialchars($r['livestock_sector']); ?>)</span></td>
               <td><span class="cg-badge <?php echo cg_health_type_badge_class($r['record_type']); ?>"><?php echo htmlspecialchars($r['record_type']); ?></span></td>
               <td><?php echo htmlspecialchars($r['description']); ?></td>
               <td><?php echo htmlspecialchars($r['record_date']); ?></td>
               <td><?php echo htmlspecialchars($r['next_due_date'] ?: '—'); ?></td>
               <td><?php echo $r['cost'] !== null ? 'UGX ' . number_format((float) $r['cost']) : '—'; ?></td>
               <td><?php echo htmlspecialchars($r['performed_by'] ?: '—'); ?></td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
