<?php
/**
 * Shared public-site footer. Optional $extraScripts (array of script src
 * strings) lets a page add page-specific JS after the common bundle.
 */
$extraScripts = $extraScripts ?? [];
?>
   <!--footer start-->
   <footer class="w-100 clearfix footer footerBg1" id="footer">
      <div class="needOurSupport">
         <div class="container">
            <div class="needOurSupportInner">
               <div class="needOurSupportTxt">
                  <h2>Stay Connected With City Gate Mixed Farm</h2>
                  <p>Get updates on new products, training intakes, farm visit schedules and price offers delivered straight to your inbox.</p>
               </div>
               <div class="needOurSupportInput">
                  <div class="input-group">
                     <input type="text" class="form-control" placeholder="Email Address">
                     <a href="javascript:void(0);" class="input-group-text subscriptionBtn"><span>Subscription</span>
                        <img src="images/icon/icon-right.png" alt="btn-arrow" class="img-fluid"></a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="footerGroup">
         <div class="footerInner">
            <div class="container">
               <div class="footerInnerRow">
                  <div class="row">
                     <div class="col-md-12 col-lg-3">
                        <div class="footerCol footerCol1">
                           <div class="footerPara">
                              <p>City Gate Mixed Farm is an integrated model farm in Amuca, Lira City, Uganda — producing poultry, goats, dairy and crops, and training the next generation of Ugandan farmers.</p>
                           </div>
                           <hr class="hrLine">
                           <div class="socialMediaIcon">
                              <ul class="nav">
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="footerCol footerCol2">
                           <div class="footerMenuHeading"><h4>Useful Links</h4></div>
                           <div class="footerMenuLink">
                              <ul class="nav flex-column">
                                 <li class="nav-item"><a class="nav-link" href="about-us.php"><i class="fa fa-caret-right" aria-hidden="true"></i> About Us</a></li>
                                 <li class="nav-item"><a class="nav-link" href="our-service.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Our Services</a></li>
                                 <li class="nav-item"><a class="nav-link" href="gallery-3-column.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Gallery</a></li>
                                 <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Shop</a></li>
                                 <li class="nav-item"><a class="nav-link" href="contact-us.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Contact Us</a></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="footerCol footerCol3">
                           <div class="footerMenuHeading"><h4>Farm Sectors</h4></div>
                           <div class="footerMenuLink">
                              <ul class="nav flex-column">
                                 <li class="nav-item"><a class="nav-link" href="poultry-feed.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Poultry (Chicken)</a></li>
                                 <li class="nav-item"><a class="nav-link" href="broiler-breeder.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Goat Farming</a></li>
                                 <li class="nav-item"><a class="nav-link" href="layer-breeders.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Dairy (Cows)</a></li>
                                 <li class="nav-item"><a class="nav-link" href="actigen.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Crops</a></li>
                                 <li class="nav-item"><a class="nav-link" href="faq.php"><i class="fa fa-caret-right" aria-hidden="true"></i> FAQ</a></li>
                                 <li class="nav-item"><a class="nav-link" href="blog.php"><i class="fa fa-caret-right" aria-hidden="true"></i> Blog</a></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4 col-lg-3">
                        <div class="footerCol footerCol4">
                           <div class="footerMenuHeading"><h4>Contact Information</h4></div>
                           <div class="footerMenuLink footerContactInfo">
                              <ul class="nav flex-column">
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><div class="contactInfo"><div class="contactInfoIcon"><i class="fa fa-map-marker" aria-hidden="true"></i></div><div class="contactInfoTxt"><h6>Location:</h6><p class="mb-0">Amuca, Lira City, Uganda</p></div></div></a></li>
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><div class="contactInfo"><div class="contactInfoIcon"><i class="fa fa-phone" aria-hidden="true"></i></div><div class="contactInfoTxt"><h6>Call Us Now:</h6><p class="mb-0">+256 123 456 789</p></div></div></a></li>
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><div class="contactInfo"><div class="contactInfoIcon"><i class="fa fa-envelope-o" aria-hidden="true"></i></div><div class="contactInfoTxt"><h6>Email Address:</h6><p class="mb-0">info@citygatefarms.com</p></div></div></a></li>
                                 <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><div class="contactInfo"><div class="contactInfoIcon"><i class="fa fa-clock-o" aria-hidden="true"></i></div><div class="contactInfoTxt"><h6>Office Hours:</h6><p class="mb-0">Mon–Sat: 07:00 – 18:00</p></div></div></a></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="footerCopyRight">
            <div class="container">
               <div class="footerCopyRightInner">
                  <p class="mb-0">Copyright © <?php echo date('Y'); ?> <a href="index.php">City Gate Mixed Farm</a>. All Rights Reserved.</p>
               </div>
            </div>
         </div>
      </div>
   </footer>
   <!--footer end-->

   <script src="js/jquery-3.6.4.min.js"></script>
   <script src="js/bootstrap.bundle.min.js"></script>
   <script src="js/wow.min.js"></script>
   <script src="js/jquery.magnific-popup.js"></script>
   <script src="js/jquery-ui.min.js"></script>
   <script src="js/slick.min.js"></script>
   <script src="js/owl.carousel.min.js"></script>
   <script src="js/grt-youtube-popup.js"></script>
   <script src="js/jquery.fancybox.min.js"></script>
   <script src="js/custom.js"></script>
<?php foreach ($extraScripts as $src): ?>
   <script src="<?php echo htmlspecialchars($src); ?>"></script>
<?php endforeach; ?>
</body>
</html>
