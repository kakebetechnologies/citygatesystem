const fs = require('fs');
const p = 'c:/Users/SEDRICK/Documents/GitHub/citygatefarms/index.html';
const s = fs.readFileSync(p, 'utf8');

const newStyles = `
   <!-- City Gate modern redesign styles -->
   <style>
      :root{
         --cg-green:#1f4d2b;
         --cg-green-deep:#143020;
         --cg-cream:#faf7f0;
         --cg-earth:#c9a978;
         --cg-ink:#111;
         --cg-ink-soft:#3a3a3a;
         --cg-line:rgba(17,17,17,.08);
         --cg-shadow:0 12px 40px rgba(20,48,32,.12);
         --cg-radius:18px;
         --cg-ease:cubic-bezier(.2,.7,.2,1);
      }
      .cg-eyebrow{display:inline-block;font-size:12px;letter-spacing:.22em;text-transform:uppercase;color:#f0e9d2;font-weight:600}
      .cg-eyebrow-dark{display:inline-block;font-size:12px;letter-spacing:.22em;text-transform:uppercase;color:var(--cg-green);font-weight:600}
      .cg-section{padding:110px 0;background:var(--cg-cream)}
      .cg-section-head{max-width:720px;margin:0 auto 60px;text-align:center;padding:0 24px}
      .cg-section-title{font-size:clamp(28px,4vw,48px);line-height:1.08;color:var(--cg-ink);font-weight:700;margin:14px 0 12px;letter-spacing:-.02em}
      .cg-section-sub{color:var(--cg-ink-soft);font-size:17px;line-height:1.6;margin:0}
      .cg-btn{display:inline-flex;align-items:center;gap:10px;padding:16px 30px;border-radius:999px;font-weight:600;letter-spacing:.01em;font-size:15px;text-decoration:none;transition:transform .35s var(--cg-ease),background .3s,color .3s,box-shadow .3s;border:0;cursor:pointer}
      .cg-btn:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(0,0,0,.18)}
      .cg-btn-primary{background:var(--cg-green);color:#fff}
      .cg-btn-primary:hover{background:var(--cg-green-deep);color:#fff}
      .cg-btn-ghost{background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.7)}
      .cg-btn-ghost:hover{background:#fff;color:var(--cg-green);border-color:#fff}
      .cg-btn-white{background:#fff;color:var(--cg-green)}
      .cg-btn-white:hover{background:var(--cg-cream);color:var(--cg-green-deep)}
      .cg-btn-sm{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:999px;background:var(--cg-green);color:#fff;font-size:13px;font-weight:600;text-decoration:none;transition:background .3s}
      .cg-btn-sm:hover{background:var(--cg-green-deep);color:#fff}

      /* HERO */
      .cg-hero{position:relative;min-height:92vh;display:flex;align-items:center;overflow:hidden;color:#fff}
      .cg-hero-bg{position:absolute;inset:0;background:url('images/gallary/rooster-farm.jpg') center/cover no-repeat;transform:scale(1.08);animation:cgKen 24s ease-in-out infinite alternate}
      @keyframes cgKen{from{transform:scale(1.05) translateX(-1%)}to{transform:scale(1.15) translateX(1%)}}
      .cg-hero::before{content:"";position:absolute;inset:0;background:linear-gradient(110deg,rgba(10,25,15,.78) 0%,rgba(10,25,15,.55) 45%,rgba(10,25,15,.25) 100%);z-index:1}
      .cg-hero-inner{position:relative;z-index:2;max-width:1240px;margin:0 auto;padding:120px 24px 80px;width:100%}
      .cg-hero-title{font-size:clamp(44px,7vw,92px);line-height:1;font-weight:700;letter-spacing:-.03em;margin:22px 0 24px;max-width:15ch;color:#fff}
      .cg-hero-sub{font-size:clamp(16px,1.4vw,20px);line-height:1.55;max-width:58ch;color:#f2ece0;margin:0 0 36px}
      .cg-cta-row{display:flex;gap:14px;flex-wrap:wrap}

      /* STATS STRIP */
      .cg-stats{background:var(--cg-green);color:#fff}
      .cg-stats-inner{max-width:1240px;margin:0 auto;padding:48px 24px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px;text-align:center}
      .cg-stat-n{display:block;font-size:clamp(28px,3vw,44px);font-weight:700;letter-spacing:-.02em;color:#fff}
      .cg-stat-l{display:block;font-size:13px;letter-spacing:.18em;text-transform:uppercase;color:#e6dcc2;margin-top:6px}

      /* SECTOR GRID */
      .cg-sectors-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;max-width:1240px;margin:0 auto;padding:0 24px}
      .cg-sector{position:relative;display:block;aspect-ratio:3/4;border-radius:var(--cg-radius);overflow:hidden;text-decoration:none;color:#fff;box-shadow:var(--cg-shadow);transition:transform .6s var(--cg-ease)}
      .cg-sector:hover{transform:translateY(-6px)}
      .cg-sector img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform 1.2s var(--cg-ease)}
      .cg-sector:hover img{transform:scale(1.08)}
      .cg-sector::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 40%,rgba(10,25,15,.85) 100%)}
      .cg-sector-meta{position:absolute;left:22px;right:22px;bottom:22px;z-index:2}
      .cg-sector-meta h3{font-size:24px;font-weight:700;margin:0 0 4px;letter-spacing:-.01em;color:#fff}
      .cg-sector-meta p{margin:0;font-size:14px;color:#e6dcc2;line-height:1.4}

      /* PILLARS */
      .cg-pillars{background:#fff;padding:120px 0}
      .cg-pillars-inner{max-width:1240px;margin:0 auto;padding:0 24px;display:grid;grid-template-columns:repeat(3,1fr);gap:48px}
      .cg-pillar{padding:8px}
      .cg-pillar-icon{width:58px;height:58px;border-radius:14px;background:var(--cg-cream);display:inline-flex;align-items:center;justify-content:center;margin-bottom:22px;color:var(--cg-green);font-size:26px}
      .cg-pillar h3{font-size:22px;font-weight:700;margin:0 0 10px;color:var(--cg-ink)}
      .cg-pillar p{font-size:16px;line-height:1.6;color:var(--cg-ink-soft);margin:0}

      /* PRODUCTS */
      .cg-products-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;max-width:1240px;margin:0 auto;padding:0 24px}
      .cg-product{background:#fff;border-radius:var(--cg-radius);overflow:hidden;transition:transform .5s var(--cg-ease),box-shadow .5s}
      .cg-product:hover{transform:translateY(-6px);box-shadow:var(--cg-shadow)}
      .cg-product-img{aspect-ratio:4/3;overflow:hidden;background:#eee}
      .cg-product-img img{width:100%;height:100%;object-fit:cover;transition:transform 1s var(--cg-ease)}
      .cg-product:hover .cg-product-img img{transform:scale(1.06)}
      .cg-product-body{padding:22px 22px 26px}
      .cg-product-body h4{font-size:18px;font-weight:600;margin:0 0 6px;color:var(--cg-ink)}
      .cg-product-body .cg-price{display:block;font-size:15px;color:var(--cg-green);font-weight:600;margin-bottom:14px}

      /* BAND */
      .cg-band{position:relative;padding:100px 24px;text-align:center;background:var(--cg-green);color:#fff;overflow:hidden}
      .cg-band::before{content:"";position:absolute;inset:0;background:url('images/gallary/pexels-chicken-1867521_1920.jpg') center/cover no-repeat;opacity:.12}
      .cg-band-inner{position:relative;z-index:1;max-width:860px;margin:0 auto}
      .cg-band h2{font-size:clamp(28px,4vw,44px);font-weight:700;line-height:1.15;margin:0 0 28px;letter-spacing:-.02em;color:#fff}

      /* BLOG */
      .cg-blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;max-width:1240px;margin:0 auto;padding:0 24px}
      .cg-blog-card{display:block;text-decoration:none;color:inherit;background:#fff;border-radius:var(--cg-radius);overflow:hidden;transition:transform .5s var(--cg-ease),box-shadow .5s}
      .cg-blog-card:hover{transform:translateY(-6px);box-shadow:var(--cg-shadow)}
      .cg-blog-img{aspect-ratio:16/10;overflow:hidden}
      .cg-blog-img img{width:100%;height:100%;object-fit:cover;transition:transform 1s var(--cg-ease)}
      .cg-blog-card:hover .cg-blog-img img{transform:scale(1.06)}
      .cg-blog-body{padding:24px 26px 28px}
      .cg-blog-date{display:inline-block;font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:var(--cg-green);font-weight:600;margin-bottom:10px}
      .cg-blog-body h3{font-size:19px;font-weight:600;line-height:1.3;margin:0 0 10px;color:var(--cg-ink)}
      .cg-blog-body p{font-size:15px;line-height:1.55;color:var(--cg-ink-soft);margin:0}

      /* Reveal on scroll */
      .cg-reveal{opacity:0;transform:translateY(24px);transition:opacity .9s var(--cg-ease),transform .9s var(--cg-ease)}
      .cg-reveal.cg-in{opacity:1;transform:none}

      /* Responsive */
      @media (max-width:1024px){
         .cg-sectors-grid,.cg-products-grid{grid-template-columns:repeat(2,1fr)}
         .cg-pillars-inner,.cg-blog-grid{grid-template-columns:1fr;gap:32px}
         .cg-stats-inner{grid-template-columns:repeat(2,1fr);gap:32px}
         .cg-section,.cg-pillars{padding:80px 0}
      }
      @media (max-width:600px){
         .cg-sectors-grid,.cg-products-grid{grid-template-columns:1fr}
         .cg-hero{min-height:88vh}
         .cg-hero-inner{padding:100px 20px 60px}
      }
   </style>
`;

