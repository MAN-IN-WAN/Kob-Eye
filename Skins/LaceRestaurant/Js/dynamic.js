// Wait Plugin for jQuery http://www.inet411.com
(function ($) {
    $.fn.wait = function (option, options) {
        milli = 1000;
        if (option && (typeof option === 'function' || isNaN(option))) {
            options = option;
        } else if (option) {
            milli = option;
        }
        var defaults = {
            msec: milli,
            onEnd: options
        },
            settings = $.extend({}, defaults, options);
        if (typeof settings.onEnd === 'function') {
            this.each(function () {
                setTimeout(settings.onEnd, settings.msec);
            });
            return this;
        } else {
            return this.queue('fx', function () {
                var self = this;
                setTimeout(function () {
                    $.dequeue(self);
                }, settings.msec);
            });
        }
    }
})(jQuery);

// permet de récupérer un code hexa d'un élément css
function rgb2hex(rgb) {
    try {
        var rgb1 = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);

        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        if (rgb1 && rgb1.length == 4) return hex(rgb1[1]) + hex(rgb1[2]) + hex(rgb1[3]);
        return rgb.substr(1, rgb.length - 1);
    } catch (e) {
        return "000000"
    }
}

var fill_captcha = function () {
        var captcha = $(".reqform .captcha");
        if (captcha.length > 0) {
            $.ajax({
                url: "/plugins/api/captcha/pages/captcha.tpl.html",
                type: "GET",
                dataType: "html",
                cache: false,
                async: false,
                success: function (data) {
                    if (data.length > 0) {
                        captcha.each(function () {
                            $(this).empty()
                        });
                        captcha.each(function () {
                            $(this).append(data)
                        });
                        var cap_in = $(".reqform input[name='captcha']");
                        cap_in.val('');
                    }
                },
                error: function () {
                    captcha.hide();
                }
            });
        }
    }

var refill_captcha = function () {
    var captcha = $(".divcaptcha .captcha");
    if (captcha.length > 0) {
        $.ajax({
            url: "/plugins/api/captcha/pages/captcha.tpl.html",
            type: "GET",
            dataType: "html",
            cache: false,
            async: false,
            success: function (data) {
                if (data.length > 0) {
                    captcha.empty();
                    captcha.append(data);
                    var cap_in = $(".divcaptcha .inputText");
                    cap_in.val('');
                }
            },
            error: function () {
                captcha.hide();
            }
        });
    }
}


function isExist(str) {
	if (typeof str !== "undefined") {
		return true;
	} else {
		return false;
	}
}


// la fonction est appelée depuis la page d'impression de l'itinéraire elle doit donc être accessible en dehors du contexte "document.ready"
var getDepart, from, to;

