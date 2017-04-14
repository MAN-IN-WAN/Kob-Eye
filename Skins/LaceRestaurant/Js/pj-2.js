(function($,h,c){var a=$([]),e=$.resize=$.extend($.resize,{}),i,k="setTimeout",j="resize",d=j+"-special-event",b="delay",f="throttleWindow";e[b]=250;e[f]=true;$.event.special[j]={setup:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.add(l);$.data(this,d,{w:l.width(),h:l.height()});if(a.length===1){g()}},teardown:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.not(l);l.removeData(d);if(!a.length){clearTimeout(i)}},add:function(l){if(!e[f]&&this[k]){return false}var n;function m(s,o,p){var q=$(this),r=$.data(this,d);r.w=o!==c?o:q.width();r.h=p!==c?p:q.height();n.apply(this,arguments)}if($.isFunction(l)){n=l;return m}else{n=l.handler;l.handler=m}}};function g(){i=h[k](function(){a.each(function(){var n=$(this),m=n.width(),l=n.height(),o=$.data(this,d);if(m!==o.w||l!==o.h){n.trigger(j,[o.w=m,o.h=l])}});g()},e[b])}})(jQuery,this);


var isMobile = {
Android: function() {
    return navigator.userAgent.match(/Android/i);
},
BlackBerry: function() {
    return navigator.userAgent.match(/BlackBerry/i);
},
iOS: function() {
    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
},
Opera: function() {
    return navigator.userAgent.match(/Opera Mini/i);
},
Windows: function() {
    return navigator.userAgent.match(/IEMobile/i);
},
any: function() {
    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
}
};

$("#popinalert").click(function(){ 
	var localite = $("#localite").val();
	var typeBien = $("#typeBien").val();
	var prixMax = $.trim($("input[name=prixMax]").val());
	var surfaceMin = $.trim($("input[name=surfaceMin]").val());
	// vérification des types des champs
	var alerte = "";
	var pattern = /^[0-9,]+$/g;
	if (pattern.exec(surfaceMin) == null && surfaceMin !== "") {
		alerte += _TEXTES.surfaceMin + "\n";
	}
	pattern = /^[0-9,]+$/g;
	if (pattern.exec(prixMax) == null && prixMax !== "") {
		alerte += _TEXTES.prixMax + "\n";
	}
	
	// affichage d'un message d'erreur si besoin
	if (alerte !== "") {
		alert(alerte);
		return false;
	}
	
	// sauvegarde des critères de recherche
	$.ajax({
		url : _DATA.urlSaveRecherche,
		data : {BiensLocalite:localite, BiensTypeBien:typeBien, BiensPrixMax:prixMax, BiensSurfaceMin:surfaceMin},
		type : "post",
		error: function() { },
		success : function(data) {
			// ouverture de le popup d'alerte
			popInAjax((isExist(_DATA)?_DATA.alerteUrl:'/alerte'), "GET", $(this)); 
		}
	});

	return false;
});
// validation formulaire envoyer le plan
//$(".contact form").submit(function () {
//	return validFormEnvoyerAlerte();
//});//Modified by Net-Ng

