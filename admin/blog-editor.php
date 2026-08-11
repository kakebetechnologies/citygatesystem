<?php
ob_start(); // buffer output so header(Location:...) redirects never fail with "headers already sent"
$pageModule = 'blog';
$pageAction = 'full'; // create/edit is a write-only page — cg_require_module() blocks anyone without full access
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pageTitle = $editId ? 'Edit Post' : 'New Post';
$pageSub = $editId ? 'Update an existing blog post' : 'Write and publish a new blog post';
require_once __DIR__ . '/includes/header.php'; // gives $pdo, enforces RBAC

/** Validates + saves an uploaded image into uploads/blog/, returns the relative path or null. */
function cg_save_uploaded_image(array $file): ?string {
   if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || (int) $file['size'] <= 0) {
      return null;
   }
   $info = @getimagesize($file['tmp_name']);
   if ($info === false) {
      return null;
   }
   $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
   $mime = $info['mime'] ?? '';
   if (!isset($allowed[$mime])) {
      return null;
   }
   $ext = $allowed[$mime];
   $filename = bin2hex(random_bytes(16)) . '.' . $ext;
   $destDir = __DIR__ . '/../uploads/blog/';
   if (!is_dir($destDir)) {
      mkdir($destDir, 0755, true);
   }
   $dest = $destDir . $filename;
   if (!move_uploaded_file($file['tmp_name'], $dest)) {
      return null;
   }
   return 'uploads/blog/' . $filename;
}

/** Generates a unique slug from a title (lowercase, hyphenated, deduped with -2, -3, ...). */
function cg_generate_slug(PDO $pdo, string $title): string {
   $base = strtolower(trim($title));
   $base = preg_replace('/[^a-z0-9]+/', '-', $base);
   $base = trim($base, '-');
   if ($base === '') {
      $base = 'post';
   }
   $slug = $base;
   $i = 2;
   $stmt = $pdo->prepare('SELECT COUNT(*) FROM blog_posts WHERE slug = ?');
   while (true) {
      $stmt->execute([$slug]);
      if ((int) $stmt->fetchColumn() === 0) {
         break;
      }
      $slug = $base . '-' . $i;
      $i++;
   }
   return $slug;
}

$errors = [];
$post = [
   'id' => 0, 'title' => '', 'category' => '', 'tags' => '', 'excerpt' => '', 'content' => '',
   'featured_image' => '', 'status' => 'draft', 'publish_date' => '', 'meta_title' => '', 'meta_description' => '',
];
$galleryImages = [];

