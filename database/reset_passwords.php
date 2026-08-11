<?php
/**
 * One-time helper: sets each seeded demo user's password using PHP's own
 * password_hash() on this exact server, so the hash is guaranteed to verify
 * correctly (rather than trusting a bcrypt string hand-pasted into schema.sql
 * from elsewhere). Run once after importing schema.sql:
 *   php database/reset_passwords.php
 */
require __DIR__ . '/../config/db.php';

$passwords = [
   'superadmin@citygatefarms.com' => 'super123',
   'manager@citygatefarms.com' => 'manager123',
   'finance@citygatefarms.com' => 'finance123',
   'admin@citygatefarms.com' => 'admin123',
];

$stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
foreach ($passwords as $email => $plain) {
   $hash = password_hash($plain, PASSWORD_DEFAULT);
   $stmt->execute([$hash, $email]);
   echo "Set password for {$email}\n";
}
echo "Done.\n";
