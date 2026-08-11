<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
/**
 * Email Visitors admin page.
 * - Recipient picker built from distinct appointment contacts, with a
 *   "visited within" filter.
 * - Compose form (subject + plain textarea body, optional {{name}} token).
 * - On send: loops recipients, calls cg_send_mail() once per person, logs
 *   one email_log row + one email_recipients row per attempt regardless of
 *   whether SMTP is actually configured (see includes/mailer.php).
 * - History table of past email_log entries.
 */
$pageModule = 'email';
$pageTitle = 'Email Visitors';
$pageSub = 'Send thank-you notes and updates to past visitors';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/mailer.php';

$flash = null;      // ['type' => 'success'|'warning'|'danger', 'html' => string]

// ---------------------------------------------------------------
// POST: send email
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send') {
   cg_verify_csrf();

   $subject = trim($_POST['subject'] ?? '');
   $bodyTemplate = trim($_POST['message'] ?? '');
   $recipientEmails = $_POST['recipients'] ?? [];   // array of emails
   $recipientNamesRaw = $_POST['recipient_names'] ?? []; // parallel array email => name (see hidden inputs below)

   if ($subject === '' || $bodyTemplate === '') {
      $flash = ['type' => 'danger', 'html' => 'Subject and message are both required.'];
   } elseif (!is_array($recipientEmails) || count($recipientEmails) === 0) {
      $flash = ['type' => 'danger', 'html' => 'Select at least one recipient.'];
   } else {
      // Build a unique list of [email, name] from the posted checkboxes.
      $recipients = [];
      foreach ($recipientEmails as $idx => $email) {
         $email = trim((string) $email);
         if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
         $name = trim((string) ($recipientNamesRaw[$idx] ?? ''));
         $recipients[$email] = $name !== '' ? $name : $email;
      }

      if (count($recipients) === 0) {
         $flash = ['type' => 'danger', 'html' => 'No valid recipient email addresses were selected.'];
      } else {
         $sentCount = 0;
         $results = []; // email => ['name'=>, 'ok'=>, 'error'=>]

         foreach ($recipients as $email => $name) {
            $personalBody = str_replace('{{name}}', htmlspecialchars($name), nl2br(htmlspecialchars($bodyTemplate)));
            $result = cg_send_mail($email, $name, $subject, $personalBody);
            $results[] = ['email' => $email, 'name' => $name, 'ok' => $result['ok'], 'error' => $result['error']];
            if ($result['ok']) $sentCount++;
         }

         $recipientType = count($results) === 1 ? 'single' : 'bulk';

         try {
            $pdo->beginTransaction();
            $logStmt = $pdo->prepare('INSERT INTO email_log (subject, body, recipient_type, sent_by) VALUES (?, ?, ?, ?)');
            $logStmt->execute([$subject, $bodyTemplate, $recipientType, $user['id']]);
            $emailLogId = (int) $pdo->lastInsertId();

            $recStmt = $pdo->prepare('INSERT INTO email_recipients (email_log_id, recipient_name, recipient_email, status, error_message) VALUES (?, ?, ?, ?, ?)');
            foreach ($results as $r) {
               $recStmt->execute([
                  $emailLogId,
                  $r['name'],
                  $r['email'],
                  $r['ok'] ? 'sent' : 'failed',
                  $r['ok'] ? null : substr((string) $r['error'], 0, 255),
               ]);
            }
            $pdo->commit();
         } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
         }

         $total = count($results);
         if ($sentCount === $total) {
            $flash = ['type' => 'success', 'html' => "Sent to {$sentCount} of {$total} recipients."];
         } elseif ($sentCount === 0) {
            $flash = ['type' => 'warning', 'html' => "Sent to 0 of {$total} recipients — SMTP not yet configured, 0 delivered. The message was still logged below."];
         } else {
            $flash = ['type' => 'warning', 'html' => "Sent to {$sentCount} of {$total} recipients — some deliveries failed. See history below."];
         }
      }
   }
}

