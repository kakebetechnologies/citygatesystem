<?php
/**
 * FAQ page. Converted from faq.html to the shared-include pattern.
 * Static content — no CMS or DB fields on this page.
 */
$pageTitle       = 'FAQ | City Gate Mixed Farm - Questions Answered';
$pageDescription = 'Frequently asked questions about City Gate Mixed Farm - visiting, training, products, bulk orders, internships and partnerships in Lira City, Northern Uganda.';
$activeNav       = 'faq';
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">
<style>
   .faq-hero {
      background: linear-gradient(135deg, rgba(47,93,58,0.92), rgba(31,63,39,0.88)), url('images/gallary/pexels-chicken-1867521_1920.jpg');
      background-size: cover;
      background-position: center;
      padding: 64px 0;
      color: #fff;
   }
   @media (max-width: 768px) {
      .faq-hero { padding: 48px 0; }
   }
   .faq-hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 1rem;
   }
   .faq-category {
      background: #fff;
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 40px rgba(0,0,0,0.08);
   }
   .faq-category h3 {
      color: var(--farm-green, #2F5D3A);
      font-weight: 700;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--earth, #C9A978);
   }
   .faq-item {
      border: none;
      margin-bottom: 1rem;
      border-radius: 15px;
      overflow: hidden;
      background: #f8fff8;
   }
   .faq-item .accordion-button {
      background: #f8fff8;
      color: #333;
      font-weight: 600;
      padding: 1.2rem 1.5rem;
      border: none;
      box-shadow: none;
   }
   .faq-item .accordion-button:focus {
      box-shadow: none;
   }
   .faq-item .accordion-button:not(.collapsed) {
      background: var(--farm-green, #2F5D3A);
      color: #fff;
   }
   .faq-item .accordion-button::after {
      filter: brightness(0) invert(0);
   }
   .faq-item .accordion-button:not(.collapsed)::after {
      filter: brightness(0) invert(1);
   }
   .faq-item .accordion-body {
      background: #fff;
      padding: 1.5rem;
      line-height: 1.8;
   }
   .info-card {
      background: linear-gradient(135deg, var(--farm-green-dark, #1F3F27) 0%, var(--farm-green, #2F5D3A) 100%);
      border-radius: 20px;
      padding: 2rem;
      color: #fff;
      margin-bottom: 2rem;
   }
   .info-card h4 {
      font-weight: 700;
      margin-bottom: 1rem;
   }
   .cta-box {
      background: linear-gradient(135deg, var(--farm-green-dark, #1F3F27) 0%, var(--farm-green, #2F5D3A) 100%);
      border-radius: 25px;
      padding: 3rem;
      text-align: center;
      color: #fff;
   }
   .cta-box h3 {
      margin-bottom: 1rem;
   }
   .cta-box .btn {
      background: #fff;
      color: var(--farm-green, #2F5D3A);
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      margin: 0.5rem;
      transition: all 0.3s ease;
   }
   .cta-box .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
   }
   .cta-box .btn-outline {
      background: transparent;
      border: 2px solid #fff;
      color: #fff;
   }
   .cta-box .btn-outline:hover {
      background: #fff;
      color: var(--farm-green, #2F5D3A);
   }
   .quick-links {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 1rem;
   }
   .quick-links a {
      background: rgba(255,255,255,0.2);
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.3s ease;
   }
   .quick-links a:hover {
      background: #fff;
      color: var(--farm-green, #2F5D3A);
   }
</style>

<!-- Hero Section -->
<section class="faq-hero">
   <div class="container">
      <div class="text-center wow fadeInUp">
         <h1>Frequently Asked Questions</h1>
         <p style="font-size: 1.2rem; opacity: 0.95;">Find answers about visiting, training, products, and partnerships</p>
         <div class="quick-links justify-content-center">
            <a href="#visiting">Farm Visits</a>
            <a href="#training">Training</a>
            <a href="#products">Products</a>
            <a href="#bulk">Bulk Orders</a>
            <a href="#partnerships">Partnerships</a>
         </div>
      </div>
   </div>
</section>

<!-- FAQ Categories -->
<section class="py-5">
   <div class="container">

      <!-- Visiting Section -->
      <div class="faq-category" id="visiting">
         <h3><i class="fa fa-map-marker"></i> Visiting Our Farm</h3>
         <div class="accordion" id="visitAccordion">
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#visit1">
                     How can I visit the farm?
                  </button>
               </h2>
               <div id="visit1" class="accordion-collapse collapse show" data-bs-parent="#visitAccordion">
                  <div class="accordion-body">
                     We welcome visitors by appointment, Monday to Saturday, 07:00 &ndash; 18:00. Call or WhatsApp us on <strong>+256 123 456 789</strong>, or email <strong>info@citygatefarms.com</strong> to book a slot. Schools, farmer groups, and individual visitors are all welcome!
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#visit2">
                     What is the cost of visiting?
                  </button>
               </h2>
               <div id="visit2" class="accordion-collapse collapse" data-bs-parent="#visitAccordion">
                  <div class="accordion-body">
                     Farm tour fees depend on group size and whether you include a training session. Individual visits and small groups have a modest entry fee; school and institution bookings get preferential rates. Contact us ahead of time for a quote in UGX.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#visit3">
                     Where exactly is the farm located?
                  </button>
               </h2>
               <div id="visit3" class="accordion-collapse collapse" data-bs-parent="#visitAccordion">
                  <div class="accordion-body">
                     City Gate Mixed Farm is located in <strong>Amuca, Lira City, Northern Uganda</strong>. We are a short drive from central Lira. Call us for precise directions or use the map on our Contact Us page.
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Training Section -->
      <div class="faq-category" id="training">
         <h3><i class="fa fa-graduation-cap"></i> Training Programs</h3>
         <div class="accordion" id="trainAccordion">
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#train1">
                     What training do you offer?
                  </button>
               </h2>
               <div id="train1" class="accordion-collapse collapse show" data-bs-parent="#trainAccordion">
                  <div class="accordion-body">
                     We offer hands-on training in <strong>poultry farming</strong>, <strong>goat breeding</strong>, <strong>dairy management</strong>, and <strong>crop production</strong>. Our training covers everything from setup to daily operations, record-keeping, and marketing. Whether you're a beginner or looking to scale up, we have something for you.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#train2">
                     How can I apply for training?
                  </button>
               </h2>
               <div id="train2" class="accordion-collapse collapse" data-bs-parent="#trainAccordion">
                  <div class="accordion-body">
                     To apply, fill out our <a href="bookings.php"><strong>online application form</strong></a>, send your name, contact details, and area of interest to info@citygatefarms.com, or message us on WhatsApp. We'll share the next intake dates, fees, and a short application form.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#train3">
                     Do you offer internships?
                  </button>
               </h2>
               <div id="train3" class="accordion-collapse collapse" data-bs-parent="#trainAccordion">
                  <div class="accordion-body">
                     Yes! We host industrial training placements and internships for agriculture, veterinary, and agribusiness students from universities and vocational institutes. Placements range from <strong>four weeks to a full semester</strong>, with hands-on rotation through poultry, goats, dairy, and crops.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#train4">
                     How much does training cost?
                  </button>
               </h2>
               <div id="train4" class="accordion-collapse collapse" data-bs-parent="#trainAccordion">
                  <div class="accordion-body">
                     Training fees vary depending on the program length and depth. We offer short courses (1-2 weeks), medium programs (1 month), and comprehensive courses (3+ months). Contact us for current pricing &ndash; we also have special rates for student groups and farmer cooperatives.
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Products Section -->
      <div class="faq-category" id="products">
         <h3><i class="fa fa-shopping-basket"></i> Our Products</h3>
         <div class="accordion" id="productAccordion">
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#prod1">
                     What products are available?
                  </button>
               </h2>
               <div id="prod1" class="accordion-collapse collapse show" data-bs-parent="#productAccordion">
                  <div class="accordion-body">
                     We sell: <strong>farm-fresh eggs</strong> (UGX 12,000/tray), <strong>1-month chicks</strong> (UGX 10,000), <strong>live broiler chickens</strong> (UGX 25,000), <strong>Boer & Savannah goats</strong> (from UGX 600,000), <strong>fresh cow milk</strong> (UGX 2,500/litre), <strong>maize</strong>, <strong>coffee</strong>, and <strong>bananas</strong>. See our <a href="shop.php">Shop page</a> for current listings and prices.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#prod2">
                     Are your products organic?
                  </button>
               </h2>
               <div id="prod2" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                  <div class="accordion-body">
                     Our farm follows integrated farming practices. We use home-grown feed for our livestock and compost manure for our crops, minimizing synthetic inputs. While we're not officially certified organic, our approach is sustainable and environmentally friendly.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#prod3">
                     Do you sell seedlings or seeds?
                  </button>
               </h2>
               <div id="prod3" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                  <div class="accordion-body">
                     Yes! We provide <strong>banana seedlings</strong>, <strong>coffee seedlings</strong>, <strong>Napier grass cuttings</strong>, and <strong>improved forage seeds</strong> (cofeedb) to farmers. These are available for sale or as part of our outgrower support program.
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Bulk Orders Section -->
      <div class="faq-category" id="bulk">
         <h3><i class="fa fa-truck"></i> Bulk Orders & Delivery</h3>
         <div class="accordion" id="bulkAccordion">
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#bulk1">
                     Can I buy in bulk?
                  </button>
               </h2>
               <div id="bulk1" class="accordion-collapse collapse show" data-bs-parent="#bulkAccordion">
                  <div class="accordion-body">
                     Yes! We supply eggs, chicks, broiler meat, milk, maize, and coffee in bulk to <strong>retailers, hotels, schools, and caf&eacute;s</strong> across Lira City and beyond. Bulk orders receive negotiated pricing &ndash; contact our sales team on <strong>sales@citygatefarms.com</strong> for a quotation.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bulk2">
                     Do you offer delivery?
                  </button>
               </h2>
               <div id="bulk2" class="accordion-collapse collapse" data-bs-parent="#bulkAccordion">
                  <div class="accordion-body">
                     Yes, we deliver across Lira City and northern Uganda for bulk orders. Delivery fees depend on distance and order size. For small orders, you can arrange pickup from the farm or our collection points in Lira City.
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Partnerships Section -->
      <div class="faq-category" id="partnerships">
         <h3><i class="fa fa-handshake-o"></i> Partnerships</h3>
         <div class="accordion" id="partnerAccordion">
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#part1">
                     How can I partner with the farm?
                  </button>
               </h2>
               <div id="part1" class="accordion-collapse collapse show" data-bs-parent="#partnerAccordion">
                  <div class="accordion-body">
                     We partner with input suppliers, buyers, financiers, NGOs, and agritech providers. Whether you want to <strong>co-invest</strong> in scaling our poultry unit, <strong>supply inputs</strong>, <strong>off-take produce</strong>, or run <strong>joint training programs</strong> &ndash; reach out to <strong>info@citygatefarms.com</strong> with a brief note on what you have in mind.
                  </div>
               </div>
            </div>
            <div class="faq-item accordion-item">
               <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#part2">
                     Do you work with farmer groups?
                  </button>
               </h2>
               <div id="part2" class="accordion-collapse collapse" data-bs-parent="#partnerAccordion">
                  <div class="accordion-body">
                     Absolutely! We support farmer cooperatives and outgrower networks. We provide training, inputs, and market access to groups looking to scale their farming operations. Contact us to discuss how we can work together.
                  </div>
               </div>
            </div>
         </div>
      </div>

   </div>
</section>

<!-- CTA Section -->
<section class="py-5">
   <div class="container">
      <div class="cta-box wow fadeInUp">
         <h3>Still Have Questions?</h3>
         <p class="mb-4">Our team is ready to help you with any questions about farming, training, or partnerships.</p>
         <div>
            <a href="bookings.php" class="btn">Apply for Training</a>
            <a href="contact-us.php" class="btn btn-outline">Contact Us</a>
         </div>
      </div>
   </div>
</section>

<script>
   window.addEventListener('load', function () {
      if (typeof WOW !== 'undefined') { new WOW().init(); }
   });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
