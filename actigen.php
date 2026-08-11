<?php
/**
 * Crops service page. Converted from actigen.html into the shared-include
 * structure. Static content — no DB queries.
 */
$pageTitle       = 'City Gate Crops | Coffee, Bananas, Maize & Pasture';
$pageDescription = 'City Gate Mixed Farm crops — coffee, bananas, maize and pasture grown across 4 hectares in Amuca, Lira City, Uganda.';
$activeNav       = 'services';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .crop-card {
      background: #fff;
      border-radius: 25px;
      overflow: hidden;
      box-shadow: var(--cg-shadow);
      transition: all 0.4s ease;
      height: 100%;
   }
   .crop-card:hover {
      transform: translateY(-15px);
      box-shadow: var(--cg-shadow);
   }
   .crop-card img {
      height: 220px;
      object-fit: cover;
      width: 100%;
   }
   .crop-card .content {
      padding: 1.5rem;
   }
   .crop-card h4 {
      color: #228B22;
      font-weight: 700;
      margin-bottom: 0.5rem;
   }
   .crop-card .tag {
      display: inline-block;
      background: #228B22;
      color: #fff;
      padding: 0.3rem 1rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      margin-bottom: 1rem;
   }
   .banana-section {
      background: linear-gradient(135deg, #fff9e6 0%, #fff3cc 100%);
      border-radius: 30px;
      padding: 3rem;
      margin: 3rem 0;
      border-left: 5px solid #FFD700;
   }
   .banana-section h3 {
      color: #B8860B;
      font-weight: 700;
      margin-bottom: 1rem;
   }
   .cofeedb-section {
      background: linear-gradient(135deg, #e6ffe6 0%, #ccffcc 100%);
      border-radius: 30px;
      padding: 3rem;
      margin: 3rem 0;
      border-left: 5px solid #32CD32;
   }
   .cofeedb-section h3 {
      color: #228B22;
      font-weight: 700;
      margin-bottom: 1rem;
   }
   .crop-showcase {
      position: relative;
      border-radius: 30px;
      overflow: hidden;
      height: 500px;
   }
   .crop-showcase img {
      width: 100%;
      height: 100%;
      object-fit: cover;
   }
   .crop-showcase .overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(0,80,0,0.8), rgba(50,205,50,0.4));
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-align: center;
   }
   .crop-showcase .overlay h2 {
      font-size: 3rem;
      font-weight: 800;
   }
   .cycle-diagram {
      display: flex;
      justify-content: space-around;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
      padding: 2rem 0;
   }
   .cycle-item {
      text-align: center;
      flex: 1;
      min-width: 150px;
   }
   .cycle-item .circle {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, #228B22 0%, #32CD32 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
   }
   .cycle-item .circle i {
      font-size: 2.5rem;
      color: #fff;
   }
   .cycle-item h5 {
      font-weight: 700;
      color: #228B22;
   }
   .cycle-arrow {
      font-size: 2rem;
      color: #228B22;
   }
   .btn-green {
      background: linear-gradient(135deg, #228B22 0%, #32CD32 100%);
      color: #fff;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
   }
   .btn-green:hover {
      transform: translateY(-3px);
      box-shadow: var(--cg-shadow);
      color: #fff;
   }
   .btn-outline-green {
      border: 2px solid #228B22;
      color: #228B22;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      background: transparent;
      transition: all 0.3s ease;
   }
   .btn-outline-green:hover {
      background: #228B22;
      color: #fff;
   }
   .feature-box {
      background: linear-gradient(135deg, #228B22 0%, #32CD32 100%);
      border-radius: 20px;
      padding: 2rem;
      color: #fff;
      text-align: center;
      height: 100%;
      transition: all 0.3s ease;
   }
   .feature-box:hover {
      transform: scale(1.05);
   }
   .feature-box h3 {
      font-size: 2.5rem;
      font-weight: 800;
   }
   @media (max-width: 768px) {
      .banana-section, .cofeedb-section { padding: 1.5rem; margin: 1.5rem 0; }
      .cycle-diagram { flex-direction: column; }
      .cycle-arrow { transform: rotate(90deg); }
   }
</style>

<!-- ═══════════════ PAGE BANNER ═══════════════ -->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1>Grown with Purpose</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Crops</li>
      </ul>
      <p class="mt-3 mb-4 wow fadeInUp">4 hectares — coffee, bananas, maize, pasture & feed crops</p>
      <div class="d-flex flex-wrap justify-content-center gap-3 wow fadeInUp">
         <a href="shop.php" class="btn btn-green">Buy Farm Produce</a>
         <a href="contact-us.php" class="btn btn-outline-green">Visit Farm</a>
      </div>
   </div>
</section>

<!-- Stats -->
<section class="py-5">
   <div class="container">
      <div class="row g-4">
         <div class="col-md-3">
            <div class="feature-box wow fadeInUp" data-wow-delay="0.1s">
               <h3>4</h3>
               <p>Hectares</p>
            </div>
         </div>
         <div class="col-md-3">
            <div class="feature-box wow fadeInUp" data-wow-delay="0.2s">
               <h3>1ha</h3>
               <p>Coffee</p>
            </div>
         </div>
         <div class="col-md-3">
            <div class="feature-box wow fadeInUp" data-wow-delay="0.3s">
               <h3>80L</h3>
               <p>Daily Milk</p>
            </div>
         </div>
         <div class="col-md-3">
            <div class="feature-box wow fadeInUp" data-wow-delay="0.4s">
               <h3>100%</h3>
               <p>Organic</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Main Crops -->
<section class="py-5">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #228B22; font-weight: 700;">What We Grow</h2>
      <div class="row g-4">
         <div class="col-md-6 col-lg-3">
            <div class="crop-card wow fadeInUp" data-wow-delay="0.1s">
               <img src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="Coffee">
               <div class="content">
                  <span class="tag">1 Hectare</span>
                  <h4>Coffee</h4>
                  <p class="text-muted">Single-origin beans for local & export markets</p>
                  <a href="shop.php" class="btn btn-sm btn-success">Buy Now</a>
               </div>
            </div>
         </div>
         <div class="col-md-6 col-lg-3">
            <div class="crop-card wow fadeInUp" data-wow-delay="0.2s">
               <img src="images/gallary/andreasgoellner-hen-5642953_1920.jpg" alt="Bananas">
               <div class="content">
                  <span class="tag">Year-Round</span>
                  <h4>Bananas</h4>
                  <p class="text-muted">Matoke & dessert varieties — for home & market</p>
                  <a href="shop.php" class="btn btn-sm btn-success">Buy Now</a>
               </div>
            </div>
         </div>
         <div class="col-md-6 col-lg-3">
            <div class="crop-card wow fadeInUp" data-wow-delay="0.3s">
               <img src="images/gallary/9883074-chicken-3727097_1920.jpg" alt="Maize">
               <div class="content">
                  <span class="tag">Feed & Grain</span>
                  <h4>Maize</h4>
                  <p class="text-muted">Poultry feed base & grain sales</p>
                  <a href="shop.php" class="btn btn-sm btn-success">Buy Now</a>
               </div>
            </div>
         </div>
         <div class="col-md-6 col-lg-3">
            <div class="crop-card wow fadeInUp" data-wow-delay="0.4s">
               <img src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="Pasture">
               <div class="content">
                  <span class="tag">Livestock Feed</span>
                  <h4>Napier Grass</h4>
                  <p class="text-muted">Elephant grass for dairy & goats</p>
                  <a href="shop.php" class="btn btn-sm btn-success">Buy Now</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Cofedb Section -->
<section class="py-4">
   <div class="container">
      <div class="cofeedb-section wow fadeInUp">
         <div class="row align-items-center">
            <div class="col-lg-8">
               <h3><i class="fa fa-leaf"></i> Cofeedb (Improved Forage Crops)</h3>
               <p class="mb-0">Our cofeedb program grows high-protein forage crops specifically designed to boost livestock productivity. These improved grasses and legumes — including <strong>Brachiaria, Chloris, Desmodium, and Lablab</strong> — are cultivated to provide nutritious feed for our dairy cows and goats. Better feed means more milk, faster growth, and healthier animals.</p>
               <ul class="mt-3 mb-0">
                  <li>High-protein content for better milk yield</li>
                  <li>Drought-tolerant varieties for year-round supply</li>
                  <li>Seeds & seedlings available for outgrowers</li>
               </ul>
            </div>
            <div class="col-lg-4 text-lg-end">
               <a href="contact-us.php" class="btn btn-green">Enquire About Seeds</a>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Banana Section -->
<section class="py-4">
   <div class="container">
      <div class="banana-section wow fadeInUp">
         <div class="row align-items-center">
            <div class="col-lg-8">
               <h3><i class="fa fa-apple"></i> Bananas — Food & Income</h3>
               <p class="mb-0">Our banana plantations include both <strong>matoke (cooking bananas)</strong> and <strong>dessert bananas</strong>. Bananas are a staple food in Uganda and provide steady income year-round. The bunches are sold to local markets in Lira City, while surplus plant material is used as livestock feed and mulch — nothing goes to waste.</p>
               <ul class="mt-3 mb-0">
                  <li>Year-round production — harvests every 8-9 months</li>
                  <li>Provides mulch & feed for livestock</li>
                  <li>Seedlings available for farmers</li>
               </ul>
            </div>
            <div class="col-lg-4 text-lg-end">
               <a href="shop.php" class="btn btn-warning" style="background: #FFD700; color: #000;">Buy Bananas</a>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Showcase -->
<section class="py-5">
   <div class="container">
      <div class="crop-showcase wow fadeInUp">
         <img src="images/gallary/engin_akyurt-egg-6757508_1920.jpg" alt="Farm Overview">
         <div class="overlay">
            <div>
               <h2>Integrated Farming</h2>
               <p>Crops feed livestock — livestock fertilise crops</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Circular Economy -->
<section class="py-5" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #228B22; font-weight: 700;">The Farm Cycle</h2>
      <div class="cycle-diagram">
         <div class="cycle-item">
            <div class="circle"><i class="fa fa-leaf"></i></div>
            <h5>Crops Grow</h5>
         </div>
         <div class="cycle-arrow"><i class="fa fa-long-arrow-right"></i></div>
         <div class="cycle-item">
            <div class="circle"><i class="fa fa-cutlery"></i></div>
            <h5>Livestock Feed</h5>
         </div>
         <div class="cycle-arrow"><i class="fa fa-long-arrow-right"></i></div>
         <div class="cycle-item">
            <div class="circle"><i class="fa fa-arrow-down"></i></div>
            <h5>Manure Returns</h5>
         </div>
         <div class="cycle-arrow"><i class="fa fa-long-arrow-right"></i></div>
         <div class="cycle-item">
            <div class="circle"><i class="fa fa-recycle"></i></div>
            <h5>Soil Enriched</h5>
         </div>
      </div>
   </div>
</section>

<!-- Sustainability -->
<section class="py-5">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #228B22; font-weight: 700;">Sustainability Practices</h2>
      <div class="row g-4">
         <div class="col-md-4">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
               <i class="fa fa-recycle" style="font-size: 3rem; color: #228B22; margin-bottom: 1rem;"></i>
               <h5>Compost Manure</h5>
               <p class="text-muted">Poultry, goat & cow waste becomes organic fertilizer</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
               <i class="fa fa-random" style="font-size: 3rem; color: #228B22; margin-bottom: 1rem;"></i>
               <h5>Crop Rotation</h5>
               <p class="text-muted">Maintains soil fertility naturally</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="text-center wow fadeInUp" data-wow-delay="0.3s">
               <i class="fa fa-pagelines" style="font-size: 3rem; color: #228B22; margin-bottom: 1rem;"></i>
               <h5>Cover Crops</h5>
               <p class="text-muted">Reduces erosion & adds nitrogen</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- CTA -->
<section class="py-5">
   <div class="container">
      <div class="text-center">
         <h2 class="mb-4" style="color: #228B22; font-weight: 700;">Get Our Farm Products</h2>
         <p class="mb-4">Coffee, bananas, maize, pasture seeds & more</p>
         <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="shop.php" class="btn btn-green">Shop Now</a>
            <a href="contact-us.php" class="btn btn-outline-green">Enquire About Seeds</a>
         </div>
      </div>
   </div>
</section>

<script>
   document.addEventListener('DOMContentLoaded', function () {
      new WOW().init();
   });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
