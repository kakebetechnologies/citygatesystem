<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
/**
 * Front-Desk Visitor Sign-In Registry.
 * - Unified daily log of `visitor_registry` (walk-ins signed in here, plus
 *   appointments checked in from visitors.php — both land in the same table).
 * - "Sign In a Visitor" form for walk-ins (full access only).
 * - Today's Log with Sign Out action (full access only).
 * - Date-filterable Full History view (defaults to today).
 */
$pageModule = 'registry';
$pageTitle = 'Front-Desk Registry';
$pageSub = 'Walk-in sign-ins and the unified daily visitor log';
require_once __DIR__ . '/includes/header.php';

$canFull = cg_can('registry', 'full');

function cgRegBadge(bool $signedOut): string {
   return $signedOut
      ? '<span class="cg-badge cg-badge-draft">Signed Out</span>'
      : '<span class="cg-badge cg-badge-approved">Signed In</span>';
}

$errors = [];
$old = ['full_name' => '', 'phone' => '', 'purpose' => '', 'host_person' => '', 'appointment_id' => ''];

// ---------------------------------------------------------------
// POST actions (sign in a walk-in / sign out) — full access only.
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   cg_verify_csrf();
   if (!$canFull) {
      http_response_code(403);
      die('You do not have permission to perform this action.');
   }

   $action = $_POST['action'] ?? '';

   if ($action === 'signout') {
      $id = (int) ($_POST['id'] ?? 0);
      $redirect = $_POST['redirect'] ?? 'registry.php';
      if (strpos($redirect, 'registry.php') !== 0) $redirect = 'registry.php';

      if ($id > 0) {
         $stmt = $pdo->prepare('UPDATE visitor_registry SET signed_out_at = NOW() WHERE id = ? AND signed_out_at IS NULL');
         $stmt->execute([$id]);
      }
      header('Location: ' . $redirect);
      exit;
   }

   if ($action === 'signin') {
      $old['full_name'] = trim($_POST['full_name'] ?? '');
      $old['phone'] = trim($_POST['phone'] ?? '');
      $old['purpose'] = trim($_POST['purpose'] ?? '');
      $old['host_person'] = trim($_POST['host_person'] ?? '');
      $old['appointment_id'] = trim($_POST['appointment_id'] ?? '');

      if ($old['full_name'] === '') $errors[] = 'Full name is required.';
      if ($old['purpose'] === '') $errors[] = 'Purpose of visit is required.';

      $appointmentId = null;
      if ($old['appointment_id'] !== '') {
         $aid = (int) $old['appointment_id'];
         if ($aid > 0) {
            $chk = $pdo->prepare("SELECT id FROM appointments WHERE id = ? AND status IN ('Pending','Approved') LIMIT 1");
            $chk->execute([$aid]);
            if ($chk->fetch()) $appointmentId = $aid;
         }
      }

      if (!$errors) {
         $stmt = $pdo->prepare('INSERT INTO visitor_registry (full_name, phone, email, purpose, host_person, appointment_id, signed_in_at, recorded_by) VALUES (?, ?, NULL, ?, ?, ?, NOW(), ?)');
         $stmt->execute([
            $old['full_name'],
            $old['phone'] !== '' ? $old['phone'] : null,
            $old['purpose'],
            $old['host_person'] !== '' ? $old['host_person'] : null,
            $appointmentId,
            $user['id'],
         ]);
         header('Location: registry.php?success=1');
         exit;
      }
   }
}

$success = isset($_GET['success']);

// ---------------------------------------------------------------
// Appointments available to link a walk-in check-in to.
// ---------------------------------------------------------------
$linkableAppointments = [];
if ($canFull) {
   $linkableAppointments = $pdo->query("SELECT id, institution, contact_name, visit_date FROM appointments WHERE status IN ('Pending','Approved') ORDER BY visit_date ASC")->fetchAll();
}

