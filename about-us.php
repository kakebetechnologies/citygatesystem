<?php
/**
 * About Us page. Converted from about-us.html to the shared-include pattern.
 * The Vision & Mission section is CMS-managed (admin/cms.php, page_key =
 * 'about', fields mission_text / vision_text) — falls back to the same
 * default copy shown in the admin editor's placeholders when nothing has
 * been saved yet.
 */
$pageTitle       = 'About Us | City Gate Mixed Farm — Lira City, Uganda';
$pageDescription = 'Learn about City Gate Mixed Farm in Amuca, Lira City, Uganda. An integrated model farm producing poultry, dairy, goats and crops while training the next generation of farmers.';
$activeNav       = 'about';
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/cms.php';

$missionText = cg_cms($pdo, 'about', 'mission_text', 'To operate a profitable, sustainable integrated farm that feeds, employs, and educates Northern Uganda.');
$visionText  = cg_cms($pdo, 'about', 'vision_text', 'To be the leading model farm and agricultural training center in Northern Uganda.');
?>
<link rel="stylesheet" href="css/cg-redesign.css">

<!-- ═══════════════ PAGE BANNER ═══════════════ -->
<section class="cg-page-banner">
   <div class="container">
      <p class="section-eyebrow">City Gate Mixed Farm</p>
      <h1>About Us</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">About Us</li>
      </ul>
   </div>
</section>

<!-- ═══════════════ WHO WE ARE ═══════════════ -->
<section class="section-pad" style="background: var(--cg-white);">
   <div class="container">
      <div class="row align-items-center g-5">
         <div class="col-lg-6 fade-in">
            <div class="cg-about-collage">
               <div class="cg-about-collage__main">
                  <img src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="City Gate Mixed Farm poultry house">
               </div>
               <div class="cg-about-collage__small">
                  <img src="images/gallary/walter46-goat-4138049_1920.jpg" alt="City Gate Mixed Farm goats">
               </div>
               <div class="cg-about-collage__badge">
                  <strong><span class="counting" data-count="8">0</span></strong>
                  <span>Years Strong</span>
               </div>
            </div>
         </div>
         <div class="col-lg-6 fade-in">
            <p class="section-eyebrow">About Us</p>
            <h2 class="section-title">Welcome to City Gate Mixed Farm</h2>
            <p class="section-sub">City Gate Mixed Farm is an integrated model farm located in Amuca, Lira City, Uganda. We operate across four productive sectors &mdash; poultry, goats, dairy cows, and crops &mdash; on a 4-hectare farm designed to demonstrate profitable, sustainable agriculture for Northern Uganda.</p>
            <p style="font-size:16px; line-height:1.75; color:#666; margin-top:16px;">Beyond production, we train farmers, host school and student tours, mentor young agripreneurs, and run industrial-training placements. Our goal is a working example of modern mixed farming that feeds, employs, and educates the community.</p>

            <ul class="cg-about-checklist">
               <li><i class="fa fa-check-circle" aria-hidden="true"></i> 14,000+ poultry &mdash; broilers &amp; layers</li>
               <li><i class="fa fa-check-circle" aria-hidden="true"></i> 80 litres of fresh milk daily</li>
               <li><i class="fa fa-check-circle" aria-hidden="true"></i> Boer &amp; Savannah goat breeding</li>
               <li><i class="fa fa-check-circle" aria-hidden="true"></i> Coffee, bananas, maize &amp; pasture</li>
               <li><i class="fa fa-check-circle" aria-hidden="true"></i> Farm training &amp; internships</li>
               <li><i class="fa fa-check-circle" aria-hidden="true"></i> Guided tours &amp; school visits</li>
            </ul>

            <div style="margin-top:32px; padding-top:24px; border-top:1px solid #eee; display:flex; align-items:center; gap:20px;">
               <div>
                  <p style="font-size:15px; color:#666; margin-bottom:4px;">Visit, learn, buy or partner</p>
                  <a href="tel:+256123456789" style="font-size:18px; font-weight:700; color:var(--cg-green);">+256 123 456 789</a>
               </div>
               <a href="bookings.php" class="modern-btn" style="background:var(--cg-green); color:var(--cg-cream); border-radius:6px; padding:14px 28px; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; text-decoration:none; display:inline-block; transition:all 0.3s ease; margin-left:auto;">Book a Visit</a>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- ═══════════════ VISION & MISSION (CMS-managed) ═══════════════ -->
