// Paramètres composant
[!ImgWidth:=990!]
[!ImgHeight:=230!]
[!DefaultFontSize:=30!]

[COUNT Boutique/Pub/Publier=1|NbImg]
[IF [!NbImg!]>0]

	// HTML
	<div id="BlockFlash"  style="height:[!ImgHeight!]px; width:[!ImgWidth!]px;">
		<div id="BlockFlashCadre"  style="height:[!ImgHeight!]px; width:[!ImgWidth!]px; overflow:hidden;">
			<div id="BlockFlashDefile" style="height:[!ImgHeight!]px; width:[!ImgWidth:*[!NbImg!]!]px;">
				[STORPROC Boutique/Pub/Publier=1|P|||Ordre|ASC]
					// On garde les première infos pour la légende
					[IF [!Pos!]=1]
						[!FontSize:=[!P::FontSize!]!]
						[!Legende:=[!P::Legende!]!]
					[/IF]
					[IF [!P::Lien!]!=]<a href="/[!P::Lien!]">[/IF]
						<img src="/[!P::Image!].mini.[!ImgWidth!]x[!ImgHeight!].jpg" alt="[!Utils::noHtml([!P::Legende!])!]" style="display:block; float:left; position: relative" />
					[IF [!P::Lien!]!=]</a>[/IF]
				[/STORPROC]
			</div>
		</div>
		<div id="BlockFlashLegende" style="font-size:[IF [!FontSize!]>0][!FontSize!][ELSE][!DefaultFontSize!][/IF]px">[!Legende!]</div>
		<div id="BlockFlashPages"></div>
		<div id="BlockFlashEtiquette"></div>
	</div>
	
	[IF [!NbImg!]>1]
	
	// Surcouche JS
	<script type="text/javascript">
	
			var current = 0;
	
			// Stockage des différentes légendes
			var legendes = [
				[STORPROC Boutique/Pub/Publier=1|P|||Ordre|ASC]
					["[JSON][!P::Legende!][/JSON]", [!P::FontSize!]][IF [!Pos!]!=[!NbResult!]],[/IF]
				[/STORPROC]
			];
		
			// Incrustation des boutons + affectation de leur action onclick
			window.addEvent('domready', function() {
				for(i=1; i<=[!NbImg!]; i++) {
					var btn = new Element('div', {
						'class': 'BlockFlashNumber' + ((i==1) ? ' BlockFlashNumberActive' : ''),
						'html': '' + i
					}).addEvent('click', function() {
						clearInterval(animInterval);
						var numbers = $('BlockFlash').getElements('div.BlockFlashNumber');
						for(i=0; i<numbers.length; i++) if(numbers[i]==this) activePub(i);
					}).inject($('BlockFlashPages'));
				}
			});
		
			function activePub( idx ) {
				if(idx != current) {
					current = idx;
					var numbers = $('BlockFlash').getElements('div.BlockFlashNumber');
			
					// Change la couleur du numéro
					numbers.removeClass('BlockFlashNumberActive');
					numbers[idx].addClass('BlockFlashNumberActive');
			
					// Fait défiler les images
					$('BlockFlashDefile').tween('margin-left', -(idx*[!ImgWidth!])+'px');
			
					// Change la légende
					var legendeDiv = $('BlockFlashLegende');
					var myFx = new Fx.Tween(legendeDiv, {property: 'margin-left', duration:'long', transition: Fx.Transitions.Expo.easeOut});
					var tempFx = new Fx.Tween(legendeDiv, {property: 'alpha'});
					/////// Masque
					var size = legendeDiv.getSize();
					myFx.start(-size.x).chain( function() {
						legendeDiv.innerHTML = legendes[idx][0];
						legendeDiv.setStyle('font-size', ((legendes[idx][1]>0) ? legendes[idx][1] : [!DefaultFontSize!]) + 'px');
						var newSize = legendeDiv.getSize();
						legendeDiv.setStyle('margin-left', -newSize.x);
						if(legendes[idx][0].trim() != '') tempFx.start(1).chain( function() {
							myFx.start(0);
						});
					});
				}
			}
	
			function changeAnim() {
				activePub((current >= [!NbImg:-1!]) ? 0 : current + 1);
			}
	
			var animInterval = setInterval("changeAnim()", 5000);
	
	</script>
	[/IF]

[/IF]