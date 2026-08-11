<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
/**
 * Visitors & Appointments admin page.
 * - Status filter list of all `appointments`.
 * - ?view=<id> detail panel with attendee list.
 * - Approve/Reject (Pending only) — requires cg_can('visitors','full').
 * - Check-In search: find Pending/Approved appointments, "Mark Arrived"
 *   sets status=Arrived + checked_in_at, and mirrors the visit into
 *   visitor_registry so the front-desk log stays unified.
 */
$pageModule = 'visitors';
$pageTitle = 'Visitors & Appointments';
$pageSub = 'Manage booking requests from the public site';
require_once __DIR__ . '/includes/header.php';

$canFull = cg_can('visitors', 'full');

function cgStatusBadge(string $status): string {
   $map = ['Pending' => 'pending', 'Approved' => 'approved', 'Rejected' => 'rejected', 'Arrived' => 'arrived'];
   $cls = $map[$status] ?? 'pending';
   return '<span class="cg-badge cg-badge-' . $cls . '">' . htmlspecialchars($status) . '</span>';
}

$flash = '';
$flashType = 'success';

// ---------------------------------------------------------------
// POST actions (approve / reject / check-in) — full access only.
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   cg_verify_csrf();
   if (!$canFull) {
      http_response_code(403);
      die('You do not have permission to perform this action.');
   }

   $action = $_POST['action'] ?? '';
   $id = (int) ($_POST['id'] ?? 0);
   $redirect = $_POST['redirect'] ?? 'visitors.php';
   // Only allow redirecting back within this page (avoid open-redirect).
   if (strpos($redirect, 'visitors.php') !== 0) $redirect = 'visitors.php';

   if ($id > 0 && $action === 'approve') {
      $stmt = $pdo->prepare("UPDATE appointments SET status='Approved' WHERE id=? AND status='Pending'");
      $stmt->execute([$id]);
   } elseif ($id > 0 && $action === 'reject') {
      $stmt = $pdo->prepare("UPDATE appointments SET status='Rejected' WHERE id=? AND status='Pending'");
      $stmt->execute([$id]);
   } elseif ($id > 0 && $action === 'checkin') {
      $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id=? LIMIT 1");
      $stmt->execute([$id]);
      $appt = $stmt->fetch();
      if ($appt && in_array($appt['status'], ['Pending', 'Approved'], true)) {
         try {
            $pdo->beginTransaction();
            $upd = $pdo->prepare("UPDATE appointments SET status='Arrived', checked_in_at=NOW() WHERE id=?");
            $upd->execute([$id]);
            $reg = $pdo->prepare('INSERT INTO visitor_registry (full_name, phone, email, purpose, host_person, appointment_id, signed_in_at, recorded_by) VALUES (?, ?, ?, ?, NULL, ?, NOW(), ?)');
            $reg->execute([
               $appt['contact_name'],
               $appt['contact_phone'],
               $appt['contact_email'],
               $appt['purpose'],
               $id,
               $user['id'],
            ]);
            $pdo->commit();
         } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
         }
      }
   }

   header('Location: ' . $redirect);
   exit;
}

// ---------------------------------------------------------------
// GET: filters
// ---------------------------------------------------------------
$statusFilter = $_GET['status'] ?? 'All';
$validStatuses = ['All', 'Pending', 'Approved', 'Rejected', 'Arrived'];
if (!in_array($statusFilter, $validStatuses, true)) $statusFilter = 'All';

if ($statusFilter === 'All') {
   $listStmt = $pdo->query('SELECT * FROM appointments ORDER BY created_at DESC');
} else {
   $listStmt = $pdo->prepare('SELECT * FROM appointments WHERE status = ? ORDER BY created_at DESC');
   $listStmt->execute([$statusFilter]);
}
$appointments = $listStmt->fetchAll();

// Build the redirect target used by POST forms so we return to the same filter/search state.
$currentQuery = $_SERVER['QUERY_STRING'] ?? '';
$returnUrl = 'visitors.php' . ($currentQuery ? ('?' . $currentQuery) : '');

// Detail view
$viewAppointment = null;
$viewAttendees = [];
if (!empty($_GET['view'])) {
   $vid = (int) $_GET['view'];
   $vStmt = $pdo->prepare('SELECT * FROM appointments WHERE id = ? LIMIT 1');
   $vStmt->execute([$vid]);
   $viewAppointment = $vStmt->fetch();
   if ($viewAppointment) {
      $aStmt = $pdo->prepare('SELECT * FROM appointment_attendees WHERE appointment_id = ? ORDER BY id');
      $aStmt->execute([$vid]);
      $viewAttendees = $aStmt->fetchAll();
   }
}

