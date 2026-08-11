<?php
/**
 * Homepage. Converted from index.html to the shared-include pattern.
 * Hero headline/sub and the "Our Impact" stats strip are CMS-managed
 * (admin/cms.php, page_key = 'home'); the "Latest from the Blog" cards
 * are pulled live from blog_posts.
 */
$pageTitle       = 'City Gate Mixed Farm - Integrated Model Farm in Lira City, Uganda';
$activeNav       = 'home';
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/cms.php';

// ---- CMS: hero headline/sub (falls back to the original hero copy) ----
$heroTitle = cg_cms($pdo, 'home', 'hero_title', 'Modern Poultry Farming');
$heroSub   = cg_cms($pdo, 'home', 'hero_sub', 'Clean housing. Organized feeding. Healthy birds.');

// ---- CMS: "Our Impact" stats strip ----
$impactYears        = cg_cms($pdo, 'home', 'impact_years', '8');
$impactYearsLabel   = cg_cms($pdo, 'home', 'impact_years_label', 'Years Operating');
$impactFarmers      = cg_cms($pdo, 'home', 'impact_farmers', '300+');
$impactFarmersLabel = cg_cms($pdo, 'home', 'impact_farmers_label', 'Farmers Trained');
$impactStudents      = cg_cms($pdo, 'home', 'impact_students', '1,200+');
$impactStudentsLabel = cg_cms($pdo, 'home', 'impact_students_label', 'Students Hosted');
$impactSectors      = cg_cms($pdo, 'home', 'impact_sectors', '4');
$impactSectorsLabel = cg_cms($pdo, 'home', 'impact_sectors_label', 'Farm Sectors');

