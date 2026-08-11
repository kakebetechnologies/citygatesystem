<?php
/**
 * Goat Farming service page. Converted from broiler-breeder.html into the
 * shared-include structure. Static content — no DB queries.
 */
$pageTitle       = 'City Gate Goats | Boer & Savannah Breeding';
$pageDescription = 'City Gate Mixed Farm goat breeding — premium Boer and Savannah goats plus cross-breeding services for Northern Uganda.';
$activeNav       = 'services';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .breed-section {
      padding: 56px 0;
   }
   .breed-card {
      background: #fff;
      border-radius: 30px;
      overflow: hidden;
      box-shadow: var(--cg-shadow);
      margin-bottom: 30px;
      transition: all 0.4s ease;
   }
   .breed-card:hover {
      transform: translateY(-10px);
      box-shadow: var(--cg-shadow);
   }
   .breed-card img {
      height: 350px;
      object-fit: cover;
      width: 100%;
   }
   .breed-card .content {
      padding: 2rem;
   }
   .breed-card h3 {
      color: #8B4513;
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 1rem;
   }
   .breed-card .price-tag {
      display: inline-block;
      background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
      color: #fff;
      padding: 0.5rem 1.5rem;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 1rem;
   }
   .breed-list {
      list-style: none;
      padding: 0;
   }
   .breed-list li {
      padding: 0.5rem 0;
      border-bottom: 1px dashed #ddd;
   }
   .breed-list li:last-child {
      border-bottom: none;
   }
   .breed-list li i {
      color: #8B4513;
      margin-right: 10px;
   }
   .horizontal-scroll {
      display: flex;
      overflow-x: auto;
      gap: 20px;
      padding: 20px 0;
      scrollbar-width: thin;
   }
   .horizontal-scroll::-webkit-scrollbar {
      height: 8px;
   }
   .horizontal-scroll::-webkit-scrollbar-thumb {
      background: #8B4513;
      border-radius: 4px;
   }
   .scroll-item {
      min-width: 300px;
      flex-shrink: 0;
   }
   .scroll-item img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 20px;
   }
   .crossbreeding-banner {
      background: linear-gradient(135deg, #2c1810 0%, #4a2c20 100%);
      border-radius: 30px;
      padding: 4rem;
      color: #fff;
      position: relative;
      overflow: hidden;
   }
   .crossbreeding-banner::before {
      content: '';
      position: absolute;
      top: -30%;
      right: -10%;
      width: 300px;
      height: 300px;
      background: rgba(210, 105, 30, 0.3);
      border-radius: 50%;
   }
   .crossbreeding-banner h2 {
      color: #D2691E;
      margin-bottom: 1rem;
   }
   .benefit-card {
      background: linear-gradient(135deg, #fff 0%, #faf6f0 100%);
      border: 2px solid #8B4513;
      border-radius: 20px;
      padding: 1.5rem;
      text-align: center;
      height: 100%;
      transition: all 0.3s ease;
   }
   .benefit-card:hover {
      background: #8B4513;
      color: #fff;
      transform: scale(1.05);
   }
   .benefit-card i {
      font-size: 2.5rem;
      color: #8B4513;
      margin-bottom: 1rem;
   }
   .benefit-card:hover i {
      color: #fff;
   }
   .benefit-card h5 {
      font-weight: 700;
   }
   .btn-brown {
      background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
      color: #fff;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
   }
   .btn-brown:hover {
      transform: translateY(-3px);
      box-shadow: var(--cg-shadow);
      color: #fff;
   }
   .btn-outline-brown {
      border: 2px solid #8B4513;
      color: #8B4513;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      background: transparent;
      transition: all 0.3s ease;
   }
   .btn-outline-brown:hover {
      background: #8B4513;
      color: #fff;
   }
   @media (max-width: 768px) {
      .crossbreeding-banner { padding: 2rem; }
   }
</style>

<!-- ═══════════════ PAGE BANNER ═══════════════ -->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">City Gate Mixed Farm</span>
      <h1>Premium Goat Breeding</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Goat Farming</li>
      </ul>
      <p class="mt-3 mb-4 wow fadeInUp">Boer & Savannah goats — superior genetics for Northern Uganda</p>
      <div class="d-flex flex-wrap justify-content-center gap-3 wow fadeInUp">
         <a href="shop.php" class="btn btn-brown">Buy Goats</a>
         <a href="contact-us.php" class="btn btn-outline-brown">Cross-Breeding Service</a>
      </div>
   </div>
</section>

<!-- Intro Paragraphs -->
<section class="py-5">
   <div class="container">
      <div class="row">
         <div class="col-lg-10">
            <h2 class="mb-4" style="color: #8B4513; font-weight: 700;">Goat Farming at City Gate Farm</h2>
            <p class="lead mb-4" style="font-size: 1.1rem; line-height: 1.8; color: #555; text-align: left;">We farm <strong>Boer</strong> and <strong>Savannah</strong> goats — two of Africa's most prized meat breeds, originally from South Africa. Boer goats are known for their heavy frames, rapid growth, and superior carcass quality. The all-white Savannah breed is celebrated for hardiness, strong maternal instincts, and adaptation to hot, dry climates. Both breeds thrive in Northern Uganda conditions.</p>
            <p class="lead" style="font-size: 1.1rem; line-height: 1.8; color: #555; text-align: left;">Quality meat goats are in high demand across northern Uganda and the wider East African region. Our herd provides premium breeding stock and cross-breeding services to help local farmers upgrade their herds for better growth rates, carcass quality, and market value.</p>
         </div>
      </div>
   </div>
</section>

<!-- Why Goat Farming - Features -->
<section class="py-5" style="background: linear-gradient(135deg, #faf6f0 0%, #f5ebe0 100%);">
   <div class="container">
      <h2 class="text-center mb-5" style="color: #8B4513; font-weight: 700;">Why Goat Farming?</h2>
      <div class="row g-4">
         <div class="col-md-4">
            <div class="benefit-card wow fadeInUp" data-wow-delay="0.1s">
               <i class="fa fa-money"></i>
               <h5>High Returns</h5>
               <p>Goats sell for UGX 600K+ — quick market turnaround in 8 months</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="benefit-card wow fadeInUp" data-wow-delay="0.2s">
               <i class="fa fa-sun-o"></i>
               <h5>Drought-Tolerant</h5>
               <p>Perfect for Northern Uganda's climate — browse on shrubs & pasture</p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="benefit-card wow fadeInUp" data-wow-delay="0.3s">
               <i class="fa fa-star"></i>
               <h5>Constant Demand</h5>
               <p>Weddings, festivals, restaurants — premium prices for quality meat</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Gallery Scroll -->
<section class="py-5">
   <div class="container">
      <h2 class="text-center mb-4" style="color: #8B4513; font-weight: 700;">Our Herd Gallery</h2>
      <div class="horizontal-scroll wow fadeInUp">
         <div class="scroll-item">
            <img src="images/gallary/walter46-goat-4138049_1920.jpg" alt="Boer Goats">
         </div>
         <div class="scroll-item">
            <img src="images/gallary/rschaubhut-goat-5642612_1920.jpg" alt="Savannah Goats">
         </div>
         <div class="scroll-item">
            <img src="images/gallary/bernhardjaeck-billy-goat-7397721_1920.jpg" alt="Billy Goat">
         </div>
         <div class="scroll-item">
            <img src="images/gallary/techvaran-goats-5902053_1920.jpg" alt="Goat Herd">
         </div>
         <div class="scroll-item">
            <img src="images/gallary/engin_akyurt-egg-6757508_1920.jpg" alt="Goat 5">
         </div>
      </div>
   </div>
</section>

<!-- Boer Breed -->
<section class="breed-section">
   <div class="container">
      <div class="breed-card wow fadeInUp">
         <div class="row g-0">
            <div class="col-lg-6">
               <img src="images/gallary/walter46-goat-4138049_1920.jpg" alt="Boer Goats">
            </div>
            <div class="col-lg-6">
               <div class="content">
                  <span class="price-tag">From UGX 600,000</span>
                  <h3>Boer Goats</h3>
                  <p class="text-muted mb-4">Heavy-framed, fast-growing meat breed. Excellent feed conversion and carcass quality.</p>
                  <ul class="breed-list">
                     <li><i class="fa fa-check"></i> Ready at 8 months</li>
                     <li><i class="fa fa-check"></i> Superior meat yield</li>
                     <li><i class="fa fa-check"></i> Good for stud breeding</li>
                     <li><i class="fa fa-check"></i> Health-checked & ear-tagged</li>
                  </ul>
                  <a href="shop.php" class="btn btn-brown mt-3">Order Boer Goat</a>
               </div>
            </div>
         </div>
      </div>

      <!-- Savannah Breed -->
      <div class="breed-card wow fadeInUp">
         <div class="row g-0">
            <div class="col-lg-6 order-lg-2">
               <img src="images/gallary/rschaubhut-goat-5642612_1920.jpg" alt="Savannah Goats">
            </div>
            <div class="col-lg-6 order-lg-1">
               <div class="content">
                  <span class="price-tag">From UGX 600,000</span>
                  <h3>Savannah (White) Goats</h3>
                  <p class="text-muted mb-4">Hardy, all-white meat breed. Thrives in hot, dry conditions.</p>
                  <ul class="breed-list">
                     <li><i class="fa fa-check"></i> Excellent maternal qualities</li>
                     <li><i class="fa fa-check"></i> Low kid mortality</li>
                     <li><i class="fa fa-check"></i> Drought-tolerant</li>
                     <li><i class="fa fa-check"></i> Clean white carcass</li>
                  </ul>
                  <a href="shop.php" class="btn btn-brown mt-3">Order Savannah Goat</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- Cross Breeding -->
<section class="py-5">
   <div class="container">
      <div class="crossbreeding-banner wow fadeInUp">
         <div class="row align-items-center">
            <div class="col-lg-8">
               <h2>Cross-Breeding Service</h2>
               <p class="mb-4">Upgrade your local herd with Boer or Savannah genetics. Better weight gain, bigger carcasses, higher market value — within one generation.</p>
               <ul class="list-unstyled mb-4">
                  <li class="mb-2"><i class="fa fa-star"></i> Fast-growing market: weddings, festivals, restaurants</li>
                  <li class="mb-2"><i class="fa fa-star"></i> Low maintenance — browse on shrubs & pasture</li>
                  <li class="mb-2"><i class="fa fa-star"></i> Drought-tolerant for Northern Uganda</li>
               </ul>
            </div>
            <div class="col-lg-4 text-lg-end">
               <a href="contact-us.php" class="btn btn-brown">Enquire Now</a>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- CTA -->
<section class="py-5">
   <div class="container">
      <div class="text-center">
         <h2 class="mb-4" style="color: #8B4513; font-weight: 700;">Ready to Start?</h2>
         <p class="mb-4">Buy goats, book cross-breeding, or visit the farm</p>
         <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="shop.php" class="btn btn-brown">Buy Goats</a>
            <a href="contact-us.php" class="btn btn-outline-brown">Book a Visit</a>
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
