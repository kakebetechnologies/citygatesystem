<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'cms';
$pageTitle = 'Website Content';
$pageSub = 'Edit the text shown on public-facing pages';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/cms.php';

$canEdit = cg_can('cms', 'full');
$message = '';

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

// Simple text/textarea fields, keyed by page_key
$textFields = [
   'home' => [
      ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text', 'placeholder' => 'Modern Poultry Farming'],
      ['key' => 'hero_sub', 'label' => 'Hero Subtitle', 'type' => 'text', 'placeholder' => 'Clean housing. Organized feeding. Healthy birds.'],
   ],
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

   header('Location: cms.php?saved=1');
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

   <form method="post" action="cms.php">
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
      <h2 class="h5 mb-3">Homepage Hero</h2>
      <?php foreach ($textFields['home'] as $f):
         $val = $currentByPage['home'][$f['key']] ?? '';
         $disabled = $canEdit ? '' : ' disabled';
      ?>
      <div class="mb-3">
         <label class="form-label" for="home_<?php echo htmlspecialchars($f['key']); ?>"><?php echo htmlspecialchars($f['label']); ?></label>
         <input type="text" class="form-control" id="home_<?php echo htmlspecialchars($f['key']); ?>"
                name="<?php echo cg_input_name('home', $f['key']); ?>" value="<?php echo htmlspecialchars($val); ?>"
                placeholder="<?php echo htmlspecialchars($f['placeholder']); ?>"<?php echo $disabled; ?>>
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
