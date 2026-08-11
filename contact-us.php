<?php
/**
 * Contact Us page. Converted from contact-us.html to the shared-include
 * pattern. The contact form posts via AJAX (js/contact_form.js) to
 * contactmail.php — field names/classes (form_type, full_name, email,
 * contact_no, subject, message, .require, .response, .submitForm) are kept
 * exactly as contactmail.php expects. No contact-page CMS fields are
 * defined in admin/cms.php, so this content is static.
 */
$pageTitle       = 'Contact Us | City Gate Mixed Farm';
$pageDescription = 'Get in touch with City Gate Mixed Farm in Amuca, Lira City, Uganda. Visit the farm, buy products, enrol in training, or discuss a partnership.';
$activeNav       = 'contact';
$extraScripts    = ['js/contact_form.js'];
require __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/cg-redesign.css">

<!--banner start-->
<section class="cg-page-banner">
   <div class="container">
      <span class="section-eyebrow">Get In Touch</span>
      <h1>Contact Us</h1>
      <ul class="breadcrumb">
         <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         <li class="breadcrumb-item active">Contact Us</li>
      </ul>
   </div>
</section>
<!--banner end-->
<!--contact start-->
<section class="w-100 clearfix contactSec" id="contactSec">
   <div class="container">
      <div class="row">
         <div class="col-lg-6">
            <div class="contactInfo">
               <div class="commonHeading">
                  <h4>Get In Touch With City Gate Mixed Farm</h4>
                  <p id="cms-contact-intro">Whether you want to visit the farm, buy products, enrol in training, or discuss a partnership &mdash; we are happy to hear from you. Use the form or reach us directly on the channels below.</p>
               </div>
               <div class="infoGroup">
                  <a href="https://maps.google.com/?q=Amuca,Lira+City,Uganda" target="_blank" rel="noopener">
                     <div class="infoGroupItem">
                        <div class="infoIcon">
                           <span><img loading="lazy" src="images/icon/location-icon.png" alt="location" class="img-fluid"></span>
                        </div>
                        <div class="infoTxt">
                           <h4>Our Location</h4>
                           <p class="mb-0">Amuca, Lira City, Northern Uganda</p>
                        </div>
                     </div>
                  </a>
                  <a href="tel:+256123456789">
                     <div class="infoGroupItem">
                        <div class="infoIcon">
                           <span><img loading="lazy" src="images/icon/phone-icon.png" alt="phone" class="img-fluid"></span>
                        </div>
                        <div class="infoTxt">
                           <h4>Phone / WhatsApp</h4>
                           <p class="mb-0">+256 123 456 789</p>
                           <p class="mb-0">Mon&ndash;Sat, 07:00 &ndash; 18:00</p>
                        </div>
                     </div>
                  </a>
                  <a href="mailto:info@citygatefarms.com">
                     <div class="infoGroupItem">
                        <div class="infoIcon">
                           <span><img loading="lazy" src="images/icon/mail-icon.png" alt="email" class="img-fluid"></span>
                        </div>
                        <div class="infoTxt">
                           <h4>Email</h4>
                           <p class="mb-0">info@citygatefarms.com</p>
                           <p class="mb-0">sales@citygatefarms.com</p>
                        </div>
                     </div>
                  </a>
               </div>
            </div>
         </div>
         <div class="col-lg-6">
            <div class="contactForm">
               <h4>Contact Now</h4>
               <form>
                  <input type="hidden" name="form_type" value="contact">
                  <div class="formControlGroup">
                    <input type="text" class="form-control require" id="full_name" placeholder="Your Name" name="full_name">
                  </div>
                  <div class="formControlGroup">
                    <input type="email" class="form-control require" id="email" placeholder="Your Email" name="email"
                       data-valid="/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/"
                       data-error="Please enter a valid email address.">
                  </div>
                  <div class="formControlGroup">
                     <input type="text" class="form-control require" id="contact_no" placeholder="Phone" name="contact_no">
                  </div>
                  <div class="formControlGroup">
                     <input type="text" class="form-control" id="subject" placeholder="Subject" name="subject">
                  </div>
                  <div class="formControlGroup">
                     <textarea class="form-control require" rows="5" id="msg" placeholder="Message" name="message"></textarea>
                  </div>
                  <div class="response"></div>
                  <div class="formSubmitBtn">
                     <button type="button" class="btnCustom5 btn-1 hover-slide-down submitForm">
                           <span>Submit <img loading="lazy" src="images/icon/icon-right.png" alt="right" class="img-fluid"></span>
                     </button>
                  </div>
                </form>
            </div>
         </div>
      </div>
   </div>
</section>
<!--contact end-->

<!--Map embed start-->
<section class="w-100 clearfix contactMapEmbed" style="padding: 60px 0; background:#f6faf2;">
   <div class="container">
      <div class="commonHeading text-center" style="margin-bottom:30px;">
         <h4>Find Us in Amuca, Lira City</h4>
         <p>City Gate Mixed Farm is a short drive from central Lira City. Call ahead for directions and tour bookings.</p>
      </div>
      <div class="mapEmbedWrap" style="border-radius:12px; overflow:hidden; box-shadow:var(--cg-shadow);">
         <iframe src="https://www.google.com/maps?q=Amuca%2C+Lira+City%2C+Uganda&amp;output=embed"
                 width="100%" height="420" style="border:0;" allowfullscreen loading="lazy"
                 referrerpolicy="no-referrer-when-downgrade" title="City Gate Mixed Farm &mdash; Amuca, Lira City, Uganda"></iframe>
      </div>
   </div>
</section>
<!--Map embed end-->

<?php require __DIR__ . '/includes/footer.php'; ?>
