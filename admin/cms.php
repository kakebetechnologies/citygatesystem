<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'cms';
$pageTitle = 'Website Content';
$pageSub = 'Edit the text shown on public-facing pages';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/cms.php';

$canEdit = cg_can('cms', 'full');
$message = '';

// ---- Hero slide defaults (used until an admin overrides them) ----
$heroSlideDefaults = [
   1 => ['eyebrow' => 'Poultry', 'title' => 'Modern Poultry Farming', 'sub' => 'Clean housing. Organized feeding. Healthy birds.', 'image' => 'images/gallary/pexels-chicken-1867521_1920.jpg'],
   2 => ['eyebrow' => 'Dairy', 'title' => 'Fresh Daily Milk', 'sub' => 'Straight from a well-cared-for herd in Amuca.', 'image' => 'images/gallary/jaclou-dl-cow-4270355_1920.jpg'],
   3 => ['eyebrow' => 'Goats', 'title' => 'Premium Goat Breeds', 'sub' => 'Boer & Savannah — strong, healthy, farm-raised.', 'image' => 'images/gallary/walter46-goat-4138049_1920.jpg'],
   4 => ['eyebrow' => 'Crops', 'title' => 'Sustainable Farming', 'sub' => 'Coffee and banana, grown on our 4-acre model farm.', 'image' => 'images/gallary/marcusvu-coffee-2992598_1920.jpg'],
   5 => ['eyebrow' => 'Training & Visits', 'title' => 'Visit. Learn. Grow.', 'sub' => 'Hands-on training and farm tours in Lira City.', 'image' => 'images/gallary/rooster-farm.jpg'],
];

/** Ensures an uploads subdirectory exists and is hardened against PHP execution. */
function cg_cms_ensure_upload_dir(string $dir): void {
   if (!is_dir($dir)) mkdir($dir, 0755, true);
   $htaccess = $dir . '/.htaccess';
   if (!file_exists($htaccess)) {
      file_put_contents($htaccess, "php_flag engine off\n<Files \"*.php\">\nDeny from all\n</Files>\n");
   }
}

/** Validates + saves an uploaded hero-slide image into uploads/hero/, returns relative path or null. */
function cg_save_hero_image(array $file): ?string {
   if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || (int) $file['size'] <= 0) return null;
   if ($file['size'] > 8 * 1024 * 1024) return null;
   $info = @getimagesize($file['tmp_name']);
   if ($info === false) return null;
   $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
   $mime = $info['mime'] ?? '';
   if (!isset($allowedMime[$mime])) return null;
   $ext = $allowedMime[$mime];
   $destDir = __DIR__ . '/../uploads/hero';
   cg_cms_ensure_upload_dir($destDir);
   $filename = bin2hex(random_bytes(8)) . '.' . $ext;
   if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) return null;
   return 'uploads/hero/' . $filename;
}

// Field groups: each field is [field_key, label, type(text|textarea), placeholder/default]
$groups = [
   'home' => [
      'title' => 'Homepage Impact Stats',
      'pairs' => [
         ['num' => 'impact_years', 'label' => 'impact_years_label', 'numTitle' => 'Years Operating — Number', 'labelTitle' => 'Years Operating — Label', 'numDefault' => '8', 'labelDefault' => 'Years Operating'],
         ['num' => 'impact_farmers', 'label' => 'impact_farmers_label', 'numTitle' => 'Farmers Trained — Number', 'labelTitle' => 'Farmers Trained — Label', 'numDefault' => '300+', 'labelDefault' => 'Farmers Trained'],
         ['num' => 'impact_students', 'label' => 'impact_students_label', 'numTitle' => 'Students Hosted — Number', 'labelTitle' => 'Students Hosted — Label', 'numDefault' => '1,200+', 'labelDefault' => 'Students Hosted'],
         ['num' => 'impact_sectors', 'label' => 'impact_sectors_label', 'numTitle' => 'Farm Sectors — Number', 'labelTitle' => 'Farm Sectors — Label', 'numDefault' => '4', 'labelDefault' => 'Farm Sectors'],
      ],
   ],
];

// Simple text/textarea fields, keyed by page_key (hero slide fields have
// their own dedicated editor block further down, not this generic loop)
$textFields = [
   'about' => [
      ['key' => 'mission_text', 'label' => 'Mission', 'type' => 'textarea', 'placeholder' => 'To operate a profitable, sustainable integrated farm that feeds, employs, and educates Northern Uganda.'],
      ['key' => 'vision_text', 'label' => 'Vision', 'type' => 'textarea', 'placeholder' => 'To be the leading model farm and agricultural training center in Northern Uganda.'],
   ],
];

