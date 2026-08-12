// Shop single product page — related-products carousel + main thumbnail
// carousel nav buttons. Loaded via $extraScripts AFTER jQuery/owl.carousel
// (footer.php loads those), so $ is guaranteed to exist here.
$(document).ready(function ($) {
   $('.owlCarouselProducts').owlCarousel({
      loop: false, margin: 0, nav: true, dots: false, responsiveClass: true,
      responsive: { 0: { items: 1 }, 768: { items: 2 }, 992: { items: 3 }, 1200: { items: 3 }, 1400: { items: 4, loop: false } }
   });
});
$(document).ready(function ($) {
   var owl = $(".owl-carousel");
   owl.owlCarousel();
   $(".next-btn").click(function () { owl.trigger("next.owl.carousel"); });
   $(".prev-btn").click(function () { owl.trigger("prev.owl.carousel"); });
   $(".prev-btn").addClass("disabled");
   $(owl).on("translated.owl.carousel", function (event) {
      if ($(".owl-prev").hasClass("disabled")) { $(".prev-btn").addClass("disabled"); } else { $(".prev-btn").removeClass("disabled"); }
      if ($(".owl-next").hasClass("disabled")) { $(".next-btn").addClass("disabled"); } else { $(".next-btn").removeClass("disabled"); }
   });
});
