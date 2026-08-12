<?php
/**
 * Database connection (PDO). Automatically switches between local XAMPP
 * dev credentials and the live server's credentials based on the request's
 * host — same codebase, no manual editing needed when deploying.
 *
 * "Local" = HTTP_HOST is localhost/127.0.0.1, or the script is run from the
 * CLI (e.g. a one-off maintenance script on the dev machine). Everything
 * else (the live domain, whatever it resolves to) uses the live credentials.
 */
$cgHttpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocalEnv = PHP_SAPI === 'cli'
   || $cgHttpHost === 'localhost'
   || $cgHttpHost === '127.0.0.1'
   || str_starts_with($cgHttpHost, 'localhost:')
   || str_starts_with($cgHttpHost, '127.0.0.1:');

if ($isLocalEnv) {
   // Local XAMPP dev
   $DB_HOST = 'localhost';
   $DB_NAME = 'citygatefarm';
   $DB_USER = 'root';
   $DB_PASS = '';
} else {
   // Live server
   $DB_HOST = 'localhost';
   $DB_NAME = 'u850523537_CityGate';
   $DB_USER = 'u850523537_CityGateUser';
   $DB_PASS = 'i#@Recover2';
}

try {
   $pdo = new PDO(
      "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
      $DB_USER,
      $DB_PASS,
      [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES => false,
      ]
   );
} catch (PDOException $e) {
   http_response_code(500);
   die('Database connection failed. Make sure MySQL is running and the "citygatefarm" database has been imported from database/schema.sql.');
}
