window.addEvent('load', function ()	{

	$$('a.popup').each( function (a) {
			a.addEvent('click',function (e) {
				e.preventDefault();
				window.open(a.href);	
			}
		)}
	);

	// Rappel dans le header
	if ($('rappel')!=null) {
		$('rappel').addEvent('submit', function (event)	{
			event.stop();
			if ($('TelRap').value.trim() == '') return alert("Merci de renseigner votre numéro de téléphone");
			new Request.HTML ({
				url:'/[!Lien!]',
				onComplete:function(){
					var ladiv= new Element ('div', {
						'class':'FormRappelReponse',
						'html':'Merci, votre demande de rappel a été prise en compte.'
					});
					$('appelrappel').empty();
					ladiv.inject($('appelrappel'));
					ladiv.setStyle ('opacity','0');
					var leffet = new Fx.Tween(ladiv, {duration:500,property:'opacity'});
					leffet.start('1');
				}
			}).post(this);
			
	
		}) ;
	}
	
	// Animation menu avec bandeau bleu au dessus
	if ($('Menu')!=null) {
		var fxTweensMenu = [];
		$('Menu').getElement('tr').getElements('td').each( function(item, idx) {
			if(!item.hasClass('current')) {
				fxTweensMenu[idx] = [];
				fxTweensMenu[idx][0] = new Fx.Tween(item.getElement('a'), {duration:150,property:'border-top-width',link:'cancel'});
				item.setProperty('indice', idx);
				item.getElement('a').setStyle('border-top-width', '0px');
				item.addEvent('mouseover', function() {
					var idx = this.get('indice');
					if(idx != null) {
						fxTweensMenu[idx][0].start('6px');
					}
				});
				item.addEvent('mouseout', function() {
					var idx = this.get('indice');
					if(idx != null) {
						fxTweensMenu[idx][0].start('0');
					}
				});
			}
		});	
	}

	// Page détail résidence
	if ($('Onglets')!=null) {

		// Changement d'onglet
		var cellsDR = $('Onglets').getElements('td');
		var liensDR = $('Onglets').getElements('a');
		var divsDR = $$('div.BlocDetailResidence');
		var blocsPlan = $$('div.BlocPlan');
		liensDR.each( function(a, idx) {
			a.addEvent('click', function(e) {
				e.stop();
				// On enleve le popup et le masque des plans si l'on change d'onglet
				if($('PopupPlan') != null) $('PopupPlan').dispose();
				$$('.MasquePlans').each( function(div) { div.dispose(); });
				// On masque tout et on affiche l'onglet choisi
				divsDR.each( function(d, idx2) {
					d.setStyle('display', 'none');
					if(idx2 == idx) d.setStyle('display', 'block');
				});
				// Mise en valeur de l'onglet choisi
				cellsDR.each( function(c, idx2) {
					c.removeClass('current');
					if(idx2 == idx) c.addClass('current');
				});
			});
		});

		// Popup pour les plans
		var liensP = $$('a.VoirPlans');
		var popupsP = $$('ul.ListePlans');
		// On masque les liste simples pour la surcouche
		popupsP.each( function(d) {
			d.addClass('nodisplay');
		});
		liensP.each(function(a, idx) {
			// On affiche les liens "voir tous les plans"
			a.setStyle('display','block');
			a.addEvent('click', function(e) {
				e.stop();
				popupsP.each( function(d, idx2) {
					if(idx2 == idx) {
						// Si popup non dispo on le créé
						var dPP = $('PopupPlan');
						if(dPP == null) dPP = new Element('div', {
							'id': 'PopupPlan'
						}).inject(document.body);
						dPP.empty();
						// Environnement POPUP
						var tPP = new Element('div', { 'class': 'TopPopup' + (idx2%3) }).inject(dPP);
						var cPP = new Element('div', { 'class': 'ClosePopup' }).inject(dPP);
						var uPP = new Element('ul', { 'class': 'ListePlans', 'html':d.innerHTML  }).inject(dPP);
						var bPP = new Element('div', { 'class': 'BottomPopup' }).inject(dPP);
						// On récupère le contenu et la position pour le popup
						var coords = a.getPosition(document.body);
						var posX = coords.x - ((idx%3) * 225);
						var posY = coords.y + 20;
						dPP.setStyles({
							'display': 'block',
							'top': posY+'px',
							'left': posX+'px'
						});
						// Fermeture
						cPP.addEvent('click', function() {
							dPP.setStyle('display','none');
							$$('.MasquePlans').each( function(div) { div.dispose(); });
						});
					}
					else { 
						new Element('div', {
							'class': 'MasquePlans'
						}).inject(blocsPlan[idx2]);
					}
				});
			});
		});

	}

	// Page Localiser sur la carte
	if( $('Localiser') != null) {
		$('ChangeCarte').setStyle('display', 'block');
		var liensCarte = $$('a.changeCarte');
		var imgsCarte = $('Localiser').getElements('img.Plan');
		imgsCarte.each(function( img, idx) {
			if(idx > 0) img.setStyle('display','none');
			else img.setStyle('display','inline');
		});
		liensCarte.each(function(a,idx) {
			a.addEvent('click', function(e) {
				e.stop();
				clearInterval(localiserInterval);
				activerCarteLocaliser(idx);
			});
		});
		
		currentCarte = 0;
		nbCartes = imgsCarte.length;
		localiserInterval = setInterval("changeCarteLocaliser()", 5000);
	}

	// Recherche - gestion departements / ville
	if($('RechercheResidence') != null) {
		var selDep = $('RechercheResidenceDepartement');
		var optionsVille = $('RechercheResidenceVille').getElements('option');
		selDep.addEvent('change', function() {
			var title = this.options[this.selectedIndex].get('title');
			optionsVille.each( function( opt ) {
				if(title == null || title == '' || opt.get('title') == null || opt.get('title') == '' || opt.get('title') == title) opt.setStyle('display','list-item');
				else opt.setStyle('display','none');
			});
		});
		if(selDep.selectedIndex != 0) {
			var title = selDep.options[selDep.selectedIndex].get('title');
			optionsVille.each( function( opt ) {
				if(title == null || title == '' || opt.get('title') == null || opt.get('title') == '' || opt.get('title') == title) opt.setStyle('display','list-item');
				else opt.setStyle('display','none');
			});
		}
	}

	// Anim
	if($('AnimCadre') != null) {
		var imgs = $('AnimCadre').getElements('img');
		nbImg = imgs.length;
		// Masque les images au départ (sauf la 1)
		for(i=1; i<=nbImg; i++) {
			if(i>1) imgs[i-1].setStyle('opacity', 0);
		}
		// Incrustation des boutons + affectation de leur action onclick
		if($('AnimPages') != null) {
			for(i=1; i<=nbImg; i++) {
				var btn = new Element('div', {
					'class': 'AnimNumber' + ((i==1) ? ' AnimNumberActive' : ''),
					'html': '' + i
				}).addEvent('click', function() {
					clearInterval(animInterval);
					var numbers = $('AnimPages').getElements('div.AnimNumber');
					for(i=0; i<numbers.length; i++) if(numbers[i]==this) activePub(i);
				}).inject($('AnimPages'));
			}
		}
		
		currentImg = 0;
		animInterval = setInterval("changeAnim()", 5000);
	
	}

	// Redaction - ouverture des articles
	if($('Redaction') != null) {
		var articles = $$('.ContenuArticle');
		var redactionfx = [];

		if (articles.length >1) {
			$('Redaction').getElements('.Buttons').setStyle('display','block');
			articles.each ( function( art, idx ) {
				redactionfx[idx]=new Fx.Slide(art);
				redactionfx[idx].hide();
				if (idx==0) redactionfx[idx].toggle();
			});
			$$('.OuvreArticle').each( function(btn,idx) {
				btn.addEvent('click', function() {
					articles.each ( function( art, idx2 ) {
						redactionfx[idx2].hide();
						if (idx==idx2) {
							redactionfx[idx2].slideIn();
						}
					});
				});
			});

			$$('.FermeArticle').each( function(btn,idx) {
				btn.addEvent('click', function() {
					redactionfx[idx].slideOut();
				});
			});



		}
		
	}
	// News - ouverture des news
	if($('News') != null) {
		var articles = $$('.ContenuNouvelle');
		var newsAouvrir =0;
		articles.each ( function( art, idx ) {
			if (art.hasClass('NouvelleEncours')) newsAouvrir =idx;
		});
		var redactionfx = [];

		if (articles.length >1) {
			$('News').getElements('.Buttons').each(function(btn) {
			   btn.setStyle('display','block'); 
			});
			articles.each ( function( art, idx ) {
				redactionfx[idx]=new Fx.Slide(art);
				redactionfx[idx].hide();
				if (idx==newsAouvrir) redactionfx[idx].toggle();
			});
			$$('.OuvreNouvelle').each( function(btn,idx) {
				btn.addEvent('click', function() {
					articles.each ( function( art, idx2 ) {
						redactionfx[idx2].hide();
						if (idx==idx2) {
							redactionfx[idx2].slideIn();
						}
					});
				});
			});

			$$('.FermeNouvelle').each( function(btn,idx) {
				btn.addEvent('click', function() {
					redactionfx[idx].slideOut();
				});
			});



		}
		
	}
	//fonction simulateurs
	if ($('simulateurForm') != null) {
		calcul_simulateurs();
		$('simulateurForm').getElements('input').addEvent('blur', calcul_simulateurs);
	}


	// News - Accueil News Pragma
	if($('paginationAccueilActusPragma') != null) {
		$('ladivvisible').setStyle('overflow','hidden');
		var mov = $('ladivadeplacer').getElements('div.AfficheInfo');
		indexmov =0;
		nbmov = mov.length;
		if (nbmov >1 ) {

			newsHeight = mov[0].getDimensions().y;
			
			var btn = new Element('a', {
				'class': 'precedent' ,
				'html': 'Précédent' 
			}).addEvent('click', function() {
				NewsChange(-1);
			}).inject($('paginationAccueilActusPragma'));
			var btn = new Element('a', {
				'class': 'suivant' ,
				'html': 'Suivant' 
			}).addEvent('click', function() {
                NewsChange(1);
			}).inject($('paginationAccueilActusPragma'));
		}

	}
	
	
	var initMultiBox = new multiBox({
		mbClass: '.mb',
		container: $(document.body),
		descClassName: 'multiBoxDesc',
		useOverlay: true
	});

});