/***************************************************/
$('#container').css('visibility','visible');
/***************************************************/
//ICONES
$('.ico, #main .repli').prepend('<span class="js-ico"><span class="js-ico1"></span><span class="js-ico2"></span></span>');
/***************************************************/
//POPIN POPOUT
function popIn(dom, link, popnClass){
	if ($("#popn-bg").length == 0) {
		if(!popnClass) {
			var popnClass = "";
		}
		$("#popn").html('').append($('<div id="popn-bg"></div><div id="popn-inner" class="'+popnClass+'"></div>')).show();
		$("#popn-inner").hide().append(dom).delay(500).show();
		$("#popn-bg").hide().fadeIn(500);
		$('html,body').scrollTop(0);

	    // Popin Alerte e-mail Immo : la popin ne se ferme que quand on clique sur annuler
	    if($("#alerteRealtyform") != null && $("#alerteRealtyform").hasClass("realtyimmo")) {
	    }
	    else {
			$("#popn-bg").click(function() { popOut(); });
			$("#popn-inner").click(function(e) { }); //Bug fix IEMobile #20707
	    }
		$("#popn-inner .close").click(function() { popOut() });
		$('#popn .ico').prepend('<span class="js-ico"><span class="js-ico1"></span><span class="js-ico2"></span></span>');
		addrem();	/*events ajout/suppression de destinataire*/
	} else {
		$("#popn-inner").empty().append(dom).delay(500).show();
		// refresh de la captcha pour mise en session
		$(".captcha img").attr("src", "/plugins/api/captcha/captcha.png?" + new Date().getTime());
		$('#popn .ico').prepend('<span class="js-ico"><span class="js-ico1"></span><span class="js-ico2"></span></span>');
	}
		
		/**/
	$("#popn input,#popn textarea").each(function(i){
		if($(this).val() == '' ) $(this).val($(this).attr('oval'));
		$(this).focus(function(){ if($(this).val() == $(this).attr('oval')) $(this).val(''); })
			   .blur(function(){ if($(this).val() == '' ) $(this).val($(this).attr('oval')); });		
	});
		
    // Popin Partager pour Bon Plan Si le click viens d'un bon plan, on affiche la barre de choix du partage
    if (link.hasClass("partager")) {
    	popinBonPlanPartager();
    	//Events
        $("#popn .facebook").click(function() {fb_share();return false;});
    	$("#popn .twitter").click(function() {twitter_share();return false;});
        $('#popn .recommander.partager').click(function(){ popInAjax((isExist(_DATA)?_DATA.recommanderUrl:'/ws/fr/recommander_html5'), "GET", $(this));; return false;});
    	$('#popn .envoyer.partager').click(function(){ popInAjax((isExist(_DATA)?_DATA.smsUrl:'./commun/ajax/sms.htm'), "GET", $(this)); return false;});
    	$("#popn-inner .close").click(function() { popOut() }); // Bug fix #19833 #35786
    }
       
    //Popin en-profiter pour Bon Plan
	if (isExist(_DATA.bon_plan_actif) && $("#popn .en-profiter").length > 0) {
		return popinBonPlanEnProfiter();
	}
    
	// recommander à un ami (mail)
    $("#popn .recommande form").submit(function () {
    	var link = $("#popn .action .envoyer");
    	return validFormRecommander(link);
    });
    
    // recommander à un ami (sms)
    $("#popn .recommandeSMS form").submit(function () {
    	return validFormRecommanderSms(link);
    });
    
    // validation formulaire envoyer le plan
    $("#popn .envoyerPlan form").submit(function () {
    	return validFormEnvoyerPlan();
    });//Modified by Net-Ng
    
    $(".contact form").submit(function () {
    	return validFormEnvoyerAlerte();
    });
//    initHtml();
}
	
	
function popOut(){
	$("#popn").hide().html('');
	return false;
	//$('html,body').css({'overflow':'visible'});
}
function popInAjax(url, type, link){
	popInAjax(url, type, link, function() {}, '');
}
function popInAjax(url, type, link, onSuccess, popnClass) {
	$.ajax({
		url : url,
		dataType : "html",
		type : type,
		error : function(data) {
			return;
		},
		success : function(data) {
			popIn(data, link, popnClass);
			if(onSuccess !== undefined) {
				onSuccess();
			}
			return;
		}});
	return false;
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");
    var params = {}, tokens, re = /[?&]?([^=]+)=([^&]*)/g;
    while (tokens = re.exec(qs)) { params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]); }
    return params;
}