// ---------------------------------------------------------------
// GET: recipient list (filtered by "visited within")
// ---------------------------------------------------------------
$rangeFilter = $_GET['range'] ?? 'all';
$validRanges = ['2weeks' => '14 DAY', 'month' => '1 MONTH', 'all' => null];
if (!array_key_exists($rangeFilter, $validRanges)) $rangeFilter = 'all';

$sql = 'SELECT contact_name, contact_email, MAX(created_at) AS last_visit
        FROM appointments
        WHERE contact_email IS NOT NULL AND contact_email <> ""';
if ($validRanges[$rangeFilter]) {
   $sql .= ' AND created_at >= DATE_SUB(NOW(), INTERVAL ' . $validRanges[$rangeFilter] . ')';
}
$sql .= ' GROUP BY contact_email, contact_name ORDER BY last_visit DESC';
$recipientRows = $pdo->query($sql)->fetchAll();

// ---------------------------------------------------------------
// History
// ---------------------------------------------------------------
$history = $pdo->query(
   "SELECT el.id, el.subject, el.recipient_type, el.sent_at, u.name AS sent_by_name,
           COUNT(er.id) AS recipient_count,
           SUM(CASE WHEN er.status = 'sent' THEN 1 ELSE 0 END) AS sent_ok
    FROM email_log el
    LEFT JOIN users u ON u.id = el.sent_by
    LEFT JOIN email_recipients er ON er.email_log_id = el.id
    GROUP BY el.id
    ORDER BY el.sent_at DESC
    LIMIT 50"
)->fetchAll();
?>

<?php
if ($flash) {
   $flashColors = [
      'success' => '#1F6B3A',
      'danger'  => '#A32E2E',
      'warning' => '#9A6700',
   ];
   $flashColor = $flashColors[$flash['type']] ?? '#9A6700';
?>
<div class="cg-panel" style="border-left:4px solid <?php echo $flashColor; ?>;">
   <p style="margin:0;"><?php echo $flash['html']; ?></p>
</div>
<?php } ?>

<form method="post" action="email.php<?php echo $rangeFilter !== 'all' ? '?range=' . urlencode($rangeFilter) : ''; ?>" id="cgEmailForm">
   <?php echo cg_csrf_field(); ?>
   <input type="hidden" name="action" value="send">

   <div class="cg-panel">
      <div class="cg-panel-head">
         <h2>1. Select Recipients</h2>
         <form method="get" action="email.php" class="d-flex gap-2 align-items-center">
            <label for="range" style="margin:0;font-size:.85rem;color:var(--muted,#666);">Visited within:</label>
            <select name="range" id="range" class="form-control" style="width:auto;" onchange="this.form.submit()">
               <option value="2weeks" <?php echo $rangeFilter === '2weeks' ? 'selected' : ''; ?>>Last 2 weeks</option>
               <option value="month" <?php echo $rangeFilter === 'month' ? 'selected' : ''; ?>>Last month</option>
               <option value="all" <?php echo $rangeFilter === 'all' ? 'selected' : ''; ?>>All time</option>
            </select>
         </form>
      </div>

      <div class="cg-table-wrap">
         <table class="cg-table">
            <thead>
               <tr>
                  <th style="width:40px;"><input type="checkbox" id="cgSelectAll"></th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Last Visit</th>
               </tr>
            </thead>
            <tbody>
            <?php if (!$recipientRows): ?>
               <tr><td colspan="4" class="text-muted">No visitors found for this filter yet.</td></tr>
            <?php else: foreach ($recipientRows as $i => $r): ?>
               <tr>
                  <td>
                     <input type="checkbox" class="cg-recipient-cb" name="recipients[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($r['contact_email']); ?>">
                     <input type="hidden" name="recipient_names[<?php echo $i; ?>]" value="<?php echo htmlspecialchars($r['contact_name']); ?>">
                  </td>
                  <td><?php echo htmlspecialchars($r['contact_name']); ?></td>
                  <td><?php echo htmlspecialchars($r['contact_email']); ?></td>
                  <td><?php echo htmlspecialchars($r['last_visit']); ?></td>
               </tr>
            <?php endforeach; endif; ?>
            </tbody>
         </table>
      </div>
   </div>

   <div class="cg-panel">
      <div class="cg-panel-head"><h2>2. Compose</h2></div>
      <div class="d-flex gap-2 mb-3">
         <button type="button" class="cg-btn cg-btn-outline cg-btn-sm" id="cgTemplateThanks">Thank you for visiting</button>
         <button type="button" class="cg-btn cg-btn-outline cg-btn-sm" id="cgTemplateUpdate">Monthly farm update</button>
      </div>
      <div class="form-group mb-3">
         <label for="subject">Subject</label>
         <input type="text" class="form-control" id="subject" name="subject" placeholder="e.g. Thank you for visiting City Gate Mixed Farm" required>
      </div>
      <div class="form-group mb-3">
         <label for="message">Message</label>
         <textarea class="form-control" id="message" name="message" rows="8" placeholder="Write your message here. Use {{name}} to insert each recipient's name." required></textarea>
         <small class="text-muted">Tip: use <code>{{name}}</code> anywhere in the message to personalize it per recipient.</small>
      </div>
      <button type="submit" class="cg-btn cg-btn-primary" id="cgSendBtn"><i class="fa fa-paper-plane"></i> Send Email</button>
   </div>