const newBody = `
   <!-- HERO -->
   <section class="cg-hero">
      <div class="cg-hero-bg"></div>
      <div class="cg-hero-inner">
         <span class="cg-eyebrow cg-reveal">Amuca · Lira City · Uganda</span>
         <h1 class="cg-hero-title cg-reveal">Farming.<br>Training.<br>Feeding Uganda.</h1>
         <p class="cg-hero-sub cg-reveal">An integrated model farm with 14,000+ birds, dairy, goats and crops — open to visitors, buyers and learners.</p>
         <div class="cg-cta-row cg-reveal">
            <a href="contact-us.html" class="cg-btn cg-btn-primary">Visit the Farm</a>
            <a href="shop.html" class="cg-btn cg-btn-ghost">Shop Produce</a>
         </div>
      </div>
   </section>

   <!-- STATS STRIP -->
   <section class="cg-stats">
      <div class="cg-stats-inner">
         <div class="cg-stat cg-reveal"><span class="cg-stat-n">14,000+</span><span class="cg-stat-l">Birds</span></div>
         <div class="cg-stat cg-reveal"><span class="cg-stat-n">80 L</span><span class="cg-stat-l">Milk / day</span></div>
         <div class="cg-stat cg-reveal"><span class="cg-stat-n">4</span><span class="cg-stat-l">Farm Sectors</span></div>
         <div class="cg-stat cg-reveal"><span class="cg-stat-n">1</span><span class="cg-stat-l">Mission</span></div>
      </div>
   </section>

   <!-- FOUR SECTORS -->
   <section class="cg-section">
      <div class="cg-section-head cg-reveal">
         <span class="cg-eyebrow-dark">Our Four Sectors</span>
         <h2 class="cg-section-title">One farm. Four promises.</h2>
         <p class="cg-section-sub">Poultry, goats, dairy and crops — each run with the same standard, on one 4-hectare site in Northern Uganda.</p>
      </div>
      <div class="cg-sectors-grid">
         <a href="poultry-feed.html" class="cg-sector cg-reveal">
            <img src="images/gallary/pexels-chicken-1867521_1920.jpg" alt="Poultry at City Gate Mixed Farm">
            <div class="cg-sector-meta">
               <h3>Poultry</h3>
               <p>14,000 birds and growing.</p>
            </div>
         </a>
         <a href="broiler-breeder.html" class="cg-sector cg-reveal">
            <img src="images/gallary/andreasgoellner-hen-5642953_1920.jpg" alt="Goat farming at City Gate">
            <div class="cg-sector-meta">
               <h3>Goats</h3>
               <p>Boer &amp; Savannah, from UGX 600,000.</p>
            </div>
         </a>
         <a href="layer-breeders.html" class="cg-sector cg-reveal">
            <img src="images/gallary/jaclou-dl-cow-4270355_1920.jpg" alt="Dairy cows at City Gate">
            <div class="cg-sector-meta">
               <h3>Dairy</h3>
               <p>~80 litres of fresh milk daily.</p>
            </div>
         </a>
         <a href="actigen.html" class="cg-sector cg-reveal">
            <img src="images/gallary/stevepb-nest-1050964_1920.jpg" alt="Crops and pasture at City Gate">
            <div class="cg-sector-meta">
               <h3>Crops</h3>
               <p>Coffee, bananas, maize, pasture.</p>
            </div>
         </a>
      </div>
   </section>

   <!-- PILLARS -->
   <section class="cg-pillars">
      <div class="cg-section-head cg-reveal">
         <span class="cg-eyebrow-dark">What We Do</span>
         <h2 class="cg-section-title">Produce. Train. Partner.</h2>
      </div>
      <div class="cg-pillars-inner">
         <div class="cg-pillar cg-reveal">
            <div class="cg-pillar-icon"><i class="fa fa-leaf"></i></div>
            <h3>Produce</h3>
            <p>Eggs, chicks, broilers, goats, milk, coffee, maize and bananas — priced in UGX, delivered from the farm gate.</p>
         </div>
         <div class="cg-pillar cg-reveal">
            <div class="cg-pillar-icon"><i class="fa fa-graduation-cap"></i></div>
            <h3>Train</h3>
            <p>Hands-on agricultural training, youth empowerment programs, and industrial-training placements for students.</p>
         </div>
         <div class="cg-pillar cg-reveal">
            <div class="cg-pillar-icon"><i class="fa fa-handshake-o"></i></div>
            <h3>Partner</h3>
            <p>We work with buyers, investors and NGOs scaling agriculture in Northern Uganda. Let's build together.</p>
         </div>
      </div>
   </section>

   <!-- FEATURED PRODUCTS -->
   <section class="cg-section">
      <div class="cg-section-head cg-reveal">
         <span class="cg-eyebrow-dark">Shop The Farm</span>
         <h2 class="cg-section-title">Priced in UGX. Fresh from Amuca.</h2>
      </div>
      <div class="cg-products-grid">
         <div class="cg-product cg-reveal">
            <div class="cg-product-img"><img src="images/gallary/engin_akyurt-egg-6757508_1920.jpg" alt="Farm fresh eggs"></div>
            <div class="cg-product-body">
               <h4>Farm Fresh Eggs</h4>
               <span class="cg-price">UGX 12,000 / tray</span>
               <a href="shop.html" class="cg-btn-sm">Enquire</a>
            </div>
         </div>
         <div class="cg-product cg-reveal">
            <div class="cg-product-img"><img src="images/gallary/andreasgoellner-hen-5642953_1920.jpg" alt="1-month chicks"></div>
            <div class="cg-product-body">
               <h4>1-Month Chicks</h4>
               <span class="cg-price">UGX 10,000 each</span>
               <a href="shop.html" class="cg-btn-sm">Enquire</a>
            </div>
         </div>
         <div class="cg-product cg-reveal">
            <div class="cg-product-img"><img src="images/gallary/couleur-milk-2474993.jpg" alt="Fresh cow milk"></div>
            <div class="cg-product-body">
               <h4>Fresh Cow Milk</h4>
               <span class="cg-price">UGX 2,500 / litre</span>
               <a href="shop.html" class="cg-btn-sm">Enquire</a>
            </div>
         </div>
         <div class="cg-product cg-reveal">
            <div class="cg-product-img"><img src="images/img7.png" alt="Boer and Savannah goats"></div>
            <div class="cg-product-body">
               <h4>Boer / Savannah Goats</h4>
               <span class="cg-price">From UGX 600,000</span>
               <a href="shop.html" class="cg-btn-sm">Enquire</a>
            </div>
         </div>
      </div>
   </section>

   <!-- VISIT BAND -->
   <section class="cg-band">
      <div class="cg-band-inner cg-reveal">
         <h2>Come see the farm in Amuca, Lira City.</h2>
         <a href="contact-us.html" class="cg-btn cg-btn-white">Book a Visit</a>
      </div>
   </section>

   <!-- BLOG -->
   <section class="cg-section">
      <div class="cg-section-head cg-reveal">
         <span class="cg-eyebrow-dark">From the Farm</span>
         <h2 class="cg-section-title">Lessons from daily operations.</h2>
      </div>
      <div class="cg-blog-grid">
         <a href="blog-single.html" class="cg-blog-card cg-reveal">
            <div class="cg-blog-img"><img src="images/gallary/rooster-farm.jpg" alt="Scaling poultry"></div>
            <div class="cg-blog-body">
               <span class="cg-blog-date">25 Mar · Poultry</span>
               <h3>Scaling from 14,000 to 100,000 birds</h3>
               <p>Our expansion plan, biosecurity first, welfare always.</p>
            </div>
         </a>
         <a href="blog-single.html" class="cg-blog-card cg-reveal">
            <div class="cg-blog-img"><img src="images/gallary/9883074-chicken-3727097_1920.jpg" alt="Cross-breeding goats"></div>
            <div class="cg-blog-body">
               <span class="cg-blog-date">18 Mar · Goats</span>
               <h3>Why Boer cross-breeding pays off</h3>
               <p>Better weight, better yield, better returns in one generation.</p>
            </div>
         </a>
         <a href="blog-single.html" class="cg-blog-card cg-reveal">
            <div class="cg-blog-img"><img src="images/gallary/jaclou-dl-cow-4270355_1920.jpg" alt="Dairy feeding"></div>
            <div class="cg-blog-body">
               <span class="cg-blog-date">12 Mar · Dairy</span>
               <h3>Feeding for 80+ litres a day</h3>
               <p>Our daily ration, pasture strategy, and what we got wrong first.</p>
            </div>
         </a>
      </div>
   </section>

`;

