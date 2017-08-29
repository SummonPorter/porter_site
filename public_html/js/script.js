
/**
 * Accepts an object of styles, and writes them to the page.
 * styles format:
 * 'selector': {
 *   style: 'value',
 *   marginTop: 'value',
 *   ...
 * }
 */
function cssConsole (styles, delay)
{
    var css = [];
    var animation = false;
    var duration = false;
    // animationName: 'shake-horizontal',
    // animationDuration: '.8s',
    for (var i in styles) {
        css.push (i);
        css.push (" {\n");
        for (var j in styles[i]) {
            if (j == 'animationName') {
                animation = styles[i][j]
            }
            if (j == 'animationDuration') {
                if (a = styles[i][j].match(/([\d.]+)s/)) {
                    duration = parseFloat(a[1]) * 1000;
                } else if (a = styles[i][j].match(/([\d.]+)ms/)) {
                    duration = parseInt(a[1]);
                }
            }
            css.push("\t"+j.replace(/([A-Z])/, function(str) { return '-'+str.toLowerCase() }));
            css.push(':');
            css.push(styles[i][j]);
            css.push(";\n");
        }

        css.push ("\n}");
    }

    $('#cssConsole').html(css.join(""));
    if (animation) {
        setTimeout(function(){
            $('#cssConsole').html($('#cssConsole').html().replace(new RegExp(animation, 'g'), ''));
        }, duration);
    }
}

$(".testimonialCarousel").flickity({
    cellAlign: "center",
    prevNextButtons: !1,
    initialIndex: 1,
    wrapAround: !0,
    percentPosition: !1,
    contain: !0,
    autoPlay: 8e3
}), $(function() {
    $('a[href*="#"]:not([href="#"]):not(.no-scroll)').click(function() {
        if (location.pathname.replace(/^\//, "") == this.pathname.replace(/^\//, "") && location.hostname == this.hostname) {
            var t = $(this.hash);
            if (t = t.length ? t : $("[name=" + this.hash.slice(1) + "]"), t.length) return $("html,body").stop(!0, !0).animate({
                scrollTop: t.offset().top
            }, 1e3, "easeInOutQuint"), !1
        }
    })
});

var rem = function () {
    var html = document.getElementsByTagName('html')[0];
    return function () {
        return parseInt(window.getComputedStyle(html)['fontSize']);
    }
}();

$(function(){

    $(window).scroll(function(){

        if ($(window).scrollTop() > 10) {
            $("header").addClass("not-page-top");
        }
        else {
            $("header").removeClass("not-page-top");
        }

    }).scroll();



    var nav = $('nav#nav');
    var mbEl = $('mobile-nav');

    var closeMbEl = function() {
        clearInterval(mbElTimer);
        mbEl.fadeOut();
        $('body, html').css({
            overflow: "auto"
        });
    };
    var mbEl = $('mobile-nav');
    var mbElTimer;
    var mbElFn = function() {
        mbEl.css('height', $(window).height()+'px');
    };
    var openMbEl = function() {
        mbElTimer = setInterval(mbElFn, 250);
        mbEl.fadeIn();
        $('body, html').css({
            overflow: "hidden"
        });
    };

    $('header .mobile-nav').click(function(){
        if(mbEl.is(':visible')) {
            closeMbEl();
        } else {
            openMbEl();
        }
        /*if (desktop_nav.hasClass("js-opened")) {
            desktop_nav.slideUp("slow", "easeOutExpo").removeClass("js-opened");
            $(this).removeClass("active");
        }
        else {
            desktop_nav.slideDown("slow", "easeOutQuart").addClass("js-opened");
            $(this).addClass("active");
        }*/

    });
    // mbEl.css('height', $(window).innerHeight()+'px');



    function escapeNav (e) {
        if (mbEl.is(':visible') && e.key === "Escape") {
            closeMbEl();
        }
    }

    $(window).keyup(escapeNav);

    $('mobile-nav .close').click(function(){
        closeMbEl();
    });
});


function debounce (delay, cb) {
    var myid = debounce.id++;
    debounce.timeouts[myid] = 0;
    return function() {
        clearTimeout(debounce.timeouts[myid]);
        var args = arguments;
        debounce.timeouts[myid] = setTimeout(function(){ cb.apply(args); }, delay);
    };
}
debounce.id = 0;
debounce.timeouts = {};
