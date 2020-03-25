/* Gestion de la vertical restaurant */
$(document).ready(

function () {

    $(".bloc.fixedPriceMenu td.desc .content").click(

    function () {
        if ($(this).hasClass("selected")) {
            $(this).parent().children(".extra").hide();
            $(this).parent().children(".content").removeClass("selected");
        } else {
            $(".bloc.fixedPriceMenu td.desc .extra").hide();
            $(".bloc.fixedPriceMenu td.desc .extra").removeAttr("style");
            $(".bloc.fixedPriceMenu td.desc .content").removeClass('selected');

            $.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
            /* IE or Chrome */
            if ($.browser.msie || $.browser.chrome) {
                var parentHeight = $(this).parent().height();
                var childrenHeight = $(this).parent().children(".content").height();
                var position = parentHeight - childrenHeight;

                $(this).parent().children(".extra").attr("style", "margin-top:" + position + "px!important;");
            }

            $(this).parent().children(".extra").show();
            $(this).parent().children(".content").addClass("selected");
        }
    });

    var carousel = $("#carousel");
    if (carousel) {
        var numPhoto = carousel.attr("nbItems");

        // Calcul de la taille de l'ul
        var widthV = 170;
        var numDecalage = 3;
        var widthT = numPhoto * widthV;
        var animate = false;
        thumbnailsUl = carousel.find("ul:first");
        thumbnailsUl.width(widthT + 60);

        // action sur les flèches du carrousel
        carousel.find("div.previous").click(

        function () {

            if (!animate) {
                animate = true;
                var i;
                for (i = 0; i < numDecalage; i++) {
                    thumbnailsUl.find('li:first').before(
                    thumbnailsUl.find('li:last'));
                }
                thumbnailsUl.css({
                    'left': -(widthV * numDecalage)
                });
                thumbnailsUl.animate({
                    left: 0
                }, 450, function () {
                    animate = false;
                });
            }
        });
        carousel.find("div.next").click(

        function () {

            if (!animate) {
                animate = true;
                thumbnailsUl.animate({
                    left: '-=' + (widthV * numDecalage)
                }, 450, function () {
                    var i;
                    for (i = 0; i < numDecalage; i++) {
                        thumbnailsUl.find('li:last').after(
                        thumbnailsUl.find('li:first'));
                    }
                    thumbnailsUl.css({
                        'left': 0
                    });
                    animate = false;
                });
            }
        });
    }
});