var indexmov;
var nbmov;
var newsHeight;
var newsInterval = setInterval(NewsChange, 3500);

function NewsChange(modif) {
    if(modif != undefined && modif != null) {
        clearInterval(newsInterval);
    }
    if(modif == 1) {
        // Suivant (vers le haut)
        if (indexmov > 0 ) {
            indexmov--;
        } else {
            indexmov = nbmov-1;
        }
    } else {
        // Précédent (vers le bas)
        if (indexmov < nbmov-1) {
            indexmov++;
        } else {
            indexmov = 0;
        }
    }
    var leffet = new Fx.Tween($('ladivadeplacer'), {duration:500,property:'margin-top'});
    leffet.start(-indexmov*parseInt(newsHeight)+'px');
}



/*-- Animation --*/
var legendesAnim = [];
var animInterval;
var currentImg;
var nbImg;

function activePub( idx ) {
	if(idx != currentImg) {
		currentImg = idx;
		// Change la couleur du numéro
		if($('AnimPages') != null) {
			var numbers = $('AnimPages').getElements('div.AnimNumber');
			numbers.removeClass('AnimNumberActive');
			numbers[idx].addClass('AnimNumberActive');
		}

		// Fait apparaitre la bonne _image
		var imgs = $('AnimCadre').getElements('img');
		imgs.each(function(item, i) {
			var myFx = new Fx.Tween(item, {property: 'opacity', duration:1300});
			if(i == idx) {
				myFx.start(1);
				if($('AnimLegende') !=null) $('AnimLegende').innerHTML = legendesAnim[idx];
			}
			else myFx.start(0);
		});
	}
}

function changeAnim() {
	activePub((currentImg >= nbImg-1) ? 0 : currentImg + 1);
}


/*-- Choix du departement --*/
function interactiveMap( n ) {
	if(n == null) n = 0;
	$('CartePS').setStyle('background-position', '100% -' + 173 * n + 'px');
}


/*-- Cartes "Localiser" --*/

var localiserInterval;
var currentCarte;
var nbCartes;

function activerCarteLocaliser( idx ) {
	currentCarte = idx;
	var liensCarte = $$('a.changeCarte');
	var imgsCarte = $('Localiser').getElements('img.Plan');
	liensCarte.each(function( a, idx2) {
		a.removeClass('currentCarte');
		if(idx2 == idx) a.addClass('currentCarte');
	});
	imgsCarte.each(function( img, idx2) {
		img.setStyle('display','none');
		if(idx2 == idx) img.setStyle('display','inline');
	});
}

function changeCarteLocaliser() {
	activerCarteLocaliser((currentCarte >= nbCartes-1) ? 0 : currentCarte + 1);
}