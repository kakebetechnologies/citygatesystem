<?php
/**
 * Gallery page — filterable masonry photo grid + video section, DB-driven
 * from gallery_media (see admin/gallery.php for how staff add new items).
 */
$pageTitle       = 'Gallery | City Gate Mixed Farm - Life on Our Farm';
$pageDescription = 'Explore City Gate Mixed Farm through photos and videos — poultry houses, goat pens, dairy operations, coffee and banana plots, training sessions and farm life in Amuca, Lira City.';
$ogImage         = 'images/gallary/rooster-farm.jpg';
$activeNav       = 'gallery';
require __DIR__ . '/includes/header.php';

$photos = $pdo->query("SELECT * FROM gallery_media WHERE type='photo' ORDER BY sort_order, id")->fetchAll();
$videos = $pdo->query("SELECT * FROM gallery_media WHERE type='video' ORDER BY sort_order, id")->fetchAll();

// Distinct categories present among photos, for the filter pill row.
$categories = [];
foreach ($photos as $p) {
   $cat = trim((string) $p['category']);
   if ($cat !== '' && !in_array($cat, $categories, true)) $categories[] = $cat;
}

/** Extracts a YouTube video ID from common URL formats, or null if not recognized. */
function cg_youtube_id(string $url): ?string {
   if (preg_match('~(?:youtube\.com/watch\?v=|youtube\.com/embed/|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
      return $m[1];
   }
   return null;
}
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .videosSection { background: var(--cg-cream, #FAF7F0); padding: 56px 0; }
   .videoCard { background: #fff; border-radius: var(--cg-radius, 10px); overflow: hidden; box-shadow: var(--cg-shadow); height: 100%; margin-bottom: 30px; }
   .videoCard .videoEmbed { position: relative; width: 100%; padding-top: 56.25%; background: #000; }
   .videoCard .videoEmbed iframe, .videoCard .videoEmbed video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
   .videoCard .videoLinkFallback { display: flex; align-items: center; justify-content: center; height: 220px; background: linear-gradient(135deg, var(--cg-green, #2F5D3A) 0%, var(--cg-green-dark, #1F3F27) 100%); }
   .videoCard .videoLinkFallback a { color: #fff; font-weight: 600; text-decoration: none; border: 2px solid rgba(255,255,255,0.6); padding: 10px 22px; border-radius: 50px; transition: all 0.3s ease; }
   .videoCard .videoLinkFallback a:hover { background: #fff; color: var(--cg-green, #2F5D3A); }
   .videoCard .videoBody { padding: 16px 20px 20px; }
   .videoCard .videoBody h5 { margin: 0 0 4px; font-weight: 700; color: var(--cg-charcoal, #1A1A1A); }
   .videoCard .videoBody p { margin: 0; color: #777; font-size: 14px; }
   .galleryCategoryPill { display: inline-block; background: var(--cg-earth, #C9A978); color: var(--cg-charcoal, #1A1A1A); font-size: 12px; font-weight: 600; padding: 3px 12px; border-radius: 20px; margin-bottom: 10px; }
</style>

<!--banner start-->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1>Life on Our Farm</h1>
      <p>A visual journey through poultry, goats, dairy &amp; crops</p>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Gallery</li>
      </ul>
   </div>
</section>
<!--banner end-->

<!--Filter pills start-->
<?php if (!empty($categories)): ?>
<section class="cg-gallery-filters">
   <div class="container">
      <div class="cg-gallery-filters__row" id="galleryFilters">
         <button type="button" class="cg-gallery-pill is-active" data-filter="all">All Photos</button>
         <?php foreach ($categories as $cat): ?>
         <button type="button" class="cg-gallery-pill" data-filter="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></button>
         <?php endforeach; ?>
      </div>
   </div>
</section>
<?php endif; ?>
<!--Filter pills end-->

<!--Gallery start-->
<section class="w-100 clearfix galleryThreeColumn" id="galleryThreeColumn">
   <div class="container">
      <?php if (empty($photos)): ?>
         <div class="cg-gallery-empty">
            <i class="fa fa-camera" aria-hidden="true"></i>
            <p>No photos have been added to the gallery yet — check back soon.</p>
         </div>
      <?php else: ?>
      <div class="cg-gallery-masonry" id="galleryMasonry">
         <?php foreach ($photos as $photo):
            $src     = htmlspecialchars($photo['file_path'] ?? '');
            $caption = htmlspecialchars($photo['caption'] ?? '');
            $cat     = htmlspecialchars($photo['category'] ?? '');
            $alt     = $caption !== '' ? $caption : 'City Gate Mixed Farm';
         ?>
         <a href="<?php echo $src; ?>" class="cg-gallery-item fade-in" data-fancybox="gallery-photos" data-caption="<?php echo $caption; ?>" data-category="<?php echo $cat; ?>">
            <img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" loading="lazy">
            <?php if ($cat !== ''): ?><span class="cg-gallery-item__badge"><?php echo $cat; ?></span><?php endif; ?>
            <span class="cg-gallery-item__zoom"><i class="fa fa-search-plus" aria-hidden="true"></i></span>
            <?php if ($caption !== ''): ?>
            <span class="cg-gallery-item__overlay"><span class="cg-gallery-item__caption"><?php echo $caption; ?></span></span>
            <?php endif; ?>
         </a>
         <?php endforeach; ?>
      </div>
      <div class="cg-gallery-empty" id="galleryNoMatch" style="display:none;margin-top:24px;">
         <i class="fa fa-camera" aria-hidden="true"></i>
         <p>No photos in this category yet.</p>
      </div>
      <?php endif; ?>
   </div>
</section>
<!--Gallery end-->

<!--Videos start-->
<section class="w-100 clearfix videosSection" id="galleryVideos">
   <div class="container">
      <div class="commonHeading text-center" style="padding: 0 0 30px;">
         <h2>Farm Videos</h2>
         <p>Watch City Gate Mixed Farm in motion — training sessions, farm tours, and daily operations.</p>
      </div>

      <?php if (empty($videos)): ?>
      <div class="cg-gallery-empty">
         <i class="fa fa-video-camera" aria-hidden="true"></i>
         <p>No videos have been added yet — check back soon for farm tours and training clips.</p>
      </div>
      <?php else: ?>
      <div class="row">
         <?php foreach ($videos as $video):
            $caption  = htmlspecialchars($video['caption'] ?? '');
            $category = htmlspecialchars($video['category'] ?? '');
            $ytId     = !empty($video['embed_url']) ? cg_youtube_id($video['embed_url']) : null;
         ?>
         <div class="col-md-6 col-lg-4">
            <div class="videoCard">
               <?php if (!empty($video['file_path'])): ?>
                  <div class="videoEmbed">
                     <video controls preload="metadata" src="<?php echo htmlspecialchars($video['file_path']); ?>"></video>
                  </div>
               <?php elseif ($ytId !== null): ?>
                  <div class="videoEmbed">
                     <iframe src="https://www.youtube.com/embed/<?php echo $ytId; ?>" title="<?php echo $caption !== '' ? $caption : 'Farm video'; ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                  </div>
               <?php elseif (!empty($video['embed_url'])): ?>
                  <div class="videoLinkFallback">
                     <a href="<?php echo htmlspecialchars($video['embed_url']); ?>" target="_blank" rel="noopener noreferrer">Watch Video <i class="fa fa-external-link" aria-hidden="true"></i></a>
                  </div>
               <?php endif; ?>
               <?php if ($caption !== '' || $category !== ''): ?>
               <div class="videoBody">
                  <?php if ($category !== ''): ?><span class="galleryCategoryPill"><?php echo $category; ?></span><?php endif; ?>
                  <?php if ($caption !== ''): ?><h5><?php echo $caption; ?></h5><?php endif; ?>
               </div>
               <?php endif; ?>
            </div>
         </div>
         <?php endforeach; ?>
      </div>
      <?php endif; ?>
   </div>
</section>
<!--Videos end-->

<script>
(function () {
   var filters = document.getElementById('galleryFilters');
   if (!filters) return;
   var items = document.querySelectorAll('#galleryMasonry .cg-gallery-item');
   var noMatch = document.getElementById('galleryNoMatch');
   filters.addEventListener('click', function (e) {
      var btn = e.target.closest('.cg-gallery-pill');
      if (!btn) return;
      filters.querySelectorAll('.cg-gallery-pill').forEach(function (b) { b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      var filter = btn.getAttribute('data-filter');
      var visibleCount = 0;
      items.forEach(function (item) {
         var match = filter === 'all' || item.getAttribute('data-category') === filter;
         item.classList.toggle('is-hidden', !match);
         if (match) visibleCount++;
      });
      if (noMatch) noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
   });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
