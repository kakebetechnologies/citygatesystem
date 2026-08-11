<?php
/**
 * Dairy (Cows) service page. Converted from layer-breeders.html into the
 * shared-include structure. Static content — no DB queries.
 */
$pageTitle       = 'City Gate Dairy | Fresh Milk from Our Farm';
$pageDescription = 'City Gate Mixed Farm dairy — 80 litres of fresh milk daily from a well-cared-for herd in Amuca, Lira City, Uganda.';
$activeNav       = 'services';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .milk-card {
      background: linear-gradient(135deg, #fff 0%, #f0f8ff 100%);
      border-radius: 25px;
      padding: 2rem;
      text-align: center;
      height: 100%;
      box-shadow: var(--cg-shadow);
      transition: all 0.4s ease;
      border: 2px solid transparent;
   }
   .milk-card:hover {
      transform: translateY(-10px);
      border-color: #0066cc;
   }
   .milk-card .icon-circle {
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, #0066cc 0%, #00aaff 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
   }
   .milk-card .icon-circle i {
      font-size: 2.5rem;
      color: #fff;
   }
   .milk-card h4 {
      color: #0066cc;
      font-weight: 700;
      margin-bottom: 0.5rem;
   }
   .timeline-section {
      position: relative;
      padding: 60px 0;
   }
   .timeline-section::before {
      content: '';
      position: absolute;
      left: 50%;
      top: 0;
      bottom: 0;
      width: 4px;
      background: linear-gradient(to bottom, #0066cc, #00aaff);
      transform: translateX(-50%);
   }
   .timeline-item {
      display: flex;
      justify-content: flex-end;
      padding-right: 50%;
      position: relative;
      margin-bottom: 50px;
   }
   .timeline-item:nth-child(even) {
      justify-content: flex-start;
      padding-right: 0;
      padding-left: 50%;
   }
   .timeline-item::before {
      content: '';
      position: absolute;
      left: 50%;
      top: 20px;
      width: 20px;
      height: 20px;
      background: #0066cc;
      border-radius: 50%;
      transform: translateX(-50%);
      box-shadow: 0 0 0 5px #fff;
   }
   .timeline-content {
      background: #fff;
      border-radius: 20px;
      padding: 1.5rem;
      max-width: 400px;
      margin-right: 30px;
      box-shadow: var(--cg-shadow);
   }
   .timeline-item:nth-child(even) .timeline-content {
      margin-right: 0;
      margin-left: 30px;
   }
   .timeline-content h4 {
      color: #0066cc;
      font-weight: 700;
   }
   .process-banner {
      background: linear-gradient(135deg, #004a99 0%, #0066cc 100%);
      border-radius: 30px;
      padding: 4rem;
      color: #fff;
      position: relative;
      overflow: hidden;
   }
   .process-banner::after {
      content: '';
      position: absolute;
      right: 10%;
      bottom: -50px;
      width: 200px;
      height: 200px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
   }
   .process-banner h2 {
      color: #66b3ff;
      margin-bottom: 1rem;
   }
   .product-showcase {
      position: relative;
      overflow: hidden;
   }
   .product-showcase img {
      width: 100%;
      height: 400px;
      object-fit: cover;
   }
   .product-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,50,100,0.9), transparent);
      padding: 2rem;
      color: #fff;
   }
   .product-overlay h3 {
      font-size: 2rem;
      margin-bottom: 0.5rem;
   }
   .btn-blue {
      background: linear-gradient(135deg, #0066cc 0%, #00aaff 100%);
      color: #fff;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
   }
   .btn-blue:hover {
      transform: translateY(-3px);
      box-shadow: var(--cg-shadow);
      color: #fff;
   }
   .btn-outline-blue {
      border: 2px solid #fff;
      color: #fff;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      background: transparent;
      transition: all 0.3s ease;
   }
   .btn-outline-blue:hover {
      background: #fff;
      color: #0066cc;
   }
   .stats-dairy {
      background: linear-gradient(135deg, #004a99 0%, #0066cc 100%);
      padding: 3rem;
      border-radius: 30px;
      color: #fff;
   }
   .stats-dairy h2 {
      font-size: 3.5rem;
      font-weight: 800;
   }
   @media (max-width: 768px) {
      .timeline-section::before { left: 20px; }
      .timeline-item, .timeline-item:nth-child(even) {
         padding: 0 0 0 50px;
         justify-content: flex-start;
      }
      .timeline-item::before { left: 20px; }
      .timeline-content, .timeline-item:nth-child(even) .timeline-content {
         margin: 0;
         max-width: 100%;
      }
      .process-banner { padding: 2rem; }
   }
</style>

<!-- ═══════════════ PAGE BANNER ═══════════════ -->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1>Pure Fresh Dairy</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Dairy (Cows)</li>
      </ul>
      <p class="mt-3 mb-4 wow fadeInUp">80 litres daily — from our happy cows to your home</p>
      <div class="d-flex flex-wrap justify-content-center gap-3 wow fadeInUp">
         <a href="shop.php" class="btn btn-blue">Order Milk</a>
         <a href="contact-us.php" class="btn btn-outline-blue">Visit the Dairy</a>
      </div>
   </div>
</section>

<!-- Stats -->
<section class="py-5">
   <div class="container">
      <div class="stats-dairy wow fadeInUp">
         <div class="row align-items-center">
            <div class="col-md-8">
               <h2>80 Litres/Day</h2>
               <p class="mb-0">Fresh milk from healthy, well-fed cows</p>
            </div>
            <div class="col-md-4 text-md-end">
               <a href="shop.php" class="btn btn-light btn-blue">Order Now</a>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- What We Offer -->
<section class="py-5">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #0066cc; font-weight: 700;">What We Offer</h2>
      <div class="row g-4">
         <div class="col-md-4">
            <div class="milk-card wow fadeInUp" data-wow-delay="0.1s">
               <div class="icon-circle"><i class="fa fa-tint"></i></div>
               <h4>Fresh Milk</h4>
               <p class="text-muted">Collected morning & evening. Hygienic, fresh, delivered daily.</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="milk-card wow fadeInUp" data-wow-delay="0.2s">
               <div class="icon-circle"><i class="fa fa-cubes"></i></div>
               <h4>Bulk Supply</h4>
               <p class="text-muted">Shops, cafes, schools, institutions — reliable ongoing supply.</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="milk-card wow fadeInUp" data-wow-delay="0.3s">
               <div class="icon-circle"><i class="fa fa-graduation-cap"></i></div>
               <h4>Training</h4>
               <p class="text-muted">Learn dairy management — feeding, milking, record keeping.</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Timeline -->
<section class="timeline-section">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #0066cc; font-weight: 700;">From Pasture to Product</h2>
      <div class="timeline-item wow fadeInLeft">
         <div class="timeline-content">
            <h4>1. Quality Feeding</h4>
            <p class="mb-0">Napier grass, legumes, maize & minerals — balanced rations for maximum yield.</p>
         </div>
      </div>
      <div class="timeline-item wow fadeInRight">
         <div class="timeline-content">
            <h4>2. Rotational Grazing</h4>
            <p class="mb-0">Pastures recover while cows stay healthy & parasite-free.</p>
         </div>
      </div>
      <div class="timeline-item wow fadeInLeft">
         <div class="timeline-content">
            <h4>3. Clean Milking</h4>
            <p class="mb-0">Washed udders, sanitised equipment, sterile containers — every step counts.</p>
         </div>
      </div>
      <div class="timeline-item wow fadeInRight">
         <div class="timeline-content">
            <h4>4. Cool Storage</h4>
            <p class="mb-0">Milk chilled immediately to preserve freshness & quality.</p>
         </div>
      </div>
      <div class="timeline-item wow fadeInLeft">
         <div class="timeline-content">
            <h4>5. Delivery</h4>
            <p class="mb-0">Fresh to households, cafes, shops — across Lira City.</p>
         </div>
      </div>
   </div>
</section>

<!-- Showcase -->
<section class="py-5">
   <div class="container">
      <div class="product-showcase wow fadeInUp">
         <img src="images/gallary/jaclou-dl-cow-4270355_1920.jpg" alt="Dairy Herd">
         <div class="product-overlay">
            <h3>Value-Added Products Coming Soon</h3>
            <p>Yoghurt, ghee & cheese — building the full dairy chain</p>
         </div>
      </div>
   </div>
</section>

<!-- Benefits -->
<section class="py-5" style="background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #0066cc; font-weight: 700;">Why Dairy Farming?</h2>
      <div class="row g-4">
         <div class="col-md-6 col-lg-3">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
               <i class="fa fa-line-chart" style="font-size: 3rem; color: #0066cc; margin-bottom: 1rem;"></i>
               <h5>Daily Income</h5>
               <p class="text-muted">Steady cash flow every day</p>
            </div>
         </div>
         <div class="col-md-6 col-lg-3">
            <div class="text-center wow fadeInUp" data-wow-delay="0.2s">
               <i class="fa fa-heart" style="font-size: 3rem; color: #0066cc; margin-bottom: 1rem;"></i>
               <h5>Nutrition</h5>
               <p class="text-muted">Whole family benefits</p>
            </div>
         </div>
         <div class="col-md-6 col-lg-3">
            <div class="text-center wow fadeInUp" data-wow-delay="0.3s">
               <i class="fa fa-recycle" style="font-size: 3rem; color: #0066cc; margin-bottom: 1rem;"></i>
               <h5>Sustainable</h5>
               <p class="text-muted">Manure for crops</p>
            </div>
         </div>
         <div class="col-md-6 col-lg-3">
            <div class="text-center wow fadeInUp" data-wow-delay="0.4s">
               <i class="fa fa-handshake-o" style="font-size: 3rem; color: #0066cc; margin-bottom: 1rem;"></i>
               <h5>Market Ready</h5>
               <p class="text-muted">Shops & cafes need milk</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- CTA -->
<section class="py-5">
   <div class="container">
      <div class="process-banner wow fadeInUp">
         <div class="row align-items-center">
            <div class="col-lg-8">
               <h2>Start Your Dairy Journey</h2>
               <p>Order fresh milk, enquire about bulk supply, or apply for dairy training</p>
            </div>
            <div class="col-lg-4 text-lg-end">
               <a href="shop.php" class="btn btn-light me-2">Order Milk</a>
               <a href="contact-us.php" class="btn btn-outline-blue">Contact Us</a>
            </div>
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
