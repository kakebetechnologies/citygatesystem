<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'users';
$pageTitle = 'User Management';
$pageSub = 'Manage admin accounts and roles';
require_once __DIR__ . '/includes/header.php';

// This module is SuperAdmin-only per RBAC (cg_require_module already blocked
// everyone else before we got here), so no extra role checks are needed —
// but we still gate writes with cg_can('users','full') defensively.
$canEdit = cg_can('users', 'full');
$roles = ['SuperAdmin', 'Manager', 'Finance', 'Admin'];
$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!$canEdit) {
      http_response_code(403);
      die('You do not have permission to manage users.');
   }
   cg_verify_csrf();
   $action = $_POST['action'] ?? '';

   if ($action === 'save') {
      $id = (int) ($_POST['id'] ?? 0);
      $name = trim($_POST['name'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $password = (string) ($_POST['password'] ?? '');
      $role = $_POST['role'] ?? '';

      if ($name === '') $errors[] = 'Name is required.';
      if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
      if (!in_array($role, $roles, true)) $errors[] = 'Invalid role.';
      if ($id === 0 && $password === '') $errors[] = 'Password is required for a new user.';

      if (!$errors) {
         // Check email uniqueness (excluding self on edit)
         $chk = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
         $chk->execute([$email, $id]);
         if ($chk->fetch()) {
            $errors[] = 'That email is already in use by another user.';
         }
      }

      if (!$errors) {
         if ($id > 0) {
            // Edit existing user
            if ($password !== '') {
               $hash = password_hash($password, PASSWORD_DEFAULT);
               $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ?, password_hash = ? WHERE id = ?');
               $stmt->execute([$name, $email, $role, $hash, $id]);
            } else {
               $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
               $stmt->execute([$name, $email, $role, $id]);
            }
            header('Location: users.php?saved=1');
            exit;
         } else {
            // New user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, ?, "active")');
            $stmt->execute([$name, $email, $hash, $role]);
            header('Location: users.php?saved=1');
            exit;
         }
      }
   } elseif ($action === 'toggle_status') {
      $id = (int) ($_POST['id'] ?? 0);
      if ($id !== (int) cg_current_user()['id']) {
         $stmt = $pdo->prepare("UPDATE users SET status = IF(status = 'active', 'suspended', 'active') WHERE id = ?");
         $stmt->execute([$id]);
      }
      header('Location: users.php?saved=1');
      exit;
   } elseif ($action === 'delete') {
      $id = (int) ($_POST['id'] ?? 0);
      if ($id !== (int) cg_current_user()['id']) {
         $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
         $stmt->execute([$id]);
      }
      header('Location: users.php?deleted=1');
      exit;
   }
}

if (isset($_GET['saved'])) $message = 'Saved.';
if (isset($_GET['deleted'])) $message = 'User deleted.';

// Determine edit mode
$editUser = null;
if ($canEdit && isset($_GET['edit'])) {
   $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
   $stmt->execute([(int) $_GET['edit']]);
   $editUser = $stmt->fetch();
}
$showForm = $canEdit && (isset($_GET['new']) || $editUser);

$users = $pdo->query('SELECT * FROM users ORDER BY created_at ASC')->fetchAll();
$myId = (int) cg_current_user()['id'];

function cg_role_badge($role) {
   return '<span class="cg-badge" style="background:#eee;color:#333;">' . htmlspecialchars($role) . '</span>';
}
?>
<?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div><?php endif; ?>

<?php if ($canEdit): ?>
<div class="cg-panel">
   <div class="cg-panel-head">
      <h2><?php echo $showForm ? ($editUser ? 'Edit User' : 'New User') : 'Users'; ?></h2>
      <?php if (!$showForm): ?><a href="users.php?new=1" class="cg-btn cg-btn-primary cg-btn-sm">+ New User</a>
      <?php else: ?><a href="users.php" class="cg-btn cg-btn-outline cg-btn-sm">Cancel</a><?php endif; ?>
   </div>

   <?php if ($showForm):
      $fName = $editUser['name'] ?? ($_POST['name'] ?? '');
      $fEmail = $editUser['email'] ?? ($_POST['email'] ?? '');
      $fRole = $editUser['role'] ?? ($_POST['role'] ?? 'Admin');
   ?>
   <form method="post" action="users.php" class="row g-3">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?php echo (int) ($editUser['id'] ?? 0); ?>">
      <div class="col-md-6">
         <label class="form-label">Name</label>
         <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($fName); ?>" required>
      </div>
      <div class="col-md-6">
         <label class="form-label">Email</label>
         <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($fEmail); ?>" required>
      </div>
      <div class="col-md-6">
         <label class="form-label">Password <?php echo $editUser ? '<span class="text-muted small">(leave blank to keep unchanged)</span>' : ''; ?></label>
         <input type="password" class="form-control" name="password" autocomplete="new-password">
      </div>
      <div class="col-md-6">
         <label class="form-label">Role</label>
         <select class="form-select" name="role">
            <?php foreach ($roles as $r): ?>
               <option value="<?php echo $r; ?>"<?php echo $r === $fRole ? ' selected' : ''; ?>><?php echo $r; ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-12">
         <button type="submit" class="cg-btn cg-btn-primary"><?php echo $editUser ? 'Save Changes' : 'Create User'; ?></button>
      </div>
   </form>
   <?php endif; ?>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>All Users</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
         <tbody>
         <?php if (!$users): ?>
            <tr><td colspan="5" class="text-muted">No users found.</td></tr>
         <?php else: foreach ($users as $u):
            $isSelf = ((int) $u['id']) === $myId;
            $statusCls = $u['status'] === 'active' ? 'cg-badge-active' : 'cg-badge-suspended';
         ?>
            <tr>
               <td><?php echo htmlspecialchars($u['name']); ?></td>
               <td><?php echo htmlspecialchars($u['email']); ?></td>
               <td><?php echo cg_role_badge($u['role']); ?></td>
               <td><span class="cg-badge <?php echo $statusCls; ?>"><?php echo htmlspecialchars(ucfirst($u['status'])); ?></span></td>
               <td>
                  <?php if ($canEdit): ?>
                     <a href="users.php?edit=<?php echo (int) $u['id']; ?>" class="cg-btn cg-btn-outline cg-btn-sm">Edit</a>
                     <?php if ($isSelf): ?>
                        <span class="text-muted small ms-2">(This is you)</span>
                     <?php else: ?>
                        <form method="post" action="users.php" style="display:inline;">
                           <?php echo cg_csrf_field(); ?>
                           <input type="hidden" name="action" value="toggle_status">
                           <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                           <button type="submit" class="cg-btn cg-btn-outline cg-btn-sm"><?php echo $u['status'] === 'active' ? 'Suspend' : 'Reactivate'; ?></button>
                        </form>
                        <form method="post" action="users.php" style="display:inline;" onsubmit="return confirm('Delete user &quot;<?php echo htmlspecialchars(addslashes($u['name'])); ?>&quot;? This cannot be undone.');">
                           <?php echo cg_csrf_field(); ?>
                           <input type="hidden" name="action" value="delete">
                           <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                           <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm">Delete</button>
                        </form>
                     <?php endif; ?>
                  <?php endif; ?>
               </td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