// ---- Latest published blog posts ----
$stmt = $pdo->prepare("SELECT title, slug, excerpt, content, featured_image, publish_date
                        FROM blog_posts
                        WHERE status = 'published'
                        ORDER BY publish_date DESC
                        LIMIT 3");
$stmt->execute();
$latestPosts = $stmt->fetchAll();
?>
<link rel="stylesheet" href="css/cg-redesign.css">

<!-- HERO SLIDER -->
<section class="cg-hero" id="cgHero" aria-label="City Gate Mixed Farm">
    <div class="cg-hero__stage">
        <div class="cg-hero__slide is-active" data-slide="0">
            <div class="cg-hero__img" style="background-image: url('images/gallary/pexels-chicken-1867521_1920.jpg');"></div>
            <div class="cg-hero__overlay"></div>
            <div class="cg-hero__content">
                <div class="cg-hero__inner">
                    <span class="cg-hero__eyebrow">Poultry</span>
                    <h1 class="cg-hero__title"><?php echo htmlspecialchars($heroTitle); ?></h1>
                    <p class="cg-hero__sub"><?php echo htmlspecialchars($heroSub); ?></p>
                    <div class="cg-hero__btns">
                        <a href="poultry-feed.php" class="cg-hero__cta cg-hero__cta--solid">Explore Poultry <i class="fa fa-arrow-right"></i></a>
                        <a href="bookings.php" class="cg-hero__cta cg-hero__cta--outline">Book a Visit</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="cg-hero__slide" data-slide="1">
            <div class="cg-hero__img" style="background-image: url('images/gallary/jaclou-dl-cow-4270355_1920.jpg');"></div>
            <div class="cg-hero__overlay"></div>
            <div class="cg-hero__content">
                <div class="cg-hero__inner">
                    <span class="cg-hero__eyebrow">Dairy</span>
                    <h1 class="cg-hero__title">Fresh <strong>Daily Milk</strong></h1>
                    <p class="cg-hero__sub">Straight from a well-cared-for herd in Amuca.</p>
                    <div class="cg-hero__btns">
                        <a href="layer-breeders.php" class="cg-hero__cta cg-hero__cta--solid">View Dairy <i class="fa fa-arrow-right"></i></a>
                        <a href="shop.php" class="cg-hero__cta cg-hero__cta--outline">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="cg-hero__slide" data-slide="2">
            <div class="cg-hero__img" style="background-image: url('images/gallary/walter46-goat-4138049_1920.jpg');"></div>
            <div class="cg-hero__overlay"></div>
            <div class="cg-hero__content">
                <div class="cg-hero__inner">
                    <span class="cg-hero__eyebrow">Goats</span>
                    <h1 class="cg-hero__title">Premium <strong>Goat Breeds</strong></h1>
                    <p class="cg-hero__sub">Boer &amp; Savannah — strong, healthy, farm-raised.</p>
                    <a href="shop.php" class="cg-hero__cta">See Goats <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="cg-hero__slide" data-slide="3">
            <div class="cg-hero__img" style="background-image: url('images/gallary/marcusvu-coffee-2992598_1920.jpg');"></div>
            <div class="cg-hero__overlay"></div>
            <div class="cg-hero__content">
                <div class="cg-hero__inner">
                    <span class="cg-hero__eyebrow">Crops</span>
                    <h1 class="cg-hero__title">Sustainable <strong>Farming</strong></h1>
                    <p class="cg-hero__sub">Coffee and banana, grown on our 4-acre model farm.</p>
                    <a href="our-service.php" class="cg-hero__cta">Explore Crops <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="cg-hero__slide" data-slide="4">
            <div class="cg-hero__img" style="background-image: url('images/gallary/rooster-farm.jpg');"></div>
            <div class="cg-hero__overlay"></div>
            <div class="cg-hero__content">
                <div class="cg-hero__inner">
                    <span class="cg-hero__eyebrow">Training &amp; Visits</span>
                    <h1 class="cg-hero__title">Visit. Learn. <strong>Grow.</strong></h1>
                    <p class="cg-hero__sub">Hands-on training and farm tours in Lira City.</p>
                    <a href="contact-us.php" class="cg-hero__cta">Book a Visit <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="cg-hero__dots" id="cgHeroDots">
        <button class="cg-hero__dot is-active" data-go="0" aria-label="Slide 1"></button>
        <button class="cg-hero__dot" data-go="1" aria-label="Slide 2"></button>
        <button class="cg-hero__dot" data-go="2" aria-label="Slide 3"></button>
        <button class="cg-hero__dot" data-go="3" aria-label="Slide 4"></button>
        <button class="cg-hero__dot" data-go="4" aria-label="Slide 5"></button>
    </div>

    <a href="#cgHeroBelow" class="cg-hero__scroll" aria-label="Scroll down">Scroll</a>
</section>
<span id="cgHeroBelow"></span>
<script>
(function(){
    var root = document.getElementById('cgHero');
    if (!root) return;
    var slides = root.querySelectorAll('.cg-hero__slide');
    var dots = root.querySelectorAll('.cg-hero__dot');
    var idx = 0, total = slides.length, timer;
    function go(n){
        idx = (n + total) % total;
        slides.forEach(function(s,i){ s.classList.toggle('is-active', i === idx); });
        dots.forEach(function(d,i){ d.classList.toggle('is-active', i === idx); });
    }
    function next(){ go(idx + 1); }
    function prev(){ go(idx - 1); }
    function restart(){ clearInterval(timer); timer = setInterval(next, 6000); }
    dots.forEach(function(d){
        d.addEventListener('click', function(){ go(parseInt(d.dataset.go, 10)); restart(); });
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'ArrowRight') { next(); restart(); }
        if (e.key === 'ArrowLeft')  { prev(); restart(); }
    });
    restart();
})();
</script>

<!-- Sticky header on scroll + fade-in-on-scroll -->
<script>
(function(){
    var hdr = document.getElementById('headerOne');
    function onScroll(){
        if (window.scrollY > 80){ hdr.classList.add('headerActive'); }
        else { hdr.classList.remove('headerActive'); }
    }
    window.addEventListener('scroll', onScroll, {passive:true});
    onScroll();

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
})();
</script>