const revealScript = [
'   <script>',
'     (function(){',
'       if(!("IntersectionObserver" in window)) { document.querySelectorAll(".cg-reveal").forEach(function(e){e.classList.add("cg-in")}); return; }',
'       var io = new IntersectionObserver(function(entries){',
'         entries.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add("cg-in"); io.unobserve(en.target); } });',
'       }, { threshold: .12, rootMargin: "0px 0px -8% 0px" });',
'       document.querySelectorAll(".cg-reveal").forEach(function(e){ io.observe(e); });',
'     })();',
'   </script>'
].join('\n');

// --- SPLICE ---
const headEnd = s.indexOf('</head>');
const step1 = s.slice(0, headEnd) + newStyles + '\n' + s.slice(headEnd);

const firstCart = step1.indexOf('<!--cart side box end-->');
const secondCart = step1.indexOf('<!--cart side box end-->', firstCart + 5);
const afterCart = step1.indexOf('\n', secondCart) + 1;
const footerStart = step1.indexOf('<!--footer start-->');

const headerPart = step1.slice(0, afterCart);
let footerPart = step1.slice(footerStart);

// kill old Maps JS (references removed map markers)
footerPart = footerPart.replace(/\/\/ Maps JS[\s\S]*?\}, 3000\)/, '// Maps JS removed in redesign');

const out = headerPart + newBody + footerPart;
const final = out.replace('</body>', revealScript + '\n</body>');

fs.writeFileSync(p, final);
console.log('Wrote', final.length, 'bytes');
