<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (cg_current_user()) {
   header('Location: dashboard.php');
   exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   cg_verify_csrf();
   $email = trim($_POST['email'] ?? '');
   $password = $_POST['password'] ?? '';
   $user = cg_login($pdo, $email, $password);
   if ($user) {
      header('Location: dashboard.php');
      exit;
   }
   $error = 'Incorrect email or password, or your account has been suspended.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Staff Login | City Gate Mixed Farm</title>
   <meta name="robots" content="noindex">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
   <link rel="stylesheet" type="text/css" href="../css/font-awesome.css">
   <link rel="stylesheet" href="../css/cg-theme.css">
   <link rel="stylesheet" href="../css/cg-admin.css">
   <link rel="shortcut icon" type="image/png" href="../images/logo/favcon.png">
</head>
<body>
   <div class="cg-admin-login-wrap">
      <div class="cg-admin-login-card">
         <img src="../images/logo/citygatelogo.png" alt="City Gate Mixed Farm">
         <h4 class="text-center mb-1" style="font-weight:700;color:var(--charcoal);">Staff &amp; Admin Login</h4>
         <p class="text-center text-muted mb-4" style="font-size:13.5px;">City Gate Mixed Farm — Management System</p>

         <form method="post" action="login.php">
            <?php echo cg_csrf_field(); ?>
            <div class="mb-3">
               <label class="form-label">Email Address</label>
               <input type="email" name="email" class="form-control" placeholder="you@citygatefarms.com" required autocomplete="username">
            </div>
            <div class="mb-3">
               <label class="form-label">Password</label>
               <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
            </div>
            <?php if ($error): ?>
               <div class="text-danger mb-3" style="font-size:13px;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <button type="submit" class="cg-btn cg-btn-primary w-100 justify-content-center" style="padding:11px;">Log In</button>
         </form>

         <div class="cg-admin-login-demo">
            <strong>Demo accounts</strong> (development environment only):<br>
            <strong>Super Admin:</strong> superadmin@citygatefarms.com / super123<br>
            <strong>Manager:</strong> manager@citygatefarms.com / manager123<br>
            <strong>Finance:</strong> finance@citygatefarms.com / finance123<br>
            <strong>Admin:</strong> admin@citygatefarms.com / admin123
         </div>
      </div>
   </div>
</body>
</html>