if ($editId) {
   $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
   $stmt->execute([$editId]);
   $existing = $stmt->fetch();
   if (!$existing) {
      http_response_code(404);
      require_once __DIR__ . '/includes/footer.php';
      exit;
   }
   $post = $existing;
   $imgStmt = $pdo->prepare('SELECT * FROM blog_post_images WHERE post_id = ? ORDER BY sort_order, id');
   $imgStmt->execute([$editId]);
   $galleryImages = $imgStmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   cg_verify_csrf();

   $post['title'] = trim($_POST['title'] ?? '');
   $post['category'] = trim($_POST['category'] ?? '');
   $post['tags'] = trim($_POST['tags'] ?? '');
   $post['excerpt'] = trim($_POST['excerpt'] ?? '');
   $post['content'] = $_POST['content'] ?? '';
   $post['status'] = in_array($_POST['status'] ?? '', ['draft', 'published', 'scheduled'], true) ? $_POST['status'] : 'draft';
   $post['publish_date'] = trim($_POST['publish_date'] ?? '') ?: null;
   $post['meta_title'] = trim($_POST['meta_title'] ?? '');
   $post['meta_description'] = trim($_POST['meta_description'] ?? '');

   if ($post['title'] === '') {
      $errors[] = 'Title is required.';
   }

   if (!$errors) {
      // Featured image: replace only if a new file was uploaded
      $featuredImage = $post['featured_image'] ?: null;
      if (!empty($_FILES['featuredImage']['name'])) {
         $saved = cg_save_uploaded_image($_FILES['featuredImage']);
         if ($saved) {
            $featuredImage = $saved;
         } elseif ($_FILES['featuredImage']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Featured image upload failed — please use a JPG, PNG, GIF or WEBP file.';
         }
      }

      if (!$errors) {
         $metaTitle = $post['meta_title'] !== '' ? $post['meta_title'] : null;
         $metaDescription = $post['meta_description'] !== '' ? $post['meta_description'] : null;

         if ($editId) {
            $stmt = $pdo->prepare("
               UPDATE blog_posts SET title=?, category=?, tags=?, excerpt=?, content=?, featured_image=?,
                  status=?, publish_date=?, meta_title=?, meta_description=?
               WHERE id=?
            ");
            $stmt->execute([
               $post['title'], $post['category'] ?: null, $post['tags'] ?: null, $post['excerpt'] ?: null,
               $post['content'], $featuredImage, $post['status'], $post['publish_date'],
               $metaTitle, $metaDescription, $editId,
            ]);
            $postId = $editId;
         } else {
            $slug = cg_generate_slug($pdo, $post['title']);
            $stmt = $pdo->prepare("
               INSERT INTO blog_posts (title, slug, category, tags, excerpt, content, featured_image, author_id, status, publish_date, meta_title, meta_description)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
               $post['title'], $slug, $post['category'] ?: null, $post['tags'] ?: null, $post['excerpt'] ?: null,
               $post['content'], $featuredImage, $user['id'], $post['status'], $post['publish_date'],
               $metaTitle, $metaDescription,
            ]);
            $postId = (int) $pdo->lastInsertId();
         }

         // Existing gallery images: update captions, delete removed
         $removeIds = array_map('intval', $_POST['removeImage'] ?? []);
         $captions = $_POST['existingCaption'] ?? [];
         if ($removeIds) {
            $in = implode(',', array_fill(0, count($removeIds), '?'));
            $del = $pdo->prepare("DELETE FROM blog_post_images WHERE post_id = ? AND id IN ($in)");
            $del->execute(array_merge([$postId], $removeIds));
         }
         foreach ($captions as $imgId => $caption) {
            $imgId = (int) $imgId;
            if (in_array($imgId, $removeIds, true)) {
               continue;
            }
            $upd = $pdo->prepare('UPDATE blog_post_images SET caption = ? WHERE id = ? AND post_id = ?');
            $upd->execute([trim($caption) ?: null, $imgId, $postId]);
         }

         // New gallery uploads (shared caption applied to the whole batch)
         $newCaption = trim($_POST['newGalleryCaption'] ?? '');
         if (!empty($_FILES['galleryImages']['name'][0])) {
            $count = count($_FILES['galleryImages']['name']);
            for ($i = 0; $i < $count; $i++) {
               if ($_FILES['galleryImages']['error'][$i] !== UPLOAD_ERR_OK) {
                  continue;
               }
               $file = [
                  'tmp_name' => $_FILES['galleryImages']['tmp_name'][$i],
                  'error' => $_FILES['galleryImages']['error'][$i],
                  'size' => $_FILES['galleryImages']['size'][$i],
               ];
               $saved = cg_save_uploaded_image($file);
               if ($saved) {
                  $ins = $pdo->prepare('INSERT INTO blog_post_images (post_id, image_path, caption, sort_order) VALUES (?, ?, ?, ?)');
                  $ins->execute([$postId, $saved, $newCaption ?: null, $i]);
               }
            }
         }

         header('Location: blog-manager.php');
         exit;
      }
   }
}
?>
<div class="cg-panel">
   <div class="cg-panel-head">
      <h2><?php echo $editId ? 'Edit Post' : 'New Post'; ?></h2>
      <a href="blog-manager.php" class="cg-btn cg-btn-outline cg-btn-sm"><i class="fa fa-arrow-left"></i> Back to Blog Manager</a>
   </div>

   <?php if ($errors): ?>
      <div class="alert alert-danger">
         <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
         </ul>
      </div>
   <?php endif; ?>

   <form method="post" enctype="multipart/form-data">
      <?php echo cg_csrf_field(); ?>
      <div class="row g-3">
         <div class="col-md-8">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
         </div>
         <div class="col-md-4">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($post['category'] ?? ''); ?>" placeholder="e.g. Poultry">
         </div>

         <div class="col-md-8">
            <label class="form-label">Tags (comma-separated)</label>
            <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($post['tags'] ?? ''); ?>" placeholder="e.g. poultry, growth, broiler">
         </div>
         <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
               <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
               <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
               <option value="scheduled" <?php echo $post['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
            </select>
         </div>

         <div class="col-md-4">
            <label class="form-label">Publish Date</label>
            <input type="date" name="publish_date" class="form-control" value="<?php echo htmlspecialchars($post['publish_date'] ?? ''); ?>">
         </div>
         <div class="col-md-8">
            <label class="form-label">Featured Image</label>
            <input type="file" name="featuredImage" class="form-control" accept="image/*">
            <?php if (!empty($post['featured_image'])): ?>
               <div class="mt-2"><img src="../<?php echo htmlspecialchars($post['featured_image']); ?>" alt="Featured image" style="max-height:90px;border-radius:8px;"></div>
               <small class="text-muted">Leave blank to keep the current image.</small>
            <?php endif; ?>
         </div>

         <div class="col-12">
            <label class="form-label">Excerpt</label>
            <textarea name="excerpt" class="form-control" rows="2" placeholder="Short summary shown on the blog list"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
         </div>

         <div class="col-12">
            <label class="form-label">Content</label>
            <textarea id="postContent" name="content" rows="12"><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
         </div>

         <div class="col-md-6">
            <label class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($post['meta_title'] ?? ''); ?>" placeholder="defaults to post title">
         </div>
         <div class="col-md-6">
            <label class="form-label">Meta Description</label>
            <textarea name="meta_description" class="form-control" rows="2" placeholder="defaults to excerpt"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
         </div>

         <div class="col-12"><hr></div>

         <div class="col-12">
            <label class="form-label">Image Gallery</label>

            <?php if ($galleryImages): ?>
            <div class="row g-3 mb-3">
               <?php foreach ($galleryImages as $img): ?>
               <div class="col-md-3 col-sm-4 col-6">
                  <div style="border:1px solid #e6e6e6;border-radius:10px;padding:8px;">
                     <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" alt="Gallery image" class="img-fluid" style="border-radius:6px;margin-bottom:8px;">
                     <input type="text" name="existingCaption[<?php echo (int) $img['id']; ?>]" class="form-control form-control-sm mb-2" value="<?php echo htmlspecialchars($img['caption'] ?? ''); ?>" placeholder="Caption">
                     <label style="font-size:12px;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" name="removeImage[]" value="<?php echo (int) $img['id']; ?>"> Remove this image
                     </label>
                  </div>
               </div>
               <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <label class="form-label">Add Gallery Images</label>
            <input type="file" name="galleryImages[]" class="form-control mb-2" accept="image/*" multiple>
            <input type="text" name="newGalleryCaption" class="form-control" placeholder="Caption for newly uploaded image(s) — optional, applied to all files selected above">
         </div>
      </div>

      <div class="mt-4">
         <button type="submit" class="cg-btn cg-btn-primary"><i class="fa fa-save"></i> Save Post</button>
         <a href="blog-manager.php" class="cg-btn cg-btn-outline">Cancel</a>
      </div>
   </form>
</div>

<?php
$extraScripts = ['https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js'];
require_once __DIR__ . '/includes/footer.php';
?>
<script>
   tinymce.init({
      selector: '#postContent',
      height: 400,
      menubar: false,
      plugins: 'lists link image',
      toolbar: 'undo redo | bold italic | bullist numlist | link'
   });
</script>
