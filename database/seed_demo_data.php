<?php
/**
 * One-off CLI script: populates realistic demo activity so the dashboard,
 * visitor/registry lists, sales, and blog don't look empty. Safe to run
 * once (uses static content so re-running just adds duplicates — not
 * idempotent by design, this is a demo-data seeder, not a migration).
 * Run: php database/seed_demo_data.php
 */
require_once __DIR__ . '/../config/db.php';

function out(string $msg): void { echo $msg . "\n"; }

// ==================================================================
// Appointments (+ attendees) — spread over the last ~6 weeks and the
// next 2 weeks, mixed statuses.
// ==================================================================
$appointments = [
   ['institution' => 'Comboni College Lira', 'contact_name' => 'Sr. Immaculate Aciro', 'contact_phone' => '+256 772 441 220', 'contact_email' => 'aciro@combonilira.ac.ug', 'purpose' => 'school-trip', 'visit_date' => '2026-08-18', 'visit_time' => 'morning', 'num_visitors' => 35, 'message' => 'S3 Agriculture class study tour — would like a poultry-house walkthrough.', 'status' => 'Pending', 'days_ago' => 2,
      'attendees' => [['Immaculate Aciro','Teacher','41','+256 772 441 220'],['Brian Okot','Student','16',''],['Fiona Adong','Student','16','']]],
   ['institution' => 'St. Katherine Girls SS', 'contact_name' => 'Mr. Denis Ojok', 'contact_phone' => '+256 782 110 933', 'contact_email' => 'denis.ojok@skgss.sc.ug', 'purpose' => 'farm-tour', 'visit_date' => '2026-08-22', 'visit_time' => 'afternoon', 'num_visitors' => 28, 'message' => 'Career-day excursion, interested in dairy and crops sections.', 'status' => 'Approved', 'days_ago' => 6,
      'attendees' => [['Denis Ojok','Teacher','38','+256 782 110 933']]],
   ['institution' => 'Gulu University — Agriculture Dept.', 'contact_name' => 'Dr. Patricia Lamwaka', 'contact_phone' => '+256 700 556 812', 'contact_email' => 'p.lamwaka@gu.ac.ug', 'purpose' => 'study', 'visit_date' => '2026-08-15', 'visit_time' => 'full-day', 'num_visitors' => 12, 'message' => 'Final-year students researching mixed-farm production models.', 'status' => 'Approved', 'days_ago' => 9,
      'attendees' => [['Patricia Lamwaka','Lecturer','—','+256 700 556 812'],['Solomon Ebong','Student','23',''],['Ruth Nabirye','Student','22','']]],
   ['institution' => null, 'contact_name' => 'James Okwir', 'contact_phone' => '+256 752 903 471', 'contact_email' => 'j.okwir@gmail.com', 'purpose' => 'purchase', 'visit_date' => '2026-08-12', 'visit_time' => 'morning', 'num_visitors' => 2, 'message' => 'Would like to inspect Boer goats before a bulk purchase.', 'status' => 'Pending', 'days_ago' => 1, 'attendees' => []],
   ['institution' => 'Northern Uganda Youth Agripreneurs', 'contact_name' => 'Grace Amito', 'contact_phone' => '+256 772 660 145', 'contact_email' => 'grace.amito@nuya.org', 'purpose' => 'training', 'visit_date' => '2026-08-27', 'visit_time' => 'full-day', 'num_visitors' => 18, 'message' => 'Requesting the 1-day poultry management training package for our cohort.', 'status' => 'Pending', 'days_ago' => 3, 'attendees' => []],
   ['institution' => 'Lira City Council — Production Office', 'contact_name' => 'Mr. Vincent Ocen', 'contact_phone' => '+256 782 214 667', 'contact_email' => 'vincent.ocen@liracity.go.ug', 'purpose' => 'partnership', 'visit_date' => '2026-07-30', 'visit_time' => 'afternoon', 'num_visitors' => 4, 'message' => 'Discussing an extension-services partnership with the district.', 'status' => 'Arrived', 'days_ago' => 14, 'attendees' => []],
   ['institution' => 'Kakebe Technologies (Internship)', 'contact_name' => 'Sarah Nansubuga', 'contact_phone' => '+256 702 337 990', 'contact_email' => 'sarah.n@kakebetech.com', 'purpose' => 'internship', 'visit_date' => '2026-07-25', 'visit_time' => 'full-day', 'num_visitors' => 1, 'message' => '6-week industrial training placement, agribusiness track.', 'status' => 'Arrived', 'days_ago' => 18, 'attendees' => []],
   ['institution' => 'Bishop Negri Primary School', 'contact_name' => 'Ms. Betty Achola', 'contact_phone' => '+256 772 884 502', 'contact_email' => 'betty.achola@bnps.sc.ug', 'purpose' => 'school-trip', 'visit_date' => '2026-07-18', 'visit_time' => 'morning', 'num_visitors' => 42, 'message' => 'P6/P7 field trip — the kids are most excited about the chicks!', 'status' => 'Arrived', 'days_ago' => 24,
      'attendees' => [['Betty Achola','Teacher','34','+256 772 884 502'],['Moses Ebulu','Teacher','29','']]],
   ['institution' => null, 'contact_name' => 'Robert Ochola', 'contact_phone' => '+256 793 214 008', 'contact_email' => 'robert.ochola@yahoo.com', 'purpose' => 'other', 'visit_date' => '2026-07-10', 'visit_time' => '', 'num_visitors' => 3, 'message' => 'Requested a Saturday afternoon slot, we had no availability that week.', 'status' => 'Rejected', 'days_ago' => 32, 'attendees' => []],
   ['institution' => 'Unity Farmers SACCO', 'contact_name' => 'Mrs. Christine Auma', 'contact_phone' => '+256 772 550 129', 'contact_email' => 'c.auma@unitysacco.co.ug', 'purpose' => 'partnership', 'visit_date' => '2026-07-05', 'visit_time' => 'afternoon', 'num_visitors' => 6, 'message' => 'SACCO members interested in a milk off-take agreement — bad weather forced a reschedule we never confirmed.', 'status' => 'Rejected', 'days_ago' => 37, 'attendees' => []],
   ['institution' => 'Starlight Nursery & Primary', 'contact_name' => 'Ms. Winnie Aketch', 'contact_phone' => '+256 774 302 981', 'contact_email' => 'winnie@starlightschool.ug', 'purpose' => 'school-trip', 'visit_date' => '2026-09-02', 'visit_time' => 'morning', 'num_visitors' => 50, 'message' => 'Annual farm day for P4-P7.', 'status' => 'Pending', 'days_ago' => 0, 'attendees' => []],
];

