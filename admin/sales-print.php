<?php
/**
 * Standalone printable Sales Report — no sidebar/nav chrome, opened in a
 * new tab from sales.php's "Print Sales Report" link. Reachable directly
 * too, so it enforces auth/RBAC itself rather than relying on sales.php.
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
$user = cg_require_module('sales', 'view');

if (!function_exists('fmt_ugx')) {
   function fmt_ugx($n) { return 'UGX ' . number_format((float) $n); }
}

$status = $_GET['status'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = [];
$params = [];
if (in_array($status, ['Paid', 'Unpaid'], true)) { $where[] = 'status = ?'; $params[] = $status; }
if ($from !== '' && DateTime::createFromFormat('Y-m-d', $from)) { $where[] = 'sale_date >= ?'; $params[] = $from; }
if ($to !== '' && DateTime::createFromFormat('Y-m-d', $to)) { $where[] = 'sale_date <= ?'; $params[] = $to; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM sales $whereSql ORDER BY sale_date DESC, id DESC");
$stmt->execute($params);
$rows = $stmt->fetchAll();

$totalPaid = 0.0;
$totalUnpaid = 0.0;
foreach ($rows as $r) {
   if ($r['status'] === 'Paid') $totalPaid += (float) $r['amount'];
   else $totalUnpaid += (float) $r['amount'];
}
$grandTotal = $totalPaid + $totalUnpaid;

$logoPath = __DIR__ . '/../images/logo/citygatelogo.png';
$logoData = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sales Report — City Gate Mixed Farm</title>
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
   .status-paid { color: #1F6B3A; font-weight: 600; }
   .status-unpaid { color: #A32E2E; font-weight: 600; }
   .totals { margin-top: 20px; width: 320px; margin-left: auto; font-size: 13.5px; }
   .totals div { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #eee; }
   .totals .grand { font-weight: 700; font-size: 16px; border-bottom: none; border-top: 2px solid #2F5D3A; padding-top: 10px; color: #2F5D3A; }
   .report-footer { margin-top: 40px; font-size: 11.5px; color: #999; text-align: center; }
   @media print {
      .print-toolbar { display: none; }
      body { padding: 0; }
   }
</style>
</head>
<body>
   <div class="print-toolbar">
      <a href="sales.php">&larr; Back to Sales</a>
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
         <h2>Sales Report</h2>
         <span>Generated <?php echo date('d M Y, H:i'); ?> by <?php echo htmlspecialchars($user['name']); ?></span>
         <?php if ($status || $from || $to): ?>
            <br><span>Filters: <?php echo htmlspecialchars($status ?: 'All statuses'); ?><?php if ($from) echo ' &middot; from ' . htmlspecialchars($from); ?><?php if ($to) echo ' &middot; to ' . htmlspecialchars($to); ?></span>
         <?php endif; ?>
      </div>
   </div>

   <table>
      <thead><tr><th>Date</th><th>Product</th><th>Qty</th><th>Buyer</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
      <?php if (!$rows): ?>
         <tr><td colspan="6" style="text-align:center;color:#999;">No sales records match this filter.</td></tr>
      <?php else: foreach ($rows as $r): ?>
         <tr>
            <td><?php echo htmlspecialchars($r['sale_date']); ?></td>
            <td><?php echo htmlspecialchars($r['product']); ?></td>
            <td><?php echo htmlspecialchars(rtrim(rtrim(number_format((float) $r['quantity'], 2), '0'), '.')); ?> <?php echo htmlspecialchars($r['unit']); ?></td>
            <td><?php echo htmlspecialchars($r['buyer']); ?></td>
            <td><?php echo fmt_ugx($r['amount']); ?></td>
            <td class="status-<?php echo strtolower($r['status']); ?>"><?php echo htmlspecialchars($r['status']); ?></td>
         </tr>
      <?php endforeach; endif; ?>
      </tbody>
   </table>

   <div class="totals">
      <div><span>Total Records</span><span><?php echo count($rows); ?></span></div>
      <div><span>Total Paid</span><span><?php echo fmt_ugx($totalPaid); ?></span></div>
      <div><span>Total Unpaid</span><span><?php echo fmt_ugx($totalUnpaid); ?></span></div>
      <div class="grand"><span>Grand Total</span><span><?php echo fmt_ugx($grandTotal); ?></span></div>
   </div>

   <div class="report-footer">City Gate Mixed Farm &mdash; Amuca, Lira City, Uganda &mdash; Confidential internal report</div>
</body>
</html>
