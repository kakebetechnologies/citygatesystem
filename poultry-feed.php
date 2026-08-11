<?php
/**
 * Poultry (Chicken) service page. Converted from poultry-feed.html into the
 * shared-include structure. Static content — no DB queries.
 */
$pageTitle       = 'City Gate Poultry | Fresh Eggs & Chicks';
$pageDescription = 'City Gate Mixed Farm poultry — 14,000+ healthy birds producing fresh eggs, broiler meat and vaccinated chicks in Lira City, Uganda.';
$activeNav       = 'services';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .poultry-hero {
      position: relative;
      height: 85vh;
      min-height: 600px;
      background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('images/gallary/pexels-chicken-1867521_1920.jpg');
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      overflow: hidden;
   }
   .poultry-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(34,139,34,0.3) 0%, rgba(0,0,0,0.4) 100%);
   }
   .poultry-hero .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #fff;
   }
   .poultry-hero h1 {
      font-size: 4rem;
      font-weight: 800;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
   }
   .poultry-hero p {
      font-size: 1.4rem;
      margin-bottom: 2rem;
      opacity: 0.95;
   }
   .stat-box {
      background: linear-gradient(135deg, #228B22 0%, #32CD32 100%);
      border-radius: 20px;
      padding: 2rem;
      text-align: center;
      color: #fff;
      transition: transform 0.3s ease;
      box-shadow: 0 10px 30px rgba(34,139,34,0.3);
   }
   .stat-box:hover {
      transform: translateY(-10px);
   }
   .stat-box h3 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
   }
   .stat-box p {
      margin: 0;
      font-size: 1rem;
      opacity: 0.9;
   }
   .product-card {
      border-radius: 20px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      transition: all 0.4s ease;
      height: 100%;
   }
   .product-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
   }
   .product-card img {
      height: 250px;
      object-fit: cover;
      width: 100%;
   }
   .product-card .card-body {
      padding: 1.5rem;
   }
   .product-card h5 {
      color: #228B22;
      font-weight: 700;
      margin-bottom: 0.5rem;
   }
   .product-card .price {
      font-size: 1.5rem;
      font-weight: 700;
      color: #228B22;
      margin-bottom: 1rem;
   }
   .feature-card {
      background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
      border-radius: 20px;
      padding: 2rem;
      text-align: center;
      height: 100%;
      transition: all 0.4s ease;
      border: 2px solid transparent;
   }
   .feature-card:hover {
      border-color: #228B22;
      transform: translateY(-8px);
   }
   .feature-card .icon-wrapper {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #228B22 0%, #32CD32 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
   }
   .feature-card .icon-wrapper i {
      font-size: 2rem;
      color: #fff;
   }
   .feature-card h5 {
      font-weight: 700;
      margin-bottom: 0.5rem;
   }
   .cta-section {
      background: linear-gradient(135deg, #228B22 0%, #1a6b1a 100%);
      border-radius: 30px;
      padding: 4rem;
      text-align: center;
      color: #fff;
      position: relative;
      overflow: hidden;
   }
   .cta-section::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 400px;
      height: 400px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
   }
   .cta-section h2 {
      margin-bottom: 1rem;
   }
   .btn-custom {
      padding: 1rem 2.5rem;
      border-radius: 50px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
   }
   .btn-custom-light {
      background: #fff;
      color: #228B22;
   }
   .btn-custom-light:hover {
      background: #f0f0f0;
      transform: translateY(-3px);
   }
   .btn-custom-outline {
      border: 2px solid #fff;
      color: #fff;
      background: transparent;
   }
   .btn-custom-outline:hover {
      background: #fff;
      color: #228B22;
   }
   .owl-nav .owl-prev,
   .owl-nav .owl-next {
      background: #228B22 !important;
      color: #fff !important;
      width: 50px;
      height: 50px;
      border-radius: 50% !important;
   }
   @media (max-width: 768px) {
      .poultry-hero h1 {
         font-size: 2.5rem;
      }
      .cta-section {
         padding: 2rem;
      }
   }
</style>

<!-- Hero Section -->
<section class="poultry-hero">
   <div class="container">
      <div class="hero-content wow fadeInUp">
         <h1>Fresh Poultry, Thriving Lives</h1>
         <p>14,000+ birds producing fresh eggs and quality chicks for Lira City</p>
         <a href="shop.php" class="btn btn-custom btn-custom-light me-3">Buy Chicks</a>
         <a href="contact-us.php" class="btn btn-custom btn-custom-outline">Visit Farm</a>
      </div>
   </div>
</section>

<!-- Stats Section -->
<section class="py-5">
   <div class="container">
      <div class="row g-4">
         <div class="col-md-4">
            <div class="stat-box wow fadeInUp" data-wow-delay="0.1s">
               <h3>14,000+</h3>
               <p>Healthy Birds</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="stat-box wow fadeInUp" data-wow-delay="0.2s">
               <h3>80L</h3>
               <p>Daily Milk Production</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="stat-box wow fadeInUp" data-wow-delay="0.3s">
               <h3>100%</h3>
               <p>Vaccinated & Healthy</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Products Carousel -->
