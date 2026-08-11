<?php
/**
 * Shared admin page shell. Every admin page (except login.php) sets:
 *   $pageModule = 'dashboard';   // one of the CG_PERMISSIONS keys in includes/auth.php
 *   $pageTitle  = 'Dashboard';
 *   $pageSub    = 'Overview of farm operations'; // optional
 *   $pageAction = 'view'; // optional, defaults to 'view' — pass 'full' if the whole page requires write access
 * ...then requires this file. It enforces RBAC server-side via
 * cg_require_module() before rendering anything, renders <head> + sidebar +
 * topbar, and opens the .cg-admin-content container (page must close it and
 * include admin/includes/footer.php).
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$pageAction = $pageAction ?? 'view';
$user = cg_require_module($pageModule, $pageAction);

function cg_initials(string $name): string {
   $parts = preg_split('/\s+/', trim($name));
   $first = $parts[0][0] ?? '?';
   $second = $parts[1][0] ?? '';
   return strtoupper($first . $second);
}

$navGroups = [
   'Overview' => [
      ['module' => 'dashboard', 'href' => 'dashboard.php', 'icon' => 'fa-th-large', 'label' => 'Dashboard'],
   ],
   'Visitor Management' => [
      ['module' => 'visitors', 'href' => 'visitors.php', 'icon' => 'fa-calendar-check-o', 'label' => 'Visitors & Appointments'],
      ['module' => 'registry', 'href' => 'registry.php', 'icon' => 'fa-address-book-o', 'label' => 'Front-Desk Registry'],
   ],
   'Farm Operations' => [
      ['module' => 'livestock', 'href' => 'livestock.php', 'icon' => 'fa-paw', 'label' => 'Livestock & Batches'],
      ['module' => 'health', 'href' => 'health.php', 'icon' => 'fa-heartbeat', 'label' => 'Health & Veterinary'],
      ['module' => 'production', 'href' => 'production.php', 'icon' => 'fa-line-chart', 'label' => 'Production Records'],
      ['module' => 'inventory', 'href' => 'inventory.php', 'icon' => 'fa-cubes', 'label' => 'Inventory & Stock'],
      ['module' => 'tasks', 'href' => 'tasks.php', 'icon' => 'fa-check-square-o', 'label' => 'Tasks & Schedule'],
   ],
   'Finance' => [
      ['module' => 'sales', 'href' => 'sales.php', 'icon' => 'fa-money', 'label' => 'Sales & Invoicing'],
      ['module' => 'expenses', 'href' => 'expenses.php', 'icon' => 'fa-credit-card', 'label' => 'Expenses'],
      ['module' => 'suppliers', 'href' => 'suppliers.php', 'icon' => 'fa-handshake-o', 'label' => 'Suppliers & Buyers'],
   ],
   'Content & Marketing' => [
      ['module' => 'blog', 'href' => 'blog-manager.php', 'icon' => 'fa-pencil-square-o', 'label' => 'Blog'],
      ['module' => 'gallery', 'href' => 'gallery.php', 'icon' => 'fa-picture-o', 'label' => 'Gallery & Videos'],
      ['module' => 'cms', 'href' => 'cms.php', 'icon' => 'fa-file-text-o', 'label' => 'Website Content'],
      ['module' => 'email', 'href' => 'email.php', 'icon' => 'fa-envelope-o', 'label' => 'Email Visitors'],
   ],
   'Administration' => [
      ['module' => 'users', 'href' => 'users.php', 'icon' => 'fa-users', 'label' => 'User Management'],
   ],
];
$currentFile = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title><?php echo htmlspecialchars($pageTitle); ?> | City Gate Farm Admin</title>
   <meta name="robots" content="noindex">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
   <link rel="stylesheet" type="text/css" href="../css/font-awesome.css">
   <link rel="stylesheet" href="../css/cg-theme.css">
   <link rel="stylesheet" href="../css/cg-admin.css">
   <link rel="shortcut icon" type="image/png" href="../images/logo/favcon.png">
</head>
<body class="cg-admin-body">
   <div class="cg-admin-overlay" id="cgAdminOverlay"></div>
   <aside class="cg-admin-sidebar" id="cgAdminSidebar">
      <div class="cg-admin-sidebar-brand">
         <img src="../images/logo/citygatelogo.png" alt="City Gate Farm">
         <span>City Gate<br>Farm Admin</span>
      </div>
      <nav class="cg-admin-nav">
         <?php foreach ($navGroups as $groupLabel => $items):
            $visibleItems = array_filter($items, fn($item) => cg_access($item['module']) !== 'hidden');
            if (!$visibleItems) continue;
         ?>
         <div class="cg-admin-nav-group">
            <div class="cg-admin-nav-group-label"><?php echo htmlspecialchars($groupLabel); ?></div>
            <?php foreach ($visibleItems as $item):
               $isActive = ($item['href'] === $currentFile) ? ' active' : '';
            ?>
            <a href="<?php echo $item['href']; ?>" class="nav-link<?php echo $isActive; ?>">
               <i class="fa <?php echo $item['icon']; ?>"></i><span><?php echo $item['label']; ?></span>
            </a>
            <?php endforeach; ?>
         </div>
         <?php endforeach; ?>
      </nav>
      <div class="cg-admin-sidebar-foot">
         <div class="cg-admin-user">
            <div class="cg-admin-user-avatar"><?php echo cg_initials($user['name']); ?></div>
            <div>
               <div class="cg-admin-user-name"><?php echo htmlspecialchars($user['name']); ?></div>
               <div class="cg-admin-user-role"><?php echo htmlspecialchars($user['role']); ?></div>
            </div>
         </div>
         <a href="logout.php" class="cg-admin-logout"><i class="fa fa-sign-out"></i> Log Out</a>
      </div>
   </aside>
   <div class="cg-admin-main">
      <header class="cg-admin-topbar">
         <div style="display:flex;align-items:center;gap:14px;">
            <button class="cg-admin-toggle" id="cgAdminToggle"><i class="fa fa-bars"></i></button>
            <div>
               <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
               <?php if (!empty($pageSub)): ?><p class="cg-admin-topbar-sub"><?php echo htmlspecialchars($pageSub); ?></p><?php endif; ?>
            </div>
         </div>
         <a href="../index.php" class="cg-btn cg-btn-outline cg-btn-sm" target="_blank"><i class="fa fa-external-link"></i> View Site</a>
      </header>
      <div class="cg-admin-content">
