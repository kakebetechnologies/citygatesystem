<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
require_once __DIR__ . '/../includes/auth.php';
cg_logout();
header('Location: login.php');
exit;