</form>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>Send History</h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead>
            <tr>
               <th>Subject</th>
               <th>Type</th>
               <th>Sent By</th>
               <th>Recipients</th>
               <th>Delivered</th>
               <th>Sent At</th>
            </tr>
         </thead>
         <tbody>
         <?php if (!$history): ?>
            <tr><td colspan="6" class="text-muted">No emails sent yet.</td></tr>
         <?php else: foreach ($history as $h): ?>
            <tr>
               <td><?php echo htmlspecialchars($h['subject']); ?></td>
               <td><?php echo htmlspecialchars(ucfirst($h['recipient_type'])); ?></td>
               <td><?php echo htmlspecialchars($h['sent_by_name'] ?: '—'); ?></td>
               <td><?php echo (int) $h['recipient_count']; ?></td>
               <td><?php echo (int) $h['sent_ok']; ?> / <?php echo (int) $h['recipient_count']; ?></td>
               <td><?php echo htmlspecialchars($h['sent_at']); ?></td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<script>
document.getElementById('cgSelectAll').addEventListener('change', function () {
   document.querySelectorAll('.cg-recipient-cb').forEach(function (cb) { cb.checked = this.checked; }.bind(this));
});

document.getElementById('cgEmailForm').addEventListener('submit', function (e) {
   var anyChecked = document.querySelectorAll('.cg-recipient-cb:checked').length > 0;
   if (!anyChecked) {
      e.preventDefault();
      alert('Select at least one recipient before sending.');
   }
});

document.getElementById('cgTemplateThanks').addEventListener('click', function () {
   document.getElementById('subject').value = 'Thank you for visiting City Gate Mixed Farm';
   document.getElementById('message').value = 'Dear {{name}},\n\nThank you for visiting City Gate Mixed Farm! It was a pleasure hosting you, and we hope you enjoyed learning more about our poultry, dairy, goat, and crop operations.\n\nWe would love to welcome you back again soon. If you have any questions or would like to plan another visit, just reply to this email.\n\nWarm regards,\nCity Gate Mixed Farm Team';
});

document.getElementById('cgTemplateUpdate').addEventListener('click', function () {
   document.getElementById('subject').value = 'Monthly Update from City Gate Mixed Farm';
   document.getElementById('message').value = 'Dear {{name}},\n\nHere is what has been happening at City Gate Mixed Farm this month: new arrivals, harvest updates, and upcoming events.\n\nWe would love to see you again soon.\n\nWarm regards,\nCity Gate Mixed Farm Team';
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