$pdo->beginTransaction();
$apptIds = [];
$stmt = $pdo->prepare('INSERT INTO appointments (institution, contact_name, contact_phone, contact_email, purpose, visit_date, visit_time, num_visitors, message, status, checked_in_at, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
$attStmt = $pdo->prepare('INSERT INTO appointment_attendees (appointment_id, full_name, designation, age, contact) VALUES (?,?,?,?,?)');
foreach ($appointments as $a) {
   $createdAt = date('Y-m-d H:i:s', strtotime("-{$a['days_ago']} days"));
   $checkedIn = $a['status'] === 'Arrived' ? $createdAt : null;
   $stmt->execute([$a['institution'], $a['contact_name'], $a['contact_phone'], $a['contact_email'], $a['purpose'], $a['visit_date'], $a['visit_time'], $a['num_visitors'], $a['message'], $a['status'], $checkedIn, $createdAt]);
   $id = (int) $pdo->lastInsertId();
   $apptIds[] = ['id' => $id, 'status' => $a['status'], 'contact_name' => $a['contact_name'], 'contact_phone' => $a['contact_phone'], 'contact_email' => $a['contact_email'], 'institution' => $a['institution'], 'purpose' => $a['purpose'], 'created_at' => $createdAt];
   foreach ($a['attendees'] as [$name, $desig, $age, $contact]) {
      $attStmt->execute([$id, $name, $desig, $age, $contact]);
   }
}
out('Inserted ' . count($appointments) . ' appointments.');

// ==================================================================
// Front-desk visitor registry — mirror the 3 "Arrived" appointments
// (as admin/visitors.php's check-in flow would), plus a handful of
// pure walk-ins with no prior booking.
// ==================================================================
$regStmt = $pdo->prepare('INSERT INTO visitor_registry (full_name, phone, email, purpose, host_person, appointment_id, signed_in_at, signed_out_at, recorded_by) VALUES (?,?,?,?,?,?,?,?,?)');
$linked = 0;
foreach ($apptIds as $a) {
   if ($a['status'] !== 'Arrived') continue;
   $signIn = $a['created_at'];
   $signOut = date('Y-m-d H:i:s', strtotime($signIn . ' +2 hours'));
   $regStmt->execute([$a['contact_name'], $a['contact_phone'], $a['contact_email'], $a['purpose'], null, $a['id'], $signIn, $signOut, 2]);
   $linked++;
}

$walkIns = [
   ['Michael Opio', '+256 701 887 234', null, 'Enquiring about broiler chick prices', 'Front Desk', 22],
   ['Agnes Nakato', '+256 772 004 561', 'agnes.nakato@gmail.com', 'Delivering veterinary supplies', 'Farm Manager', 15],
   ['Hassan Mugisha', '+256 782 337 120', null, 'Meeting about feed supply contract', 'David Okello', 10],
   ['Florence Aol', '+256 700 118 943', 'florence.aol@outlook.com', 'Journalist — feature story on model farms', 'Sarah Achen', 7],
   ['Peter Lubega', '+256 772 665 890', null, 'Dropping off a job application', 'Front Desk', 4],
   ['Joyce Amongi', '+256 793 441 076', null, 'Picking up an egg order', 'Front Desk', 1],
];
foreach ($walkIns as [$name, $phone, $email, $purpose, $host, $daysAgo]) {
   $signIn = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days 9 hours"));
   $signOut = $daysAgo > 1 ? date('Y-m-d H:i:s', strtotime($signIn . ' +45 minutes')) : null; // today's most recent one still "in"
   $regStmt->execute([$name, $phone, $email, $purpose, $host, null, $signIn, $signOut, 2]);
}
out('Inserted ' . $linked . ' registry entries linked to appointments + ' . count($walkIns) . ' walk-ins.');

// ==================================================================
// Sales — more history across recent weeks.
// ==================================================================
$sales = [
   ['Broiler Chickens', 150, 'Birds', 'Savannah Grill Restaurant', '+256 772 118 400', 1500000, 'Paid', '2026-06-14'],
   ['Coffee Beans', 80, 'Kg', 'Lira Roasters Co-op', '+256 782 552 310', 960000, 'Paid', '2026-06-20'],
   ['Bananas', 60, 'Bunches', 'Amuca Market Traders', '+256 701 229 887', 900000, 'Unpaid', '2026-06-28'],
   ['Fresh Eggs', 25, 'Trays', 'Divine Hotel Lira', '+256 772 900 214', 300000, 'Paid', '2026-07-04'],
   ['Fresh Cow Milk', 100, 'Litres', 'Lira Dairy Processors', '+256 793 447 761', 250000, 'Paid', '2026-07-11'],
   ['Day-Old Chicks', 500, 'Chicks', 'Otwal Poultry Farm', '+256 772 336 004', 5000000, 'Unpaid', '2026-07-19'],
   ['Boer Goats', 4, 'Heads', 'Kitgum Livestock Traders', '+256 782 118 552', 2400000, 'Paid', '2026-07-28'],
   ['Maize', 300, 'Kg', 'Northern Grain Bank', '+256 700 664 219', 540000, 'Paid', '2026-08-02'],
   ['Fresh Eggs', 15, 'Trays', 'Corner Café Lira', '+256 772 553 809', 180000, 'Paid', '2026-08-06'],
   ['Fresh Cow Milk', 40, 'Litres', 'Amuca Trading Center', '+256 782 990 447', 100000, 'Unpaid', '2026-08-09'],
];
$saleStmt = $pdo->prepare('INSERT INTO sales (product, quantity, unit, buyer, buyer_phone, amount, status, sale_date, created_by) VALUES (?,?,?,?,?,?,?,?,?)');
foreach ($sales as [$product, $qty, $unit, $buyer, $phone, $amount, $status, $date]) {
   $saleStmt->execute([$product, $qty, $unit, $buyer, $phone, $amount, $status, $date, 3]);
}
out('Inserted ' . count($sales) . ' sales records.');

// ==================================================================
// Blog — 3 more published posts (one with a captioned image gallery).
// ==================================================================
$posts = [
   [
      'title' => 'Inside Our Coffee & Banana Intercropping Trial', 'slug' => 'coffee-banana-intercropping-trial', 'category' => 'Crops', 'tags' => 'coffee,bananas,intercropping,soil-health',
      'excerpt' => 'How planting bananas alongside coffee is improving soil moisture and giving us a second income stream from the same plot.',
      'content' => '<p>Two seasons ago we began intercropping bananas with our Robusta coffee on the eastern plot. The banana canopy shades the young coffee bushes, cuts water loss, and gives us matoke to sell while the coffee matures.</p><p>Early results: soil moisture readings are consistently higher between rows, and we have not lost a single coffee seedling to sun-scorch this dry season — a real problem in previous years.</p>',
      'featured_image' => 'images/gallary/christoph-coffee-171653_1920.jpg', 'author_id' => 2, 'status' => 'published', 'publish_date' => '2026-06-18',
      'meta_title' => 'Coffee & Banana Intercropping at City Gate Mixed Farm', 'meta_description' => 'How City Gate Mixed Farm is intercropping coffee and bananas to improve soil health and diversify income in Lira City, Uganda.',
      'gallery' => [
         ['images/gallary/christoph-coffee-171653_1920.jpg', 'Coffee cherries ripening under banana shade'],
         ['images/gallary/marcusvu-coffee-2992598_1920.jpg', 'Rows of intercropped coffee and banana'],
         ['images/gallary/hat3m-seedling-4394118_1920.jpg', 'New seedlings ready for the next plot'],
      ],
   ],
   [
      'title' => 'Meet the Team Behind Our Dairy Herd', 'slug' => 'meet-the-team-behind-our-dairy-herd', 'category' => 'Dairy', 'tags' => 'dairy,team,cows,milk',
      'excerpt' => 'A day in the life of the herders and milkers who keep roughly 80 litres flowing every single morning.',
      'content' => '<p>Before sunrise, our dairy team is already in the milking shed. Consistency is everything in dairy — the same feeding schedule, the same hygiene routine, the same calm handling every day is what keeps yields steady.</p><p>We currently milk twice daily and supply both retail customers and a local processor in Lira.</p>',
      'featured_image' => 'images/gallary/jaclou-dl-cow-4270355_1920.jpg', 'author_id' => 4, 'status' => 'published', 'publish_date' => '2026-07-02',
      'meta_title' => 'Meet the Dairy Team | City Gate Mixed Farm', 'meta_description' => 'The daily routine behind City Gate Mixed Farm\'s dairy herd in Amuca, Lira City, Uganda.',
      'gallery' => [],
   ],
   [
      'title' => 'Preparing for Our Busiest School-Visit Season', 'slug' => 'preparing-for-busiest-school-visit-season', 'category' => 'Training', 'tags' => 'school-trips,visits,training',
      'excerpt' => 'Term 3 brings our highest volume of school bookings — here is how the front desk keeps every group organised.',
      'content' => '<p>Between August and October we typically host 15-20 school groups. Preparation matters: each teacher submits a headcount and purpose in advance through our online booking form, and our front desk checks every group in on arrival so nothing is duplicated on paper.</p><p>If you are a teacher planning a visit, book early — our September slots are filling up fast.</p>',
      'featured_image' => 'images/gallary/pexels-chicken-1867521_1920.jpg', 'author_id' => 2, 'status' => 'published', 'publish_date' => '2026-08-05',
      'meta_title' => 'Planning a School Farm Visit | City Gate Mixed Farm', 'meta_description' => 'What to expect when booking a school trip to City Gate Mixed Farm in Lira City, Uganda.',
      'gallery' => [],
   ],
];
$postStmt = $pdo->prepare('INSERT INTO blog_posts (title, slug, category, tags, excerpt, content, featured_image, author_id, status, publish_date, meta_title, meta_description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
$imgStmt = $pdo->prepare('INSERT INTO blog_post_images (post_id, image_path, caption, sort_order) VALUES (?,?,?,?)');
$newPostIds = [];
foreach ($posts as $p) {
   $postStmt->execute([$p['title'], $p['slug'], $p['category'], $p['tags'], $p['excerpt'], $p['content'], $p['featured_image'], $p['author_id'], $p['status'], $p['publish_date'], $p['meta_title'], $p['meta_description']]);
   $pid = (int) $pdo->lastInsertId();
   $newPostIds[$p['slug']] = $pid;
   $order = 0;
   foreach ($p['gallery'] as [$path, $caption]) {
      $imgStmt->execute([$pid, $path, $caption, $order++]);
   }
}
out('Inserted ' . count($posts) . ' blog posts (1 with a 3-image gallery).');

// A few more comments across posts (existing seeded posts have ids 1-3).
$comments = [
   [1, 'Michael Ojara', 'michael.o@gmail.com', 'Impressive growth! Would love to see a follow-up on your feed conversion ratio.'],
   [1, 'Agnes N.', '', 'We toured the poultry houses last month — exactly as described here. Very clean operation.'],
   [2, 'Dr. Komakech', 'komakech@health.go.ug', 'Great, simple explanation. Sharing this with our community health volunteers.'],
   [$newPostIds['coffee-banana-intercropping-trial'], 'Farmer Richard', '', 'Been thinking about intercropping my own plot — how far apart did you space the banana rows?'],
   [$newPostIds['meet-the-team-behind-our-dairy-herd'], 'Susan A.', 'susan.a@yahoo.com', 'Your milk is the best in Lira, hands down!'],
];
$comStmt = $pdo->prepare('INSERT INTO blog_comments (post_id, name, email, comment, created_at) VALUES (?,?,?,?,?)');
foreach ($comments as $i => [$pid, $name, $email, $comment]) {
   $ts = date('Y-m-d H:i:s', strtotime('-' . (20 - $i * 3) . ' days'));
   $comStmt->execute([$pid, $name, $email ?: null, $comment, $ts]);
}
out('Inserted ' . count($comments) . ' blog comments.');

// ==================================================================
// Product reviews (shop-single.php's demo product slug: bovans-browns).
// ==================================================================
$reviews = [
   ['Patricia K.', 5, 'Bought 10 pullets last month, all healthy and laying well already.'],
   ['Emmanuel T.', 4, 'Good stock, transport arrangement from the farm was smooth.'],
   ['Nakato Ritah', 5, 'Second time buying from City Gate — consistent quality every time.'],
];
$revStmt = $pdo->prepare('INSERT INTO product_reviews (product_slug, name, rating, comment, created_at) VALUES (?,?,?,?,?)');
foreach ($reviews as $i => [$name, $rating, $comment]) {
   $ts = date('Y-m-d H:i:s', strtotime('-' . (15 - $i * 4) . ' days'));
   $revStmt->execute(['bovans-browns', $name, $rating, $comment, $ts]);
}
out('Inserted ' . count($reviews) . ' product reviews.');

// ==================================================================
// A couple more realistic contact messages.
// ==================================================================
$contacts = [
   ['Moses Okello', 'moses.okello@gmail.com', '+256 772 118 664', 'Bulk maize order', 'Hello, I run a small feed shop in Lira and would like a quote for 1 tonne of maize delivered monthly.'],
   ['Immaculate Aweko', 'i.aweko@yahoo.com', '+256 782 990 213', 'School partnership', 'We are a primary school in Otuke district interested in a termly educational partnership. Please advise on availability.'],
];
$cmStmt = $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?,?,?,?,?,?)');
foreach ($contacts as $i => [$name, $email, $phone, $subject, $message]) {
   $ts = date('Y-m-d H:i:s', strtotime('-' . (10 - $i * 3) . ' days'));
   $cmStmt->execute([$name, $email, $phone, $subject, $message, $ts]);
}
out('Inserted ' . count($contacts) . ' contact messages.');

$pdo->commit();
out('Done.');