// Check-in search (full access only)
$checkinQ = trim($_GET['checkin_q'] ?? '');
$checkinResults = [];
if ($canFull) {
   if ($checkinQ !== '') {
      $cStmt = $pdo->prepare("SELECT * FROM appointments WHERE status IN ('Pending','Approved') AND (institution LIKE ? OR contact_name LIKE ?) ORDER BY visit_date ASC LIMIT 25");
      $like = '%' . $checkinQ . '%';
      $cStmt->execute([$like, $like]);
      $checkinResults = $cStmt->fetchAll();
   } else {
      $checkinResults = $pdo->query("SELECT * FROM appointments WHERE status IN ('Pending','Approved') ORDER BY visit_date ASC LIMIT 25")->fetchAll();
   }
}
?>

<?php if ($viewAppointment): ?>
<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>Appointment #<?php echo (int) $viewAppointment['id']; ?> — <?php echo htmlspecialchars($viewAppointment['institution'] ?: $viewAppointment['contact_name']); ?></h2>
      <a href="visitors.php<?php echo $statusFilter !== 'All' ? '?status=' . urlencode($statusFilter) : ''; ?>" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-arrow-left"></i> Back to List</a>
   </div>
   <div class="row g-3 mb-3">
      <div class="col-md-4"><strong>Institution / Group:</strong><br><?php echo htmlspecialchars($viewAppointment['institution'] ?: '—'); ?></div>
      <div class="col-md-4"><strong>Contact Name:</strong><br><?php echo htmlspecialchars($viewAppointment['contact_name']); ?></div>
      <div class="col-md-4"><strong>Status:</strong><br><?php echo cgStatusBadge($viewAppointment['status']); ?></div>
      <div class="col-md-4"><strong>Phone:</strong><br><?php echo htmlspecialchars($viewAppointment['contact_phone']); ?></div>
      <div class="col-md-4"><strong>Email:</strong><br><?php echo htmlspecialchars($viewAppointment['contact_email']); ?></div>
      <div class="col-md-4"><strong>Purpose:</strong><br><?php echo htmlspecialchars($viewAppointment['purpose']); ?></div>
      <div class="col-md-4"><strong>Visit Date:</strong><br><?php echo htmlspecialchars($viewAppointment['visit_date'] ?: '—'); ?></div>
      <div class="col-md-4"><strong>Preferred Time:</strong><br><?php echo htmlspecialchars($viewAppointment['visit_time'] ?: '—'); ?></div>
      <div class="col-md-4"><strong># Visitors:</strong><br><?php echo htmlspecialchars((string) ($viewAppointment['num_visitors'] ?? '—')); ?></div>
      <div class="col-md-4"><strong>Checked In At:</strong><br><?php echo htmlspecialchars($viewAppointment['checked_in_at'] ?: '—'); ?></div>
      <div class="col-md-4"><strong>Requested On:</strong><br><?php echo htmlspecialchars($viewAppointment['created_at']); ?></div>
      <div class="col-12"><strong>Additional Notes:</strong><br><?php echo nl2br(htmlspecialchars($viewAppointment['message'] ?: '—')); ?></div>
   </div>

   <?php if ($canFull && $viewAppointment['status'] === 'Pending'): ?>
      <div class="d-flex gap-2 mb-3">
         <form method="post" action="visitors.php">
            <?php echo cg_csrf_field(); ?>
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="id" value="<?php echo (int) $viewAppointment['id']; ?>">
            <input type="hidden" name="redirect" value="visitors.php?view=<?php echo (int) $viewAppointment['id']; ?>">
            <button type="submit" class="cg-btn cg-btn-primary"><i class="fa fa-check"></i> Approve</button>
         </form>
         <form method="post" action="visitors.php">
            <?php echo cg_csrf_field(); ?>
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="id" value="<?php echo (int) $viewAppointment['id']; ?>">
            <input type="hidden" name="redirect" value="visitors.php?view=<?php echo (int) $viewAppointment['id']; ?>">
            <button type="submit" class="cg-btn cg-btn-danger"><i class="fa fa-times"></i> Reject</button>
         </form>
      </div>
   <?php endif; ?>

   <h6 style="font-weight:700;color:var(--charcoal);margin-top:10px;">Attendees</h6>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Full Name</th><th>Designation</th><th>Age / Age Group</th><th>Contact</th></tr></thead>
         <tbody>
         <?php if (!$viewAttendees): ?>
            <tr><td colspan="4" class="text-muted">No attendees listed.</td></tr>
         <?php else: foreach ($viewAttendees as $a): ?>
            <tr>
               <td><?php echo htmlspecialchars($a['full_name']); ?></td>
               <td><?php echo htmlspecialchars($a['designation'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($a['age'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars($a['contact'] ?: '—'); ?></td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>
<?php endif; ?>

<?php if ($canFull): ?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2>Check-In Search</h2></div>
   <form method="get" action="visitors.php" class="d-flex gap-2 mb-3" style="max-width:480px;">
      <input type="text" name="checkin_q" class="form-control" placeholder="Search by institution or contact name..." value="<?php echo htmlspecialchars($checkinQ); ?>">
      <button type="submit" class="cg-btn cg-btn-outline"><i class="fa fa-search"></i> Search</button>
      <?php if ($checkinQ !== ''): ?><a href="visitors.php" class="cg-btn cg-btn-outline">Clear</a><?php endif; ?>
   </form>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Institution / Contact</th><th>Purpose</th><th>Visit Date</th><th>Status</th><th></th></tr></thead>
         <tbody>
         <?php if (!$checkinResults): ?>
            <tr><td colspan="5" class="text-muted">No matching Pending/Approved appointments.</td></tr>
         <?php else: foreach ($checkinResults as $r): ?>
            <tr>
               <td><?php echo htmlspecialchars($r['institution'] ?: $r['contact_name']); ?></td>
               <td><?php echo htmlspecialchars($r['purpose']); ?></td>
               <td><?php echo htmlspecialchars($r['visit_date'] ?: '—'); ?></td>
               <td><?php echo cgStatusBadge($r['status']); ?></td>
               <td>
                  <form method="post" action="visitors.php" onsubmit="return confirm('Mark this visitor as Arrived?');">
                     <?php echo cg_csrf_field(); ?>
                     <input type="hidden" name="action" value="checkin">
                     <input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>">
                     <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($returnUrl); ?>">
                     <button type="submit" class="cg-btn cg-btn-primary cg-btn-sm"><i class="fa fa-sign-in"></i> Mark Arrived</button>
                  </form>
               </td>
            </tr>
         <?php endforeach; endif; ?>
         </tbody>
      </table>
   </div>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>All Appointment Requests</h2>
      <div class="d-flex gap-2 flex-wrap">
         <?php foreach ($validStatuses as $s): ?>
            <a href="visitors.php?status=<?php echo urlencode($s); ?>" class="cg-btn cg-btn-sm <?php echo $s === $statusFilter ? 'cg-btn-primary' : 'cg-btn-outline'; ?>"><?php echo htmlspecialchars($s); ?></a>
         <?php endforeach; ?>
      </div>
   </div>
   <div class="cg-table-wrap">
      <table class="cg-table">
         <thead><tr><th>Institution / Contact</th><th>Purpose</th><th>Visit Date</th><th># Visitors</th><th>Status</th><th></th><?php if ($canFull): ?><th>Actions</th><?php endif; ?></tr></thead>
         <tbody>
         <?php if (!$appointments): ?>
            <tr><td colspan="7" class="text-muted">No appointment requests found for this filter.</td></tr>
         <?php else: foreach ($appointments as $a): ?>
            <tr>
               <td><?php echo htmlspecialchars($a['institution'] ?: $a['contact_name']); ?></td>
               <td><?php echo htmlspecialchars($a['purpose']); ?></td>
               <td><?php echo htmlspecialchars($a['visit_date'] ?: '—'); ?></td>
               <td><?php echo htmlspecialchars((string) ($a['num_visitors'] ?? '—')); ?></td>
               <td><?php echo cgStatusBadge($a['status']); ?></td>
               <td><a href="visitors.php?view=<?php echo (int) $a['id']; ?><?php echo $statusFilter !== 'All' ? '&status=' . urlencode($statusFilter) : ''; ?>" class="cg-btn cg-btn-outline cg-btn-sm">View</a></td>
               <?php if ($canFull): ?>
               <td>
                  <?php if ($a['status'] === 'Pending'): ?>
                     <div class="d-flex gap-1">
                        <form method="post" action="visitors.php">
                           <?php echo cg_csrf_field(); ?>
                           <input type="hidden" name="action" value="approve">
                           <input type="hidden" name="id" value="<?php echo (int) $a['id']; ?>">
                           <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($returnUrl); ?>">
                           <button type="submit" class="cg-btn cg-btn-primary cg-btn-sm"><i class="fa fa-check"></i></button>
                        </form>
                        <form method="post" action="visitors.php">
                           <?php echo cg_csrf_field(); ?>
                           <input type="hidden" name="action" value="reject">
                           <input type="hidden" name="id" value="<?php echo (int) $a['id']; ?>">
                           <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($returnUrl); ?>">
                           <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm"><i class="fa fa-times"></i></button>
                        </form>
                     </div>
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