<section class="stats-strip" style="padding:80px 0;">
   <div class="container">
      <div class="text-center mb-5 fade-in">
         <p class="section-eyebrow">Why We Exist</p>
         <h2 class="section-title" style="color:var(--cg-cream);">Vision &amp; Mission</h2>
      </div>
      <div class="row g-4">
         <div class="col-md-6 fade-in">
            <div style="background:rgba(250,247,240,0.08); border:1px solid rgba(250,247,240,0.15); border-radius:16px; padding:36px; height:100%; transition:all 0.3s ease;" onmouseenter="this.style.background='rgba(250,247,240,0.14)'; this.style.transform='translateY(-4px)';" onmouseleave="this.style.background='rgba(250,247,240,0.08)'; this.style.transform='translateY(0)';">
               <div style="width:56px;height:56px;background:var(--cg-earth);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:24px;font-size:22px;color:var(--cg-green);">
                  <i class="fa fa-eye"></i>
               </div>
               <h3 style="font-size:22px;font-weight:700;color:var(--cg-cream);margin-bottom:14px;">Our Vision</h3>
               <p style="font-size:16px;line-height:1.75;color:rgba(250,247,240,0.82);"><?php echo htmlspecialchars($visionText); ?></p>
            </div>
         </div>
         <div class="col-md-6 fade-in">
            <div style="background:rgba(250,247,240,0.08); border:1px solid rgba(250,247,240,0.15); border-radius:16px; padding:36px; height:100%; transition:all 0.3s ease;" onmouseenter="this.style.background='rgba(250,247,240,0.14)'; this.style.transform='translateY(-4px)';" onmouseleave="this.style.background='rgba(250,247,240,0.08)'; this.style.transform='translateY(0)';">
               <div style="width:56px;height:56px;background:var(--cg-earth);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:24px;font-size:22px;color:var(--cg-green);">
                  <i class="fa fa-bullseye"></i>
               </div>
               <h3 style="font-size:22px;font-weight:700;color:var(--cg-cream);margin-bottom:14px;">Our Mission</h3>
               <p style="font-size:16px;line-height:1.75;color:rgba(250,247,240,0.82);"><?php echo htmlspecialchars($missionText); ?></p>
            </div>
         </div>
      </div>
      <!-- Stats row -->
      <div class="row g-4 mt-2" style="padding-top:32px; border-top:1px solid rgba(250,247,240,0.12);">
         <div class="col-4 text-center fade-in">
            <div class="stat-number">14%</div>
            <div class="stat-label">Poultry Capacity Used</div>
         </div>
         <div class="col-4 text-center fade-in">
            <div class="stat-number">90%</div>
            <div class="stat-label">Milk Consistency</div>
         </div>
         <div class="col-4 text-center fade-in">
            <div class="stat-number">365</div>
            <div class="stat-label">Training Days / Year</div>
         </div>
      </div>
   </div>
</section>

<!-- ═══════════════ FOUR SECTORS ═══════════════ -->
<section class="section-pad" style="background:var(--cg-cream);">
   <div class="container">
      <div class="section-head-center text-center fade-in">
         <p class="section-eyebrow">What We Produce</p>
         <h2 class="section-title">Four Farm Sectors</h2>
         <p class="section-sub">Every sector on the farm is integrated &mdash; livestock feed crops, manure enriches soil, and crops feed livestock. One farm, one ecosystem.</p>
      </div>
      <div class="sectors-grid">
         <a href="poultry-feed.php" class="sector-card fade-in">
            <img loading="lazy" src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="Poultry">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Poultry</span>
               <span class="sector-card-promise">14,000+ birds &mdash; broilers, layers &amp; eggs</span>
            </div>
         </a>
         <a href="layer-breeders.php" class="sector-card fade-in">
            <img loading="lazy" src="images/gallary/jaclou-dl-cow-4270355_1920.jpg" alt="Dairy">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Dairy</span>
               <span class="sector-card-promise">80 litres of fresh milk daily</span>
            </div>
         </a>
         <a href="broiler-breeder.php" class="sector-card fade-in">
            <img loading="lazy" src="images/gallary/walter46-goat-4138049_1920.jpg" alt="Goats">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Goats</span>
               <span class="sector-card-promise">Boer &amp; Savannah breeds</span>
            </div>
         </a>
         <a href="actigen.php" class="sector-card fade-in">
            <img loading="lazy" src="images/gallary/marcusvu-coffee-2992598_1920.jpg" alt="Crops">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Crops</span>
               <span class="sector-card-promise">Coffee, bananas, maize &amp; pasture</span>
            </div>
         </a>
      </div>
   </div>
</section>

