<?php
/**
 * Blog single post. Converted from blog-single.html.
 * - Reads ?slug=, 404s (friendly message) if not found / not published.
 * - Sets full SEO/OG meta (title, description, image, canonical) before
 *   including includes/header.php, since header.php renders those tags
 *   from whichever of these vars are set.
 * - Renders featured image, author, date, category/tags, full HTML content
 *   (raw — it's TinyMCE-authored HTML, not escaped), an optional captioned
 *   multi-image gallery from blog_post_images, and a real comments section
 *   (blog_comments) with a self-POST + PRG submit form.
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
   <section style="padding:90px 0;text-align:center;">
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
   LIMIT 3
");
$recentStmt->execute([$post['id']]);
$recentPosts = $recentStmt->fetchAll();

// ---- SEO / Open Graph meta (must be set BEFORE including header.php) ----
$excerptFallback = $post['excerpt'] !== null && $post['excerpt'] !== '' ? $post['excerpt'] : trim(strip_tags((string) $post['content']));
if (mb_strlen($excerptFallback) > 300) {
   $excerptFallback = rtrim(mb_substr($excerptFallback, 0, 297)) . '...';
}

$pageTitle       = $post['title'] . ' | City Gate Mixed Farm';
$pageDescription = ($post['meta_description'] ?: $excerptFallback);
$ogTitle         = $post['meta_title'] ?: $post['title'];
$ogDescription   = $pageDescription;

$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$siteBase = $scheme . '://' . $_SERVER['HTTP_HOST'] . $basePath;
$imgRel   = ($post['featured_image'] !== null && $post['featured_image'] !== '') ? $post['featured_image'] : 'images/gallary/rooster-farm.jpg';
$ogImage  = $siteBase . '/' . ltrim($imgRel, '/');
$canonicalUrl = $siteBase . '/blog-single.php?slug=' . urlencode($post['slug']);

$activeNav = 'blog';
require __DIR__ . '/includes/header.php';

$tagList = [];
if (!empty($post['tags'])) {
   foreach (explode(',', $post['tags']) as $t) {
      $t = trim($t);
      if ($t !== '') $tagList[] = $t;
   }
}
$dateStr = $post['publish_date'] ? date('d M Y', strtotime($post['publish_date'])) : '';
$dayNum  = $post['publish_date'] ? date('d', strtotime($post['publish_date'])) : '';
$monShort = $post['publish_date'] ? date('M', strtotime($post['publish_date'])) : '';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .blog-gallery { margin: 30px 0; }
   .blog-gallery h4 { font-weight:700; color:#1a1a1a; margin-bottom:16px; }
   .blog-gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
   }
   @media (max-width: 767px) { .blog-gallery-grid { grid-template-columns: repeat(2, 1fr); } }
   @media (max-width: 480px) { .blog-gallery-grid { grid-template-columns: 1fr; } }
   .blog-gallery-item { border-radius: 10px; overflow: hidden; background:#f4f4f4; }
   .blog-gallery-item img { width:100%; height:180px; object-fit:cover; display:block; }
   .blog-gallery-caption { font-size:13px; color:#666; padding:8px 10px; text-align:center; }
   .comment-errors { background:#fbe2e2; border:2px solid #c0392b; border-radius:10px; padding:14px 18px; margin-bottom:20px; color:#a32e2e; }
   .comment-errors ul { margin:0; padding-left:1.1rem; }
</style>

<!-- PAGE BANNER -->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1><?php echo htmlspecialchars($post['title']); ?></h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
         <li class="breadcrumb-item active">Post</li>
      </ul>
   </div>
</section>

<section class="w-100 clearfix blogSingle" id="blogSingle">
   <div class="container">
      <div class="blogSingleInner">
         <div class="row">
            <div class="col-lg-8">
               <div class="blogSingleBlog" data-post-id="<?php echo (int) $post['id']; ?>">
                  <div class="latestNewsCardInner">
                     <div class="latestNewsCardImg">
                        <a href="javascript:void(0);"><img loading="lazy" src="<?php echo htmlspecialchars($imgRel); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-100 img-fluid"></a>
                        <?php if ($dayNum !== ''): ?>
                        <div class="latestNewsDate">
                           <a href="javascript:void(0);">
                              <h5><?php echo htmlspecialchars($dayNum); ?></h5>
                              <span><?php echo htmlspecialchars($monShort); ?></span>
                           </a>
                        </div>
                        <?php endif; ?>
                     </div>
                     <div class="latestNewsCardInnerContent">
                        <div class="latestNewsList">
                           <div class="latestNewsUser">
                              <a href="javascript:void(0);">
                                 <img loading="lazy" src="images/icon/user.png" alt="icon" class="img-fluid"><span>by <?php echo htmlspecialchars($post['author_name'] ?? 'City Gate Mixed Farm'); ?></span>
                              </a>
                           </div>
                           <div class="latestNewsMessage">
                              <a href="javascript:void(0);">
                                 <img loading="lazy" src="images/icon/message.png" alt="icon" class="img-fluid"><span><?php echo count($comments); ?> comment<?php echo count($comments) === 1 ? '' : 's'; ?></span>
                              </a>
                           </div>
                           <?php if (!empty($post['category'])): ?>
                           <div class="latestNewsMessage">
                              <span><i class="fa fa-folder-o" aria-hidden="true"></i> <?php echo htmlspecialchars($post['category']); ?></span>
                           </div>
                           <?php endif; ?>
                        </div>
                        <div class="latestNewsTxt">
                           <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                           <?php
                              // Post content is stored HTML (authored via TinyMCE) — echo raw, do NOT escape.
                              echo $post['content'];
                           ?>
                        </div>
                     </div>

                     <?php if (!empty($galleryImages)): ?>
                     <div class="blog-gallery">
                        <h4>Gallery</h4>
                        <div class="blog-gallery-grid">
                           <?php foreach ($galleryImages as $img): ?>
                           <div class="blog-gallery-item">
                              <img loading="lazy" src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars($img['caption'] ?? $post['title']); ?>">
                              <?php if (!empty($img['caption'])): ?>
                                 <div class="blog-gallery-caption"><?php echo htmlspecialchars($img['caption']); ?></div>
                              <?php endif; ?>
                           </div>
                           <?php endforeach; ?>
                        </div>
                     </div>
                     <?php endif; ?>

                     <div class="tagShareGroup">
                        <div class="tagShareGroupInner">
                           <?php if ($tagList): ?>
                           <div class="tagGroup">
                              <ul class="nav">
                                 <li class="nav-item tagHeading">Tags :</li>
                                 <?php foreach ($tagList as $i => $t): ?>
                                    <li class="nav-item"><a class="nav-link tag<?php echo $i === 0 ? ' tagActive' : ''; ?>" href="javascript:void(0);"><?php echo htmlspecialchars($t); ?></a></li>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                           <?php endif; ?>
                           <div class="shareGroup">
                              <ul class="nav">
                                 <li class="nav-item shareHeading">Share :</li>
                                 <li class="nav-item shareSocialIcon">
                                    <a class="nav-link" href="javascript:void(0);"><img loading="lazy" src="images/icon/fb.png" alt="facebook" class="img-fluid"></a>
                                    <a class="nav-link" href="javascript:void(0);"><img loading="lazy" src="images/icon/insta.png" alt="instagram" class="img-fluid"></a>
                                    <a class="nav-link" href="javascript:void(0);"><img loading="lazy" src="images/icon/twitter.png" alt="twitter" class="img-fluid"></a>
                                    <a class="nav-link" href="javascript:void(0);"><img loading="lazy" src="images/icon/whatsapp.png" alt="whatsapp" class="img-fluid"></a>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>

                     <div class="coment" id="comments">
                        <div class="comentInner">
                           <div class="comentHeading">
                              <h2 id="commentsCount">Comments (<?php echo count($comments); ?>)</h2>
                           </div>
                           <div class="comentGroup" id="commentsList">
                              <?php if (empty($comments)): ?>
                                 <p style="color:#888;">Be the first to comment on this post.</p>
                              <?php endif; ?>
                              <?php foreach ($comments as $c): ?>
                              <div class="commentGroup">
                                 <div class="commentLeft">
                                    <div class="commentUserImg">
                                       <a href="javascript:void(0);"><img loading="lazy" src="images/user2.png" alt="comment-user-img" class="img-fluid"></a>
                                    </div>
                                 </div>
                                 <div class="commentRight">
                                    <div class="commentRightTxt">
                                       <div class="commentUserTxt">
                                          <h2><?php echo htmlspecialchars($c['name']); ?></h2>
                                          <p class="mb-0"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                                       </div>
                                       <div class="dateGroup">
                                          <img src="images/icon/calendar.png" alt="calendar" class="img-fluid"><span><?php echo htmlspecialchars(date('d F Y', strtotime($c['created_at']))); ?></span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php endforeach; ?>
                           </div>
                        </div>
                     </div>

                     <!--comment box-->
                     <div class="commentBox">
                        <div class="commentBoxInner">
                           <div class="commentBoxHeading">
                              <h4>Leave Comment</h4>
                              <p>Your email address will not be published. Required fields are marked *</p>
                           </div>

                           <?php if ($commentErrors): ?>
                              <div class="comment-errors">
                                 <ul>
                                    <?php foreach ($commentErrors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
                                 </ul>
                              </div>
                           <?php endif; ?>

                           <div class="commentBoxForm">
                              <form id="commentForm" method="post" action="blog-single.php?slug=<?php echo urlencode($slug); ?>#comments">
                                 <?php echo cg_csrf_field(); ?>
                                 <input type="hidden" name="comment_submit" value="1">
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="commentFormGroup">
                                          <input type="text" class="form-control" id="yourName" placeholder="Your Name *" name="name" required>
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="commentFormGroup">
                                          <input type="email" class="form-control" id="email" placeholder="Email (optional)" name="email">
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="commentFormGroup">
                                          <textarea class="form-control" rows="5" id="writeComment" placeholder="Write a Comment *" name="comment" required></textarea>
                                       </div>
                                    </div>
                                 </div>
                                 <button type="submit" class="btnCustom5 btn-1 hover-slide-down"><span>Send <img loading="lazy" src="images/icon/icon-right.png" alt="right" class="img-fluid"></span></button>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-4">
               <div class="blogSingleAside">
                  <div class="blogAdminCard">
                     <div class="blogAdminImg">
                        <img loading="lazy" src="images/user1.png" alt="admin" class="img-fluid">
                     </div>
                     <div class="blogAdminTxt">
                        <h4><?php echo htmlspecialchars($post['author_name'] ?? 'City Gate Mixed Farm'); ?></h4>
                        <p>Sharing stories, lessons, and updates from City Gate Mixed Farm — poultry, dairy, goats and crops in Lira City, Uganda.</p>
                     </div>
                  </div>
                  <div class="searchKeyword customCard">
                     <div class="searchKeywordInner">
                        <h4>Search Keyword</h4>
                        <h6>Search</h6>
                        <form>
                           <div class="input-group">
                              <input type="text" class="form-control" placeholder="Search here" value="">
                              <button type="submit" class="input-group-text"><img loading="lazy" src="images/icon/search.png" alt="search" class="img-fluid"></button>
                           </div>
                        </form>
                     </div>
                  </div>
                  <?php if (!empty($recentPosts)): ?>
                  <div class="recentPost customCard">
                     <h4>Recent Post</h4>
                     <div class="recentPostList">
                        <?php foreach ($recentPosts as $rp):
                           $rpImg = ($rp['featured_image'] !== null && $rp['featured_image'] !== '') ? $rp['featured_image'] : 'images/gallary/rooster-farm.jpg';
                           $rpDate = $rp['publish_date'] ? date('d F Y', strtotime($rp['publish_date'])) : '';
                        ?>
                        <div class="recentPostGroupList">
                           <a href="blog-single.php?slug=<?php echo urlencode($rp['slug']); ?>">
                              <div class="recentPostImg">
                                 <img loading="lazy" src="<?php echo htmlspecialchars($rpImg); ?>" alt="<?php echo htmlspecialchars($rp['title']); ?>" class="img-fluid">
                              </div>
                              <div class="recentPostTxt">
                                 <p><?php echo htmlspecialchars($rp['title']); ?></p>
                                 <?php if ($rpDate !== ''): ?><span><img loading="lazy" src="images/icon/calendar.png" alt="calendar" class="img-fluid"> <?php echo htmlspecialchars($rpDate); ?></span><?php endif; ?>
                              </div>
                           </a>
                        </div>
                        <?php endforeach; ?>
                     </div>
                  </div>
                  <?php endif; ?>
                  <?php if ($tagList): ?>
                  <div class="popularTags customCard">
                     <h4>Popular Tags</h4>
                     <div class="tagGroup">
                        <ul class="nav">
                           <?php foreach ($tagList as $i => $t): ?>
                              <li class="nav-item"><a class="nav-link tag<?php echo $i === 0 ? ' tagActive' : ''; ?>" href="javascript:void(0);"><?php echo htmlspecialchars($t); ?></a></li>
                           <?php endforeach; ?>
                        </ul>
                     </div>
                  </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
