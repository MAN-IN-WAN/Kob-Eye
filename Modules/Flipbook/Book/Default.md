[HEADER JS]Skins/[!Systeme::Skin!]/Js/modernizr.2.5.3.min.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/jquery.mousewheel.min.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/jquery.172.min.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/jquery-ui-1.8.20.custom.min.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/magazine.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/turn.min.js[/HEADER]
[HEADER JS]Skins/[!Systeme::Skin!]/Js/zoom.min.js[/HEADER]

<script type="text/javascript">
var navigateur = navigator.appName
if (navigateur == 'Microsoft Internet Explorer')
{
	var ieVersion = false;
	/*@cc_on
	  @if (@_jscript_version >= 9)
	    ieVersion = true;	  
	  @end
	@*/
	
	if(!ieVersion)
	{
		var oScript =  document.createElement("script");
		oScript.src = "Skins/[!Systeme::Skin!]/Js/turn.html4.min.js";
		oScript.type = "text/javascript";
		document.body.appendChild(oScript);
	}
}
</script>

[HEADER CSS]Skins/[!Systeme::Skin!]/Css/magazine.css[/HEADER]
[HEADER CSS]Skins/[!Systeme::Skin!]/Css/jquery.ui.html4.css[/HEADER]
[HEADER CSS]Skins/[!Systeme::Skin!]/Css/jquery.ui.css[/HEADER]

[IF [!Img!]='']
	[!NbPages:=0!]
	[!Img:=!]
	[STORPROC [!Query!]/Page|Pages|0|10000|Id|ASC]
		[!Img+=[!Domaine!]/[!Pages::Image!].limit.1654x2339.jpg;!]
		[!NbPages+=1!]
	[/STORPROC]
[/IF]

<div class="magazine-viewport">
	[COUNT [!Query!]/Page|PagesNb]
    [STORPROC [!Query!]|B]
    <div class="container">
		<div id="direction">
			<div class="fond_direction">
				<div class="fond_direction_g">
					<img src="/Skins/[!Systeme::Skin!]/Img/picto/gaucheFin_03.png" class="previous_end" onmouseover="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/gaucheFinHover_19.png';" onmouseout="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/gaucheFin_03.png';" onclick="$('.magazine').turn('page', 1)" alt="Aller au début" title="Aller au début" />
					<img src="/Skins/[!Systeme::Skin!]/Img/picto/gauche_05.png" class="previous" onmouseover="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/gaucheHover_21.png';" onmouseout="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/gauche_05.png';" onclick="$('.magazine').turn('previous')" alt="Page précédente" title="Page précédente" />
				</div>
				<div class="page_nb">1 / 56</div>
				<div class="fond_direction_d">
					<img src="/Skins/[!Systeme::Skin!]/Img/picto/droite_07.png" class="next" onmouseover="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/droiteHover_23.png';" onmouseout="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/droite_07.png';" onclick="$('.magazine').turn('next')" alt="Page suivante" title="Page suivante" />
					<img src="/Skins/[!Systeme::Skin!]/Img/picto/droiteFin_09.png" class="previous_end" onmouseover="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/droiteFinHover_25.png';" onmouseout="this.src = '/Skins/[!Systeme::Skin!]/Img/picto/droiteFin_09.png';" onclick="$('.magazine').turn('page', [!NbPages!])" alt="Aller à la fin" title="Aller à la fin" />
				</div>
			</div>
		</div>
        <div class="magazine">
        		<!-- Next button -->
				<!--<div ignore="1" class="next-button"></div>-->
				<!-- Previous button -->
				<!--<div ignore="1" class="previous-button"></div>-->
    		[STORPROC [!Query!]/Chapitre|C]
    			[MODULE Flipbook/Chapitre]
    		[/STORPROC]
    		[!cpt:=0!]
    		[STORPROC [!Query!]/Page|P|||Id|ASC]
    			<div class="page">
					[MODULE Flipbook/Page]
				</div>
				[!cpt+=1!]
    		[/STORPROC]
    	</div>
    </div>
    [/STORPROC]
</div>
<div class="bottom">
	<div id="slider-bar" class="turnjs-slider">
		<div id="slider"></div>
	</div>
</div>

