[!REQUETE:=Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image!]
[COUNT [!REQUETE!]|NbImg]
//[!REQUETE!]
[IF [!NbImg!]>0]
	<div class="[!MenC::Url!]">
		// HTML
		<div id="BlockFlash"  style="height:[!IMGHEIGHT!]px; width:[!IMGWIDTH!]px;">
			<div id="BlockFlashCadre"  style="height:[!IMGHEIGHT!]px; width:[!IMGWIDTH!]px; overflow:hidden;">
				<div id="BlockFlashDefile" style="height:[!IMGHEIGHT!]px; width:[!IMGWIDTH:*[!NbImg!]!]px;">
					[STORPROC [!REQUETE!]|P|||Ordre|ASC]
						[IF [!P::Lien!]!=]<a href="/[!P::Lien!]">[/IF]
							<img src="/[!P::Lien!].mini.[!IMGWIDTH!]x[!IMGHEIGHT!].jpg" alt="[!Utils::noHtml([!P::[!LEGENDE!]!])!]" style="display:block; float:left; position: relative;" />
						[IF [!P::Lien!]!=]</a>[/IF]
					[/STORPROC]
				</div>
			</div>
			<div id="BlockFlashLegende" style="font-size:[!FONTSIZE!]px">[!P::[!LEGENDE!]!]</div>
			<div id="BlockFlashPages"></div>
			<div id="BlockFlashEtiquette"></div>
		</div>
		
		[IF [!NbImg!]>1]
			// Surcouche JS
			<script type="text/javascript">
					var current = 0;
					// Stockage des différentes légendes
					var legendes = [
						[STORPROC [!REQUETE!]|P|||Ordre|ASC]
							["[JSON][!P::[!LEGENDE!]!][/JSON]", [!FONTSIZE!]][IF [!Pos!]!=[!NbResult!]],[/IF]
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
							$('BlockFlashDefile').tween('margin-left', -(idx*[!IMGWIDTH!])+'px');
					
							// Change la légende
							var legendeDiv = $('BlockFlashLegende');
							var myFx = new Fx.Tween(legendeDiv, {property: 'margin-left', duration:'long', transition: Fx.Transitions.Expo.easeOut});
							var tempFx = new Fx.Tween(legendeDiv, {property: 'alpha'});
							/////// Masque
							var size = legendeDiv.getSize();
							myFx.start(-size.x).chain( function() {
								legendeDiv.innerHTML = legendes[idx][0];
								legendeDiv.setStyle('font-size', [!FONTSIZE!] + 'px');
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
	</DIV>
[/IF]