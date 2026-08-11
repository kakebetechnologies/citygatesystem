// City Gate Farm - Custom JavaScript

// ============================================
// SMART HEADER — Scroll handler
// CSS (cg-redesign.css) handles all visual states.
// Toggles .headerActive for the homepage transparent→white transition, and
// .nav-faded to fade the navbar out while scrolling down, back in when
// scrolling up (or near the top) — same behavior on every page.
// ============================================
$(document).ready(function() {
    var $header = $('#headerOne');
    var threshold = 80;
    var fadeThreshold = 160;

    // Some page layouts scroll the window; others (where body has a
    // constrained height) scroll <body> itself. Read whichever is actually
    // moving so this works either way.
    function currentScrollTop() {
        return Math.max(
            window.pageYOffset || 0,
            document.documentElement.scrollTop || 0,
            document.body.scrollTop || 0
        );
    }

    var lastScrollTop = currentScrollTop();

    function updateHeader() {
        var scrollTop = currentScrollTop();

        if (scrollTop > threshold) {
            $header.addClass('headerActive');
        } else {
            $header.removeClass('headerActive');
        }

        if (scrollTop > lastScrollTop && scrollTop > fadeThreshold) {
            $header.addClass('nav-faded');
        } else {
            $header.removeClass('nav-faded');
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }

    $(window).add(document).add('body').on('scroll', updateHeader);
    updateHeader(); // run on load
});


// ============================================
// DROPDOWN MENUS
// ============================================
$('.dropdown').on('show.bs.dropdown', function(e){
    $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
});
$('.dropdown').on('hide.bs.dropdown', function(e){
    $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
});


// ============================================
// HEADER HEIGHT CALCULATIONS
// ============================================
$(document).ready(function($) {
    var mastHeight = $('.header').outerHeight();
    $('.searchBox').css('top', mastHeight); 
    $('.mainHeader .navbar div#collapsibleNavbar').css('top', mastHeight); 
    
    var topHeaderHeight = $('.topHeader').outerHeight();
});


// ============================================
// SEARCH BOX TOGGLE
// ============================================
$(".searchBtn").click(function(){
    $(".searchBox").toggleClass("active");
});


// ============================================
// CART SIDEBAR TOGGLE
// ============================================
$(".cardBtn").click(function(){
    $(".cartSideBox").toggleClass("active");
});
$(".closeCart").click(function(){
    $(".cartSideBox").toggleClass("active");
});


// ============================================
// QUANTITY INCREMENT/DECREMENT
// ============================================
var buttonPlus  = $(".qty-btn-plus");
var buttonMinus = $(".qty-btn-minus");

buttonPlus.click(function() {
    var $n = $(this).parent(".qty-container").find(".input-qty");
    $n.val(Number($n.val())+1);
});

buttonMinus.click(function() {
    var $n = $(this).parent(".qty-container").find(".input-qty");
    var amount = Number($n.val());
    if (amount > 0) {
        $n.val(amount-1);
    }
});


// ============================================
// DROPDOWN ON HOVER
// ============================================
$(document).ready(function(){
    $(".navbar-nav .dropdown").hover(            
        function() {
            $('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideDown("400");
            $(this).toggleClass('open');        
        },
        function() {
            $('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideUp("400");
            $(this).toggleClass('open');       
        }
    );
});


// ============================================
// CLOSE SEARCH WHEN CLICKING OUTSIDE
// ============================================
$(function() {
    $(document).click(function(e) {
        var target = e.target;
        if (!$(target).is('.searchBtn, .searchBoxInner') && !$(target).parents().is('.searchBtn, .searchBoxInner')) {
            $(".searchBox.active").removeClass("active");
        }
    });
});


// ============================================
// CLOSE CART WHEN CLICKING OUTSIDE
// ============================================
$(function() {
    $(document).click(function(e) {
        var target = e.target;
        if (!$(target).is('.cartSideBox, .cardBtn') && !$(target).parents().is('.cartSideBox, .cardBtn')) {
            $(".cartSideBox.active").removeClass("active");
        }
    });
});


// ============================================
// SCROLL ANIMATIONS (Fade-in)
// ============================================
$(document).on("scroll", function () {
    var pageTop = $(document).scrollTop();
    var pageBottom = pageTop + $(window).height();
    var tags = $(".fadein");

    for (var i = 0; i < tags.length; i++) {
        var tag = tags[i];
        if ($(tag).offset().top < pageBottom) {
            $(tag).addClass("visible");
        } else {
            $(tag).removeClass("visible");
        }
    }
});


// ============================================
// CUSTOM CURSOR
// ============================================
var cursor = document.querySelector('.cursor');
var cursorinner = document.querySelector('.cursor2');
var links = document.querySelectorAll('a');

document.addEventListener('mousemove', function(e){
    cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`;
});

document.addEventListener('mousemove', function(e){
    cursorinner.style.left = e.clientX + 'px';
    cursorinner.style.top = e.clientY + 'px';
});

document.addEventListener('mousedown', function(){
    cursor.classList.add('click');
    cursorinner.classList.add('cursorinnerhover');
});

document.addEventListener('mouseup', function(){
    cursor.classList.remove('click');
    cursorinner.classList.remove('cursorinnerhover');
});

links.forEach(link => {
    link.addEventListener('mouseover', () => {
        cursorinner.classList.add('hover');
    });
    link.addEventListener('mouseleave', () => {
        cursorinner.classList.remove('hover');
    });
});


// ============================================
// IMAGE LIGHTBOX / POPUP
// ============================================
$('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: true,
    fixedContentPos: false
});

$('.image-popup-vertical-fit').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    mainClass: 'mfp-img-mobile',
    image: {
        verticalFit: true
    }
});


// ============================================
// GALLERY
// ============================================
$('.portfolio_img_text').magnificPopup({
    delegate: '.img-link',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1]
    },
    image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: function (item) {
            return item.el.attr('title') + '<small></small>';
        }
    }
});

$('.portfolio_img_icon').magnificPopup({
    delegate: '.img-link',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1]
    },
    image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        titleSrc: function (item) {
            return item.el.attr('title') + '<small></small>';
        }
    }
});
