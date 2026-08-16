<?php
/**
 * Admin folder entry point. Nothing lives here except a redirect — signed-in
 * staff go straight to the dashboard, everyone else to the login page.
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Location: ' . (cg_current_user() ? 'dashboard.php' : 'login.php'));
exit;
