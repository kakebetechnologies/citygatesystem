<?php
/**
 * Handles the public contact form AJAX POST (js/contact_form.js).
 * Contract preserved exactly: echoes 1 on success, 0 on failure — the
 * frontend JS only checks for that value, nothing else changed.
 *
 * Every valid submission is saved to contact_messages regardless of whether
 * outgoing mail is actually configured (config/mail.php is blank until the
 * site owner adds real SMTP credentials), so nothing is lost. We then try
 * to notify the farm's own inbox via the shared cg_send_mail() helper.
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/mailer.php';

if (!isset($_POST) || empty($_POST) || ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
   echo 0;
   exit;
}

$formType = trim($_POST['form_type'] ?? '');
$fullName = trim($_POST['full_name'] ?? '');
$firstName = trim($_POST['first_name'] ?? '');
$middleName = trim($_POST['middle_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$contactNo = trim($_POST['contact_no'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');

if ($fullName === '') {
   $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);
   $fullName = preg_replace('/\s+/', ' ', $fullName);
}

// Minimum required fields to accept the submission at all.
if ($fullName === '' || $message === '' || !in_array($formType, ['contact', 'inquiry'], true)) {
   echo 0;
   exit;
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
   // Don't hard-fail on a malformed email — still record the message —
   // but don't pass an invalid address to PHPMailer/addAddress later.
   $email = '';
}

// ---------------------------------------------------------------
// Persist to contact_messages regardless of mail outcome.
// ---------------------------------------------------------------
try {
   $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
   $stmt->execute([
      $fullName,
      $email !== '' ? $email : null,
      $contactNo !== '' ? $contactNo : null,
      $subject !== '' ? $subject : ($formType === 'inquiry' ? 'Inquiry' : 'Contact'),
      $message,
   ]);
} catch (Throwable $e) {
   echo 0;
   exit;
}

// ---------------------------------------------------------------
// Notify the farm inbox (best-effort; doesn't affect the response code —
// the message is already saved above either way).
// ---------------------------------------------------------------
$mailConfig = require __DIR__ . '/config/mail.php';
$notifyTo = $mailConfig['from_email'] ?: 'info@citygatefarms.com';

if ($formType === 'contact') {
   $mailSubject = 'Contact Details';
   $bodyHtml = "<p>Hello,</p><p>" . htmlspecialchars($fullName) . " has sent a message having </p><p><b>Phone No:</b> " . htmlspecialchars($contactNo) . "</p><p><b>Email id:</b> " . htmlspecialchars($email) . "</p><p><b>Subject:</b> " . htmlspecialchars($subject) . "</p><p><b>Query is:</b> " . nl2br(htmlspecialchars($message)) . "</p>";
} else {
   $mailSubject = 'Inquiry Details';
   $bodyHtml = "<p>Hello,</p><p>" . htmlspecialchars($fullName) . " has sent a message having </p><p><b>Email id:</b> " . htmlspecialchars($email) . "</p><p><b>Phone:</b> " . htmlspecialchars($contactNo) . "</p><p><b>Subject:</b> " . htmlspecialchars($subject) . "</p><p><b>Date :</b> " . htmlspecialchars($date) . "</p><p><b>Time :</b> " . htmlspecialchars($time) . "</p><p><b>Message :</b> " . nl2br(htmlspecialchars($message)) . "</p>";
}

cg_send_mail($notifyTo, 'City Gate Mixed Farm', $mailSubject, $bodyHtml, $email !== '' ? $email : null, $fullName);

// The message was saved successfully — report success to the frontend even
// if SMTP isn't configured yet, since the visitor's message was captured.
echo 1;