<!-- INTRO STATEMENT — high-level summary of who City Gate is -->
<section class="cg-intro-statement">
    <div class="container">
        <div class="cg-intro-statement__inner fade-in">
            <span class="cg-intro-statement__mark">Who We Are</span>
          <p class="cg-intro-statement__lead">A living model of integrated farming in Amuca, Lira City — where poultry, dairy, goats, and crops work together as one thriving ecosystem.</p>
<p>City Gate Mixed Farm is a 4‑hectare demonstration farm and training hub. We combine poultry, goats, dairy, and crops to prove sustainable agriculture works in Northern Uganda. Beyond production, we host school tours, mentor young agripreneurs, and offer hands-on training. Our mission: to build a farm that feeds, employs, and inspires — a model for the community and beyond.</p>
            <hr class="cg-intro-statement__rule">
        </div>
    </div>
</section>

<!-- WHY CITY GATE? (value proposition — bold photo-card treatment) -->
<section class="value-prop-row">
    <div class="container">
        <p class="section-eyebrow">Why City Gate?</p>
        <h2 class="section-title">Produce &middot; Train &middot; Partner</h2>
        <div class="value-prop-grid">
            <div class="value-prop-card fade-in">
                <img loading="lazy" src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="Produce">
                <div class="value-prop-overlay"></div>
                <div class="value-prop-icon"><i class="fa fa-leaf"></i></div>
                <div class="value-prop-label">
                    <h3>Produce</h3>
                    <p>Farm-fresh eggs, chicks, broiler meat, fresh milk, and premium goats. Direct from the farm, priced in UGX.</p>
                </div>
            </div>
            <div class="value-prop-card fade-in">
                <img loading="lazy" src="images/gallary/hat3m-seedling-4394118_1920.jpg" alt="Train">
                <div class="value-prop-overlay"></div>
                <div class="value-prop-icon"><i class="fa fa-graduation-cap"></i></div>
                <div class="value-prop-label">
                    <h3>Train</h3>
                    <p>Hands-on farming training, youth empowerment programs, and industrial placements for agriculture students.</p>
                </div>
            </div>
            <div class="value-prop-card fade-in">
                <img loading="lazy" src="images/gallary/techvaran-goats-5902053_1920.jpg" alt="Partner">
                <div class="value-prop-overlay"></div>
                <div class="value-prop-icon"><i class="fa fa-users"></i></div>
                <div class="value-prop-label">
                    <h3>Partner</h3>
                    <p>Join us in scaling from 14,000 to 100,000 birds. Farm tours, school visits, and investor partnerships.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOUR SECTORS (product/sector grid) -->
<section class="section-pad">
    <div class="container">
        <p class="section-eyebrow">Our Farm</p>
        <h2 class="section-title">Four Sectors</h2>
        <div class="sectors-grid">
            <div class="sector-card fade-in">
                <img loading="lazy" src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="Poultry">
                <div class="sector-card-overlay">
                    <span class="sector-card-label">Poultry</span>
                    <span class="sector-card-promise">Broilers, layers, eggs</span>
                </div>
            </div>
            <div class="sector-card fade-in">
                <img loading="lazy" src="images/gallary/jaclou-dl-cow-4270355_1920.jpg" alt="Dairy">
                <div class="sector-card-overlay">
                    <span class="sector-card-label">Dairy</span>
                    <span class="sector-card-promise">Fresh milk daily</span>
                </div>
            </div>
            <div class="sector-card fade-in">
                <img loading="lazy" src="images/gallary/rschaubhut-goat-5642612_1920.jpg" alt="Goats">
                <div class="sector-card-overlay">
                    <span class="sector-card-label">Goats</span>
                    <span class="sector-card-promise">Boer &amp; Savannah</span>
                </div>
            </div>
            <div class="sector-card fade-in">
                <img loading="lazy" src="images/gallary/christoph-coffee-171653_1920.jpg" alt="Crops">
                <div class="sector-card-overlay">
                    <span class="sector-card-label">Crops</span>
                    <span class="sector-card-promise">Coffee, bananas, maize</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- OUR IMPACT (CMS-managed stats strip) -->