<!-- ═══════════════ GET INVOLVED ═══════════════ -->
<section class="section-pad" style="background:var(--cg-white);">
   <div class="container">
      <div class="section-head-center text-center fade-in">
         <p class="section-eyebrow">Get Involved</p>
         <h2 class="section-title">Programs &amp; Opportunities</h2>
         <p class="section-sub">Four ways to engage with City Gate Mixed Farm &mdash; whether you are a visitor, buyer, learner, or partner.</p>
      </div>
      <div class="pillars-grid" style="margin-top:52px;">
         <div class="pillar-item fade-in" style="border-left-color:var(--cg-earth);">
            <div class="pillar-icon"><i class="fa fa-map-marker"></i></div>
            <h3 class="pillar-title">Visit the Farm</h3>
            <p class="pillar-desc">Take a guided tour of our poultry houses, goat pens, dairy unit, and crop plots. Great for schools, community groups, and individuals curious about modern farming.</p>
            <a href="bookings.php" style="display:inline-block; margin-top:16px; font-size:13px; font-weight:700; color:var(--cg-green); letter-spacing:0.5px;">Book a Visit &rarr;</a>
         </div>
         <div class="pillar-item fade-in">
            <div class="pillar-icon"><i class="fa fa-graduation-cap"></i></div>
            <h3 class="pillar-title">Learn &amp; Train</h3>
            <p class="pillar-desc">Hands-on training in poultry, goats, dairy, and crops. Internships and industrial-training placements available for agriculture students from across Uganda.</p>
            <a href="contact-us.php" style="display:inline-block; margin-top:16px; font-size:13px; font-weight:700; color:var(--cg-green); letter-spacing:0.5px;">Enquire Now &rarr;</a>
         </div>
         <div class="pillar-item fade-in">
            <div class="pillar-icon"><i class="fa fa-shopping-basket"></i></div>
            <h3 class="pillar-title">Buy Farm Products</h3>
            <p class="pillar-desc">Eggs, chicks, broiler meat, goats, fresh milk, maize, coffee and bananas &mdash; direct from the farm, priced in UGX. Bulk orders welcome.</p>
            <a href="shop.php" style="display:inline-block; margin-top:16px; font-size:13px; font-weight:700; color:var(--cg-green); letter-spacing:0.5px;">View Products &rarr;</a>
         </div>
         <div class="pillar-item fade-in" style="border-left-color:var(--cg-earth);">
            <div class="pillar-icon"><i class="fa fa-handshake-o"></i></div>
            <h3 class="pillar-title">Partner or Invest</h3>
            <p class="pillar-desc">Join us in scaling the farm from 14,000 to 100,000 birds, expanded dairy, and new crop acreage. Investor partnerships and institutional collaborations welcome.</p>
            <a href="contact-us.php" style="display:inline-block; margin-top:16px; font-size:13px; font-weight:700; color:var(--cg-green); letter-spacing:0.5px;">Let&rsquo;s Talk &rarr;</a>
         </div>
      </div>
   </div>
</section>

<!-- ═══════════════ COMMUNITY & GROWTH ═══════════════ -->
<section style="position:relative; padding:100px 0; background:url('images/gallary/couleur-milk-2474993.jpg') center/cover no-repeat;">
   <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(31,63,39,0.93) 0%,rgba(47,93,58,0.87) 100%);"></div>
   <div class="container" style="position:relative;z-index:1;">
      <div class="text-center mb-5 fade-in">
         <p class="section-eyebrow">Our Impact</p>
         <h2 class="section-title" style="color:var(--cg-cream);">Community &amp; Growth</h2>
         <p style="color:rgba(250,247,240,0.75); font-size:17px; max-width:580px; margin:0 auto;">How we&rsquo;re building the future of farming in Northern Uganda</p>
      </div>
      <div class="row g-4">
         <div class="col-md-6 fade-in">
            <div style="background:rgba(250,247,240,0.09); border:1px solid rgba(250,247,240,0.16); border-radius:16px; padding:40px; height:100%; transition:all 0.3s ease;" onmouseenter="this.style.background='rgba(250,247,240,0.15)'; this.style.transform='translateY(-3px)';" onmouseleave="this.style.background='rgba(250,247,240,0.09)'; this.style.transform='translateY(0)';">
               <h4 style="font-size:22px; font-weight:700; color:var(--cg-earth); margin-bottom:18px;">Community Impact</h4>
               <p style="color:var(--cg-cream); font-size:16px; line-height:1.75; margin-bottom:12px;">We employ locally, buy inputs from nearby suppliers, and host regular training days for smallholders across Lira and surrounding districts.</p>
               <p style="color:rgba(250,247,240,0.8); font-size:16px; line-height:1.75; margin-bottom:0;">Our student placements give agriculture learners real exposure to a working commercial farm. Every visitor, trainee and customer is part of Northern Uganda farming its own future.</p>
            </div>
         </div>
         <div class="col-md-6 fade-in">
            <div style="background:rgba(250,247,240,0.09); border:1px solid rgba(250,247,240,0.16); border-radius:16px; padding:40px; height:100%; transition:all 0.3s ease;" onmouseenter="this.style.background='rgba(250,247,240,0.15)'; this.style.transform='translateY(-3px)';" onmouseleave="this.style.background='rgba(250,247,240,0.09)'; this.style.transform='translateY(0)';">
               <h4 style="font-size:22px; font-weight:700; color:var(--cg-earth); margin-bottom:18px;">Future Expansion</h4>
               <p style="color:var(--cg-cream); font-size:16px; line-height:1.75; margin-bottom:12px;">Our roadmap includes scaling the poultry unit from 14,000 to 100,000 birds, expanding dairy capacity, and adding a value-addition line for milk products.</p>
               <p style="color:rgba(250,247,240,0.8); font-size:16px; line-height:1.75; margin-bottom:0;">We&rsquo;re also growing coffee and banana acreage, with new infrastructure &mdash; improved housing, a training centre, and a farm-gate shop &mdash; rolling out in phases.</p>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- ═══════════════ GALLERY ═══════════════ -->
