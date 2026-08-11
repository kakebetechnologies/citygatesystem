<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'dashboard';
$pageTitle = 'Dashboard';
$pageSub = 'Overview of farm operations';
require_once __DIR__ . '/includes/header.php';

function fmt_ugx($n) { return 'UGX ' . number_format((float)$n); }

$thisMonth = (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();
$pending = (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn();
$revenueWeek = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM sales WHERE status='Paid' AND sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
$publishedPosts = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status='published'")->fetchColumn();

$recentAppointments = $pdo->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentSales = $pdo->query("SELECT * FROM sales ORDER BY sale_date DESC LIMIT 5")->fetchAll();

// Appointments per month, last 6 months
$apptTrend = $pdo->query("
   SELECT DATE_FORMAT(created_at, '%Y-%m') ym, COUNT(*) c
   FROM appointments
   WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
   GROUP BY ym ORDER BY ym
")->fetchAll();

// Sales revenue per month, last 6 months
$salesTrend = $pdo->query("
   SELECT DATE_FORMAT(sale_date, '%Y-%m') ym, SUM(amount) total
   FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
   GROUP BY ym ORDER BY ym
")->fetchAll();

// Sales by product
$byProduct = $pdo->query("SELECT product, SUM(amount) total FROM sales GROUP BY product ORDER BY total DESC LIMIT 6")->fetchAll();

// Appointment purpose breakdown
$byPurpose = $pdo->query("SELECT purpose, COUNT(*) c FROM appointments GROUP BY purpose")->fetchAll();

// ============ Farm management suite additions ============
$activeLivestock = (int) $pdo->query("SELECT COALESCE(SUM(quantity),0) FROM livestock_records WHERE status='Active'")->fetchColumn();
$lowStockCount = (int) $pdo->query("SELECT COUNT(*) FROM inventory_items WHERE quantity_on_hand <= reorder_level")->fetchColumn();
$overdueTasksCount = (int) $pdo->query("SELECT COUNT(*) FROM tasks WHERE status='Pending' AND due_date < CURDATE()")->fetchColumn();
$revenueMonth = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM sales WHERE status='Paid' AND MONTH(sale_date)=MONTH(CURDATE()) AND YEAR(sale_date)=YEAR(CURDATE())")->fetchColumn();
$expensesMonth = (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE MONTH(expense_date)=MONTH(CURDATE()) AND YEAR(expense_date)=YEAR(CURDATE())")->fetchColumn();
$netMonth = $revenueMonth - $expensesMonth;

$livestockBySector = $pdo->query("SELECT sector, SUM(quantity) qty FROM livestock_records WHERE status='Active' GROUP BY sector")->fetchAll();
$expensesByCategory = $pdo->query("
   SELECT category, SUM(amount) total FROM expenses
   WHERE expense_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
   GROUP BY category ORDER BY total DESC
")->fetchAll();

$upcomingHealth = $pdo->query("
   SELECT h.*, l.identifier, l.sector FROM livestock_health_records h
   JOIN livestock_records l ON l.id = h.livestock_id
   WHERE h.next_due_date IS NOT NULL AND h.next_due_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
   ORDER BY h.next_due_date ASC LIMIT 5
")->fetchAll();
$overdueTasksList = $pdo->query("
   SELECT t.*, u.name AS assignee FROM tasks t
   LEFT JOIN users u ON u.id = t.assigned_to
   WHERE t.status='Pending' AND t.due_date < CURDATE()
   ORDER BY t.due_date ASC LIMIT 5
")->fetchAll();

function statusBadge($status) {
   $map = ['Pending' => 'pending', 'Approved' => 'approved', 'Rejected' => 'rejected', 'Arrived' => 'arrived'];
   $cls = $map[$status] ?? 'pending';
   return '<span class="cg-badge cg-badge-' . $cls . '">' . htmlspecialchars($status) . '</span>';
}
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$firstName = explode(' ', trim($user['name']))[0];
?>
<div class="cg-welcome-banner" style="background-image:url('../images/gallary/pexels-chicken-1867521_1920.jpg');">
   <div class="cg-welcome-banner-inner">
      <div class="eyebrow">City Gate Mixed Farm &middot; <?php echo htmlspecialchars($user['role']); ?></div>
      <h1><?php echo $greeting; ?>, <?php echo htmlspecialchars($firstName); ?> 👋</h1>
      <p><?php echo date('l, d F Y'); ?> &mdash; here's how the farm is doing today.</p>
   </div>
</div>

<div class="cg-stat-group-label"><i class="fa fa-users"></i> Visitors &amp; Content</div>
<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon"><i class="fa fa-calendar-check-o"></i></div><div><div class="cg-stat-card-num"><?php echo $thisMonth; ?></div><div class="cg-stat-card-label">Visitors This Month</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon blue"><i class="fa fa-clock-o"></i></div><div><div class="cg-stat-card-num"><?php echo $pending; ?></div><div class="cg-stat-card-label">Pending Appointments</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon purple"><i class="fa fa-pencil-square-o"></i></div><div><div class="cg-stat-card-num"><?php echo $publishedPosts; ?></div><div class="cg-stat-card-label">Published Blog Posts</div></div></div>
</div>

<div class="cg-stat-group-label"><i class="fa fa-paw"></i> Farm Operations</div>
<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon teal"><i class="fa fa-paw"></i></div><div><div class="cg-stat-card-num"><?php echo number_format($activeLivestock); ?></div><div class="cg-stat-card-label">Total Head Count (all sectors)</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon rose"><i class="fa fa-cubes"></i></div><div><div class="cg-stat-card-num"><?php echo $lowStockCount; ?></div><div class="cg-stat-card-label">Low-Stock Inventory Items</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon rose"><i class="fa fa-exclamation-triangle"></i></div><div><div class="cg-stat-card-num"><?php echo $overdueTasksCount; ?></div><div class="cg-stat-card-label">Overdue Tasks</div></div></div>
</div>

<div class="cg-stat-group-label"><i class="fa fa-money"></i> Financial Health</div>
<div class="cg-stat-cards">
   <div class="cg-stat-card"><div class="cg-stat-card-icon gold"><i class="fa fa-money"></i></div><div><div class="cg-stat-card-num"><?php echo fmt_ugx($revenueWeek); ?></div><div class="cg-stat-card-label">Revenue This Week</div></div></div>
   <div class="cg-stat-card"><div class="cg-stat-card-icon <?php echo $netMonth >= 0 ? 'gold' : 'rose'; ?>"><i class="fa fa-balance-scale"></i></div><div><div class="cg-stat-card-num"><?php echo fmt_ugx($netMonth); ?></div><div class="cg-stat-card-label">Net (Revenue − Expenses) This Month</div></div></div>
</div>

<?php if ($upcomingHealth || $overdueTasksList): ?>
<div class="cg-panel" style="border-left:4px solid #B23A48;">
   <div class="cg-panel-head"><h2><i class="fa fa-bell" style="color:#B23A48;margin-right:6px;"></i>Needs Attention</h2></div>
   <div class="row g-3">
      <div class="col-md-6">
         <h6 class="mb-2" style="font-size:13px;color:#888;text-transform:uppercase;">Health Due Within 30 Days</h6>
         <?php if (!$upcomingHealth): ?>
            <p class="text-muted small mb-0">Nothing due soon.</p>
         <?php else: foreach ($upcomingHealth as $h): $overdue = $h['next_due_date'] < date('Y-m-d'); ?>
            <div class="d-flex justify-content-between small mb-2">
               <span><a href="livestock.php?id=<?php echo (int) $h['livestock_id']; ?>"><?php echo htmlspecialchars($h['identifier']); ?></a> — <?php echo htmlspecialchars($h['record_type']); ?></span>
               <span class="<?php echo $overdue ? 'text-danger fw-bold' : 'text-muted'; ?>"><?php echo htmlspecialchars($h['next_due_date']); ?></span>
            </div>
         <?php endforeach; endif; ?>
      </div>
      <div class="col-md-6">
         <h6 class="mb-2" style="font-size:13px;color:#888;text-transform:uppercase;">Overdue Tasks</h6>
         <?php if (!$overdueTasksList): ?>
            <p class="text-muted small mb-0">No overdue tasks.</p>
         <?php else: foreach ($overdueTasksList as $t): ?>
            <div class="d-flex justify-content-between small mb-2">
               <span><?php echo htmlspecialchars($t['title']); ?><?php if ($t['assignee']): ?> <span class="text-muted">(<?php echo htmlspecialchars($t['assignee']); ?>)</span><?php endif; ?></span>
               <span class="text-danger fw-bold"><?php echo htmlspecialchars($t['due_date']); ?></span>
            </div>
         <?php endforeach; endif; ?>
      </div>
   </div>
</div>
<?php endif; ?>

<div class="cg-stat-group-label"><i class="fa fa-bar-chart"></i> Analytics &amp; Trends</div>
<div class="row g-4 mb-1">
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Appointments — Last 6 Months</h2></div>
         <canvas id="apptChart" height="200"></canvas>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Revenue — Last 6 Months</h2></div>
         <canvas id="revenueChart" height="200"></canvas>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Sales by Product</h2></div>
         <canvas id="productChart" height="220"></canvas>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Visit Purpose Breakdown</h2></div>
         <canvas id="purposeChart" height="220"></canvas>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Active Livestock by Sector</h2></div>
         <canvas id="livestockChart" height="220"></canvas>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Expenses by Category — Last 6 Months</h2></div>
         <canvas id="expenseChart" height="220"></canvas>
      </div>
   </div>
</div>

<div class="cg-stat-group-label"><i class="fa fa-history"></i> Recent Activity</div>
<div class="row g-4">
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Recent Appointment Requests</h2><a href="visitors.php" class="cg-btn cg-btn-outline cg-btn-sm">View All</a></div>
         <div class="cg-table-wrap"><table class="cg-table"><thead><tr><th>Institution / Contact</th><th>Visit Date</th><th>Status</th></tr></thead><tbody>
         <?php if (!$recentAppointments): ?>
            <tr><td colspan="3" class="text-muted">No appointment requests yet.</td></tr>
         <?php else: foreach ($recentAppointments as $a): ?>
            <tr><td><?php echo htmlspecialchars($a['institution'] ?: $a['contact_name']); ?></td><td><?php echo htmlspecialchars($a['visit_date'] ?: '—'); ?></td><td><?php echo statusBadge($a['status']); ?></td></tr>
         <?php endforeach; endif; ?>
         </tbody></table></div>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="cg-panel">
         <div class="cg-panel-head"><h2>Recent Sales</h2><a href="sales.php" class="cg-btn cg-btn-outline cg-btn-sm">View All</a></div>
         <div class="cg-table-wrap"><table class="cg-table"><thead><tr><th>Product</th><th>Buyer</th><th>Amount</th></tr></thead><tbody>
         <?php if (!$recentSales): ?>
            <tr><td colspan="3" class="text-muted">No sales recorded yet.</td></tr>
         <?php else: foreach ($recentSales as $s): ?>
            <tr><td><?php echo htmlspecialchars($s['product']); ?></td><td><?php echo htmlspecialchars($s['buyer']); ?></td><td><?php echo fmt_ugx($s['amount']); ?></td></tr>
         <?php endforeach; endif; ?>
         </tbody></table></div>
      </div>
   </div>
</div>

<?php
$extraScripts = ['https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js'];
require_once __DIR__ . '/includes/footer.php';
?>
<script>
(function () {
   var green = '#2F5D3A', gold = '#C9A978', chartFont = { family: 'Inter, sans-serif', size: 12 };
   Chart.defaults.font = chartFont;
   Chart.defaults.color = '#666';

   new Chart(document.getElementById('apptChart'), {
      type: 'bar',
      data: {
         labels: <?php echo json_encode(array_column($apptTrend, 'ym')); ?>,
         datasets: [{ label: 'Appointments', data: <?php echo json_encode(array_map('intval', array_column($apptTrend, 'c'))); ?>, backgroundColor: green, borderRadius: 6 }]
      },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
   });

   new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: {
         labels: <?php echo json_encode(array_column($salesTrend, 'ym')); ?>,
         datasets: [{ label: 'Revenue (UGX)', data: <?php echo json_encode(array_map('floatval', array_column($salesTrend, 'total'))); ?>, borderColor: gold, backgroundColor: 'rgba(201,169,120,0.15)', fill: true, tension: 0.35 }]
      },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
   });

   new Chart(document.getElementById('productChart'), {
      type: 'bar',
      data: {
         labels: <?php echo json_encode(array_column($byProduct, 'product')); ?>,
         datasets: [{ label: 'Revenue (UGX)', data: <?php echo json_encode(array_map('floatval', array_column($byProduct, 'total'))); ?>, backgroundColor: green, borderRadius: 6 }]
      },
      options: { indexAxis: 'y', plugins: { legend: { display: false } } }
   });

   new Chart(document.getElementById('purposeChart'), {
      type: 'doughnut',
      data: {
         labels: <?php echo json_encode(array_column($byPurpose, 'purpose')); ?>,
         datasets: [{ data: <?php echo json_encode(array_map('intval', array_column($byPurpose, 'c'))); ?>, backgroundColor: ['#2F5D3A', '#C9A978', '#7a3518', '#6E7F38', '#1F3F27', '#b8a57a'] }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
   });

   new Chart(document.getElementById('livestockChart'), {
      type: 'doughnut',
      data: {
         labels: <?php echo json_encode(array_column($livestockBySector, 'sector')); ?>,
         datasets: [{ data: <?php echo json_encode(array_map('intval', array_column($livestockBySector, 'qty'))); ?>, backgroundColor: ['#2F5D3A', '#C9A978', '#1F3F27'] }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
   });

   new Chart(document.getElementById('expenseChart'), {
      type: 'bar',
      data: {
         labels: <?php echo json_encode(array_column($expensesByCategory, 'category')); ?>,
         datasets: [{ label: 'Expenses (UGX)', data: <?php echo json_encode(array_map('floatval', array_column($expensesByCategory, 'total'))); ?>, backgroundColor: '#B23A48', borderRadius: 6 }]
      },
      options: { indexAxis: 'y', plugins: { legend: { display: false } } }
   });
})();
</script>