<section class="stats-strip">
    <div class="container">
        <p class="section-eyebrow" style="text-align:center;">Our Impact</p>
        <div class="stats-strip-inner">
            <div class="stat-item fade-in">
                <div class="stat-number"><?php echo htmlspecialchars($impactYears); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($impactYearsLabel); ?></div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number"><?php echo htmlspecialchars($impactFarmers); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($impactFarmersLabel); ?></div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number"><?php echo htmlspecialchars($impactStudents); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($impactStudentsLabel); ?></div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number"><?php echo htmlspecialchars($impactSectors); ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($impactSectorsLabel); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- LATEST FROM THE BLOG -->
<section class="blog-section">
    <div class="container">
        <p class="section-eyebrow">Blog</p>
        <h2 class="section-title">From the Farm</h2>
        <?php if (empty($latestPosts)): ?>
            <p class="section-sub" style="margin-top:24px;">No blog posts published yet — check back soon.</p>
        <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($latestPosts as $post):
                $excerptSrc = $post['excerpt'] !== null && $post['excerpt'] !== '' ? $post['excerpt'] : strip_tags((string) $post['content']);
                $excerptSrc = trim($excerptSrc);
                if (mb_strlen($excerptSrc) > 120) {
                    $excerptSrc = rtrim(mb_substr($excerptSrc, 0, 117)) . '...';
                }
                $img = $post['featured_image'] !== null && $post['featured_image'] !== '' ? $post['featured_image'] : 'images/gallary/rooster-farm.jpg';
                $dateStr = $post['publish_date'] ? date('F j, Y', strtotime($post['publish_date'])) : '';
            ?>
            <a href="blog-single.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-card fade-in">
                <div class="blog-card-img">
                    <img loading="lazy" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
                <div class="blog-card-body">
                    <?php if ($dateStr !== ''): ?><p class="blog-card-date"><?php echo htmlspecialchars($dateStr); ?></p><?php endif; ?>
                    <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p style="font-size:14px;line-height:1.7;color:#666;margin:10px 0 0;"><?php echo htmlspecialchars($excerptSrc); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="w-100 clearfix testimonialsBox" id="testimonialsBox">
    <div class="testimonialsRow">
        <div class="testimonialsCol testimonialsCol1">
            <h4 class="fadein">TESTIMONIALS</h4>
            <h2 class="fadein">What our customers are
                talking about us</h2>
            <a class="btn btn-1 hover-slide-down mt-3 fadein" href="javascript:void(0);">
                <span>View All <img loading="lazy" src="images/icon/icon-black-right.png" alt="icon" class="img-fluid"></span>
            </a>
        </div>
        <div class="testimonialsCol testimonialsCol2">
            <div class="testimonialSlider">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="slide1">
                        <div class="testimonialSliderItem fadein">
                            <p>City Gate Mixed Farm changed how I think about farming. I came as a visitor, stayed for the training, and I am now running my own poultry unit back home. The practical sessions on feeding and biosecurity were the most useful.</p>
                            <h4>Okello Dennis</h4>
                            <span>Smallholder Farmer, Lira</span>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="slide2">
                        <div class="testimonialSliderItem fadein">
                            <p>We buy 1-month chicks and fresh milk from City Gate every week. The chicks survive and grow well, and the milk is always fresh. Reliable farm, reliable quality.</p>
                            <h4>Akello Grace</h4>
                            <span>Retailer, Lira City</span>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="slide3">
                        <div class="testimonialSliderItem fadein">
                            <p>My industrial training placement at City Gate Mixed Farm gave me exposure to real livestock and crop operations — poultry, goats, dairy, and pasture management. The mentorship was excellent.</p>
                            <h4>Achan Mercy</h4>
                            <span>Agriculture Student, Gulu University</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonialSliderImg">
                <!-- Nav pills -->
                <ul class="nav nav-pills fadein">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="pill" href="#slide1">
                            <span class="nav-pill-avatar">OD</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="pill" href="#slide2">
                            <span class="nav-pill-avatar">AG</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="pill" href="#slide3">
                            <span class="nav-pill-avatar">AM</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
