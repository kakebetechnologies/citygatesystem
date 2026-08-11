<?php
/**
 * Blog list. Converted from blog.html — cards are now driven live from
 * blog_posts (status='published'), newest first. Author name is joined
 * from users.author_id.
 */
$pageTitle       = 'Farm Blog | City Gate Mixed Farm';
$pageDescription = 'Stories, lessons, and updates from City Gate Mixed Farm — poultry, dairy, goats and crops in Lira City, Northern Uganda.';
$activeNav       = 'blog';
require __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare("
   SELECT bp.*, u.name AS author_name
   FROM blog_posts bp
   LEFT JOIN users u ON u.id = bp.author_id
   WHERE bp.status = 'published'
   ORDER BY bp.publish_date DESC, bp.id DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<link rel="stylesheet" href="css/cg-redesign.css">

<!-- PAGE BANNER -->
<section class="cg-page-banner">
   <div class="container">
      <p class="section-eyebrow">City Gate Mixed Farm</p>
      <h1>From the Farm</h1>
      <p style="color:rgba(255,255,255,0.7);font-size:17px;margin-top:8px;margin-bottom:20px;">Stories, lessons, and updates straight from the fields</p>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Blog</li>
      </ul>
   </div>
</section>

<!-- BLOG GRID -->
<section class="blog-section">
   <div class="container">
      <?php if (empty($posts)): ?>
         <p class="section-sub text-center" style="text-align:center;">No blog posts published yet — check back soon.</p>
      <?php else: ?>
      <div class="blog-grid">
         <?php foreach ($posts as $post):
            $excerptSrc = ($post['excerpt'] !== null && $post['excerpt'] !== '') ? $post['excerpt'] : strip_tags((string) $post['content']);
            $excerptSrc = trim($excerptSrc);
            if (mb_strlen($excerptSrc) > 140) {
               $excerptSrc = rtrim(mb_substr($excerptSrc, 0, 137)) . '...';
            }
            $img = ($post['featured_image'] !== null && $post['featured_image'] !== '') ? $post['featured_image'] : 'images/gallary/rooster-farm.jpg';
            $dateStr = $post['publish_date'] ? date('d M Y', strtotime($post['publish_date'])) : '';
         ?>
         <a href="blog-single.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-card fade-in">
            <div class="blog-card-img">
               <img loading="lazy" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
            <div class="blog-card-body">
               <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                  <?php if (!empty($post['category'])): ?>
                     <span style="background:rgba(47,93,58,0.1);color:var(--cg-green);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 12px;border-radius:50px;"><?php echo htmlspecialchars($post['category']); ?></span>
                  <?php endif; ?>
                  <?php if ($dateStr !== ''): ?><span class="blog-card-date" style="margin-bottom:0;"><?php echo htmlspecialchars($dateStr); ?></span><?php endif; ?>
               </div>
               <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
               <p style="font-size:14px;line-height:1.7;color:#666;margin:10px 0 14px;"><?php echo htmlspecialchars($excerptSrc); ?></p>
               <div style="display:flex;align-items:center;justify-content:space-between;">
                  <span style="font-size:12px;color:#888;">by <?php echo htmlspecialchars($post['author_name'] ?? 'City Gate Mixed Farm'); ?></span>
                  <span style="font-size:13px;font-weight:700;color:var(--cg-green);">Read Article &rarr;</span>
               </div>
            </div>
         </a>
         <?php endforeach; ?>
      </div>
      <?php endif; ?>
   </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
   <div class="container">
      <p class="cta-band-text">Want to see the farm in person?</p>
      <a href="bookings.php" class="modern-btn">Book a Farm Visit</a>
   </div>
</section>

<script>
(function(){
   if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(e){ e.forEach(function(x){ if(x.isIntersecting){ x.target.classList.add('visible'); io.unobserve(x.target); } }); },{threshold:0.12});
      document.querySelectorAll('.fade-in').forEach(function(el){ io.observe(el); });
   } else { document.querySelectorAll('.fade-in').forEach(function(el){ el.classList.add('visible'); }); }
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
