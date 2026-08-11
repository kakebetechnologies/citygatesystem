<?php
/**
 * Public "Book a Visit" page. POSTs to itself, validates server-side,
 * inserts into appointments (+ appointment_attendees) inside a transaction,
 * then redirects to itself with ?success=1 so a page refresh never
 * re-submits the form.
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php'; // session + CSRF helpers only (no login required to book)

$purposes = [
   'farm-tour'    => 'Farm Tour',
   'school-trip'  => 'School Trip',
   'study'        => 'Study / Research',
   'training'     => 'Training',
   'internship'   => 'Internship',
   'partnership'  => 'Partnership',
   'purchase'     => 'Purchase Products',
   'other'        => 'Other',
];
$times = [
   'morning'   => 'Morning (7:00 AM - 12:00 PM)',
   'afternoon' => 'Afternoon (12:00 PM - 5:00 PM)',
   'full-day'  => 'Full Day',
];

$errors = [];
$old = [
   'institution' => '', 'contact_name' => '', 'contact_phone' => '', 'contact_email' => '',
   'purpose' => '', 'visit_date' => '', 'visit_time' => '', 'num_visitors' => '', 'message' => '',
];
$oldAttendees = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   cg_verify_csrf();

   $old['institution']   = trim($_POST['institution'] ?? '');
   $old['contact_name']  = trim($_POST['contact_name'] ?? '');
   $old['contact_phone'] = trim($_POST['contact_phone'] ?? '');
   $old['contact_email'] = trim($_POST['contact_email'] ?? '');
   $old['purpose']       = trim($_POST['purpose'] ?? '');
   $old['visit_date']    = trim($_POST['visit_date'] ?? '');
   $old['visit_time']    = trim($_POST['visit_time'] ?? '');
   $old['num_visitors']  = trim($_POST['num_visitors'] ?? '');
   $old['message']       = trim($_POST['message'] ?? '');

   if ($old['contact_name'] === '') $errors[] = 'Contact person\'s name is required.';
   if ($old['contact_phone'] === '') $errors[] = 'Contact person\'s phone is required.';
   if ($old['contact_email'] === '') $errors[] = 'Contact person\'s email is required.';
   elseif (!filter_var($old['contact_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
   if ($old['purpose'] === '' || !isset($purposes[$old['purpose']])) $errors[] = 'Please select a purpose of visit.';
   if ($old['visit_date'] === '') $errors[] = 'Date of visit is required.';
   if ($old['visit_time'] !== '' && !isset($times[$old['visit_time']])) $errors[] = 'Invalid preferred time selected.';

   $numVisitors = null;
   if ($old['num_visitors'] !== '') {
      if (!ctype_digit($old['num_visitors']) || (int)$old['num_visitors'] < 1) {
         $errors[] = 'Number of visitors must be a positive whole number.';
      } else {
         $numVisitors = (int) $old['num_visitors'];
      }
   }

   $attNames = $_POST['attendee_name'] ?? [];
   $attDesig = $_POST['attendee_designation'] ?? [];
   $attAge = $_POST['attendee_age'] ?? [];
   $attContact = $_POST['attendee_contact'] ?? [];
   $attendees = [];
   if (is_array($attNames)) {
      foreach ($attNames as $i => $name) {
         $name = trim((string) $name);
         if ($name === '') continue;
         $attendees[] = [
            'full_name'   => $name,
            'designation' => trim((string) ($attDesig[$i] ?? '')),
            'age'         => trim((string) ($attAge[$i] ?? '')),
            'contact'     => trim((string) ($attContact[$i] ?? '')),
         ];
      }
   }
   $oldAttendees = $attendees;

   if (!$errors) {
      try {
         $pdo->beginTransaction();
         $stmt = $pdo->prepare('INSERT INTO appointments (institution, contact_name, contact_phone, contact_email, purpose, visit_date, visit_time, num_visitors, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, \'Pending\')');
         $stmt->execute([
            $old['institution'] !== '' ? $old['institution'] : null,
            $old['contact_name'],
            $old['contact_phone'],
            $old['contact_email'],
            $old['purpose'],
            $old['visit_date'],
            $old['visit_time'] !== '' ? $old['visit_time'] : null,
            $numVisitors,
            $old['message'] !== '' ? $old['message'] : null,
         ]);
         $appointmentId = (int) $pdo->lastInsertId();

         if ($attendees) {
            $attStmt = $pdo->prepare('INSERT INTO appointment_attendees (appointment_id, full_name, designation, age, contact) VALUES (?, ?, ?, ?, ?)');
            foreach ($attendees as $a) {
               $attStmt->execute([
                  $appointmentId,
                  $a['full_name'],
                  $a['designation'] !== '' ? $a['designation'] : null,
                  $a['age'] !== '' ? $a['age'] : null,
                  $a['contact'] !== '' ? $a['contact'] : null,
               ]);
            }
         }

         $pdo->commit();
         header('Location: bookings.php?success=1');
         exit;
      } catch (Throwable $e) {
         if ($pdo->inTransaction()) $pdo->rollBack();
         $errors[] = 'Something went wrong saving your booking. Please try again.';
      }
   }
}

$success = isset($_GET['success']);

$pageTitle = 'Book a Visit | City Gate Mixed Farm';
$pageDescription = 'Book a farm visit, training session, or school tour at City Gate Mixed Farm in Amuca, Lira City, Uganda.';
$activeNav = '';
require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .booking-form-card {
      background: #fff;
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: var(--cg-shadow);
      margin-bottom: 2rem;
   }
   .booking-form-card h3 { color: var(--farm-green); font-weight: 700; margin-bottom: 0.5rem; }
   .booking-form-card .subtitle { color: #666; margin-bottom: 2rem; }
   .booking-form-card .form-label { font-weight: 600; color: #333; margin-bottom: 0.5rem; }
   .booking-form-card .form-control, .booking-form-card .form-select {
      border-radius: 10px; padding: 0.8rem 1rem; border: 2px solid #e0e0e0; transition: all 0.3s ease;
   }
   .booking-form-card .form-control:focus, .booking-form-card .form-select:focus {
      border-color: var(--farm-green); box-shadow: 0 0 0 0.2rem rgba(47,93,58,0.2);
   }
   .btn-submit {
      background: linear-gradient(135deg, var(--farm-green), var(--farm-green-dark));
      color: #fff; padding: 1rem 2rem; border-radius: 50px; font-weight: 600; border: none;
      width: 100%; font-size: 1.1rem; transition: all 0.3s ease;
   }
   .btn-submit:hover { transform: translateY(-3px); color: #fff; }
   .success-message { background: #e8f5e9; border: 2px solid var(--farm-green); border-radius: 15px; padding: 2rem; text-align: center; }
   .success-message i { font-size: 3rem; color: var(--farm-green); margin-bottom: 1rem; }
   .booking-errors { background: #fbe2e2; border: 2px solid #c0392b; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; color: #a32e2e; }
   .booking-errors ul { margin: 0; padding-left: 1.1rem; }
   .info-section { background: #f8fff8; padding: 3rem 0; }
   .info-card { background: #fff; border-radius: 15px; padding: 1.5rem; text-align: center; height: 100%; box-shadow: var(--cg-shadow); }
   .info-card i { font-size: 2rem; color: var(--farm-green); margin-bottom: 1rem; }
   .info-card h5 { color: #333; font-weight: 700; margin-bottom: 0.5rem; }
   .attendeesWrap { background: #f8fff8; border: 2px dashed #cfe8cf; border-radius: 15px; padding: 1.25rem; margin-bottom: 1.5rem; }
   .attendeesHead { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 0.75rem; }
   .attendeesHead small { color: #777; }
   .btn-add-attendee { background: var(--farm-green); color: #fff; border: none; border-radius: 20px; padding: 6px 18px; font-size: 0.9rem; font-weight: 600; }
   .btn-add-attendee:hover { background: var(--farm-green-dark); color: #fff; }
   .attendee-row { background: #fff; border-radius: 10px; padding: 10px 12px 2px; margin-bottom: 10px; border: 1px solid #e6e6e6; }
   .attendee-row .form-control { padding: 0.5rem 0.75rem; border-width: 1px; }
   .remove-attendee-btn { border-radius: 8px; border: 1px solid #e0a0a0; color: #c0392b; background: #fff; width: 100%; }
   .attendeesEmpty { color: #888; font-size: 0.9rem; margin: 0; }
   @media (max-width: 768px) { .booking-form-card { padding: 1.5rem; } }
</style>

<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1>Book a Farm Visit</h1>
      <p style="color:rgba(255,255,255,.85); margin-bottom:14px;">Schedule a tour, training session, or school visit at City Gate Mixed Farm</p>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Book a Visit</li>
      </ul>
   </div>
</section>

<section class="py-5">
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-lg-9">
            <div class="booking-form-card">
               <?php if ($success): ?>
                  <div class="success-message">
                     <i class="fa fa-check-circle"></i>
                     <h4 style="color: var(--farm-green);">Thank You!</h4>
                     <p>Your booking request has been submitted and is now <strong>pending review</strong>. We'll contact you within 48 hours to confirm your visit.</p>
                     <p class="mb-0"><strong>City Gate Mixed Farm</strong><br>Amuca, Lira City, Uganda</p>
                  </div>
               <?php else: ?>
                  <h3>Book / Enquire</h3>
                  <p class="subtitle">Fill in the form below and we'll get back to you within 48 hours.</p>

                  <?php if ($errors): ?>
                     <div class="booking-errors">
                        <ul>
                           <?php foreach ($errors as $err): ?>
                              <li><?php echo htmlspecialchars($err); ?></li>
                           <?php endforeach; ?>
                        </ul>
                     </div>
                  <?php endif; ?>

                  <form id="bookingForm" method="post" action="bookings.php" novalidate>
                     <?php echo cg_csrf_field(); ?>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Institution / Group Name</label>
                           <input type="text" class="form-control" name="institution" value="<?php echo htmlspecialchars($old['institution']); ?>" placeholder="School, company or group name (leave blank if visiting alone)">
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Number of Visitors</label>
                           <input type="number" min="1" step="1" class="form-control" name="num_visitors" value="<?php echo htmlspecialchars($old['num_visitors']); ?>" placeholder="e.g. 1">
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Contact Person's Full Name *</label>
                           <input type="text" class="form-control" name="contact_name" value="<?php echo htmlspecialchars($old['contact_name']); ?>" required placeholder="Your full name">
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Contact Person's Phone *</label>
                           <input type="tel" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($old['contact_phone']); ?>" required placeholder="+256 XXX XXX XXX">
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Contact Person's Email *</label>
                           <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($old['contact_email']); ?>" required placeholder="your@email.com">
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Purpose of Visit *</label>
                           <select class="form-select" name="purpose" required>
                              <option value="">Select purpose...</option>
                              <?php foreach ($purposes as $val => $label): ?>
                                 <option value="<?php echo htmlspecialchars($val); ?>"<?php echo $old['purpose'] === $val ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Date of Visit *</label>
                           <input type="date" class="form-control" name="visit_date" value="<?php echo htmlspecialchars($old['visit_date']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                           <label class="form-label">Preferred Time</label>
                           <select class="form-select" name="visit_time">
                              <option value="">Select time...</option>
                              <?php foreach ($times as $val => $label): ?>
                                 <option value="<?php echo htmlspecialchars($val); ?>"<?php echo $old['visit_time'] === $val ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>

                     <!-- Dynamic Attendees -->
                     <div class="attendeesWrap">
                        <div class="attendeesHead">
                           <div>
                              <label class="form-label mb-0">Attendees</label><br>
                              <small>Optional — list attendees by name, especially for groups</small>
                           </div>
                           <button type="button" id="addAttendeeBtn" class="btn-add-attendee"><i class="fa fa-plus"></i> Add Attendee</button>
                        </div>
                        <div id="attendeesList">
                           <?php foreach ($oldAttendees as $a): ?>
                              <div class="attendee-row row g-2 align-items-end">
                                 <div class="col-sm-3 mb-2">
                                    <label class="form-label small mb-1">Full Name</label>
                                    <input type="text" class="form-control" name="attendee_name[]" value="<?php echo htmlspecialchars($a['full_name']); ?>" placeholder="Full name">
                                 </div>
                                 <div class="col-sm-3 mb-2">
                                    <label class="form-label small mb-1">Designation</label>
                                    <input type="text" class="form-control" name="attendee_designation[]" value="<?php echo htmlspecialchars($a['designation']); ?>" placeholder="e.g. Teacher, Student">
                                 </div>
                                 <div class="col-sm-2 mb-2">
                                    <label class="form-label small mb-1">Age / Age Group</label>
                                    <input type="text" class="form-control" name="attendee_age[]" value="<?php echo htmlspecialchars($a['age']); ?>" placeholder="e.g. 15 or 10-12">
                                 </div>
                                 <div class="col-sm-3 mb-2">
                                    <label class="form-label small mb-1">Contact</label>
                                    <input type="text" class="form-control" name="attendee_contact[]" value="<?php echo htmlspecialchars($a['contact']); ?>" placeholder="Phone/email (optional)">
                                 </div>
                                 <div class="col-sm-1 mb-2">
                                    <button type="button" class="remove-attendee-btn" title="Remove attendee">&times;</button>
                                 </div>
                              </div>
                           <?php endforeach; ?>
                        </div>
                        <p class="attendeesEmpty" id="attendeesEmptyMsg" style="<?php echo $oldAttendees ? 'display:none;' : ''; ?>">No attendees added yet. Click "Add Attendee" to list who's coming.</p>
                     </div>

                     <div class="mb-4">
                        <label class="form-label">Additional Notes</label>
                        <textarea class="form-control" name="message" rows="4" placeholder="Anything else we should know?"><?php echo htmlspecialchars($old['message']); ?></textarea>
                     </div>

                     <button type="submit" class="btn-submit">Submit Booking Request</button>
                  </form>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Info Section -->
<section class="info-section">
   <div class="container">
      <div class="row g-4">
         <div class="col-md-4">
            <div class="info-card">
               <i class="fa fa-map-marker"></i>
               <h5>Location</h5>
               <p class="text-muted mb-0">Amuca, Lira City, Uganda</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="info-card">
               <i class="fa fa-clock-o"></i>
               <h5>Visiting Hours</h5>
               <p class="text-muted mb-0">Monday - Saturday<br>7:00 AM - 5:00 PM</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="info-card">
               <i class="fa fa-phone"></i>
               <h5>Quick Contact</h5>
               <p class="text-muted mb-0">+256 123 456 789<br>WhatsApp available</p>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
(function () {
   var attendeesList = document.getElementById('attendeesList');
   var addBtn = document.getElementById('addAttendeeBtn');
   var emptyMsg = document.getElementById('attendeesEmptyMsg');
   if (!attendeesList || !addBtn) return;

   function toggleEmptyMsg() {
      emptyMsg.style.display = attendeesList.children.length > 0 ? 'none' : 'block';
   }

   function addAttendeeRow() {
      var row = document.createElement('div');
      row.className = 'attendee-row row g-2 align-items-end';
      row.innerHTML =
         '<div class="col-sm-3 mb-2">' +
            '<label class="form-label small mb-1">Full Name</label>' +
            '<input type="text" class="form-control" name="attendee_name[]" placeholder="Full name">' +
         '</div>' +
         '<div class="col-sm-3 mb-2">' +
            '<label class="form-label small mb-1">Designation</label>' +
            '<input type="text" class="form-control" name="attendee_designation[]" placeholder="e.g. Teacher, Student">' +
         '</div>' +
         '<div class="col-sm-2 mb-2">' +
            '<label class="form-label small mb-1">Age / Age Group</label>' +
            '<input type="text" class="form-control" name="attendee_age[]" placeholder="e.g. 15 or 10-12">' +
         '</div>' +
         '<div class="col-sm-3 mb-2">' +
            '<label class="form-label small mb-1">Contact</label>' +
            '<input type="text" class="form-control" name="attendee_contact[]" placeholder="Phone/email (optional)">' +
         '</div>' +
         '<div class="col-sm-1 mb-2">' +
            '<button type="button" class="remove-attendee-btn" title="Remove attendee">&times;</button>' +
         '</div>';
      attendeesList.appendChild(row);
      toggleEmptyMsg();
   }

   addBtn.addEventListener('click', addAttendeeRow);

   attendeesList.addEventListener('click', function (e) {
      var btn = e.target.closest('.remove-attendee-btn');
      if (!btn) return;
      btn.closest('.attendee-row').remove();
      toggleEmptyMsg();
   });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
