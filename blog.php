<?php
/**
 * Blog list — magazine-style layout: rotating hero of the newest posts,
 * a category strip, an "about the blog" band, then a featured big-card +
 * list row, with any remaining posts falling into a classic grid below.
 * Everything is driven live from blog_posts (status='published').
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
$allPosts = $stmt->fetchAll();

$defaultImage = 'images/gallary/rooster-farm.jpg';

// Representative photo per category, used by the category strip when a
// category has no published post image of its own yet.
$categoryFallbackImages = [
   'Poultry'  => 'images/gallary/pexels-chicken-1867521_1920.jpg',
   'Dairy'    => 'images/gallary/jaclou-dl-cow-4270355_1920.jpg',
   'Goats'    => 'images/gallary/walter46-goat-4138049_1920.jpg',
   'Crops'    => 'images/gallary/christoph-coffee-171653_1920.jpg',
   'Training' => 'images/gallary/citygate-farm-photo-3.jpg',
   'Nutrition'=> 'images/gallary/stevepb-nest-1050964_1920.jpg',
];

function cg_reading_time(string $content): int {
   $words = str_word_count(strip_tags($content));
   return max(1, (int) ceil($words / 200));
}
function cg_post_image(array $post): string {
   global $defaultImage;
   return ($post['featured_image'] !== null && $post['featured_image'] !== '') ? $post['featured_image'] : $defaultImage;
}
function cg_post_excerpt(array $post, int $len = 140): string {
   $src = ($post['excerpt'] !== null && $post['excerpt'] !== '') ? $post['excerpt'] : strip_tags((string) $post['content']);
   $src = trim($src);
   if (mb_strlen($src) > $len) { $src = rtrim(mb_substr($src, 0, $len - 3)) . '...'; }
   return $src;
}

// ---- Category filter (optional ?category=) + search filter (optional ?q=) ----
$activeCategory = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
$searchQuery    = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$posts = $allPosts;
if ($activeCategory !== '') {
   $posts = array_values(array_filter($posts, fn($p) => strcasecmp((string) $p['category'], $activeCategory) === 0));
}
if ($searchQuery !== '') {
   $needle = mb_strtolower($searchQuery);
   $posts = array_values(array_filter($posts, function ($p) use ($needle) {
      $haystack = mb_strtolower($p['title'] . ' ' . (string) $p['excerpt'] . ' ' . strip_tags((string) $p['content']));
      return mb_strpos($haystack, $needle) !== false;
   }));
}

// ---- Category strip: every distinct category across all published posts,
// using that category's own post photo (falls back to a stock farm photo) ----
$categories = [];
foreach ($allPosts as $p) {
   $cat = trim((string) $p['category']);
   if ($cat === '' || isset($categories[$cat])) continue;
   $categories[$cat] = $categoryFallbackImages[$cat] ?? cg_post_image($p);
}

// ---- Hero slides: up to 5 most recent posts ----
$heroSlides = array_slice($allPosts, 0, 5);

// ---- Featured row: big card (newest of the filtered set) + list (next 4) ----
$featuredMain = $posts[0] ?? null;
$featuredList = array_slice($posts, 1, 4);
$morePosts    = array_slice($posts, 5);
?>
<link rel="stylesheet" href="css/cg-redesign.css">

<?php if (empty($allPosts)): ?>

<!-- PAGE BANNER (empty state — no posts published yet) -->
<section class="cg-page-banner">
   <div class="container">
      <p class="section-eyebrow">City Gate Mixed Farm</p>
      <h1>From the Farm</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Blog</li>
      </ul>
   </div>
</section>
<section class="blog-section">
   <div class="container">
      <p class="section-sub text-center" style="text-align:center;">No blog posts published yet — check back soon.</p>
   </div>
</section>

<?php else: ?>

<!-- HERO SLIDER — latest stories -->
<section class="blog-hero" id="blogHero" aria-label="Latest stories">
   <?php foreach ($heroSlides as $i => $post):
      $dateStr = $post['publish_date'] ? date('d M Y', strtotime($post['publish_date'])) : '';
   ?>
   <div class="blog-hero__slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-slide="<?php echo $i; ?>">
      <div class="blog-hero__img" style="background-image:url('<?php echo htmlspecialchars(cg_post_image($post)); ?>');"></div>
      <div class="blog-hero__overlay"></div>
      <div class="blog-hero__content">
         <div class="blog-hero__count"><strong><?php echo str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT); ?></strong> / <?php echo str_pad((string)count($heroSlides), 2, '0', STR_PAD_LEFT); ?></div>
         <div class="blog-hero__meta">
            <?php if (!empty($post['category'])): ?><span class="blog-hero__pill"><?php echo htmlspecialchars($post['category']); ?></span><?php endif; ?>
            <?php if ($dateStr !== ''): ?><span class="blog-hero__date"><?php echo htmlspecialchars($dateStr); ?></span><?php endif; ?>
         </div>
         <a href="blog-single.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-hero__title"><?php echo htmlspecialchars($post['title']); ?></a>
         <p class="blog-hero__excerpt"><?php echo htmlspecialchars(cg_post_excerpt($post, 130)); ?></p>
      </div>
   </div>
   <?php endforeach; ?>

   <?php if (count($heroSlides) > 1): ?>
   <button class="blog-hero__arrow blog-hero__arrow--prev" id="blogHeroPrev" aria-label="Previous story"><i class="fa fa-angle-left"></i></button>
   <button class="blog-hero__arrow blog-hero__arrow--next" id="blogHeroNext" aria-label="Next story"><i class="fa fa-angle-right"></i></button>
   <div class="blog-hero__dots" id="blogHeroDots">
      <?php foreach ($heroSlides as $i => $post): ?>
      <button class="blog-hero__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-go="<?php echo $i; ?>" aria-label="Story <?php echo $i + 1; ?>"></button>
      <?php endforeach; ?>
   </div>
   <?php endif; ?>
</section>
<?php if (count($heroSlides) > 1): ?>
<script>
(function(){
   var root = document.getElementById('blogHero');
   if (!root) return;
   var slides = root.querySelectorAll('.blog-hero__slide');
   var dots = root.querySelectorAll('.blog-hero__dot');
   var idx = 0, total = slides.length, timer;
   function go(n){
      idx = (n + total) % total;
      slides.forEach(function(s,i){ s.classList.toggle('is-active', i === idx); });
      dots.forEach(function(d,i){ d.classList.toggle('is-active', i === idx); });
   }
   function next(){ go(idx + 1); }
   function prev(){ go(idx - 1); }
   function restart(){ clearInterval(timer); timer = setInterval(next, 6500); }
   dots.forEach(function(d){ d.addEventListener('click', function(){ go(parseInt(d.dataset.go, 10)); restart(); }); });
   var nextBtn = document.getElementById('blogHeroNext');
   var prevBtn = document.getElementById('blogHeroPrev');
   if (nextBtn) nextBtn.addEventListener('click', function(){ next(); restart(); });
   if (prevBtn) prevBtn.addEventListener('click', function(){ prev(); restart(); });
   restart();
})();
</script>
<?php endif; ?>

<!-- CATEGORY STRIP -->
<?php if (!empty($categories)): ?>
<section class="blog-cat-strip">
   <div class="container">
      <div class="blog-cat-strip__row">
         <a href="blog.php" class="blog-cat-item<?php echo $activeCategory === '' ? ' is-active' : ''; ?>">
            <img src="<?php echo htmlspecialchars($defaultImage); ?>" alt="All Stories" loading="lazy">
            <div class="blog-cat-item__overlay"><span class="blog-cat-item__pill">All Stories</span></div>
         </a>
         <?php foreach ($categories as $cat => $img): ?>
         <a href="blog.php?category=<?php echo urlencode($cat); ?>#blogFeatured" class="blog-cat-item<?php echo strcasecmp($activeCategory, $cat) === 0 ? ' is-active' : ''; ?>">
            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($cat); ?>" loading="lazy">
            <div class="blog-cat-item__overlay"><span class="blog-cat-item__pill"><?php echo htmlspecialchars($cat); ?></span></div>
         </a>
         <?php endforeach; ?>
      </div>
   </div>
</section>
<div class="blog-divider"><hr class="blog-divider__line"></div>
<?php endif; ?>

<!-- ABOUT THE BLOG BAND -->
<section class="blog-intro-band">
   <div class="container">
      <div class="blog-intro-band__row">
         <div class="blog-intro-band__media fade-in">
            <img src="images/gallary/citygate-farm-photo-6.jpg" alt="City Gate Mixed Farm staff and visitors on a farm walk-through" loading="lazy">
            <div class="blog-intro-band__badge">
               <span>Fresh Posts</span>
               <strong><?php echo count($allPosts); ?> Stories</strong>
            </div>
         </div>
         <div class="fade-in">
            <p class="section-eyebrow">From The Farm</p>
            <h2 class="blog-intro-band__title">Real Stories From a Working Farm</h2>
            <p class="blog-intro-band__text">We write about what actually happens at City Gate Mixed Farm in Amuca — poultry, dairy, goats and crops, training intakes, and the lessons we pick up along the way. No stock advice, just what works on our own 4-acre model farm.</p>
            <a href="about-us.php" class="modern-btn modern-btn-solid">About The Farm</a>
         </div>
      </div>
   </div>
</section>

<!-- FEATURED STORIES: big card + list -->
<?php if ($featuredMain): ?>
<section class="blog-featured" id="blogFeatured">
   <div class="container">
      <div class="section-head-center">
         <p class="section-eyebrow"><?php echo $activeCategory !== '' ? htmlspecialchars($activeCategory) : 'Latest Updates'; ?></p>
         <h2 class="section-title"><?php echo $searchQuery !== '' ? 'Search Results for &ldquo;' . htmlspecialchars($searchQuery) . '&rdquo;' : 'Stories From the Fields'; ?></h2>
      </div>

      <div class="blog-featured__grid">
         <a href="blog-single.php?slug=<?php echo urlencode($featuredMain['slug']); ?>" class="blog-featured__main fade-in">
            <div class="blog-featured__main-img">
               <img src="<?php echo htmlspecialchars(cg_post_image($featuredMain)); ?>" alt="<?php echo htmlspecialchars($featuredMain['title']); ?>" loading="lazy">
            </div>
            <div class="blog-featured__main-body">
               <?php if (!empty($featuredMain['category'])): ?><span class="blog-post-pill"><?php echo htmlspecialchars($featuredMain['category']); ?></span><?php endif; ?>
               <h3 class="blog-card-title" style="margin-top:14px;font-size:24px;"><?php echo htmlspecialchars($featuredMain['title']); ?></h3>
               <p style="font-size:15px;line-height:1.7;color:#666;margin:12px 0 0;"><?php echo htmlspecialchars(cg_post_excerpt($featuredMain, 180)); ?></p>
               <div class="blog-post-meta-row">
                  <span><i class="fa fa-user-o" aria-hidden="true"></i> <?php echo htmlspecialchars($featuredMain['author_name'] ?? 'City Gate Mixed Farm'); ?></span>
                  <span>&middot;</span>
                  <span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo cg_reading_time((string) $featuredMain['content']); ?> min read</span>
                  <?php if ($featuredMain['publish_date']): ?><span>&middot;</span><span><?php echo htmlspecialchars(date('d M Y', strtotime($featuredMain['publish_date']))); ?></span><?php endif; ?>
               </div>
            </div>
         </a>

         <?php if (!empty($featuredList)): ?>
         <div class="blog-featured__list">
            <?php foreach ($featuredList as $post): ?>
            <a href="blog-single.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-featured__item fade-in">
               <div class="blog-featured__item-img">
                  <img src="<?php echo htmlspecialchars(cg_post_image($post)); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
               </div>
               <div class="blog-featured__item-body">
                  <?php if (!empty($post['category'])): ?><span class="blog-post-pill"><?php echo htmlspecialchars($post['category']); ?></span><?php endif; ?>
                  <h4 class="blog-featured__item-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                  <div class="blog-featured__item-meta">
                     <?php echo $post['publish_date'] ? htmlspecialchars(date('d M Y', strtotime($post['publish_date']))) : ''; ?>
                     &middot; <?php echo cg_reading_time((string) $post['content']); ?> min read
                  </div>
               </div>
            </a>
            <?php endforeach; ?>
         </div>
         <?php endif; ?>
      </div>
   </div>
</section>
<?php elseif ($activeCategory !== '' || $searchQuery !== ''): ?>
<section class="blog-featured" id="blogFeatured">
   <div class="container">
      <p class="section-sub text-center" style="text-align:center;">
         <?php if ($searchQuery !== ''): ?>
            No stories match &ldquo;<?php echo htmlspecialchars($searchQuery); ?>&rdquo;.
         <?php else: ?>
            No stories in "<?php echo htmlspecialchars($activeCategory); ?>" yet.
         <?php endif; ?>
         <a href="blog.php">View all stories &rarr;</a>
      </p>
   </div>
</section>
<?php endif; ?>

<!-- MORE STORIES (any posts beyond the featured row) -->
<?php if (!empty($morePosts)): ?>
<section class="blog-more">
   <div class="container">
      <div class="section-head-center" style="margin-bottom:24px;">
         <h2 class="section-title" style="font-size:28px;">More From the Farm</h2>
      </div>
      <div class="blog-grid">
         <?php foreach ($morePosts as $post):
            $dateStr = $post['publish_date'] ? date('d M Y', strtotime($post['publish_date'])) : '';
         ?>
         <a href="blog-single.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-card fade-in">
            <div class="blog-card-img">
               <img loading="lazy" src="<?php echo htmlspecialchars(cg_post_image($post)); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
            <div class="blog-card-body">
               <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                  <?php if (!empty($post['category'])): ?><span class="blog-post-pill"><?php echo htmlspecialchars($post['category']); ?></span><?php endif; ?>
                  <?php if ($dateStr !== ''): ?><span class="blog-card-date" style="margin-bottom:0;"><?php echo htmlspecialchars($dateStr); ?></span><?php endif; ?>
               </div>
               <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
               <p style="font-size:14px;line-height:1.7;color:#666;margin:10px 0 14px;"><?php echo htmlspecialchars(cg_post_excerpt($post)); ?></p>
               <span style="font-size:13px;font-weight:700;color:var(--cg-green);">Read Article &rarr;</span>
            </div>
         </a>
         <?php endforeach; ?>
      </div>
   </div>
</section>
<?php endif; ?>

<!-- CTA BAND -->
<section class="cta-band">
   <div class="container">
      <p class="cta-band-text">Want to see the farm in person?</p>
      <a href="bookings.php" class="modern-btn">Book a Farm Visit</a>
   </div>
</section>

<?php endif; ?>

<script>
(function(){
   if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(e){ e.forEach(function(x){ if(x.isIntersecting){ x.target.classList.add('visible'); io.unobserve(x.target); } }); },{threshold:0.12});
      document.querySelectorAll('.fade-in').forEach(function(el){ io.observe(el); });
   } else { document.querySelectorAll('.fade-in').forEach(function(el){ el.classList.add('visible'); }); }
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