// ---------------------------------------------------------------
// Today's Log — always CURDATE(), regardless of the history filter.
// ---------------------------------------------------------------
$todayStmt = $pdo->query('SELECT * FROM visitor_registry WHERE DATE(signed_in_at) = CURDATE() ORDER BY signed_in_at DESC');
$todayRows = $todayStmt->fetchAll();

// ---------------------------------------------------------------
// Full History — date filterable, defaults to today.
// ---------------------------------------------------------------
$dateFilter = $_GET['date'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) $dateFilter = date('Y-m-d');

$histStmt = $pdo->prepare('SELECT * FROM visitor_registry WHERE DATE(signed_in_at) = ? ORDER BY signed_in_at DESC');
$histStmt->execute([$dateFilter]);
$histRows = $histStmt->fetchAll();

// Return URL for sign-out forms in the history table (preserve the date filter).
$returnUrl = 'registry.php?date=' . urlencode($dateFilter);
?>

<?php if ($success): ?>
<div class="cg-alert cg-alert-success" style="background:#DDF0E2;color:#1F6B3A;border-radius:10px;padding:12px 18px;margin-bottom:18px;font-weight:600;">
   <i class="fa fa-check-circle"></i> Visitor signed in successfully.
</div>
<?php endif; ?>

<?php if ($canFull): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2>Sign In a Visitor</h2></div>

   <?php if ($errors): ?>
      <div style="background:#FBE2E2;color:#A32E2E;border-radius:10px;padding:12px 18px;margin-bottom:18px;">
         <ul style="margin:0;padding-left:1.1rem;">
            <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
         </ul>
      </div>
   <?php endif; ?>

   <form method="post" action="registry.php">
      <?php echo cg_csrf_field(); ?>
      <input type="hidden" name="action" value="signin">
      <div class="row g-3">
         <div class="col-md-4">
            <label class="form-label">Full Name *</label>
            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($old['full_name']); ?>" required>
         </div>
         <div class="col-md-4">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($old['phone']); ?>">
         </div>
         <div class="col-md-4">
            <label class="form-label">Purpose of Visit *</label>
            <input type="text" class="form-control" name="purpose" value="<?php echo htmlspecialchars($old['purpose']); ?>" required placeholder="e.g. Farm tour, delivery, meeting...">
         </div>
         <div class="col-md-4">
            <label class="form-label">Person They've Come To See</label>
            <input type="text" class="form-control" name="host_person" value="<?php echo htmlspecialchars($old['host_person']); ?>">
         </div>
         <div class="col-md-8">
            <label class="form-label">Link to an Existing Appointment (optional)</label>
            <select class="form-select" name="appointment_id">
               <option value="">— Not linked to a booking —</option>
               <?php foreach ($linkableAppointments as $a): ?>
                  <option value="<?php echo (int) $a['id']; ?>" <?php echo $old['appointment_id'] == $a['id'] ? 'selected' : ''; ?>>
                     <?php echo htmlspecialchars(($a['institution'] ?: $a['contact_name']) . ' — ' . ($a['visit_date'] ?: 'no date') . ' (' . $a['contact_name'] . ')'); ?>
                  </option>
               <?php endforeach; ?>
            </select>
         </div>
      </div>
      <div class="mt-3">
         <button type="submit" class="cg-btn cg-btn-primary"><i class="fa fa-sign-in"></i> Sign In Visitor</button>
      </div>
   </form>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head"><h2>Today's Log — <?php echo date('F j, Y'); ?></h2></div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead>
            <tr>
               <th>Full Name</th><th>Phone</th><th>Purpose</th><th>Host</th>
               <th>Signed In</th><th>Signed Out</th><th>Status</th><th>Appointment</th>
               <?php if ($canFull): ?><th></th><?php endif; ?>
            </tr>
         </thead>
         <tbody>
         <?php if (!$todayRows): ?>
            <tr><td colspan="9" class="text-muted">No visitors signed in today yet.</td></tr>
         <?php else: foreach ($todayRows as $r): ?>
            <tr>
               <td><?php echo htmlspecialchars($r['full_name']); ?></td>
               <td><?php echo htmlspecialchars($r['phone'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($r['purpose']); ?></td>
               <td><?php echo htmlspecialchars($r['host_person'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars(date('g:i A', strtotime($r['signed_in_at']))); ?></td>
               <td><?php echo $r['signed_out_at'] ? htmlspecialchars(date('g:i A', strtotime($r['signed_out_at']))) : '—'; ?></td>
               <td><?php echo cgRegBadge((bool) $r['signed_out_at']); ?></td>
               <td><?php echo $r['appointment_id'] ? '<span class="cg-badge cg-badge-arrived">Linked</span>' : '<span class="text-muted">—</span>'; ?></td>
               <?php if ($canFull): ?>
               <td>
                  <?php if (!$r['signed_out_at']): ?>
                     <form method="post" action="registry.php" onsubmit="return confirm('Sign out this visitor?');">
                        <?php echo cg_csrf_field(); ?>
                        <input type="hidden" name="action" value="signout">
                        <input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>">
                        <input type="hidden" name="redirect" value="registry.php">
                        <button type="submit" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-sign-out"></i> Sign Out</button>
                     </form>
                  <?php else: ?>
                     <span class="text-muted">—</span>
                  <?php endif; ?>
               </td>
               <?php endif; ?>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>Full History</h2>
      <form method="get" action="registry.php" class="d-flex gap-2 align-items-center">
         <label class="form-label mb-0" style="font-weight:600;">Date:</label>
         <input type="date" class="form-control form-control-sm" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="this.form.submit()">
         <button type="submit" class="cg-btn cg-btn-outline cg-btn-sm">Go</button>
         <?php if ($dateFilter !== date('Y-m-d')): ?><a href="registry.php" class="cg-btn cg-btn-outline cg-btn-sm">Today</a><?php endif; ?>
      </form>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead>
            <tr>
               <th>Full Name</th><th>Phone</th><th>Purpose</th><th>Host</th>
               <th>Signed In</th><th>Signed Out</th><th>Status</th><th>Appointment</th>
               <?php if ($canFull): ?><th></th><?php endif; ?>
            </tr>
         </thead>
         <tbody>
         <?php if (!$histRows): ?>
            <tr><td colspan="9" class="text-muted">No visitor registry entries for <?php echo htmlspecialchars($dateFilter); ?>.</td></tr>
         <?php else: foreach ($histRows as $r): ?>
            <tr>
               <td><?php echo htmlspecialchars($r['full_name']); ?></td>
               <td><?php echo htmlspecialchars($r['phone'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($r['purpose']); ?></td>
               <td><?php echo htmlspecialchars($r['host_person'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars(date('g:i A', strtotime($r['signed_in_at']))); ?></td>
               <td><?php echo $r['signed_out_at'] ? htmlspecialchars(date('g:i A', strtotime($r['signed_out_at']))) : '—'; ?></td>
               <td><?php echo cgRegBadge((bool) $r['signed_out_at']); ?></td>
               <td><?php echo $r['appointment_id'] ? '<span class="cg-badge cg-badge-arrived">Linked</span>' : '<span class="text-muted">—</span>'; ?></td>
               <?php if ($canFull): ?>
               <td>
                  <?php if (!$r['signed_out_at']): ?>
                     <form method="post" action="registry.php" onsubmit="return confirm('Sign out this visitor?');">
                        <?php echo cg_csrf_field(); ?>
                        <input type="hidden" name="action" value="signout">
                        <input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>">
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($returnUrl); ?>">
                        <button type="submit" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-sign-out"></i> Sign Out</button>
                     </form>
                  <?php else: ?>
                     <span class="text-muted">—</span>
                  <?php endif; ?>
               </td>
               <?php endif; ?>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
