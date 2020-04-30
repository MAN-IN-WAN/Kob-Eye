/**************************************/

var agent = navigator.userAgent.toLowerCase();
if ( agent.indexOf("msie 7")>0 ) { $('#sidebar').attr('id',"side").wrap('<div id="sidebar" />');}

/*HEADER*/
if($('#header-left #logo').length<1 || $('#header-left').length<1 ){ $('#header-left').hide(); $('#header-right').append('<style> @media screen and (min-width:760px) {#header-right > * { padding-left:20px !important;}}</style>'); }
/***********************************************/
/*footer*/
/***********************************************/
var title = $('#footer .inner .cols .col4 .col-inner h2').eq(0).html();
var social = $('#footer .inner .cols .col4 .col-inner ul').eq(0).html();
$('#footer .inner .cols .col4 .col-inner h2').eq(0).remove();
$('#footer .inner .cols .col4 .col-inner ul').eq(0).remove();

if (typeof $('#footer .inner .cols .col4 .col-inner *').eq(0).prop("tagName") !== "undefined") {
	while( ($('#footer .inner .cols .col4 .col-inner *').eq(0).prop("tagName")).toUpperCase() == 'BR')
	{ $('#footer .inner .cols .col4 .col-inner *').eq(0).remove();}
}

var content = $('#footer .inner .cols .col4 .col-inner').html();
$('#footer .inner .cols .col4 .col-inner').html('<div class="soc-links"><h2>'+title+'</h2><ul class="social">'+social+'</ul></div>').append('<div class="foot-location">'+content+'</div>');
/***********************************************/
/*RESPONSIVE*/
/***********************************************/
//Positionnement des blocs selon le support
if (!$.browser.msie || parseInt($.browser.version, 10) >8){
	var responsive = '';
	var telurl = $('.ico.ico-tel').attr('href');
	$(window).resize(function() {
		//Largeur d'écran sans scrollbar
		$('html').css('overflow-y', 'hidden');
		var W = $(window).width();
		$('html').css('overflow-y', 'visible');
		//Détermination du support
		var new_responsive = '';
		if (W<=760) 
			new_responsive = 'mobile';
		else if (W<=1024) 
			new_responsive = 'tablette';
		else 
			new_responsive = 'desktop';
		//Si changement de support, modification de l'ordre
		if(responsive!=new_responsive){
			placement(new_responsive);
			
			responsive=new_responsive;
			if(typeof(tim) != "undefined")clearInterval(tim); 
			tim = setInterval(function(){videoResize()},500);
			
			var tel = $('.ico.ico-tel').text().replace(/ /g,'').substring(1);
			//var prefixeTel = tel.substring(1,4);
			if(tel.length>=10) tel = tel.substring(tel.length-9);
			if (responsive == "mobile") var tel_href = 'tel:'+'+'+countryCode+tel;
			else var tel_href=telurl;
			$('.ico.ico-tel').attr('href',tel_href);
			
		}
	}).resize();
} else {
	var main = $('#main').detach(), sidebar = $('#sidebar').detach();
	$('#content-inner').append(sidebar);
	$('#content-inner').append(main);
}

/***********************************************/
/*$(document).ready(function() {

var color1 = getContrastYIQ(rgb2hex($('#nav .close').css('color')));
var color2 = getContrastYIQ(rgb2hex($('#tooltip').css('background-color')));
alert(toHSL(color2));
if (color2[1] == "white") {
	// On applique la couleur1 comme couleur de typo des tooltips et comme couleur de bordure et background sur les .ico
	$('#tooltip').css('color',$('#nav .close').css('color'));

}

});

// Fonction permettant de calculer si une couleur est plus proche du noir ou du blanc (permet de switcher entre le blanc et la couleur la plus foncée des 2 couleurs custom)
function getContrastYIQ(hexcolor){
	var r = parseInt(hexcolor.substr(0,2),16);
	var g = parseInt(hexcolor.substr(2,2),16);
	var b = parseInt(hexcolor.substr(4,2),16);
	var yiq = ((r*299)+(g*587)+(b*114))/1000;
	var response = new Array();
	var colorPrinc = (yiq >= 128) ? 'white' : 'black';
	response.push(yiq);response.push(colorPrinc);
	return response;
}*/