<div id="bookActions">
	<img src="/Skins/[!Systeme::Skin!]/Img/picto/zoom+_03.png" class="zoom" onclick="btnZoom();" alt="Zoom" title="Zoom" />
	<img style="display:none" src="/Skins/[!Systeme::Skin!]/Img/picto/zoom-_03.png" class="zoom_exit" onclick="btnZoom();" alt="Dé-Zoom" title="Dé-Zoom" />
	<img src="/Skins/[!Systeme::Skin!]/Img/picto/pleinEcran_14.png" class="full_screen" onclick="enterFullscreen(document.documentElement);" alt="Plein écran" title="Plein écran" />
	<img style="display:none" src="/Skins/[!Systeme::Skin!]/Img/picto/normal_15.png" class="full_screen_exit" onclick="exitFullscreen();" alt="Sortir plein écran" title="Quitter mode plein écran" />
	<img src="/Skins/[!Systeme::Skin!]/Img/picto/imprimer_05.png" class="print" onclick="afficherEtImprimerImage('[!Img!]');" alt="Imprimer" title="Imprimer" />
	<img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterHD_12.png" name="HD" id="HD" class="HD" onclick="transToHD();" alt="Exporter en HD" title="Exporter en HD" />
	<img style="display:none" src="/Skins/[!Systeme::Skin!]/Img/picto/exporterHD2_12.png" name="HD" id="HD" class="HD_exit" onclick="transToHD();" alt="Exporter" title="Exporter" />
	<input type="hidden" id="PageId" value="" />
	    	
    <span id="All"> 
    <a href="[!DOMAINE!]/[!Query!]/Pdf"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterPDF_07.png" alt="Exporter flipbook" title="Exporter flipbook" /></a>
    </span>
   	<span style="display:none;" id="All_HD"> 
   		<a href="[!DOMAINE!]/[!Query!]/PdfHD"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterPDF_07.png" alt="Exporter flipbook HD" title="Exporter flipbook HD" /></a>
   	</span>
   	    
	[COUNT [!Query!]/Page|TotPages]
	[!CptPage:=1!]
	[STORPROC [!Query!]/Page|P|0|[!TotPages!]|Id|ASC]
		[IF [!CptPage!]<=[!TotPages!]]
			[IF [!CptPage!]=1||[!CptPage!]=[!TotPages!]]
				[IF [!CptPage!]=1]
					<span id="[!CptPage!]">
						<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/Pdf"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterGauche_10.png" alt="Exporter page gauche" title="Exporter page gauche" /></a>
					</span>
				[/IF]
				[IF [!CptPage!]=1]
					<span style="display:none;" id="[!CptPage!]">
						<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/Pdf"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterGauche_10.png" alt="Exporter page gauche" title="Exporter page gauche" /></a>
					</span>
					<span style="display:none;" id="[!CptPage!]_HD">
						<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/PdfHD"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterGauche_10.png" alt="Exporter page gauche HD" title="Exporter page gauche HD" /></a>
					</span>
				[/IF]
				[IF [!CptPage!]=[!TotPages!]]
					<span style="display:none;" id="[!CptPage!]">
						<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/Pdf"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterDroite_09.png" alt="Exporter page droite" title="Exporter page droite" /></a>
					</span>
					<span style="display:none;" id="[!CptPage!]_HD">
						<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/PdfHD"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterDroite_09.png" alt="Exporter page droite" title="Exporter page droite" /></a>
					</span>
				[/IF]
				[!CptPage+=1!]
			[ELSE]
				<span style="display:none;" id="[!CptPage!]">
					<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/Pdf"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterGauche_10.png" alt="Exporter page gauche" title="Exporter page gauche" /></a>
					<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage:+1!]/Pdf"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterDroite_09.png" alt="Exporter page droite" title="Exporter page droite" /></a>
					<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/DblPage"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterLes2_11.png" alt="Exporter double page" title="Exporter double page" /></a>
				</span>
				<span style="display:none;" id="[!CptPage!]_HD">
					<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/PdfHD"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterGauche_10.png" alt="Exporter page gauche HD" title="Exporter page gauche HD" /></a> 
					<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage:+1!]/PdfHD"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterDroite_09.png" alt="Exporter page droite HD" title="Exporter page droite HD" /></a> 
					<a href="[!DOMAINE!]/[!Query!]/Page/[!CptPage!]/DblPageHD"><img src="/Skins/[!Systeme::Skin!]/Img/picto/exporterLes2_11.png" alt="Exporter double page HD" title="Exporter double page HD" /></a>
				</span>
				[!CptPage+=2!]
			[/IF]
		[/IF]
	[/STORPROC]

	<img src="/Skins/[!Systeme::Skin!]/Img/picto/facebook_16.png" onclick="shareFB();" alt="Partager sur Facebook" title="Partager sur Facebook" />
	<a href="[!Domaine!]/[!Query!].html" title="Partage de mon Flipbook" id="FacebookLink"></a>
</div>


