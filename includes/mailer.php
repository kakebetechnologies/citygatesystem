<?php
/**
 * Shared outgoing-mail helper (PHPMailer). Used by both the public contact
 * form (contactmail.php) and the admin "Email Visitors" module
 * (admin/email.php) so there is exactly one place that knows how to talk to
 * SMTP.
 *
 * config/mail.php ships with blank 'username'/'password' until the site
 * owner adds real Gmail SMTP credentials. That is expected right now — this
 * function detects that case and returns a clean, honest failure instead of
 * throwing, so callers can still log the attempt (subject/body/recipient)
 * to the database and show the user an accurate status.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Send a single HTML email.
 *
 * @param string $toEmail
 * @param string $toName
 * @param string $subject
 * @param string $htmlBody
 * @param string|null $replyToEmail Optional reply-to address (e.g. a visitor's own email on the contact form).
 * @param string|null $replyToName
 * @return array{ok: bool, error: ?string}
 */
function cg_send_mail(
   string $toEmail,
   string $toName,
   string $subject,
   string $htmlBody,
   ?string $replyToEmail = null,
   ?string $replyToName = null
): array {
   $mailConfig = require __DIR__ . '/../config/mail.php';

   if (empty($mailConfig['username']) || empty($mailConfig['password'])) {
      return ['ok' => false, 'error' => 'SMTP not configured yet'];
   }

   $mail = new PHPMailer(true);
   try {
      $mail->isSMTP();
      $mail->Host = $mailConfig['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $mailConfig['username'];
      $mail->Password = $mailConfig['password'];
      $mail->SMTPSecure = $mailConfig['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = (int) $mailConfig['port'];

      $fromEmail = $mailConfig['from_email'] ?: $mailConfig['username'];
      $mail->setFrom($fromEmail, $mailConfig['from_name'] ?: 'City Gate Mixed Farm');
      $mail->addAddress($toEmail, $toName);

      if ($replyToEmail) {
         $mail->addReplyTo($replyToEmail, $replyToName ?: $replyToEmail);
      }

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;
      $mail->AltBody = strip_tags($htmlBody);

      $mail->send();
      return ['ok' => true, 'error' => null];
   } catch (PHPMailerException $e) {
      return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
   } catch (Throwable $e) {
      return ['ok' => false, 'error' => $e->getMessage()];
   }
}
