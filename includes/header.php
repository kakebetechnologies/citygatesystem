<?php
/**
 * Shared public-site header. Include after setting these optional vars:
 *   $pageTitle        (string) — <title>, defaults to the site name
 *   $pageDescription  (string) — meta description
 *   $activeNav        (string) — one of: home,about,services,gallery,blog,shop,faq,contact
 *   $ogImage/$ogTitle/$ogDescription/$canonicalUrl — override Open Graph tags (used by blog-single.php)
 */
require_once __DIR__ . '/../config/db.php';

$siteName = 'City Gate Mixed Farm';
$pageTitle = $pageTitle ?? $siteName;
$pageDescription = $pageDescription ?? 'City Gate Mixed Farm is an integrated model farm in Amuca, Lira City, Uganda specialising in poultry, goats, dairy, and crops. Farm visits, training, and livestock sales available.';
$activeNav = $activeNav ?? '';

$ogTitle = $ogTitle ?? $pageTitle;
$ogDescription = $ogDescription ?? $pageDescription;
$ogImage = $ogImage ?? 'images/gallary/rooster-farm.jpg';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$canonicalUrl = $canonicalUrl ?? ($scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

function cg_nav_active(string $key, string $active): string {
   return $key === $active ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title><?php echo htmlspecialchars($pageTitle); ?></title>
   <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
   <meta content="width=device-width, initial-scale=1.0" name="viewport">

   <meta property="og:type" content="website">
   <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
   <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
   <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
   <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
   <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
   <meta name="twitter:card" content="summary_large_image">
   <meta name="twitter:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
   <meta name="twitter:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
   <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">
   <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">

   <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
   <link rel="stylesheet" type="text/css" href="css/fonts.css">
   <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
   <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
   <link rel="stylesheet" type="text/css" href="css/magnific-popup.css">
   <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
   <link rel="stylesheet" type="text/css" href="css/owl.theme.default.min.css">
   <link rel="stylesheet" type="text/css" href="css/animate.css">
   <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
   <link rel="stylesheet" type="text/css" href="css/slick.css">
   <link rel="stylesheet" type="text/css" href="css/grt-youtube-popup.css">
   <link rel="stylesheet" type="text/css" href="css/fancybox.min.css">
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" type="text/css" href="css/responsive.css">
   <link rel="stylesheet" href="css/cg-theme.css">
   <link rel="shortcut icon" type="image/png" href="images/logo/favcon.png">
</head>
<body>
   <div class='cursor cursor1'></div>
   <div class='cursor cursor2'></div>
   <header class="w-100 clearfix header headerOne" id="headerOne">
      <div class="topHeader">
         <div class="container">
            <div class="topHeaderInner">
               <div class="mobile boxGroupHeader">
                  <a href="javascript:void(0);">
                     <div class="flexGroupHeader">
                        <div class="icon"><img src="images/icon/phone.png" alt="icon" class="img-fluid"></div>
                        <div class="iconTxt"><span>+256 123 456 789</span></div>
                     </div>
                  </a>
               </div>
               <div class="mail boxGroupHeader">
                  <a href="javascript:void(0);">
                     <div class="flexGroupHeader">
                        <div class="icon"><img src="images/icon/mail.png" alt="icon" class="img-fluid"></div>
                        <div class="iconTxt"><span>info@citygatefarms.com</span></div>
                     </div>
                  </a>
               </div>
               <div class="language boxGroupHeader ms-auto">
                  <div class="flexGroupHeader">
                     <div class="icon"><img src="images/icon/lang.png" alt="icon" class="img-fluid"></div>
                     <div class="iconTxt">
                        <select class="form-select">
                           <option>English</option>
                           <option>Luo</option>
                           <option>Luganda</option>
                        </select>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="mainHeader">
         <nav class="navbar navbar-expand-xl">
            <div class="container">
               <a class="navbar-brand" href="index.php"><img src="images/logo/citygatelogo.png" alt="City Gate Mixed Farm" class="img-fluid" style="max-width:180px;"></a>
               <div class="collapse navbar-collapse" id="collapsibleNavbar">
                  <ul class="navbar-nav">
                     <li class="nav-item<?php echo cg_nav_active('home', $activeNav); ?>"><a class="nav-link" href="index.php"><span>Home</span></a></li>
                     <li class="nav-item<?php echo cg_nav_active('about', $activeNav); ?>"><a class="nav-link" href="about-us.php">About Us</a></li>
                     <li class="nav-item dropdown<?php echo cg_nav_active('services', $activeNav); ?>">
                        <a class="nav-link" href="our-service.php" data-bs-toggle="dropdown">Services <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu">
                           <li><a class="dropdown-item" href="our-service.php">All Services</a></li>
                           <li><a class="dropdown-item" href="poultry-feed.php">Poultry (Chicken)</a></li>
                           <li><a class="dropdown-item" href="broiler-breeder.php">Goat Farming</a></li>
                           <li><a class="dropdown-item" href="layer-breeders.php">Dairy (Cows)</a></li>
                           <li><a class="dropdown-item" href="actigen.php">Crops</a></li>
                        </ul>
                     </li>
                     <li class="nav-item<?php echo cg_nav_active('gallery', $activeNav); ?>"><a class="nav-link" href="gallery-3-column.php">Gallery</a></li>
                     <li class="nav-item<?php echo cg_nav_active('blog', $activeNav); ?>"><a class="nav-link" href="blog.php">Blog</a></li>
                     <li class="nav-item<?php echo cg_nav_active('shop', $activeNav); ?>"><a class="nav-link" href="shop.php">Shop</a></li>
                     <li class="nav-item<?php echo cg_nav_active('faq', $activeNav); ?>"><a class="nav-link" href="faq.php">FAQ</a></li>
                     <li class="nav-item<?php echo cg_nav_active('contact', $activeNav); ?>"><a class="nav-link" href="contact-us.php">Contact</a></li>
                     <li class="nav-item loginBtn d-block d-md-none">
                        <div class="btnGroup"><a class="nav-link btn" href="bookings.php">Book a Visit</a></div>
                     </li>
                  </ul>
               </div>
               <div class="rightMenu">
                  <ul class="nav">
                     <li class="nav-item searchBtn"><a class="nav-link" href="javascript:void(0);"><img src="images/icon/search.png" alt="loader" class="img-fluid"></a></li>
                     <li class="nav-item loginBtn d-none d-md-block">
                        <div class="btnGroup"><a class="nav-link btn" href="bookings.php">Book a Visit</a></div>
                     </li>
                     <li class="nav-item toggleBtn">
                        <a class="nav-link navbar-toggler" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                           <span class="navbar-toggler-icon"></span>
                        </a>
                     </li>
                  </ul>
               </div>
            </div>
         </nav>
      </div>
      <div class="widgetOverlay"></div>
   </header>
   <!--search box start-->
   <div class="searchBox">
      <div class="container">
         <div class="searchBoxInner">
            <div class="searchHeading"><h4>Search Our Site</h4></div>
            <div class="searchInput">
               <div class="input-group">
                  <input type="text" class="form-control" placeholder="Search">
                  <a href="javascript:void(0);" class="input-group-text searchButton"><span>Search</span> <img src="images/icon/icon-right.png" alt="btn-arrow" class="img-fluid"></a>
               </div>
            </div>
            <div class="quickSearch"><p><span>Quick Search:</span>Poultry, Pig Farm, Fish Feed, Hen Feeds</p></div>
         </div>
      </div>
   </div>
