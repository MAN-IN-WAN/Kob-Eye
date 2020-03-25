$(document).ready(function() {
	$("#container").prepend('<div id="header-fixed"></div>');

	if ($(".index").length > 0 && ! $("#content-inner #content-top").length) {
		var componentsTop;
		if($("#main #slider1, #main #slider2").length) {
			componentsTop = $(".nav-vignette,.ctn-vignette");
		} else {
			componentsTop = $(".nav-vignette,.ctn-vignette,#slider1,#slider2");
		}
		if(componentsTop.length) {
			$("#content-inner").prepend('<div id="content-top"></div>');
			$("#content-top").prepend(componentsTop);
		}
	}

	upMenu();

	// add arrow to indicate submenues
	$('.inner>#nav>ul a').each(function() {
		if ($(this).next().is("ul")) { $(this).addClass("submenu"); }
	});
	
	// position submenues
	$('.inner>#nav>ul ul').each(function() {
		$(this).css({ top: "0px", width : "270px", left : "-270px" });
//		$(this).css({ top: "0px", left: ($(this).width() * -1 - 5) + "px" });
	});
	$('.inner>#nav>ul ul').hide();

	$("select").selectric();
});

function placement(new_responsive) {
	$('.br_responsive').remove();
	var	main = $('#main').detach(), sidebar = $('#sidebar').detach();
	
	//Positionnement
	switch (new_responsive){
		case 'desktop':
			$('#content-inner').append(sidebar, main, "<br class='clear br_responsive' />");
			sidebar.removeClass("sidebar_tablet sidebar_mobile");
			$(".nosidebar").prepend($('#content-top'));
			$('#baseline').insertAfter("#header-left");
			$('#header-right-nav4').insertBefore('#baseline');
			$('#header-right-nav5').insertBefore('#baseline');
			$('#nav>ul').show();
			upMenu();
			break;
			
		case 'tablette':
			$('#content-inner').append(main, "<br class='clear br_responsive' />");
			sidebar.insertAfter("#nav");
			$('#baseline').insertAfter("#header-left");
			$(".nosidebar").prepend($('#content-top'));
			sidebar.removeClass("sidebar_mobile").addClass("sidebar_tablet");
			$('#header-right-nav4').insertBefore('#baseline');
			$('#header-right-nav5').insertBefore('#baseline');
			$('#nav>ul').show();
			upMenu();
			break;
			
		case 'mobile':
			$('#content-inner').append(main, sidebar, "<br class='clear br_responsive' />");
			sidebar.removeClass("sidebar_tablet").addClass("sidebar_mobile");
			$(".nosidebar").prepend($('#content-top'));
			$('#baseline').insertAfter("#header-left");
			$('#header-right-nav4').insertBefore('#baseline');
			$('#header-right-nav5').insertBefore('#baseline');
			$('#nav>ul').hide();
			$(".index #nav").css("top", "0px");
			break;
	}
	
	// change position and size of panier button depending on the screen size
	if ($("#header-fixed").position() != null && $("#header-fixed").position().top == 0) {
		$("#header-fixed").prepend($("#btn-panier"));
		$("#header-fixed").prepend($("#btn-reserver"));
		$("#header").css("margin-top", $("#header-fixed").height());
	
	} else  {
		$("#header-right-navpanier").prepend($("#btn-panier"));
		$("#header-right-nav3bis").prepend($("#btn-reserver"));
		$("#header").css("margin-top", 0);
	}
}

function initHtml() {
	$("select:not(.localiteInput)").selectric();
}

function upMenu(){
	if ($(".index #content-inner").is(':visible')) {
		var move_down =  $(window).height() - $("#header").height() - 75;
		$(".index #content-inner").css("margin-top", move_down + "px");
		/*$(".index #nav").css("top", "-" + move_down + "px");*/
	}
	
}
