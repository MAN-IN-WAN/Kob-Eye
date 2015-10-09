window.addEvent('domready', function ()	{

		
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



});


var indexmov;


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
