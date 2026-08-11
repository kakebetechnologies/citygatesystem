<?php
/**
 * Standalone printable Expenses Report — no sidebar/nav chrome. Applies the
 * same filters (category/supplier/date range/search) as expenses.php so
 * "Print / PDF" always reflects exactly what's on screen.
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
$user = cg_require_module('expenses', 'view');

if (!function_exists('fmt_ugx')) {
   function fmt_ugx($n) { return 'UGX ' . number_format((float) $n); }
}

const CG_EXPENSE_CATEGORIES_PRINT = ['Feed', 'Labor', 'Veterinary', 'Utilities', 'Repairs', 'Transport', 'Other'];

$categoryFilter = $_GET['category'] ?? '';
$supplierFilter = (int) ($_GET['supplier_id'] ?? 0);
$fromFilter = $_GET['from'] ?? '';
$toFilter = $_GET['to'] ?? '';
$searchFilter = trim($_GET['q'] ?? '');

$where = [];
$params = [];
if (in_array($categoryFilter, CG_EXPENSE_CATEGORIES_PRINT, true)) { $where[] = 'e.category = ?'; $params[] = $categoryFilter; }
if ($supplierFilter > 0) { $where[] = 'e.supplier_id = ?'; $params[] = $supplierFilter; }
if ($fromFilter !== '' && DateTime::createFromFormat('Y-m-d', $fromFilter)) { $where[] = 'e.expense_date >= ?'; $params[] = $fromFilter; }
if ($toFilter !== '' && DateTime::createFromFormat('Y-m-d', $toFilter)) { $where[] = 'e.expense_date <= ?'; $params[] = $toFilter; }
if ($searchFilter !== '') { $where[] = 'e.description LIKE ?'; $params[] = '%' . $searchFilter . '%'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT e.*, s.name AS supplier_name FROM expenses e LEFT JOIN suppliers_buyers s ON e.supplier_id = s.id $whereSql ORDER BY e.expense_date DESC, e.id DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$grandTotal = 0.0;
$byCategory = [];
foreach ($rows as $r) {
   $grandTotal += (float) $r['amount'];
   $byCategory[$r['category']] = ($byCategory[$r['category']] ?? 0) + (float) $r['amount'];
}
arsort($byCategory);

$logoPath = __DIR__ . '/../images/logo/citygatelogo.png';
$logoData = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Expenses Report — City Gate Mixed Farm</title>
<style>
   * { box-sizing: border-box; }
   body { font-family: 'Inter', -apple-system, sans-serif; color: #1A1A1A; margin: 0; padding: 32px; background: #fff; }
   .print-toolbar { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 20px; }
   .print-toolbar button, .print-toolbar a {
      font-family: inherit; font-size: 13px; font-weight: 600; padding: 9px 18px; border-radius: 8px;
      border: 1.5px solid #2F5D3A; background: #2F5D3A; color: #fff; cursor: pointer; text-decoration: none;
   }
   .print-toolbar a { background: #fff; color: #2F5D3A; }
   .report-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #C9A978; padding-bottom: 16px; margin-bottom: 20px; }
   .report-header img { height: 64px; }
   .report-header h1 { font-size: 20px; margin: 0 0 4px; color: #2F5D3A; }
   .report-header p { margin: 0; font-size: 12.5px; color: #666; }
   .report-title { text-align: right; }
   .report-title h2 { margin: 0; font-size: 22px; color: #1A1A1A; }
   .report-title span { font-size: 12.5px; color: #888; }
   table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 10px; }
   th { text-align: left; background: #FAFAF8; padding: 9px 10px; border-bottom: 2px solid #e2e2e2; font-size: 11px; text-transform: uppercase; color: #666; }
   td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; }
   .summary-cols { display: flex; gap: 40px; margin-top: 24px; }
   .totals { width: 300px; font-size: 13.5px; }
   .totals div { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #eee; }
   .totals .grand { font-weight: 700; font-size: 16px; border-bottom: none; border-top: 2px solid #B23A48; padding-top: 10px; color: #B23A48; }
   .by-cat { flex: 1; font-size: 13px; }
   .by-cat div { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f4f4f4; }
   .report-footer { margin-top: 40px; font-size: 11.5px; color: #999; text-align: center; }
   @media print { .print-toolbar { display: none; } body { padding: 0; } }
</style>
</head>
<body>
   <div class="print-toolbar">
      <a href="expenses.php">&larr; Back to Expenses</a>
      <button onclick="window.print()">Print / Save as PDF</button>
   </div>

   <div class="report-header">
      <div style="display:flex;align-items:center;gap:14px;">
         <?php if ($logoData): ?><img src="<?php echo $logoData; ?>" alt="City Gate Mixed Farm"><?php endif; ?>
         <div>
            <h1>CITY GATE MIXED FARM</h1>
            <p>Amuca, Lira City, Uganda &middot; +256 123 456 789 &middot; info@citygatefarms.com</p>
         </div>
      </div>
      <div class="report-title">
         <h2>Expenses Report</h2>
         <span>Generated <?php echo date('d M Y, H:i'); ?> by <?php echo htmlspecialchars($user['name']); ?></span>
         <?php if ($categoryFilter || $supplierFilter || $fromFilter || $toFilter || $searchFilter): ?>
            <br><span>Filtered<?php if ($categoryFilter) echo ' &middot; ' . htmlspecialchars($categoryFilter); ?><?php if ($fromFilter) echo ' &middot; from ' . htmlspecialchars($fromFilter); ?><?php if ($toFilter) echo ' &middot; to ' . htmlspecialchars($toFilter); ?></span>
         <?php endif; ?>
      </div>
   </div>

   <table>
      <thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th><th>Supplier</th></tr></thead>
      <tbody>
      <?php if (!$rows): ?>
         <tr><td colspan="5" style="text-align:center;color:#999;">No expenses match this filter.</td></tr>
      <?php else: foreach ($rows as $r): ?>
         <tr>
            <td><?php echo htmlspecialchars($r['expense_date']); ?></td>
            <td><?php echo htmlspecialchars($r['category']); ?></td>
            <td><?php echo htmlspecialchars($r['description']); ?></td>
            <td><?php echo fmt_ugx($r['amount']); ?></td>
            <td><?php echo htmlspecialchars($r['supplier_name'] ?: '—'); ?></td>
         </tr>
      <?php endforeach; endif; ?>
      </tbody>
   </table>

   <div class="summary-cols">
      <div class="by-cat">
         <strong style="display:block;margin-bottom:8px;color:#666;font-size:11px;text-transform:uppercase;">By Category</strong>
         <?php foreach ($byCategory as $cat => $amt): ?>
            <div><span><?php echo htmlspecialchars($cat); ?></span><span><?php echo fmt_ugx($amt); ?></span></div>
         <?php endforeach; ?>
      </div>
      <div class="totals">
         <div><span>Total Records</span><span><?php echo count($rows); ?></span></div>
         <div class="grand"><span>Grand Total</span><span><?php echo fmt_ugx($grandTotal); ?></span></div>
      </div>
   </div>

   <div class="report-footer">City Gate Mixed Farm &mdash; Amuca, Lira City, Uganda &mdash; Confidential internal report</div>
</body>
</html>
