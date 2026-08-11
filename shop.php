<?php
/**
 * Farm Shop list. Converted from shop.html — products here are static
 * (no `products` table in the schema), enquiry-only per the existing
 * redesign direction: "Enquire to Order" links to contact-us.php, no cart.
 */
$pageTitle       = 'Farm Shop | City Gate Mixed Farm - Fresh Eggs, Chicks, Goats & More';
$pageDescription = 'Buy fresh farm products from City Gate Mixed Farm in Lira City - eggs, chicks, broiler chickens, Boer goats, fresh milk, maize, coffee and bananas. All prices in UGX.';
$activeNav       = 'shop';
require __DIR__ . '/includes/header.php';

$products = [
   [
      'name' => 'Fresh Eggs',
      'desc' => 'Tray of 30 — collected daily from our layer houses',
      'price' => 'UGX 12,000',
      'img' => 'images/gallary/akirevarga-egg-7345934.jpg',
      'badge' => 'Popular',
      'badgeColor' => '#e0a800',
   ],
   [
      'name' => 'Day-Old Chicks',
      'desc' => 'Vaccinated — ready for brooding and on-growing',
      'price' => 'UGX 10,000',
      'img' => 'images/gallary/stevepb-nest-1050964_1920.jpg',
      'badge' => 'New',
      'badgeColor' => '#2f5d3a',
   ],
   [
      'name' => 'Boer / Savannah Goats',
      'desc' => '8-month-old goats for breeding or meat',
      'price' => 'From UGX 600,000',
      'img' => 'images/gallary/rschaubhut-goat-5642612_1920.jpg',
      'badge' => 'Premium',
      'badgeColor' => '#c0392b',
   ],
   [
      'name' => 'Fresh Cow Milk',
      'desc' => 'Per litre — ~80L produced daily',
      'price' => 'UGX 2,500/litre',
      'img' => 'images/gallary/couleur-milk-2474993.jpg',
      'badge' => 'Daily',
      'badgeColor' => '#3178c6',
   ],
];
?>
<link rel="stylesheet" href="css/cg-redesign.css">

<!-- BANNER -->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">Farm Products</span>
      <h1>Farm Shop</h1>
      <p style="color:rgba(255,255,255,.85); margin-bottom:14px;">Fresh products from City Gate Mixed Farm</p>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Shop</li>
      </ul>
   </div>
</section>

<!-- PRODUCTS -->
<section class="products-section">
   <div class="container">
      <div class="text-center mb-5 fade-in">
         <p class="section-eyebrow">Our Farm</p>
         <h2 class="section-title">Our Farm Products</h2>
         <p class="section-sub" style="max-width:640px;margin:0 auto;">Fresh from our farm in Amuca, Lira City — eggs, chicks, goats, milk, and more. Contact us to order or enquire about bulk supply and delivery.</p>
      </div>

      <div class="products-grid">
         <?php foreach ($products as $p): ?>
         <div class="product-card fade-in">
            <div class="product-card-img" style="position:relative;">
               <img loading="lazy" src="<?php echo htmlspecialchars($p['img']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
               <span style="position:absolute;top:12px;right:12px;background:<?php echo htmlspecialchars($p['badgeColor']); ?>;color:#fff;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;"><?php echo htmlspecialchars($p['badge']); ?></span>
            </div>
            <div class="product-card-body">
               <h5 class="product-card-name"><?php echo htmlspecialchars($p['name']); ?></h5>
               <p style="font-size:13px;color:#777;margin-bottom:10px;min-height:38px;"><?php echo htmlspecialchars($p['desc']); ?></p>
               <div class="product-card-price"><?php echo htmlspecialchars($p['price']); ?></div>
               <a href="contact-us.php" class="product-card-btn">Enquire to Order</a>
            </div>
         </div>
         <?php endforeach; ?>
      </div>

      <div class="text-center mt-5 pt-4 border-top">
         <p class="text-muted mb-3">We also offer broilers, maize, coffee, bananas, and forage seeds. Contact us for availability and pricing.</p>
         <a href="contact-us.php" class="btn btn-outline-success">Contact Us for Full Catalogue</a>
      </div>
   </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
   <div class="container">
      <p class="cta-band-text">Prefer to see the products in person?</p>
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