// All field_keys we manage, per page_key (used to build the submit list)
$allFieldKeys = [
   'home' => ['impact_years', 'impact_years_label', 'impact_farmers', 'impact_farmers_label', 'impact_students', 'impact_students_label', 'impact_sectors', 'impact_sectors_label', 'hero_title', 'hero_sub'],
   'about' => ['mission_text', 'vision_text'],
];
foreach ($heroSlideDefaults as $n => $d) {
   $allFieldKeys['home'][] = "hero_slide_{$n}_eyebrow";
   $allFieldKeys['home'][] = "hero_slide_{$n}_title";
   $allFieldKeys['home'][] = "hero_slide_{$n}_sub";
   // hero_slide_N_image is handled separately below (file upload, not a plain text field).
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (!$canEdit) {
      http_response_code(403);
      die('You do not have permission to edit website content.');
   }
   cg_verify_csrf();

   $upsert = $pdo->prepare('INSERT INTO cms_content (page_key, field_key, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
   $delete = $pdo->prepare('DELETE FROM cms_content WHERE page_key = ? AND field_key = ?');

   foreach ($allFieldKeys as $pageKey => $fieldKeys) {
      foreach ($fieldKeys as $fieldKey) {
         $postName = $pageKey . '__' . $fieldKey;
         $value = trim($_POST[$postName] ?? '');
         if ($value !== '') {
            $upsert->execute([$pageKey, $fieldKey, $value]);
         } else {
            $delete->execute([$pageKey, $fieldKey]);
         }
      }
   }

   // Hero slide image uploads — only touched when a new file was actually chosen.
   foreach (array_keys($heroSlideDefaults) as $n) {
      $fieldKey = "hero_slide_{$n}_image";
      $fileKey = "home__{$fieldKey}";
      if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
         $path = cg_save_hero_image($_FILES[$fileKey]);
         if ($path !== null) {
            $upsert->execute(['home', $fieldKey, $path]);
         }
      }
   }

   header('Location: cms.php?saved=1#hero');
   exit;
}

// Load current values for both page keys
$currentHome = cg_cms_all($pdo, 'home');
$currentAbout = cg_cms_all($pdo, 'about');
$currentByPage = ['home' => $currentHome, 'about' => $currentAbout];

if (isset($_GET['saved'])) {
   $message = 'Changes saved.';
}