<script type="text/javascript">

var fontSize = 12;

function loadApp() {

	var flipbook = $('.magazine');
	
    $('.magazine').turn({
		acceleration: false,
        width: 820,
        height: 580,
        elevation: 50,
        gradients: true,
        autoCenter: true,
        pages:56,
        when: {
				turning: function(event, page, view) {
					var book = $(this);
					currentPage = book.turn('page'),
					pages = book.turn('pages');
					
					var stop = parseInt(view[1])
					if(view[1] == 0)
						stop = parseInt(view[0]);
						
					for(var i=1; i<stop; i++){
						$('#'+i).css('display', 'none');
						$('#'+i+'_HD').css('display', 'none');
					}
					for(var i=stop+1; i<=pages; i++){
						$('#'+i).css('display', 'none');
						$('#'+i+'_HD').css('display', 'none');
					}
						
					var hd='';
					if(document.getElementById("HD").checked)
						hd='_HD';
						
					//première page
					if(view[0] == "0")
						$('#'+view[1]+hd).show('slow');
					
					$('#'+view[0]+hd).show('slow');	
					$('#PageId').val(view[0]);	
	
					$('.page_nb').text(((view[0] != 0)? view[0] : view[1]) +' / '+pages);	
				},

				turned: function(event, page, view) {
					$(this).turn('center');
					$('#slider').slider('value', getViewNumber($(this), page));
				}
			}
    });
    
    // Slider
	$("#slider").slider({
		min: 1,
		max: numberOfViews(flipbook),
		start: function(event, ui) {

		},
		slide: function(event, ui) {

		},
		stop: function() {
			if (window._thumbPreview)
				_thumbPreview.removeClass('show');
			$('.magazine').turn('page', Math.max(1, $(this).slider('value')*2 - 2));
		}
	});

    // Zoom.js
    $('.magazine-viewport').zoom({
        flipbook: $('.magazine'),
        acceleration: function() {
            return navigator.userAgent.indexOf('Chrome') == -1;
        },
        max: 3, 
        when: {
            doubleTap: function(event) {
                if ($(this).zoom('value')==1) {
                    $('.magazine').removeClass('animated').addClass('zoom-in');
                    $(this).zoom('zoomIn', event);
                } else {
                	$('.magazine').addClass('animated').removeClass('zoom-in');
                    $(this).zoom('zoomOut');
                }
            },

            resize: function(event, scale, page, pageElement) {
                
                if (scale==1) {
                    // On a de-zoomé
                    $('#slider-bar').fadeIn();
                    pageElement.css('font-size', fontSize);
                    var img = pageElement.find('img');
                    img.attr('src', img.attr('original-img'));
                    
                    $('.zoom').css('display', 'inline');
					$('.zoom_exit').css('display', 'none');
                } else {
                    // On a zoomé
                    $('#slider-bar').hide();
                    pageElement.css('font-size', fontSize * scale + 'px');
                    var img = pageElement.find('img');
                    img.css('width', '100%');
                    img.css('height', '100%');
                    img.attr('original-img', img.attr('src'));
                    img.attr('src', $(img).next().attr('href')); 
                    
                    $('.zoom').css('display', 'none');
					$('.zoom_exit').css('display', 'inline');
                }
            },

            swipeLeft: function() {
                $('.magazine').turn('next');
            },

            swipeRight: function() {
                $('.magazine').turn('previous');
            }
        }
    });

    // Using arrow keys to turn the page

    $(document).keydown(function(e){

        var previous = 37, next = 39, esc = 27;

        switch (e.keyCode) {
            case previous:

                // left arrow
                if($('.magazine-viewport').zoom('value') == 1) {
                    $('.magazine').turn('previous');
                    e.preventDefault();
                }

            break;
            case next:

                //right arrow
                if($('.magazine-viewport').zoom('value') == 1) {
                    $('.magazine').turn('next');
                    e.preventDefault();
                }

            break;
            case esc:
                $('.full_screen').css('display', 'inline');
				$('.full_screen_exit').css('display', 'none');
				
                $('.zoom').css('display', 'inline');
				$('.zoom_exit').css('display', 'none');
                
                $('.magazine-viewport').zoom('zoomOut');    
                e.preventDefault();

            break;
        }
    });
    
    // Events for the next button
	$('.next-button').bind($.mouseEvents.over, function() {
		$(this).addClass('next-button-hover');
	}).bind($.mouseEvents.out, function() {
		$(this).removeClass('next-button-hover');
	}).bind($.mouseEvents.down, function() {
		$(this).addClass('next-button-down');
	}).bind($.mouseEvents.up, function() {
		$(this).removeClass('next-button-down');
	}).click(function() {
		$('.magazine').turn('next');
	});

	// Events for the next button
	$('.previous-button').bind($.mouseEvents.over, function() {
		$(this).addClass('previous-button-hover');
	}).bind($.mouseEvents.out, function() {
		$(this).removeClass('previous-button-hover');
	}).bind($.mouseEvents.down, function() {
		$(this).addClass('previous-button-down');
	}).bind($.mouseEvents.up, function() {
		$(this).removeClass('previous-button-down');
	}).click(function() {
		$('.magazine').turn('previous');
	});


    $(window).resize(function() {
        resizeViewport();
    }).bind('orientationchange', function() {
        resizeViewport();
    });


    resizeViewport();

    $('.magazine').addClass('animated');

}