// initialisation des différents composants
$(document).ready(function () {
    // expression régulière pour tester la validité d'un e-mail
    var mail1 = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    var mail = new RegExp(/^([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{3,}[; ]*)+$/i);
    var tel_reg = new RegExp(/^0[1-7][0-9]{8}$/i);

    // empêche l'iframe des statistiques d'ajouter une bordure blanche en bas de page
    $("iframe[height=1]").css("position", "absolute");

    // Si ancre #emergency
    if (location.hash == "#emergency") {
        $("#urgence").removeClass();
        $("#urgence").addClass("bloc heure open");
    }

    // actions d'ouverture sur les infos
    $(".bloc.infos .titreBloc, .bloc.heure p.fleche, .bloc.heure h2, .bloc.emergency p.fleche").each(function () {
        openBloc(this);
        $(this).css('zoom', '1');
    });

    function openBloc(bloc, forceSize) {
        var content = $(bloc).parent().find(".content");
        content.css("position", "relative");
        content.css("display", "block");

        var maxHeight = content.outerHeight() + 40;

        // On regarde si le bloc doit etre ouvert par defaut
        // Regle : bloc horaire tjs ouvert et bloc form contact
        // ouvert uniquement si nbOffice < 2 (cf contact.xsl de
        // chaque template)
        if ($(bloc).parents(".bloc.heure.open").length > 0 || $(bloc).parents(".bloc.emergency.open").length > 0) {
            content.css("height", maxHeight + "px");
            $(bloc).addClass("on");
        } else {
            content.css("height", "0px");
        }
        content.css("overflow", "hidden");
        $(bloc).css("cursor", "pointer");
        $(bloc).click(

        function () {
            var height = 0;
            if ($(bloc).hasClass("on")) {
                $(bloc).removeClass("on");
                content.css("display", "none");
            } else {
                height = maxHeight;
                $(bloc).addClass("on");
                content.css("display", "block");
            }
            content.animate({
                height: height
            }, {
                complete: function () {
                    // permet
                    // d'afficher
                    // correctement
                    // la
                    // feuille
                    // de route
                    // si carto
                    // mappy en
                    // accordéon
                    if (height > 0 && content.find('.bloc.mappy').length > 0) content.css("height", "auto");
                }
            });
        });

    }
    // Ouverture du bloc Urgence au clic sur le bouton
    // 'Urgences' en colonne de droite
    $("#btn_urgence").live("click", function () {
        $("#urgence").removeClass();
        $("#urgence").addClass("bloc emergency open");
        $("#urgence .content").removeAttr("style");
        $(".bloc.emergency h3").each(function () {
            openBloc(this);
        });

    });

    // permet d'ouvrir le bloc plan si on vient de la page "nous
    // situer"
    if (location.hash == "#nousSituer") {
        var h3 = $(".bloc.mappy").parents(".heure");
        if (h3.length > 0) h3.find("h3").trigger("click");
    }

    // actions d'ouverture sur les moyens d'accès
    $(".bloc.moyensAcces h3").each(function () {
        var content = $(this).parent().find(".content");
        content.css("display", "block");
        content.css("position", "relative");
        var maxHeight = content.outerHeight() + 40;
        content.css("height", maxHeight + "px");
        $(this).addClass("on");
        content.css("overflow", "hidden");
        $(this).css("cursor", "pointer");
        $(this).click(function () {

            var height = 0;
            if ($(this).hasClass("on")) {
                $(this).removeClass("on");
            } else {
                height = maxHeight;
                $(this).addClass("on");
            }
            content.animate({
                height: height
            });
        });
    });

    // action de focus et blur sur les input et textarea
    $("input[type=text], textarea").each(function () {
        var thisEl = $(this);
        var oVal = thisEl.val();
        thisEl.attr("oVal", oVal);
        thisEl.focus(function () {
            if (thisEl.val() == oVal) thisEl.val("");
        });
        thisEl.blur(function () {
            if ($.trim(thisEl.val()) == "") thisEl.val(oVal);
        });
    });

    // j'aime facebook
    var likeFB = $(".like");

    if (likeFB.length > 0) $(".like").html('<iframe src="http://www.facebook.com/plugins/like.php?send=false&amp;locale=' + ($("meta[content=en]").length == 1 ? 'en_US' : 'fr_FR') + '&amp;href=' + encodeURIComponent(document.location.toString()) + '&amp;layout=button_count&amp;show_faces=false&amp;width=120&amp;action=recommend&amp;font=arial&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" allowTransparency="true"  style="border:none;overflow:hidden;width:130px;height:20px;"></iframe>');
    else {
        // permet d'enlever l'espace si pas de bouton facebook
        // (pvi santé)
        $("#action").css("marginRight", "0px");
        $("#action ul").css("marginLeft", "0px");
    }

    // captcha
    fill_captcha();

    // charge catalogue
    var catalogue = $("#left .bloc.catalogue, .center-left .bloc.catalogue");
    if (catalogue.length > 0) {
        loadCatalogue('catalogue01.htm');
    }

    function loadCatalogue(urlCATALOGUE) {
    	var urlCat = '/plugins/api/catalog/' + _DATA.idBlocCatalogue + '/' + _DATA.catalogueRef + '/';
        $.ajax({
            url: urlCat + urlCATALOGUE,
            dataType: "html",
            beforeSend: function () {
                catalogue.empty();
                catalogue.html("<img src='" + _DATA.ajaxLoader + "' alt='' align='top'/> En chargement");
            },
            success: function (data) {
                // résoud les problème d'id
                data = data.replace(/id="/gi, "class=\"");
                // résoud les problèmes d'url
                data = data.replace(/src="/gi, "src=\"");
                catalogue.empty();
                catalogue.append($(data).find("div:first").parent());
                // corrige les liens pour chargement en
                // ajax
                catalogue.find("a[href!=#]").each(

                function () {
                    var href = $(this).attr("href");
                    // cas spécifique IE
                    // qui renvoi le
                    // href en absolu
                    if (!$.support.hrefNormalized) {
                        var base = window.location.href.substring(
                        0,
                        window.location.href.lastIndexOf("/") + 1);
                        href = href.replace(
                        base, "");
                    }
                    $(this).attr("href", "#");
                    $(this).click(

                    function () {
                        loadCatalogue(href);
                        return false;
                    });
                });
            },
            error: function () {
                catalogue.hide();
            }
        });
    }

    // charge la carto Mappy

    // charge le diaporama
    var diaporama = $("#diaporama");
    
    if (typeof _DIAPORAMA !== "undefined" && diaporama.length > 0) {
        if (_DIAPORAMA.photos.length > 0) {
            if (_DIAPORAMA.photos.length >= 2) {
                launchDiapo();
            } else {
                var img = $("<img src='" + _DIAPORAMA.photos[0] + "' alt=''/>");
                diaporama.append(img);
                img.animate({
                    opacity: 1
                });
                img.fadeIn('slow');
            }
        } else diaporama.hide();
    }

    function launchDiapo() {
        var img = $("<img src='" + _DIAPORAMA.photos[_DIAPORAMA.actuel] + "' alt='diapo. " + (_DIAPORAMA.actuel + 1) + "/" + _DIAPORAMA.photos.length + "'/>");
        diaporama.append(img);
        img.css({
            "position": "absolute",
            "top": "0px",
            "left": "0px",
            "opacity": "0"
        });
        // erreur de chargement (404)
        img.bind('error', function () {
            _DIAPORAMA.photos.splice(_DIAPORAMA.actuel, 1);
            transitionDiapo(img);
        });
        // on attend que l'image soit chargée
        img.one("load", function () {
            // apparition
            img.animate({
                opacity: 1
            }, {
                duration: 1500
            }).wait(4000, function () {
                transitionDiapo(img);
            });
        }).each(

        function () {
            // force l'évènement load selon les
            // cas
            if (this.complete || (jQuery.browser.msie && parseInt(jQuery.browser.version) == 6)) $(this).trigger("load");
        });
    }

    function transitionDiapo(img) {
        if (_DIAPORAMA.photos.length > 0) {
            if (_DIAPORAMA.actuel == _DIAPORAMA.photos.length - 1) _DIAPORAMA.actuel = 0;
            else _DIAPORAMA.actuel++;
            if (_DIAPORAMA.photos.length >= 2) {
                launchDiapo();
                // disparition
                img.animate({
                    opacity: 0
                }, {
                    queue: false,
                    duration: 1000,
                    complete: function () {
                        img.remove();
                    }
                });
            } else {
                var img = $("<img src='" + _DIAPORAMA.photos[0] + "' alt=''/>");
                diaporama.append(img);
                img.animate({
                    opacity: 1
                });
                img.fadeIn('slow');
            }
        } else diaporama.hide();
    }

    $(".infoEd").click(function () {
        popup(_DATA.infoEd_link, 1000, 590, "yes");
        return false;
    });

    $(".infoCookies").click(function () {
        popup(_DATA.infoCookies_link, 1000, 590, "yes");
        return false;
    });
    
    $(".fermerBandeau").click(function () {
    	$(".bandeau-cookies").remove();
    	return false;
    });
    
    $(".imprimerpvi").click(function () {
        popup(_DATA.print_link, 1000, 590, "yes");
        return false;
    });

    $(".imprimerplan").click(function () {
        popup(_DATA.printplan_link, 1000, 590, "yes");
        return false;
    });

    $(".imprimerbonplan").live("click", function () {
        popup(_DATA.printebp_link, 1000, 590, "yes");
        return false;
    });
    $(".cgvlink").click(function () {
        popup(_DATA.cgv_link, 1000, 590, "yes");
        return false;
    });
    
    // contenu alternatif : animation javascript par défaut
    var hasFlash = false;
    if (window.ActiveXObject) {
        var activex = ["ShockwaveFlash.ShockwaveFlash", "ShockwaveFlash.ShockwaveFlash.3", "ShockwaveFlash.ShockwaveFlash.4", "ShockwaveFlash.ShockwaveFlash.5", "ShockwaveFlash.ShockwaveFlash.6", "ShockwaveFlash.ShockwaveFlash.7"];
        for (var i = 0; i < activex.length; i++) {
            try {
                new ActiveXObject(activex[i]);
                hasFlash = true;
            } catch (e) {}
        }
    } else {
        $.each(navigator.plugins, function () {
            if (this.name.match(/flash/gim)) {
                hasFlash = true;
                return false;
            } else {
                hasFlash = false;
            }
        });
    }
    if ($(".accueil").length > 0 && $("#accueil").length == 0) {
        $("#menu").after('<div id="accueil"><div id="smart"></div></div>');
        hasFlash = false;
    }
    if (!hasFlash) {
    	var parent = this;
        $.ajax({
        	
        	
            url: '/_static/' + _DATA.pvi_version + '/js/ajax/AnimHome.html',
            dataType: 'html',
            error: function (data) {
            	if(typeof _ANIM !== 'undefined') {
                    $("#smart").append("<img src='" + _ANIM.img + "' alt='" + _ANIM.titre1 + " " + _ANIM.titre2 + (isExist(_ANIM.titre3) ? _ANIM.titre3 : "") + "' width='995' height='395'/>");
            	}
            },
            success: function (data) {
                data = data.replace(/\/css\/img/gi, '/_static/' + _DATA.pvi_version + '/css/img');
                $("#smart").append(data).queue(

                function () {
                    InitAnim(
                    _ANIM.img,
                    _ANIM.titre1,
                    _ANIM.titre2, (isExist(_ANIM.titre3) ? _ANIM.titre3 : ""));
                });
            }
        });

        // Pour éviter l'affichage du texte de présentation
        // lorsque le plug-in flash n'est pas présent
        $("#noFlash").remove();
    }

    // appel Gratuit
    $(".appelGratuit").click(function () {
        popup(_DATA.call_link, 510, 490, "yes");
        return false;
    });

});

// éviter les erreurs sur les statistiques pagesjaunes
var _PJS = {};
var eStat_id = {};
eStat_id.serial = function () {};
eStat_id.master = function () {};
eStat_id.niveau = function () {};
var eStat_tag = {};
eStat_tag.post = function () {};
