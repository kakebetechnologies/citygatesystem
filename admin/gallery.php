<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'gallery';
$pageTitle = 'Gallery & Videos';
$pageSub = 'Manage photos and videos shown on the public website';
require_once __DIR__ . '/includes/header.php'; // gives $pdo, enforces RBAC, blocks Finance

$canFull = cg_can('gallery', 'full');
$categories = ['Poultry', 'Goats', 'Dairy', 'Crops', 'Other'];

/** Ensures an uploads subdirectory exists and is hardened against PHP execution. */
function cg_ensure_upload_dir(string $dir): void {
   if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
   }
   $htaccess = $dir . '/.htaccess';
   if (!file_exists($htaccess)) {
      file_put_contents($htaccess, "php_flag engine off\n<Files \"*.php\">\nDeny from all\n</Files>\n");
   }
}

/** Validates + saves an uploaded image into uploads/gallery/, returns relative path or null. */
function cg_save_gallery_photo(array $file): ?string {
   if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || (int) $file['size'] <= 0) {
      return null;
   }
   if ($file['size'] > 8 * 1024 * 1024) { // 8MB cap
      return null;
   }
   $info = @getimagesize($file['tmp_name']);
   if ($info === false) {
      return null;
   }
   $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
   $mime = $info['mime'] ?? '';
   if (!isset($allowedMime[$mime])) {
      return null;
   }
   $ext = $allowedMime[$mime];
   $destDir = __DIR__ . '/../uploads/gallery';
   cg_ensure_upload_dir($destDir);
   $filename = bin2hex(random_bytes(8)) . '.' . $ext;
   $dest = $destDir . '/' . $filename;
   if (!move_uploaded_file($file['tmp_name'], $dest)) {
      return null;
   }
   return 'uploads/gallery/' . $filename;
}

/** Validates + saves an uploaded video into uploads/videos/, returns relative path or null. */
function cg_save_gallery_video(array $file): ?string {
   if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || (int) $file['size'] <= 0) {
      return null;
   }
   if ($file['size'] > 50 * 1024 * 1024) { // 50MB cap
      return null;
   }
   $allowedMime = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
   $finfo = finfo_open(FILEINFO_MIME_TYPE);
   $mime = finfo_file($finfo, $file['tmp_name']);
   finfo_close($finfo);
   if (!isset($allowedMime[$mime])) {
      return null;
   }
   $ext = $allowedMime[$mime];
   $destDir = __DIR__ . '/../uploads/videos';
   cg_ensure_upload_dir($destDir);
   $filename = bin2hex(random_bytes(8)) . '.' . $ext;
   $dest = $destDir . '/' . $filename;
   if (!move_uploaded_file($file['tmp_name'], $dest)) {
      return null;
   }
   return 'uploads/videos/' . $filename;
}

