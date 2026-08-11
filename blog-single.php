<?php
/**
 * Blog single post — magazine-style layout matching blog.php's redesign.
 * - Reads ?slug=, 404s (friendly message) if not found / not published.
 * - Sets full SEO/OG/article meta + JSON-LD Article schema before including
 *   includes/header.php (which renders the <head> tags from these vars).
 * - Renders a full-bleed lead-photo hero, article content, an optional
 *   captioned multi-image gallery from blog_post_images, and a real
 *   comments section (blog_comments) with a self-POST + PRG submit form.
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php'; // session + CSRF helpers (public comment form)

$slug = trim($_GET['slug'] ?? '');

$post = null;
if ($slug !== '') {
   $stmt = $pdo->prepare("
      SELECT bp.*, u.name AS author_name
      FROM blog_posts bp
      LEFT JOIN users u ON u.id = bp.author_id
      WHERE bp.slug = ? AND bp.status = 'published'
      LIMIT 1
   ");
   $stmt->execute([$slug]);
   $post = $stmt->fetch();
}

// ---- Handle new comment submission (PRG) ----
$commentErrors = [];
if ($post && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
   cg_verify_csrf();

   $cName    = trim($_POST['name'] ?? '');
   $cEmail   = trim($_POST['email'] ?? '');
   $cComment = trim($_POST['comment'] ?? '');

   if ($cName === '') $commentErrors[] = 'Please enter your name.';
   if ($cComment === '') $commentErrors[] = 'Please write a comment.';
   if ($cEmail !== '' && !filter_var($cEmail, FILTER_VALIDATE_EMAIL)) $commentErrors[] = 'Please enter a valid email address.';

   if (!$commentErrors) {
      $ins = $pdo->prepare('INSERT INTO blog_comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)');
      $ins->execute([$post['id'], $cName, $cEmail !== '' ? $cEmail : null, $cComment]);
      header('Location: blog-single.php?slug=' . urlencode($slug) . '#comments');
      exit;
   }
}

if (!$post) {
   $pageTitle = 'Post Not Found | City Gate Mixed Farm';
   $activeNav = 'blog';
   require __DIR__ . '/includes/header.php';
   ?>
   <section class="cg-page-banner">
      <div class="container">
         <p class="section-eyebrow">City Gate Mixed Farm</p>
         <h1>Post Not Found</h1>
         <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Blog</li>
         </ul>
      </div>
   </section>
   <section class="section-pad" style="text-align:center;">
      <div class="container">
         <p style="font-size:17px;color:#666;margin-bottom:24px;">Sorry, we couldn't find that blog post. It may have been unpublished or the link may be incorrect.</p>
         <a href="blog.php" class="modern-btn" style="display:inline-block;background:var(--cg-green,#2f5d3a);color:#fff;padding:14px 32px;border-radius:4px;text-decoration:none;font-weight:700;">&larr; Back to Blog</a>
      </div>
   </section>
   <?php
   require __DIR__ . '/includes/footer.php';
   exit;
}

// ---- Gallery images ----
$imgStmt = $pdo->prepare('SELECT * FROM blog_post_images WHERE post_id = ? ORDER BY sort_order, id');
$imgStmt->execute([$post['id']]);
$galleryImages = $imgStmt->fetchAll();

// ---- Comments ----
$cmStmt = $pdo->prepare('SELECT * FROM blog_comments WHERE post_id = ? ORDER BY created_at ASC');
$cmStmt->execute([$post['id']]);
$comments = $cmStmt->fetchAll();

// ---- Recent posts (sidebar) ----
$recentStmt = $pdo->prepare("
   SELECT title, slug, featured_image, publish_date
   FROM blog_posts
   WHERE status = 'published' AND id != ?
   ORDER BY publish_date DESC
   LIMIT 4
");
$recentStmt->execute([$post['id']]);
$recentPosts = $recentStmt->fetchAll();

function cg_reading_time(string $content): int {
   return max(1, (int) ceil(str_word_count(strip_tags($content)) / 200));
}
function cg_initials(string $name): string {
   $parts = preg_split('/\s+/', trim($name));
   $initials = '';
   foreach (array_slice($parts, 0, 2) as $p) { if ($p !== '') $initials .= mb_strtoupper(mb_substr($p, 0, 1)); }
   return $initials !== '' ? $initials : '?';
}

// ---- SEO / Open Graph / article meta (must be set BEFORE including header.php) ----
$excerptFallback = $post['excerpt'] !== null && $post['excerpt'] !== '' ? $post['excerpt'] : trim(strip_tags((string) $post['content']));
if (mb_strlen($excerptFallback) > 300) {
   $excerptFallback = rtrim(mb_substr($excerptFallback, 0, 297)) . '...';
}

$pageTitle       = $post['title'] . ' | City Gate Mixed Farm';
$pageDescription = ($post['meta_description'] ?: $excerptFallback);
$ogTitle         = $post['meta_title'] ?: $post['title'];
$ogDescription   = $pageDescription;
$ogType          = 'article';

$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$siteBase = $scheme . '://' . $_SERVER['HTTP_HOST'] . $basePath;
$imgRel   = ($post['featured_image'] !== null && $post['featured_image'] !== '') ? $post['featured_image'] : 'images/gallary/rooster-farm.jpg';
$ogImage  = $siteBase . '/' . ltrim($imgRel, '/');
$canonicalUrl = $siteBase . '/blog-single.php?slug=' . urlencode($post['slug']);

$publishedIso = $post['publish_date'] ? date('c', strtotime($post['publish_date'])) : null;
$modifiedIso  = $post['updated_at'] ? date('c', strtotime($post['updated_at'])) : $publishedIso;
$tagListForMeta = [];
if (!empty($post['tags'])) {
   foreach (explode(',', $post['tags']) as $t) { $t = trim($t); if ($t !== '') $tagListForMeta[] = $t; }
}
$articleMeta = [
   'published_time' => $publishedIso,
   'modified_time'  => $modifiedIso,
   'author'         => $post['author_name'] ?? 'City Gate Mixed Farm',
   'section'        => $post['category'] ?? null,
   'tags'           => $tagListForMeta,
];

$activeNav = 'blog';
require __DIR__ . '/includes/header.php';

$tagList = $tagListForMeta;
$dateStr = $post['publish_date'] ? date('d M Y', strtotime($post['publish_date'])) : '';
$readMin = cg_reading_time((string) $post['content']);
$authorName = $post['author_name'] ?? 'City Gate Mixed Farm';

// ---- JSON-LD Article structured data ----
$jsonLd = [
   '@context' => 'https://schema.org',
   '@type'    => 'Article',
   'headline' => $post['title'],
   'description' => $pageDescription,
   'image'    => [$ogImage],
   'author'   => ['@type' => 'Person', 'name' => $authorName],
   'publisher' => [
      '@type' => 'Organization',
      'name'  => 'City Gate Mixed Farm',
      'logo'  => ['@type' => 'ImageObject', 'url' => $siteBase . '/images/logo/citygatelogo.png'],
   ],
   'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
];
if ($publishedIso) $jsonLd['datePublished'] = $publishedIso;
if ($modifiedIso) $jsonLd['dateModified'] = $modifiedIso;
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<script type="application/ld+json"><?php echo json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

<!-- LEAD PHOTO HERO -->
<section class="blog-post-hero">
   <div class="blog-post-hero__img" style="background-image:url('<?php echo htmlspecialchars($imgRel); ?>');"></div>
   <div class="blog-post-hero__overlay"></div>
   <div class="blog-post-hero__content">
      <div class="blog-post-hero__breadcrumb">
         <a href="index.php">Home</a> / <a href="blog.php">Blog</a> / <span><?php echo htmlspecialchars($post['title']); ?></span>
      </div>
      <?php if (!empty($post['category'])): ?><span class="blog-hero__pill"><?php echo htmlspecialchars($post['category']); ?></span><?php endif; ?>
      <h1 class="blog-post-hero__title"><?php echo htmlspecialchars($post['title']); ?></h1>
      <div class="blog-post-hero__meta">
         <span><i class="fa fa-user-o" aria-hidden="true"></i> <?php echo htmlspecialchars($authorName); ?></span>
         <?php if ($dateStr !== ''): ?><span><i class="fa fa-calendar-o" aria-hidden="true"></i> <?php echo htmlspecialchars($dateStr); ?></span><?php endif; ?>
         <span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $readMin; ?> min read</span>
         <span><i class="fa fa-comment-o" aria-hidden="true"></i> <?php echo count($comments); ?> comment<?php echo count($comments) === 1 ? '' : 's'; ?></span>
      </div>
   </div>
</section>

<section class="blog-post-section">
   <div class="container">
      <div class="row">
         <div class="col-lg-8">

            <div class="blog-post-card blog-post-content">
               <?php
                  // Post content is stored HTML (authored via TinyMCE) — echo raw, do NOT escape.
                  echo $post['content'];
               ?>
            </div>

            <?php if (!empty($galleryImages)): ?>
            <div class="blog-post-card blog-post-gallery">
               <h4 class="blog-post-gallery__title">Gallery</h4>
               <div class="blog-post-gallery__grid">
                  <?php foreach ($galleryImages as $img): ?>
                  <div class="blog-post-gallery__item">
                     <img loading="lazy" src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars($img['caption'] ?? $post['title']); ?>">
                     <?php if (!empty($img['caption'])): ?>
                        <div class="blog-post-gallery__caption"><?php echo htmlspecialchars($img['caption']); ?></div>
                     <?php endif; ?>
                  </div>
                  <?php endforeach; ?>
               </div>
            </div>
            <?php endif; ?>

            <div class="blog-post-tagshare">
               <?php if ($tagList): ?>
               <div class="blog-post-tags">
                  <span class="blog-post-tags__label">Tags:</span>
                  <?php foreach ($tagList as $t): ?>
                     <span class="blog-post-tag"><?php echo htmlspecialchars($t); ?></span>
                  <?php endforeach; ?>
               </div>
               <?php else: ?><div></div><?php endif; ?>
               <div class="blog-post-share">
                  <span class="blog-post-share__label">Share:</span>
                  <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($canonicalUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                  <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($canonicalUrl); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                  <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($post['title'] . ' ' . $canonicalUrl); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                  <a href="javascript:void(0);" id="blogCopyLink" data-url="<?php echo htmlspecialchars($canonicalUrl); ?>" aria-label="Copy link"><i class="fa fa-link" aria-hidden="true"></i></a>
               </div>
            </div>

            <div class="blog-post-card blog-comments" id="comments">
               <h3 class="blog-comments__heading">Comments (<?php echo count($comments); ?>)</h3>
               <?php if (empty($comments)): ?>
                  <p class="blog-comment-empty">Be the first to comment on this post.</p>
               <?php endif; ?>
               <?php foreach ($comments as $c): ?>
               <div class="blog-comment">
                  <div class="blog-comment__avatar"><?php echo htmlspecialchars(cg_initials($c['name'])); ?></div>
                  <div>
                     <span class="blog-comment__name"><?php echo htmlspecialchars($c['name']); ?></span>
                     <span class="blog-comment__date"><?php echo htmlspecialchars(date('d F Y', strtotime($c['created_at']))); ?></span>
                     <p class="blog-comment__text"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                  </div>
               </div>
               <?php endforeach; ?>
            </div>

            <div class="blog-post-card blog-comment-form">
               <h4 class="blog-side-card__title">Leave a Comment</h4>
               <p style="font-size:13px;color:#888;margin-top:-12px;margin-bottom:20px;">Your email address will not be published. Required fields are marked *</p>

               <?php if ($commentErrors): ?>
                  <div class="comment-errors" style="background:#fbe2e2;border:2px solid #c0392b;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#a32e2e;">
                     <ul style="margin:0;padding-left:1.1rem;">
                        <?php foreach ($commentErrors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
                     </ul>
                  </div>
               <?php endif; ?>

               <form method="post" action="blog-single.php?slug=<?php echo urlencode($slug); ?>#comments">
                  <?php echo cg_csrf_field(); ?>
                  <input type="hidden" name="comment_submit" value="1">
                  <div class="row">
                     <div class="col-md-6">
                        <label for="commentName">Your Name *</label>
                        <input type="text" id="commentName" name="name" placeholder="Jane Farmer" required>
                     </div>
                     <div class="col-md-6">
                        <label for="commentEmail">Email (optional)</label>
                        <input type="email" id="commentEmail" name="email" placeholder="you@example.com">
                     </div>
                     <div class="col-md-12">
                        <label for="commentText">Comment *</label>
                        <textarea id="commentText" name="comment" rows="5" placeholder="Write your comment..." required></textarea>
                     </div>
                  </div>
                  <button type="submit">Post Comment</button>
               </form>
            </div>

         </div>

         <div class="col-lg-4">
            <div class="blog-side-card">
               <div class="blog-side-author">
                  <div class="blog-side-author__avatar"><?php echo htmlspecialchars(cg_initials($authorName)); ?></div>
                  <div>
                     <div class="blog-side-author__name"><?php echo htmlspecialchars($authorName); ?></div>
                     <div class="blog-side-author__role">City Gate Mixed Farm</div>
                  </div>
               </div>
            </div>

            <div class="blog-side-card">
               <h4 class="blog-side-card__title">Search</h4>
               <form class="blog-side-search" action="blog.php" method="get">
                  <input type="text" name="q" placeholder="Search the blog...">
                  <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
               </form>
            </div>

            <?php if (!empty($recentPosts)): ?>
            <div class="blog-side-card">
               <h4 class="blog-side-card__title">Recent Posts</h4>
               <?php foreach ($recentPosts as $rp):
                  $rpImg = ($rp['featured_image'] !== null && $rp['featured_image'] !== '') ? $rp['featured_image'] : 'images/gallary/rooster-farm.jpg';
                  $rpDate = $rp['publish_date'] ? date('d M Y', strtotime($rp['publish_date'])) : '';
               ?>
               <a href="blog-single.php?slug=<?php echo urlencode($rp['slug']); ?>" class="blog-side-recent">
                  <div class="blog-side-recent__img"><img loading="lazy" src="<?php echo htmlspecialchars($rpImg); ?>" alt="<?php echo htmlspecialchars($rp['title']); ?>"></div>
                  <div>
                     <div class="blog-side-recent__title"><?php echo htmlspecialchars($rp['title']); ?></div>
                     <?php if ($rpDate !== ''): ?><div class="blog-side-recent__date"><?php echo htmlspecialchars($rpDate); ?></div><?php endif; ?>
                  </div>
               </a>
               <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($tagList): ?>
            <div class="blog-side-card">
               <h4 class="blog-side-card__title">Popular Tags</h4>
               <div class="blog-side-tags">
                  <?php foreach ($tagList as $t): ?><span class="blog-post-tag"><?php echo htmlspecialchars($t); ?></span><?php endforeach; ?>
               </div>
            </div>
            <?php endif; ?>

            <div class="blog-side-cta">
               <h4>Visit City Gate</h4>
               <p>See the farm in person — poultry, dairy, goats and crops, up close.</p>
               <a href="bookings.php">Book a Farm Visit</a>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
(function(){
   var copyBtn = document.getElementById('blogCopyLink');
   if (!copyBtn) return;
   copyBtn.addEventListener('click', function(){
      var url = copyBtn.getAttribute('data-url');
      if (navigator.clipboard) {
         navigator.clipboard.writeText(url).then(function(){
            copyBtn.classList.add('is-copied');
            setTimeout(function(){ copyBtn.classList.remove('is-copied'); }, 1600);
         });
      }
   });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