function btnZoom(event) {
    $('.magazine-viewport').zoom(
        ($('.magazine-viewport').zoom('value') == 1) ? 'zoomIn' : 'zoomOut',
        event
    ); 
}

function afficherEtImprimerImage(imageString)
{
	alert("Chargement des images en cours... Veuillez patienter, la fenêtre de paramétrage de l'imprimante va s'afficher...");
  	var images = imageString.split(';');
  	hauteur=Math.round(screen.availHeight);
  	largeur=Math.round(screen.availWidth);
  	
  	win = window.open ('', 'Impression PDF', 'top='+hauteur+',left='+largeur+',width=1,height=1,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=false,resizable=false')
  	doc = win.document;
  	
  	doc.writeln('<html>');
  	doc.writeln('<head>');
  	doc.writeln('<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">');
  	doc.writeln('<title>Impression PDF</title>');
  	doc.writeln('</head>');
  	doc.writeln('<body style="margin:0; padding:0">');

  	for(var i=0; i<images.length-1; i++){
  		if(i == images.length-2)
  			doc.writeln('<im' + 'g src="' + images[i] + '" alt="" onload="window.setTimeout(\'window.print()\', 100);">');
  		else
  			doc.writeln('<im' + 'g src="' + images[i] + '" alt="">');
  	}
  	doc.writeln('</bo'+'dy>');
  	doc.write('</ht'+'ml>');
 	doc.close();

 	return false;
}

function enterFullscreen(element) {
	$('.full_screen').css('display', 'none');
	$('.full_screen_exit').css('display', 'inline');

    if(element.requestFullScreen) {
            //fonction officielle du w3c
            element.requestFullScreen();
    } else if(element.webkitRequestFullScreen) {
            //fonction pour Google Chrome (on lui passe un argument pour autoriser le plein écran lors d'une pression sur le clavier)
            element.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
    } else if(element.mozRequestFullScreen){
            //fonction pour Firefox
            element.mozRequestFullScreen();
    } else {
    		$('.full_screen').css('display', 'inline');
			$('.full_screen_exit').css('display', 'none');
            alert('Votre navigateur ne supporte pas le mode plein écran, il est temps de passer à un plus récent');
    }
}

function exitFullscreen() {
	$('.full_screen').css('display', 'inline');
	$('.full_screen_exit').css('display', 'none');
	
    if(document.cancelFullScreen) {
            //fonction officielle du w3c
            document.cancelFullScreen();
    } else if(document.webkitCancelFullScreen) {
            //fonction pour Google Chrome
            document.webkitCancelFullScreen();
    } else if(document.mozCancelFullScreen){
            //fonction pour Firefox
            document.mozCancelFullScreen();
    }
}

function transToHD(){	
	var Id = document.getElementById("PageId").value;
	if(Id == "0" || Id == "" || Id == "undefined")
		Id = 1;

	$('#'+Id+'_HD').css('display', 'none');
	$('#'+Id).css('display', 'none');
	$('#All_HD').css('display', 'none');
	$('#All').css('display', 'none');
	
	if($('.HD').css('display') == 'inline'){
		$('#All_HD').show('slow');
		$('#'+Id+'_HD').show('slow');
		$('.HD').css('display', 'none');
		$('.HD_exit').css('display', 'inline');
	}
	else{
		$('#All').show('slow');
		$('#'+Id).show('slow');
		$('.HD').css('display', 'inline');
		$('.HD_exit').css('display', 'none');
	}
}

function shareFB() {
	var u=$('#FacebookLink').attr("href");
	var t=$('#FacebookLink').attr("title");

	window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');
	return false;
}

// Load the HTML4 version if there's not CSS transform

yepnope({
    test : Modernizr.csstransforms,
    complete: loadApp
});

</script>