/** Extracts a YouTube video ID from common URL formats, or null if not recognized. */
function cg_youtube_id(string $url): ?string {
   if (preg_match('~(?:youtube\.com/watch\?v=|youtube\.com/embed/|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
      return $m[1];
   }
   return null;
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   cg_verify_csrf();
   if (!$canFull) {
      http_response_code(403);
      die('You do not have permission to manage the gallery.');
   }

   $action = $_POST['action'] ?? '';

   if ($action === 'delete') {
      $deleteId = (int) ($_POST['id'] ?? 0);
      if ($deleteId > 0) {
         $stmt = $pdo->prepare('SELECT * FROM gallery_media WHERE id = ?');
         $stmt->execute([$deleteId]);
         $row = $stmt->fetch();
         if ($row) {
            if (!empty($row['file_path'])) {
               $abs = __DIR__ . '/../' . $row['file_path'];
               // Only unlink files inside our own uploads/ tree — never touch the pre-seeded
               // images/gallary/* assets, which are shared site content, not uploads.
               $realAbs = realpath($abs);
               $realUploadsRoot = realpath(__DIR__ . '/../uploads');
               if ($realAbs !== false && $realUploadsRoot !== false && strpos($realAbs, $realUploadsRoot) === 0 && is_file($realAbs)) {
                  @unlink($realAbs);
               }
            }
            $del = $pdo->prepare('DELETE FROM gallery_media WHERE id = ?');
            $del->execute([$deleteId]);
         }
      }
      header('Location: gallery.php');
      exit;
   }

   if ($action === 'add_photo') {
      $caption = trim($_POST['caption'] ?? '');
      $category = trim($_POST['category'] ?? '');
      if (empty($_FILES['photo']['name'])) {
         $errors[] = 'Please choose a photo to upload.';
      } else {
         $saved = cg_save_gallery_photo($_FILES['photo']);
         if (!$saved) {
            $errors[] = 'Photo upload failed — please use a JPG, PNG or WEBP file under 8MB.';
         } else {
            $stmt = $pdo->prepare('INSERT INTO gallery_media (type, file_path, caption, category, uploaded_by) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute(['photo', $saved, $caption ?: null, $category ?: null, $user['id']]);
            header('Location: gallery.php?added=photo');
            exit;
         }
      }
   }

   if ($action === 'add_video') {
      $caption = trim($_POST['caption'] ?? '');
      $category = trim($_POST['category'] ?? '');
      $embedUrl = trim($_POST['embed_url'] ?? '');
      $hasFile = !empty($_FILES['video']['name']);

      $filePath = null;
      if ($hasFile) {
         $filePath = cg_save_gallery_video($_FILES['video']);
         if (!$filePath) {
            $errors[] = 'Video upload failed — please use an MP4 or WEBM file under 50MB.';
         }
      } elseif ($embedUrl !== '') {
         if (!filter_var($embedUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Please enter a valid video URL.';
         }
      } else {
         $errors[] = 'Please either upload a video file or paste a video URL.';
      }

      if (!$errors) {
         // Uploaded file wins if both were somehow provided.
         $finalFilePath = $filePath;
         $finalEmbedUrl = $filePath ? null : $embedUrl;
         $stmt = $pdo->prepare('INSERT INTO gallery_media (type, file_path, embed_url, caption, category, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)');
         $stmt->execute(['video', $finalFilePath, $finalEmbedUrl, $caption ?: null, $category ?: null, $user['id']]);
         header('Location: gallery.php?added=video');
         exit;
      }
   }
}

if (isset($_GET['added']) && !$errors) {
   $success = $_GET['added'] === 'photo' ? 'Photo added to the gallery.' : 'Video added to the gallery.';
}

$items = $pdo->query('SELECT gm.*, u.name AS uploader_name FROM gallery_media gm LEFT JOIN users u ON u.id = gm.uploaded_by ORDER BY gm.sort_order ASC, gm.created_at DESC')->fetchAll();
$photoCount = 0;
$videoCount = 0;
foreach ($items as $it) {
   if ($it['type'] === 'video') { $videoCount++; } else { $photoCount++; }
}
?>
<?php if ($errors): ?>
   <div class="alert alert-danger">
      <ul class="mb-0">
         <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
      </ul>
   </div>
<?php endif; ?>
<?php if ($success): ?>
   <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($canFull): ?>
<div class="row g-3 mb-3">
   <div class="col-md-6">
      <div class="cg-panel">
         <div class="cg-panel-head">
            <h2><i class="fa fa-picture-o"></i> Add Photo</h2>
         </div>
         <form method="post" enctype="multipart/form-data">
            <?php echo cg_csrf_field(); ?>
            <input type="hidden" name="action" value="add_photo">
            <div class="mb-2">
               <label class="form-label">Photo file *</label>
               <input type="file" name="photo" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-2">
               <label class="form-label">Caption</label>
               <input type="text" name="caption" class="form-control" placeholder="e.g. Layer hens at City Gate">
            </div>
            <div class="mb-2">
               <label class="form-label">Category</label>
               <select name="category" class="form-select">
                  <option value="">— Select —</option>
                  <?php foreach ($categories as $c): ?>
                     <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            <button type="submit" class="cg-btn cg-btn-primary cg-btn-sm"><i class="fa fa-plus"></i> Add Photo</button>
         </form>
      </div>
   </div>

   <div class="col-md-6">
      <div class="cg-panel">
         <div class="cg-panel-head">
            <h2><i class="fa fa-video-camera"></i> Add Video</h2>
         </div>
         <form method="post" enctype="multipart/form-data">
            <?php echo cg_csrf_field(); ?>
            <input type="hidden" name="action" value="add_video">
            <div class="mb-2">
               <label class="form-label">Upload video file</label>
               <input type="file" name="video" class="form-control" accept="video/mp4,video/webm">
               <small class="text-muted">MP4 or WEBM, up to 50MB.</small>
            </div>
            <div class="mb-2 text-center text-muted">— or —</div>
            <div class="mb-2">
               <label class="form-label">Paste a video URL</label>
               <input type="text" name="embed_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
               <small class="text-muted">YouTube, Facebook or Vimeo link. Used only if no file is uploaded above.</small>
            </div>
            <div class="mb-2">
               <label class="form-label">Caption</label>
               <input type="text" name="caption" class="form-control" placeholder="e.g. A day at City Gate Mixed Farm">
            </div>
            <div class="mb-2">
               <label class="form-label">Category</label>
               <select name="category" class="form-select">
                  <option value="">— Select —</option>
                  <?php foreach ($categories as $c): ?>
                     <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
            <button type="submit" class="cg-btn cg-btn-primary cg-btn-sm"><i class="fa fa-plus"></i> Add Video</button>
         </form>
      </div>
   </div>
</div>
<?php endif; ?>

<div class="cg-panel">
   <div class="cg-panel-head">
      <h2>Gallery Media (<?php echo count($items); ?> — <?php echo $photoCount; ?> photos, <?php echo $videoCount; ?> videos)</h2>
   </div>

   <?php if (!$items): ?>
      <p class="text-muted">No gallery media yet.</p>
   <?php else: ?>
   <div class="row g-3">
      <?php foreach ($items as $it): ?>
         <?php
         $isVideo = $it['type'] === 'video';
         $thumbHtml = '';
         if (!$isVideo && !empty($it['file_path'])) {
            $thumbHtml = '<img src="../' . htmlspecialchars($it['file_path']) . '" alt="' . htmlspecialchars($it['caption'] ?: 'Gallery photo') . '" style="width:100%;height:150px;object-fit:cover;border-radius:8px 8px 0 0;">';
         } elseif ($isVideo && !empty($it['file_path'])) {
            $thumbHtml = '<div style="width:100%;height:150px;border-radius:8px 8px 0 0;background:#222;display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;"><i class="fa fa-file-video-o"></i></div>';
         } elseif ($isVideo && !empty($it['embed_url'])) {
            $ytId = cg_youtube_id($it['embed_url']);
            if ($ytId) {
               $thumbHtml = '<img src="https://img.youtube.com/vi/' . htmlspecialchars($ytId) . '/mqdefault.jpg" alt="' . htmlspecialchars($it['caption'] ?: 'Video') . '" style="width:100%;height:150px;object-fit:cover;border-radius:8px 8px 0 0;">';
            } else {
               $thumbHtml = '<div style="width:100%;height:150px;border-radius:8px 8px 0 0;background:#222;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;"><i class="fa fa-film me-2"></i> Video</div>';
            }
         }
         ?>
         <div class="col-lg-3 col-md-4 col-sm-6">
            <div style="border:1px solid #e6e6e6;border-radius:8px;overflow:hidden;height:100%;display:flex;flex-direction:column;">
               <?php echo $thumbHtml; ?>
               <div style="padding:10px;flex:1;display:flex;flex-direction:column;gap:6px;">
                  <div>
                     <span class="cg-badge cg-badge-<?php echo $isVideo ? 'scheduled' : 'published'; ?>"><?php echo $isVideo ? 'Video' : 'Photo'; ?></span>
                     <?php if (!empty($it['category'])): ?>
                        <span class="cg-badge cg-badge-draft"><?php echo htmlspecialchars($it['category']); ?></span>
                     <?php endif; ?>
                  </div>
                  <div style="font-size:13px;color:#333;flex:1;"><?php echo htmlspecialchars($it['caption'] ?: '—'); ?></div>
                  <?php if ($canFull): ?>
                  <form method="post" onsubmit="return confirm('Delete this <?php echo $isVideo ? 'video' : 'photo'; ?> permanently?');">
                     <?php echo cg_csrf_field(); ?>
                     <input type="hidden" name="action" value="delete">
                     <input type="hidden" name="id" value="<?php echo (int) $it['id']; ?>">
                     <button type="submit" class="cg-btn cg-btn-danger cg-btn-sm" style="width:100%;"><i class="fa fa-trash"></i> Delete</button>
                  </form>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      <?php endforeach; ?>
   </div>
   <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
