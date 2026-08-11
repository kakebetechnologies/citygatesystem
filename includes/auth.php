<?php
/**
 * Session-based auth + RBAC. Include this (after config/db.php) at the top
 * of every admin page. RBAC is enforced here, server-side, before any HTML
 * is sent — not just hidden in the UI.
 */

if (session_status() === PHP_SESSION_NONE) {
   session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'httponly' => true,
      'samesite' => 'Lax',
   ]);
   session_start();
}

// module -> role -> 'full' | 'read' | 'hidden'
const CG_PERMISSIONS = [
   'dashboard' => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'full', 'Admin' => 'full'],
   'visitors'  => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'read'],
   'registry'  => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'read'],
   'sales'     => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'full', 'Admin' => 'read'],
   'blog'      => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'hidden', 'Admin' => 'full'],
   'gallery'   => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'hidden', 'Admin' => 'full'],
   'cms'       => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'full'],
   'email'     => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'hidden', 'Admin' => 'hidden'],
   'users'     => ['SuperAdmin' => 'full', 'Manager' => 'hidden', 'Finance' => 'hidden', 'Admin' => 'hidden'],

   // Farm management suite
   'livestock'  => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'hidden'],
   'health'     => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'hidden', 'Admin' => 'hidden'],
   'inventory'  => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'hidden'],
   'production' => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'hidden'],
   'expenses'   => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'full', 'Admin' => 'hidden'],
   'tasks'      => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'hidden', 'Admin' => 'hidden'],
   'suppliers'  => ['SuperAdmin' => 'full', 'Manager' => 'full', 'Finance' => 'read', 'Admin' => 'hidden'],
];

function cg_current_user(): ?array {
   return $_SESSION['cg_user'] ?? null;
}

function cg_access(string $module): string {
   $user = cg_current_user();
   if (!$user) return 'hidden';
   return CG_PERMISSIONS[$module][$user['role']] ?? 'hidden';
}

function cg_can(string $module, string $action = 'view'): bool {
   $level = cg_access($module);
   if ($action === 'view') return $level === 'full' || $level === 'read';
   return $level === 'full';
}

/** Redirects to login if not authenticated. Call at the top of every admin page. */
function cg_require_login(): array {
   $user = cg_current_user();
   if (!$user) {
      header('Location: login.php');
      exit;
   }
   return $user;
}

/** Redirects to login if not authenticated, or renders a 403 if the role can't view this module at all. */
function cg_require_module(string $module, string $action = 'view'): array {
   $user = cg_require_login();
   if (!cg_can($module, $action)) {
      http_response_code(403);
      echo '<!DOCTYPE html><html><head><title>Access Denied</title></head><body style="font-family:sans-serif;padding:60px;text-align:center;color:#333;"><h2>Access Denied</h2><p>Your role (' . htmlspecialchars($user['role']) . ') does not have access to this page.</p><p><a href="dashboard.php">Back to Dashboard</a></p></body></html>';
      exit;
   }
   return $user;
}

function cg_login(PDO $pdo, string $email, string $password): ?array {
   $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
   $stmt->execute([$email]);
   $user = $stmt->fetch();

   if (!$user || !password_verify($password, $user['password_hash'])) {
      return null;
   }
   if ($user['status'] !== 'active') {
      return null;
   }

   session_regenerate_id(true);
   $_SESSION['cg_user'] = [
      'id' => $user['id'],
      'name' => $user['name'],
      'email' => $user['email'],
      'role' => $user['role'],
   ];
   return $_SESSION['cg_user'];
}

function cg_logout(): void {
   $_SESSION = [];
   session_destroy();
}

function cg_csrf_token(): string {
   if (empty($_SESSION['cg_csrf'])) {
      $_SESSION['cg_csrf'] = bin2hex(random_bytes(32));
   }
   return $_SESSION['cg_csrf'];
}

function cg_csrf_field(): string {
   return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(cg_csrf_token()) . '">';
}

function cg_verify_csrf(): void {
   $token = $_POST['csrf_token'] ?? '';
   if (!$token || !hash_equals($_SESSION['cg_csrf'] ?? '', $token)) {
      http_response_code(400);
      die('Invalid or expired form submission (CSRF check failed). Go back and try again.');
   }
}