/***************************************************/
//AJOUT DESTINATAIRE DANS LE FORMULAIRE RECOMMANDER
function addrem(){
	$(".addrem").unbind();
	$(".addrem").click(function(){
		if($(this).parent().parent().hasClass('dest')){
			$(this).parent().parent().parent().find(">.addrem").show();
			$(this).parent().parent().remove();
		}else{
			chaine = '<div class="dest" id="secondDestinataire">'
			+'<div class="formBox"><label>Coordonnées du second destinataire</label> <a class="addrem">Retirer ce destinataire</a></div>'
			+'<div class="formBox half first"><input name="re_nomDestinataire2" oval="e-mail*" required="required" type="text" /></div>'
			+'<div class="formBox half"><input name="re_emailDestinataire2" oval="Numéro de téléphone*" required="required" type="text"></div>'
			+'</div>';//Modified by Net-Ng
			$(this).after(chaine).hide();
		}
		addrem();
	});
}

/***************************************************/
//Rollover/rollout
function toolTips(o,pos){
	if(!isMobile.any()) {
		$(o).attr('tooltip',$(o).attr('title')).removeAttr('title').mouseenter(function(){
			if($(this).attr('tooltip') && $(this).attr('tooltip')!= ""){
				$('#tooltip .content').html($(this).attr('tooltip'));
				$('#tooltip').stop(true,true).css({
					'left':$(pos).offset().left+($(pos).width()/2),
					'top':$(pos).offset().top+$(pos).height()+10
				}).fadeIn(600);
			}
		}).mouseleave(function(){
			$('#tooltip').fadeOut(300);
		});	
	}
}

/***************************************************/
function videoResize(){
	if($("#video").length>0){
		var video = $("#video object"),
			H = video.height(),
			W = video.width(),
			ratio = H/W;
		video.width($('#video').width()).height($('#video').width()*ratio);
		if(W>1 && H>1){ if(typeof(tim)!="undefined")clearInterval(tim); }
	}else{ if(typeof(tim)!="undefined")clearInterval(tim); }
}