<section class="py-5" style="background: #f8fff8;">
   <div class="container">
      <div class="text-center mb-5 wow fadeInUp">
         <h2 style="color: #228B22; font-weight: 700;">Our Poultry Products</h2>
         <p>Farm-raised, healthy, and ready for you</p>
      </div>
      <div class="owl-carousel owl-theme" id="productCarousel">
         <div class="item">
            <div class="product-card">
               <img src="images/gallary/9883074-chicken-3727097_1920.jpg" alt="Broilers">
               <div class="card-body">
                  <h5>Broiler Chicken</h5>
                  <p class="text-muted">Ready for market in 6 weeks</p>
                  <div class="price">UGX 25,000</div>
                  <a href="shop.php" class="btn btn-success w-100">Order Now</a>
               </div>
            </div>
         </div>
         <div class="item">
            <div class="product-card">
               <img src="images/gallary/andreasgoellner-hen-5642953_1920.jpg" alt="Layers">
               <div class="card-body">
                  <h5>Fresh Eggs</h5>
                  <p class="text-muted">Collected daily, super fresh</p>
                  <div class="price">UGX 12,000/tray</div>
                  <a href="shop.php" class="btn btn-success w-100">Order Now</a>
               </div>
            </div>
         </div>
         <div class="item">
            <div class="product-card">
               <img src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="Chicks">
               <div class="card-body">
                  <h5>1-Month Chicks</h5>
                  <p class="text-muted">Vaccinated & healthy</p>
                  <div class="price">UGX 10,000</div>
                  <a href="shop.php" class="btn btn-success w-100">Order Now</a>
               </div>
            </div>
         </div>
         <div class="item">
            <div class="product-card">
               <img src="images/gallary/engin_akyurt-egg-6757508_1920.jpg" alt="Dual Purpose">
               <div class="card-body">
                  <h5>Dual Purpose</h5>
                  <p class="text-muted">Eggs + meat breed</p>
                  <div class="price">UGX 15,000</div>
                  <a href="shop.php" class="btn btn-success w-100">Order Now</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Features -->
<section class="py-5">
   <div class="container">
      <div class="row g-4">
         <div class="col-md-4">
            <div class="feature-card wow fadeInUp" data-wow-delay="0.1s">
               <div class="icon-wrapper"><i class="fa fa-shield"></i></div>
               <h5>Fully Vaccinated</h5>
               <p class="text-muted">Newcastle, Gumboro & more — full protection</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="feature-card wow fadeInUp" data-wow-delay="0.2s">
               <div class="icon-wrapper"><i class="fa fa-leaf"></i></div>
               <h5>Quality Feed</h5>
               <p class="text-muted">Home-grown maize & balanced nutrition</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="feature-card wow fadeInUp" data-wow-delay="0.3s">
               <div class="icon-wrapper"><i class="fa fa-truck"></i></div>
               <h5>Delivery Available</h5>
               <p class="text-muted">Across Lira City & northern Uganda</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Why Poultry -->
<section class="py-5" style="background: linear-gradient(135deg, #1a5c1a 0%, #228B22 100%);">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-lg-6 text-white wow fadeInLeft">
            <h2 class="mb-4">Why Poultry Farming?</h2>
            <p class="mb-4" style="opacity: 0.9;">Fast returns, growing demand, easy to scale. Broilers ready in 6 weeks. Layers start producing in months.</p>
            <ul class="list-unstyled">
               <li class="mb-3"><i class="fa fa-check-circle me-2"></i> Quick capital cycle — fast profits</li>
               <li class="mb-3"><i class="fa fa-check-circle me-2"></i> High demand in Lira City</li>
               <li class="mb-3"><i class="fa fa-check-circle me-2"></i> Perfect for youth & beginners</li>
               <li class="mb-3"><i class="fa fa-check-circle me-2"></i> Learn record-keeping skills</li>
            </ul>
         </div>
         <div class="col-lg-6">
            <img src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="Poultry" class="img-fluid rounded-4 shadow-lg wow fadeInRight">
         </div>
      </div>
   </div>
</section>

<!-- CTA Section -->
<section class="py-5">
   <div class="container">
      <div class="cta-section wow fadeInUp">
         <h2>Start Your Poultry Journey Today</h2>
         <p class="mb-4">Whether you're buying for your home or starting a farm — we have you covered</p>
         <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="shop.php" class="btn btn-custom btn-custom-light">Buy Chicks & Eggs</a>
            <a href="contact-us.php" class="btn btn-custom btn-custom-outline">Apply for Training</a>
         </div>
      </div>
   </div>
</section>

<script>
   document.addEventListener('DOMContentLoaded', function () {
      new WOW().init();
      $('#productCarousel').owlCarousel({
         loop: true,
         margin: 20,
         nav: true,
         autoplay: true,
         autoplayTimeout: 4000,
         responsive: {
            0: { items: 1 },
            768: { items: 2 },
            1024: { items: 4 }
         }
      });
   });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