<section class="section-pad" style="background:var(--cg-cream);">
   <div class="container">
      <div class="section-head-center text-center fade-in">
         <p class="section-eyebrow">Life at the Farm</p>
         <h2 class="section-title">A Visual Journey</h2>
      </div>
      <div class="sectors-grid" style="margin-top:48px;">
         <div class="sector-card fade-in" style="aspect-ratio:3/4;">
            <img loading="lazy" src="images/gallary/rooster-farm.jpg" alt="Morning Rounds">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Daily Life</span>
               <span class="sector-card-promise">Morning Rounds</span>
            </div>
         </div>
         <div class="sector-card fade-in" style="aspect-ratio:3/4;">
            <img loading="lazy" src="images/gallary/jaclou-dl-cow-4270355_1920.jpg" alt="Dairy Operations">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Dairy</span>
               <span class="sector-card-promise">Daily Milk Collection</span>
            </div>
         </div>
         <div class="sector-card fade-in" style="aspect-ratio:3/4;">
            <img loading="lazy" src="images/gallary/engin_akyurt-egg-6757508_1920.jpg" alt="Fresh Eggs">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Poultry</span>
               <span class="sector-card-promise">Fresh Eggs</span>
            </div>
         </div>
         <div class="sector-card fade-in" style="aspect-ratio:3/4;">
            <img loading="lazy" src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="Chick Nursery">
            <div class="sector-card-overlay">
               <span class="sector-card-label">Poultry</span>
               <span class="sector-card-promise">Chick Nursery</span>
            </div>
         </div>
      </div>
      <div class="text-center mt-5 fade-in">
         <a href="gallery-3-column.php" class="modern-btn" style="background:var(--cg-green); color:var(--cg-cream); border-radius:6px; padding:16px 40px; font-size:13px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; text-decoration:none; display:inline-block; transition:all 0.3s ease;">View Full Gallery</a>
      </div>
   </div>
</section>

<!-- ═══════════════ CTA BAND ═══════════════ -->
<section class="cta-band">
   <div class="container">
      <p class="cta-band-text">Come see the farm in Amuca, Lira City</p>
      <a href="bookings.php" class="modern-btn">Plan Your Visit</a>
   </div>
</section>

<!-- Sticky header on scroll + fade-in-on-scroll + counters -->
<script>
(function(){
   var hdr = document.getElementById('headerOne');
   if (hdr) {
      window.addEventListener('scroll', function(){ hdr.classList.toggle('headerActive', window.scrollY > 80); }, {passive:true});
   }
   if ('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
         entries.forEach(function(e){
            if (e.isIntersecting){ e.target.classList.add('visible'); io.unobserve(e.target); }
         });
      }, {threshold: 0.12});
      document.querySelectorAll('.fade-in').forEach(function(el){ io.observe(el); });
   } else {
      document.querySelectorAll('.fade-in').forEach(function(el){ el.classList.add('visible'); });
   }
   if ('IntersectionObserver' in window){
      var co = new IntersectionObserver(function(entries){
         entries.forEach(function(e){
            if (e.isIntersecting){ animateCount(e.target); co.unobserve(e.target); }
         });
      }, {threshold: 0.5});
      document.querySelectorAll('.counting').forEach(function(el){ co.observe(el); });
   }
   function animateCount(el){
      var target = parseInt(el.getAttribute('data-count'), 10), dur = 1800, start = null;
      function step(ts){
         if (!start) start = ts;
         var p = Math.min((ts - start) / dur, 1);
         el.textContent = Math.floor(p * target).toLocaleString();
         if (p < 1) requestAnimationFrame(step); else el.textContent = target.toLocaleString();
      }
      requestAnimationFrame(step);
   }
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