$(document).ready(function() {	

	//Events
	$(".ico-social1").click(function() {fb_share();return false;});
	$(".ico-social2").click(function() {twitter_share();return false;});
	$(".ico-social3").click(function() {google_share();return false;});//Add by Net-ng
	$('.ico-social4').click(function(){ popInAjax((isExist(_DATA)?_DATA.recommanderUrl:'/ws/fr/recommander_html5'), "GET", $(this));});
	$('.ico-social5').click(function(){ popInAjax((isExist(_DATA)?_DATA.smsUrl:'/ws/fr/sms_html5'), "GET", $(this));});
	$('.bonsplansdetails .partager').click(function(){ popInAjax((isExist(_DATA)?_DATA.recommanderUrl:'/ws/fr/recommander_html5'), "GET", $(this));});
	$('.bonsplansdetails .en-profiter').click(function(){ popInAjax((isExist(_DATA)?_DATA.enProfiterUrl:'/script/ajax/fr/en_profiter_html5.html'), "POST", $(this));});
    $(".situer #actionMappy .envoyer").click(function(){ popInAjax((isExist(_DATA)?_DATA.planUrl:'/ws/fr/plan_html5'), "GET", $(this)); return false;});//Modified by Net-ng


	/***********************************************/
	//IMAGES VIGNETTES
	// Repositionne les images en hauteur
	/***********************************************/

    //	$('.liste-vignette li').hide().css('visibility','visible').fadeIn(1000,"easeInOutCubic");
	
	//NAV
	//Ouverture et fermeture du menu mobile
	$('#nav .resp-mobile').click(function(){
		$('#nav>ul').stop(true,true).slideToggle();
	});
	//Ouverture et fermeture des sous-menus
	$('#nav>.inner>ul ul').hide();
	if (!("ontouchstart" in window || "ontouch" in window)) {
		$('#nav a').mouseover(function(event){
			$(this).parents('li').children('ul').stop(true,true).fadeIn(300);
			event.stopPropagation();
			if ($(this).attr('href')=="#") return false;
		});
		$('#nav a').mouseout(function(event){
			$(this).parents('li').children('ul').stop(true,true).delay(100).fadeOut(300);
			event.stopPropagation();
			if ($(this).attr('href')=="#") return false;
		});
	}
	/***************************************************/
	//(AFFICHER/CACHER) LE CONTENU DE L'ARTICLE
	$('article header .std').parents("header").click(function(){
		var objs = $(this).parent().find(">*").not("header,.visible");
		if($(objs).length){
			$(objs).slideToggle(300);
			$(this).toggleClass("repli");
		}
	});
	$('.conso header').toggleClass("repli");
	$('.conso').find(">*").not("header").hide(0);
	/**ZOOOM**/
	var topscroll = document.documentElement.scrollTop + document.body.scrollTop;
	$('#zonezoom').mouseenter(function(event){
	 var X = event.clientX-$(this).offset().left-10;
	 var Y = event.clientY-$(this).offset().top-10+topscroll;
	 $(this).css({
		  'top':Y+41+'px',
		  'left':X-41+'px'
		});
	});
	
	// Start easyZoom
	var $zoom = $('.figures figure:eq(0)').easyZoom({
		parent: 'div.description',
		id: 'previewzoom'
	});
	
	
	$('.figures-nav img.mini').click(function(){
		
		
		var index = $(".figures-nav img.mini").index(this);
 		
		
		// Start easyZoom
		var $zoom = $('.figures figure:eq('+index+')').easyZoom({
			parent: 'div.description',
			id: 'previewzoom'
		});
		
	});
	
	//SITUER
	if($(".situer").length!==0) {
		$('label[for=itiDepart]').html(_TEXTES.depart);
		$('label[for=itiArrive]').html(_TEXTES.arrivee);
	}

	//(AFFICHER/CACHER) CONTACT URGENCE
	$(".ico-mrgcy").click(function() { 
		if($(".contact_urgences").length>0){ 
			$(".contact_urgences").find(">*").not("header").slideToggle(300); 
			$(".contact_urgences header").toggleClass("repli"); 
			return false; 
		}
	});
	
	var $_GET = getQueryParams(document.location.search);
	if(typeof($_GET['open']) != "undefined"){ $("."+$_GET['open']+' *').show(); /*alert($_GET['open']);*/ }
	/***************************************************/
	//PLACEHOLDER
	$("input,textarea").each(function(i){
		if($(this).val() == '' ) $(this).val($(this).attr('oval'));
		$(this).focus(function(){ if($(this).val() == $(this).attr('oval')) $(this).val(''); })
			   .blur(function(){ if($(this).val() == '' ) $(this).val($(this).attr('oval')); });		
	});
	
	addrem();
	/***************************************************/
	//ONGLETS
	$(".onglets li").click(function(){
		$(".onglets li").removeClass("on");
		$(this).addClass("on");
		return false;
	});
	/***************************************************/
	//LISTE A PUCES
	$('#main ul').each(function(){
		if(!$(this).attr('class')) $('li',this).wrapInner('<q />'); 
		if($(this).attr('class')=="large" || $(this).attr('class')=="indent") $('li',this).wrapInner('<q />'); 
	});
	/***************************************************/
	//TOOLTIP
	var tooltip = $('body').append('<div id="tooltip"><div class="arrow-up"></div><div class="content"></div></div>');
	$('#tooltip').hide();
	//Icones header
	$('#header .ico').each(function(){
		toolTips($(this),$('.js-ico',this));
	});
	//Activation sur les .tooltip
	toolTips($('.tooltip'),$('.tooltip'));

	/***************************************************/
	//Datepicker reservation
	//	$("#co_dateFrom").datepicker();
	/***************************************************/
	/*LANGUE*/
	var isopen = 0;
	$('.langue div').click(function(){
		if( isopen ==0 )	{ isopen = 1; $(this).parent().find("ul").slideDown(300);}
		else				{ isopen = 0; $(this).parent().find("ul").slideUp(300);}
		return false;
	});
	$('.langue li').click(function(){
		text = $(this).html();
		$(this).parent().parent().find("div").html(text);
		isopen = 0; $(this).parent().slideUp(300);
	});

	/**************************************************/
	//BOUTIQUE 
	
	if($('body.liste').length>0){
		$(window).resize(function(){
			if($(window).width()<760){
				$('body.liste ul.liste-vignette').toggleClass("liste-vignette",false).toggleClass("liste-desc",true); 
			}
		});
	}
	
			
	// Lien "More" sur descriptions liste produit - Limite
//	minimizeDesc();
//	initEtiquettes();
	
	
	/*carousel video*/
	if ($(".ctn-images-video").length) $(".ctn-images-video").jCarouselLite({
		visible: 4,
		circular: false,
	    btnNext: ".nav-images-videos .next",
	    btnPrev: ".nav-images-videos .prev"
	});
	
	
	/* Carte Resto */
	
	if ($('body').hasClass('restaurant')) {
		
		$('h1.slim').each(function (id,ele){
			
			var h1 = $(ele).find('div');
			var h2 = $(ele).parent().find('h2').find('div');
			
			// Largeur du header
			var parentW = $(ele).parent().width();
			// Largeur du H1
			var h1W = $(h1).width();
			// Largeur du H2
			var h2W = $(h2).width();
			
			var h1textWidth = $(ele).text().length;
            var h2textWidth = $(ele).parent().find('h2').text().length;
           	
           	// Le H1 et le H2 se chevauchent, le H2 se positionne à la suite du H1 en relative et on réduit le padding du H1
           	if ((h1W+h2W)>=(parentW-20)) {
           	$(h1).parent().addClass('oneLine');
           	$(h2).parent().addClass('underH1');
           	}
		});
	
	}
	
	
	/* MAIN ie7 avec sidebar visible */
	if ($('html').hasClass('ie7')) {
		if ($('#sidebar').is(':visible')) $('#main').addClass('wsidebar');
	}
	
    if( $("html.ie7, html.ie8, html.ie9").length>0 ){
        $.each($("input[placeholder]"), function(){
            
            $(this).parent().resize(function(){
                $.each($("div.placeholder",this),function(){
                    var nom = $(this).attr("id").replace('p_',''); var obj = $("input[name='"+nom+"']");
                    if($(obj).val()==''){
                        $(this).attr("style", "display:block; clear:none; z-index:5; text-indent:4px; overflow:hidden; color:#CCCCCC; font-weight:normal; position:absolute; line-height:"+$(obj).outerHeight()+"px; height:"+$(obj).outerHeight()+"px; margin-top:"+$(obj).css('margin-top')+"; top:"+$(obj).position().top+"px; left:"+$(obj).position().left+"px; width:"+$(obj).width()+"px" );
                    }
                });
            });
            
            if($(this).val()=="" && $("#p_"+$(this).attr("name")).length<1){ 
                $(this).parent().css('position','relative').append("<div style='display:block; clear:none; z-index:5; text-indent:4px; overflow:hidden; color:#CCCCCC; font-weight:normal; position:absolute; line-height:"+$(this).outerHeight()+"px; height:"+$(this).outerHeight()+"px; margin-top:"+$(this).css('margin-top')+"; top:"+$(this).position().top+"px; left:"+$(this).position().left+"px; width:"+$(this).width()+"px' id='p_"+$(this).attr("name")+"' class='placeholder' >"+$(this).attr("placeholder")+"</div>");
            }
        });
        $('div.placeholder').click(function(){
            $( 'input[name="'+$(this).attr("id").replace('p_','')+'"]' ).focus(); $(this).hide(0);
        });
        $("input[placeholder]").focus(function(){ $('#p_'+$(this).attr('name')).hide(0); });
        $("input[placeholder]").blur(function(){ if($(this).val()=="")$('#p_'+$(this).attr('name')).show(0); });

    }

});

function rtrim (str, charlist) {
  charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
  var re = new RegExp('[' + charlist + ']+$', 'g');
  return (str + '').replace(re, '');
}
