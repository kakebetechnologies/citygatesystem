<?php
/**
 * Shop single product. Converted from shop-single.html — the product info
 * itself is still static/hardcoded (no `products` table in the schema),
 * same as shop-single.html's demo "Bovans Browns" product. The
 * data-product-id="bovans-browns" attribute from the old prototype is kept
 * as the stable identifier and used as product_slug against the real
 * product_reviews table (replacing the old localStorage cg-store.js demo).
 *
 * Enquiry-only per the site's non-ecommerce direction: the old "Guaranteed
 * Safe Check Out" payment-logo block and worldwide-shipping blurb implied a
 * real checkout flow that doesn't exist here, so those were dropped; the
 * "Enquire About This Product" CTA (already present, no cart) is kept as-is.
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php'; // session + CSRF helpers (public review form)

$productSlug = 'bovans-browns';
$productName = 'Bovans Browns';

// ---- Handle new review submission (PRG) ----
$reviewErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submit'])) {
   cg_verify_csrf();

   $rName    = trim($_POST['fname'] ?? '');
   $rComment = trim($_POST['message'] ?? '');
   $rRatingRaw = $_POST['rating'] ?? '';
   $rRating = ctype_digit((string) $rRatingRaw) ? (int) $rRatingRaw : 0;

   if ($rName === '') $reviewErrors[] = 'Please enter your name.';
   if ($rComment === '') $reviewErrors[] = 'Please write a comment.';
   if ($rRating < 1 || $rRating > 5) $reviewErrors[] = 'Please select a star rating (1 to 5).';

   if (!$reviewErrors) {
      $ins = $pdo->prepare('INSERT INTO product_reviews (product_slug, name, rating, comment) VALUES (?, ?, ?, ?)');
      $ins->execute([$productSlug, $rName, $rRating, $rComment]);
      header('Location: shop-single.php?product=' . urlencode($productSlug) . '#reviews');
      exit;
   }
}

// ---- Existing reviews ----
$revStmt = $pdo->prepare('SELECT * FROM product_reviews WHERE product_slug = ? ORDER BY created_at DESC');
$revStmt->execute([$productSlug]);
$reviews = $revStmt->fetchAll();
$reviewCount = count($reviews);
$avgRating = $reviewCount ? array_sum(array_column($reviews, 'rating')) / $reviewCount : 0;
$avgRounded = (int) round($avgRating);

$pageTitle       = $productName . ' | City Gate Mixed Farm - Fresh Eggs, Chicks, Goats & More';
$pageDescription = 'Bovans Browns from City Gate Mixed Farm in Lira City - quality poultry breeds and farm products, all prices in UGX.';
$activeNav       = 'shop';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .review-errors { background:#fbe2e2; border:2px solid #c0392b; border-radius:10px; padding:14px 18px; margin-bottom:20px; color:#a32e2e; }
   .review-errors ul { margin:0; padding-left:1.1rem; }
</style>

<!--banner start-->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">Farm Shop</span>
      <h1>Product Details</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
         <li class="breadcrumb-item active">Product Details</li>
      </ul>
   </div>
</section>
<!--banner end-->

<!--Shop Single start-->
<section class="w-100 clearfix shopSingle" id="shopSingle">
   <div class="container">
      <div class="shopSingleInner">
         <div class="row">
            <div class="col-lg-5">
               <div class="productThumbnailsSlider">
                  <div id="custCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                     <div class="carousel-inner">
                        <div class="carousel-item active">
                           <div class="carouselItem"><img loading="lazy" src="images/gallary/andreasgoellner-hen-5642953_1920.jpg" alt="Bovans Browns" class="img-fluid"></div>
                        </div>
                        <div class="carousel-item">
                           <div class="carouselItem"><img loading="lazy" src="images/gallary/neelam279-floor-7049278_1920.jpg" alt="Bovans Browns" class="img-fluid"></div>
                        </div>
                        <div class="carousel-item">
                           <div class="carouselItem"><img loading="lazy" src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="Bovans Browns" class="img-fluid"></div>
                        </div>
                        <div class="carousel-item">
                           <div class="carouselItem"><img loading="lazy" src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="Bovans Browns" class="img-fluid"></div>
                        </div>
                     </div>

                     <ol class="carousel-indicators list-inline">
                        <li class="list-inline-item">
                           <a id="carousel-selector-0" class="selected active" data-bs-slide-to="0" data-bs-target="#custCarousel">
                              <div class="carouselIndicatorsItem"><img loading="lazy" src="images/gallary/andreasgoellner-hen-5642953_1920.jpg" alt="img" class="img-fluid"></div>
                           </a>
                        </li>
                        <li class="list-inline-item">
                           <a id="carousel-selector-1" data-bs-slide-to="1" data-bs-target="#custCarousel">
                              <div class="carouselIndicatorsItem"><img loading="lazy" src="images/gallary/neelam279-floor-7049278_1920.jpg" alt="img" class="img-fluid"></div>
                           </a>
                        </li>
                        <li class="list-inline-item">
                           <a id="carousel-selector-2" data-bs-slide-to="2" data-bs-target="#custCarousel">
                              <div class="carouselIndicatorsItem"><img loading="lazy" src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="img" class="img-fluid"></div>
                           </a>
                        </li>
                        <li class="list-inline-item">
                           <a id="carousel-selector-3" data-bs-slide-to="3" data-bs-target="#custCarousel">
                              <div class="carouselIndicatorsItem"><img loading="lazy" src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="img" class="img-fluid"></div>
                           </a>
                        </li>
                     </ol>
                  </div>
               </div>
            </div>
            <div class="col-lg-7">
               <div class="productDetail" data-product-id="<?php echo htmlspecialchars($productSlug); ?>">
                  <h2><?php echo htmlspecialchars($productName); ?></h2>
                  <div class="productPrice">
                     <ins>UGX 40,000</ins>
                     <del>UGX 60,000</del>
                  </div>
                  <div class="starGroup" id="avgStarGroup">
                     <?php for ($i = 1; $i <= 5; $i++): ?>
                        <img loading="lazy" src="images/icon/star-<?php echo $i <= $avgRounded ? 'yellow' : 'grey'; ?>.png" alt="star" class="img-fluid star-icon">
                     <?php endfor; ?>
                  </div>
                  <div class="productPara">
                     <h6>Size : 3 KG</h6>
                     <p>Bovans Browns are a hardy, high-yield layer breed raised in our clean, well-ventilated poultry houses. Our facilities include separate brooding, growing and laying sheds, cold storage for hatching eggs, and on-site feed and disease-testing capacity.</p>
                  </div>
                  <div class="leftStock">
                     <p>Only <span>45 Items</span> Left In stock!</p>
                  </div>
                  <div class="stockLeftprogress">
                     <div class="progress"><div class="progress-bar" style="width:70%"></div></div>
                  </div>
                  <div class="productQty">
                     <div class="add-cart-container">
                        <a class="btnCustom5 btn-1 hover-slide-down" href="contact-us.php">
                           <span>Enquire About This Product <img loading="lazy" src="images/icon/icon-right.png" alt="right" class="img-fluid"></span>
                        </a>
                        <div class="likeProduct">
                           <a href="javascript:void(0);">
                              <svg width="25" height="23" viewBox="0 0 25 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M12.5 22.9375L10.6875 21.2875C4.25 15.45 0 11.5875 0 6.875C0 3.0125 3.025 0 6.875 0C9.05 0 11.1375 1.0125 12.5 2.6C13.8625 1.0125 15.95 0 18.125 0C21.975 0 25 3.0125 25 6.875C25 11.5875 20.75 15.45 14.3125 21.2875L12.5 22.9375Z" fill="white" />
                              </svg>
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="guaranteedInfo">
                     <div class="guaranteedInfoItem">
                        <div class="guaranteedInfoImg"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                        <div class="guaranteedInfoTxt"><p>Available for pickup at the farm in Amuca, Lira City</p></div>
                     </div>
                     <div class="guaranteedInfoItem">
                        <div class="guaranteedInfoImg"><i class="fa fa-truck" aria-hidden="true"></i></div>
                        <div class="guaranteedInfoTxt"><p>Contact us to arrange delivery or collection timing</p></div>
                     </div>
                     <hr class="HorizLine">
                     <div class="sku"><p><span>SKU :</span> BOVANS-001</p></div>
                     <div class="tagsShareGroup">
                        <div class="tags"><p><span>Tags :</span> Chicken, Eggs, Poultry, Layer</p></div>
                        <div class="share">
                           <div class="shareInner">
                              <span>Share :</span>
                              <div class="productSocialIcon">
                                 <a href="javascript:void(0);" class="instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                                 <a href="javascript:void(0);" class="whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                                 <a href="javascript:void(0);" class="facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                 <a href="javascript:void(0);" class="twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="tabSection" id="reviews">
         <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#description">Description</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#reviewsPane">Reviews(<span id="reviewsTabCount"><?php echo $reviewCount; ?></span>)</a></li>
         </ul>

         <div class="tab-content">
            <div class="tab-pane active" id="description">
               <div class="descriptionInner">
                  <p>Bovans Browns are one of the most reliable brown-egg layer breeds we keep at City Gate Mixed Farm — calm temperament, strong feed conversion, and consistent lay rates from point-of-lay onward.</p>
                  <ul>
                     <li>Reaches point-of-lay at approximately 18-20 weeks</li>
                     <li>Strong brown shell quality, consistent size</li>
                     <li>Well suited to Northern Uganda's climate</li>
                     <li>Vaccinated and health-checked before sale</li>
                  </ul>
                  <p>Available as day-old chicks, growers, or point-of-lay birds — contact us for current availability and bulk pricing.</p>
               </div>
            </div>
            <div class="tab-pane fade" id="reviewsPane">
               <div class="reviewsInner">
                  <div class="commentBox">
                     <div class="commentBoxInner">
                        <div class="commentBoxGroup">
                           <div class="commentBoxHeading">
                              <h2 id="reviewsHeading"><?php echo $reviewCount; ?> Review<?php echo $reviewCount === 1 ? '' : 's'; ?></h2>
                           </div>

                           <?php if ($reviewErrors): ?>
                              <div class="review-errors">
                                 <ul>
                                    <?php foreach ($reviewErrors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
                                 </ul>
                              </div>
                           <?php endif; ?>

                           <div class="commentGroup" id="reviewsList">
                              <?php if (empty($reviews)): ?>
                                 <p style="color:#888;">No reviews yet — be the first to review this product.</p>
                              <?php endif; ?>
                              <?php foreach ($reviews as $r):
                                 $rInitialsParts = preg_split('/\s+/', trim($r['name']));
                                 $rInitials = '';
                                 foreach (array_slice($rInitialsParts, 0, 2) as $rp) { if ($rp !== '') $rInitials .= mb_strtoupper(mb_substr($rp, 0, 1)); }
                                 if ($rInitials === '') $rInitials = '?';
                              ?>
                              <div class="commentItem mb-4">
                                 <div class="commentPost">
                                    <div class="userProfile">
                                       <div style="width:100px;height:100px;border-radius:50px;background:var(--cg-green,#2f5d3a);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:28px;"><?php echo htmlspecialchars($rInitials); ?></div>
                                    </div>
                                    <div class="postContent">
                                       <div class="postGroup">
                                          <div class="userName">
                                             <p><?php echo htmlspecialchars($r['name']); ?></p> <span><?php echo htmlspecialchars(date('d/m/Y \a\t g:i a', strtotime($r['created_at']))); ?></span>
                                          </div>
                                          <div class="reviewStar">
                                             <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa fa-star<?php echo $i <= (int) $r['rating'] ? '' : '-o'; ?>" aria-hidden="true"></i>
                                             <?php endfor; ?>
                                          </div>
                                       </div>
                                       <div class="userComment">
                                          <p><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <?php endforeach; ?>
                           </div>
                        </div>
                        <div class="leaveComment">
                           <div class="commentBoxHeading">
                              <h2>Add a review</h2>
                           </div>
                           <div class="leaveCommentForm">
                              <form id="reviewForm" method="post" action="shop-single.php#reviews">
                                 <?php echo cg_csrf_field(); ?>
                                 <input type="hidden" name="review_submit" value="1">
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="formGroup">
                                          <label for="fname" class="form-label">Your Name <span>*</span></label>
                                          <input type="text" class="form-control" id="fname" placeholder="Your Name" name="fname" required>
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="formGroup">
                                          <label for="email" class="form-label">Email Address</label>
                                          <input type="email" class="form-control" id="email" placeholder="Email Address" name="email">
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="formGroup">
                                          <label class="form-label">Your Rating <span>*</span></label>
                                          <div id="full-stars-example-two">
                                             <div class="rating-group">
                                                <input disabled checked class="rating__input rating__input--none" name="rating" id="rating-none" value="0" type="radio">
                                                <label aria-label="1 star" class="rating__label" for="rating-1"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                                <input class="rating__input" name="rating" id="rating-1" value="1" type="radio">
                                                <label aria-label="2 stars" class="rating__label" for="rating-2"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                                <input class="rating__input" name="rating" id="rating-2" value="2" type="radio">
                                                <label aria-label="3 stars" class="rating__label" for="rating-3"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                                <input class="rating__input" name="rating" id="rating-3" value="3" type="radio">
                                                <label aria-label="4 stars" class="rating__label" for="rating-4"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                                <input class="rating__input" name="rating" id="rating-4" value="4" type="radio">
                                                <label aria-label="5 stars" class="rating__label" for="rating-5"><i class="rating__icon rating__icon--star fa fa-star"></i></label>
                                                <input class="rating__input" name="rating" id="rating-5" value="5" type="radio">
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="formGroup">
                                          <label for="message" class="form-label">Your Comment <span>*</span></label>
                                          <textarea class="form-control" rows="5" id="message" name="message" placeholder="Your Comment" required></textarea>
                                       </div>
                                    </div>
                                 </div>
                                 <button type="submit" class="btnCustom5 btn-1 hover-slide-down"><span>Submit <img loading="lazy" src="images/icon/icon-right.png" alt="right" class="img-fluid"></span></button>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!--Shop Single end-->

<!--Related Products start-->
<section class="w-100 clearfix poultryProductsSlider" id="poultryProductsSlider">
   <div class="container">
      <div class="poultryProductsHead">
         <div class="commonHeading"><h4>Related Products</h4></div>
         <div class="owlNavBtn">
            <div class="btn-wrap">
               <button class="prev-btn"><img loading="lazy" src="images/icon/icon-left.png" alt="left" class="img-fluid"></button>
               <button class="next-btn"><img loading="lazy" src="images/icon/icon-right.png" alt="right" class="img-fluid"></button>
            </div>
         </div>
      </div>
      <div class="owl-carousel owl-theme owlCarouselProducts">
         <?php
         $related = [
            ['img' => 'images/gallary/akirevarga-egg-7345934.jpg', 'name' => 'White & Brown Eggs', 'badge' => 'New', 'badgeClass' => 'bg-success'],
            ['img' => 'images/gallary/9883074-chicken-3727097_1920.jpg', 'name' => 'Little Chicks', 'badge' => 'Hot', 'badgeClass' => 'bg-danger'],
            ['img' => 'images/gallary/andreasgoellner-hen-5642953_1920.jpg', 'name' => 'Bovans Browns', 'badge' => 'New', 'badgeClass' => 'bg-success'],
            ['img' => 'images/gallary/pexels-chicken-1867521_1920.jpg', 'name' => 'Chicken Broiler', 'badge' => '', 'badgeClass' => ''],
         ];
         foreach ($related as $rp):
         ?>
         <div class="item">
            <div class="owlSlideItem">
               <div class="owlSlideItemImg">
                  <div class="sliderIconMenu">
                     <?php if ($rp['badge'] !== ''): ?>
                     <div class="sliderHotBadge"><a href="javascript:void(0);" class="badge <?php echo htmlspecialchars($rp['badgeClass']); ?>"><?php echo htmlspecialchars($rp['badge']); ?></a></div>
                     <?php endif; ?>
                     <div class="heartIcon"><a href="javascript:void(0);" class="heart"><i class="fa fa-heart" aria-hidden="true"></i></a></div>
                  </div>
                  <img loading="lazy" src="<?php echo htmlspecialchars($rp['img']); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>" class="img-fluid">
               </div>
               <div class="owlSlideItemContent">
                  <div class="owlProductHeading"><h4><?php echo htmlspecialchars($rp['name']); ?></h4></div>
                  <a href="contact-us.php" style="font-size:12px;font-weight:700;color:var(--cg-green,#2f5d3a);">Enquire &rarr;</a>
               </div>
            </div>
         </div>
         <?php endforeach; ?>
      </div>
   </div>
</section>
<!--Related Products end-->

<?php
$extraScripts = ['js/shop-single.js'];
require __DIR__ . '/includes/footer.php';
?>
