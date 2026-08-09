// City Gate Farm - Custom JavaScript

// ============================================
// SMART HEADER - Fixed on scroll, hide on scroll down, show on scroll up
// ============================================
$(document).ready(function() {
    let lastScrollTop = 0;
    let isFixed = false; // track if header is in fixed state
    const header = $('.header');
    const fixedThreshold = 150; // px - when header becomes fixed
    
    // Initial state
    header.addClass('header-visible');
    
    $(window).on('scroll', function() {
        const currentScroll = $(this).scrollTop();
        const windowWidth = $(window).width();
        const nowFixed = currentScroll > fixedThreshold;
        
        // State change: became fixed or returned to relative
        if (nowFixed && !isFixed) {
            // Just became fixed
            header.addClass('headerActive');
            isFixed = true;
            // Ensure header is visible when it first sticks
            header.removeClass('header-hidden').addClass('header-visible');
        } else if (!nowFixed && isFixed) {
            // Just became relative (scrolled back to top)
            header.removeClass('headerActive header-hidden header-visible header-hidden-mobile');
            isFixed = false;
        }
        
        // If currently fixed, handle hide/show based on scroll direction
        if (isFixed) {
            if (currentScroll > lastScrollTop) {
                // Scrolling down - hide
                if (windowWidth > 768) {
                    header.addClass('header-hidden').removeClass('header-visible');
                } else {
                    header.addClass('header-hidden-mobile');
                }
            } else {
                // Scrolling up - show
                if (windowWidth > 768) {
                    header.removeClass('header-hidden').addClass('header-visible');
                } else {
                    header.removeClass('header-hidden-mobile');
                }
            }
        }
        
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });
});
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