function cg_input_name($pageKey, $fieldKey) {
   return htmlspecialchars($pageKey . '__' . $fieldKey);
}
?>
<div class="cg-panel">
   <div class="cg-panel-head"><h2><?php echo htmlspecialchars($groups['home']['title']); ?></h2></div>
   <?php if (!$canEdit): ?><p class="text-muted">You have read-only access to this module.</p><?php endif; ?>
   <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

   <form method="post" action="cms.php" enctype="multipart/form-data">
      <?php echo cg_csrf_field(); ?>
      <div class="row g-3 mb-2">
         <?php foreach ($groups['home']['pairs'] as $pair):
            $numVal = $currentByPage['home'][$pair['num']] ?? '';
            $labelVal = $currentByPage['home'][$pair['label']] ?? '';
            $disabled = $canEdit ? '' : ' disabled';
         ?>
         <div class="col-md-6 col-lg-3">
            <div class="border rounded p-3 h-100">
               <label class="form-label small text-muted mb-1"><?php echo htmlspecialchars($pair['numTitle']); ?></label>
               <input type="text" class="form-control mb-2" name="<?php echo cg_input_name('home', $pair['num']); ?>"
                      value="<?php echo htmlspecialchars($numVal); ?>" placeholder="<?php echo htmlspecialchars($pair['numDefault']); ?>"<?php echo $disabled; ?>>
               <label class="form-label small text-muted mb-1"><?php echo htmlspecialchars($pair['labelTitle']); ?></label>
               <input type="text" class="form-control" name="<?php echo cg_input_name('home', $pair['label']); ?>"
                      value="<?php echo htmlspecialchars($labelVal); ?>" placeholder="<?php echo htmlspecialchars($pair['labelDefault']); ?>"<?php echo $disabled; ?>>
            </div>
         </div>
         <?php endforeach; ?>
      </div>

      <hr class="my-4">
      <h2 class="h5 mb-3" id="hero">Homepage Hero Slider</h2>
      <p class="text-muted small mb-3">The homepage hero rotates through 5 full-screen slides. Edit each one's eyebrow label, headline, subtitle and background photo below — changes appear on the live site immediately after saving.</p>

      <?php foreach ($heroSlideDefaults as $n => $defaults):
         $disabled = $canEdit ? '' : ' disabled';
         $eyebrowVal = $currentByPage['home']["hero_slide_{$n}_eyebrow"] ?? '';
         $imageVal   = $currentByPage['home']["hero_slide_{$n}_image"] ?? '';
         $currentImage = $imageVal !== '' ? $imageVal : $defaults['image'];
         if ($n === 1) {
            // Slide 1's title/sub reuse the original hero_title/hero_sub keys (predates the multi-slide feature).
            $titleVal = $currentByPage['home']['hero_title'] ?? '';
            $subVal   = $currentByPage['home']['hero_sub'] ?? '';
            $titleName = cg_input_name('home', 'hero_title');
            $subName   = cg_input_name('home', 'hero_sub');
         } else {
            $titleVal = $currentByPage['home']["hero_slide_{$n}_title"] ?? '';
            $subVal   = $currentByPage['home']["hero_slide_{$n}_sub"] ?? '';
            $titleName = cg_input_name('home', "hero_slide_{$n}_title");
            $subName   = cg_input_name('home', "hero_slide_{$n}_sub");
         }
      ?>
      <div class="border rounded p-3 mb-3">
         <div class="d-flex align-items-center justify-content-between mb-2">
            <strong>Slide <?php echo $n; ?></strong>
         </div>
         <div class="row g-3">
            <div class="col-md-4">
               <img src="../<?php echo htmlspecialchars($currentImage); ?>" alt="Slide <?php echo $n; ?> preview" class="img-fluid rounded mb-2" style="aspect-ratio:16/10;object-fit:cover;width:100%;">
               <label class="form-label small text-muted mb-1">Replace Photo</label>
               <input type="file" class="form-control form-control-sm" name="<?php echo cg_input_name('home', "hero_slide_{$n}_image"); ?>" accept="image/jpeg,image/png,image/webp"<?php echo $disabled; ?>>
            </div>
            <div class="col-md-8">
               <label class="form-label small text-muted mb-1">Eyebrow Label</label>
               <input type="text" class="form-control mb-2" name="<?php echo cg_input_name('home', "hero_slide_{$n}_eyebrow"); ?>" value="<?php echo htmlspecialchars($eyebrowVal); ?>" placeholder="<?php echo htmlspecialchars($defaults['eyebrow']); ?>"<?php echo $disabled; ?>>
               <label class="form-label small text-muted mb-1">Headline</label>
               <input type="text" class="form-control mb-2" name="<?php echo $titleName; ?>" value="<?php echo htmlspecialchars($titleVal); ?>" placeholder="<?php echo htmlspecialchars($defaults['title']); ?>"<?php echo $disabled; ?>>
               <label class="form-label small text-muted mb-1">Subtitle</label>
               <input type="text" class="form-control" name="<?php echo $subName; ?>" value="<?php echo htmlspecialchars($subVal); ?>" placeholder="<?php echo htmlspecialchars($defaults['sub']); ?>"<?php echo $disabled; ?>>
            </div>
         </div>
      </div>
      <?php endforeach; ?>

      <hr class="my-4">
      <h2 class="h5 mb-3">About Us — Mission / Vision</h2>
      <?php foreach ($textFields['about'] as $f):
         $val = $currentByPage['about'][$f['key']] ?? '';
         $disabled = $canEdit ? '' : ' disabled';
      ?>
      <div class="mb-3">
         <label class="form-label" for="about_<?php echo htmlspecialchars($f['key']); ?>"><?php echo htmlspecialchars($f['label']); ?></label>
         <textarea class="form-control" id="about_<?php echo htmlspecialchars($f['key']); ?>" rows="3"
                   name="<?php echo cg_input_name('about', $f['key']); ?>"
                   placeholder="<?php echo htmlspecialchars($f['placeholder']); ?>"<?php echo $disabled; ?>><?php echo htmlspecialchars($val); ?></textarea>
      </div>
      <?php endforeach; ?>

      <?php if ($canEdit): ?>
         <button type="submit" class="cg-btn cg-btn-primary">Save Changes</button>
         <span class="ms-2 text-muted small">Leave a field blank to revert it to its default text.</span>
      <?php endif; ?>
   </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
