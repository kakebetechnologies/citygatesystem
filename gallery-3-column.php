<?php
/**
 * Gallery page. Converted from gallery-3-column.html into the shared-include
 * structure, and made DB-driven: photos and videos both come from
 * gallery_media (see admin/gallery.php for how staff add new items).
 */
$pageTitle       = 'Gallery | City Gate Mixed Farm - Life on Our Farm';
$pageDescription = 'Explore City Gate Mixed Farm through photos and videos — poultry houses, goat pens, dairy operations, coffee and banana plots, training sessions and farm life in Amuca, Lira City.';
$activeNav       = 'gallery';
require __DIR__ . '/includes/header.php';

$photos = $pdo->query("SELECT * FROM gallery_media WHERE type='photo' ORDER BY sort_order, id")->fetchAll();
$videos = $pdo->query("SELECT * FROM gallery_media WHERE type='video' ORDER BY sort_order, id")->fetchAll();

// How many photos show before the "Load More" toggle reveals the rest —
// mirrors the original static page's 6-then-load-more pattern.
$initialPhotoCount = 6;
$firstPhotos = array_slice($photos, 0, $initialPhotoCount);
$morePhotos  = array_slice($photos, $initialPhotoCount);

/** Extracts a YouTube video ID from common URL formats, or null if not recognized. */
function cg_youtube_id(string $url): ?string {
   if (preg_match('~(?:youtube\.com/watch\?v=|youtube\.com/embed/|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
      return $m[1];
   }
   return null;
}

/** Renders a single gallery photo <div class="col-*"> block. */
function cg_render_gallery_photo(array $photo): void {
   $src     = htmlspecialchars($photo['file_path'] ?? '');
   $caption = htmlspecialchars($photo['caption'] ?? '');
   $alt     = $caption !== '' ? $caption : 'City Gate Mixed Farm';
   ?>
   <div class="col-md-4 col-sm-6 galleryCol">
      <div class="galleryItem">
         <a href="<?php echo $src; ?>" class="galleryLink" data-fancybox="gallery-photos" data-caption="<?php echo $caption; ?>">
            <div class="promo image">
               <img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" class="img-fluid" loading="lazy">
               <div class="caption">
                  <div class="plusIcon"><img src="images/icon/plus-icon.png" alt="plus-img" class="img-fluid"></div>
                  <?php if ($caption !== ''): ?><p class="galleryCaptionTxt"><?php echo $caption; ?></p><?php endif; ?>
               </div>
            </div>
         </a>
      </div>
   </div>
   <?php
}
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .galleryCaptionTxt {
      color: #fff;
      font-size: 14px;
      margin: 10px 0 0;
      text-align: center;
      padding: 0 10px;
   }
   .galleryCategoryPill {
      display: inline-block;
      background: var(--cg-earth, #C9A978);
      color: var(--cg-charcoal, #1A1A1A);
      font-size: 12px;
      font-weight: 600;
      padding: 3px 12px;
      border-radius: 20px;
      margin-bottom: 10px;
   }
   .videosSection {
      background: var(--cg-cream, #FAF7F0);
      padding: 56px 0;
   }
   .videoCard {
      background: #fff;
      border-radius: var(--cg-radius, 10px);
      overflow: hidden;
      box-shadow: var(--cg-shadow);
      height: 100%;
      margin-bottom: 30px;
   }
   .videoCard .videoEmbed {
      position: relative;
      width: 100%;
      padding-top: 56.25%;
      background: #000;
   }
   .videoCard .videoEmbed iframe,
   .videoCard .videoEmbed video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: 0;
   }
   .videoCard .videoLinkFallback {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 220px;
      background: linear-gradient(135deg, var(--cg-green, #2F5D3A) 0%, var(--cg-green-dark, #1F3F27) 100%);
   }
   .videoCard .videoLinkFallback a {
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      border: 2px solid rgba(255,255,255,0.6);
      padding: 10px 22px;
      border-radius: 50px;
      transition: all 0.3s ease;
   }
   .videoCard .videoLinkFallback a:hover {
      background: #fff;
      color: var(--cg-green, #2F5D3A);
   }
   .videoCard .videoBody {
      padding: 16px 20px 20px;
   }
   .videoCard .videoBody h5 {
      margin: 0 0 4px;
      font-weight: 700;
      color: var(--cg-charcoal, #1A1A1A);
   }
   .videoCard .videoBody p {
      margin: 0;
      color: #777;
      font-size: 14px;
   }
   .videosEmptyState {
      text-align: center;
      padding: 50px 20px;
      background: #fff;
      border-radius: var(--cg-radius, 10px);
      border: 2px dashed #ddd;
   }
   .videosEmptyState i {
      font-size: 2.5rem;
      color: var(--cg-earth, #C9A978);
      margin-bottom: 14px;
      display: inline-block;
   }
   .videosEmptyState p {
      margin: 0;
      color: #777;
   }
</style>

<!--banner start-->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1>Life on Our Farm</h1>
      <p>A visual journey through poultry, goats, dairy & crops</p>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Gallery</li>
      </ul>
   </div>
</section>
<!--banner end-->

<!--Gallery start-->
<section class="w-100 clearfix galleryThreeColumn galleryColumn" id="galleryThreeColumn">
   <div class="container">
      <div class="commonHeading text-center" style="padding: 40px 0 30px;">
         <h2>Life at City Gate Mixed Farm</h2>
         <p>A visual tour of our four sectors — poultry houses, goat pens, dairy operations, coffee and banana plots — plus training sessions, school visits, and daily farm life in Amuca, Lira City.</p>
      </div>

      <?php if (empty($photos)): ?>
         <div class="videosEmptyState">
            <i class="fa fa-camera" aria-hidden="true"></i>
            <p>No photos have been added to the gallery yet — check back soon.</p>
         </div>
      <?php else: ?>
      <div class="row galleryRow">
         <?php foreach ($firstPhotos as $photo): cg_render_gallery_photo($photo); endforeach; ?>

         <?php if (!empty($morePhotos)): ?>
         <div class="loadMore">
            <div class="row">
               <?php foreach ($morePhotos as $photo): cg_render_gallery_photo($photo); endforeach; ?>
            </div>
         </div>
         <?php endif; ?>
      </div>
      <?php if (!empty($morePhotos)): ?>
      <div id="show-more" class="col-md-12 text-center">
         <a class="btnCustom5 btn-1 hover-slide-down" id="LoadMoreBtn" href="javascript:void(0);">
            <span><samp class="btnInnerTxt">Load More</samp> <img src="images/icon/icon-right.png" alt="right" class="img-fluid"></span>
         </a>
      </div>
      <?php endif; ?>
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
      <div class="videosEmptyState">
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
   document.addEventListener('DOMContentLoaded', function () {
      $('#LoadMoreBtn span .btnInnerTxt').click(function() {
         $('.loadMore').slideToggle();
         if ($('#LoadMoreBtn span .btnInnerTxt').text() == "Load Less") {
            $(this).text("Load More")
         } else {
            $(this).text("Load Less")
         }
      });
   });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
