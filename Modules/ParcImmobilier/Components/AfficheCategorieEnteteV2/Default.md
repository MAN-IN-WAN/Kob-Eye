[!BLOCKWIDTH:=1024!]
[!IMGHEIGHT:=322!]
[!IMGWIDTH:=1024!]
[COUNT ParcImmobilier/CategorieHeader/[!CATEGORIE!]/CategorieHeader/*/Header/Publier=1|NbImg]
<div class="[!NOMDIV!]">
	<div class="AnimationsSite">
		// HTML
		<div id="BlockFlashLogo" ></div>
		<div id="BlockFlash"  style="height:[!IMGHEIGHT!]px; width:[!BLOCKWIDTH!]px;">
			// les images qui défilent
			<div id="BlockFlashCadre"  style="height:[!IMGHEIGHT!]px; width:[!BLOCKWIDTH!]px; overflow:hidden;position:relative;">
				<div id="BlockFlashDefile" style="height:[!IMGHEIGHT!]px; width:[!IMGWIDTH:*[!NbImg!]!]px;">
					[STORPROC ParcImmobilier/CategorieHeader/[!CATEGORIE!]/CategorieHeader|HCat|||Ordre|ASC]
						[STORPROC ParcImmobilier/CategorieHeader/[!HCat::Id!]/Header/Publier=1|H]
							[IF [!H::Lien!]~http||[!H::Lien!]~www]
								<a href="[!H::Lien!]"  target="_blank" ><img src="/[!H::Bandeau!]" rel="link" alt="" style="display:block; float:left; position: relative;height:[!IMGHEIGHT!]px; width:[!BLOCKWIDTH!]px;" /></a>
							[ELSE]
								<a href="[IF [!H::Lien!]~/][ELSE]/[/IF][!H::Lien!]" target="_blank" ><img src="/[!H::Bandeau!]" alt="" style="display:block; float:left; position: relative;height:[!IMGHEIGHT!]px; width:[!BLOCKWIDTH!]px;" " /></a>
							[/IF]
							
						[/STORPROC]
	
					[/STORPROC]
					<div id="BlockFlashPages" style="display:none;" ></div>
					<div id="BlockFlashEtiquette" style="display:none;"></div>
				</div>
				// petite animation à gauche		
				<div id="SommaireListeFond"></div>
				<ul class="SommaireListe" id="sommdiv">
					[!Nb:=1!][!IndexTot:=0!]
					[STORPROC ParcImmobilier/CategorieHeader/[!CATEGORIE!]/CategorieHeader|Cat|||Ordre|ASC]
						[COUNT ParcImmobilier/CategorieHeader/[!CATEGORIE!]/CategorieHeader/[!Cat::Id!]/Header/Publier=1|NbHeader]
						[IF [!NbHeader!]]
							[!Index[!Nb!]+=[!IndexTot!]!]
							
							<li id="CatIndex[!Nb!]" class="AnimTxtGauche [IF  [!Pos!]=1]AnimActive BackCatIndex1[/IF]" [IF  [!Pos!]=[!NbResult!]] style=" border-bottom:none;"[/IF]  >
								<a href='#' onclick="activePub([!IndexTot!]);" >[!Cat::Nom!] </a>
							</li>
							[!Nb+=1!]
							[!IndexTot+=[!NbHeader!]!]
							
						[/IF]
	
					[/STORPROC]
				</ul>
	
			</div>
			
		</div>
		
		[IF [!NbImg!]>1]
			// Surcouche JS
			<script type="text/javascript">
				var current = 0;
				// Incrustation des boutons + affectation de leur action onclick
				window.addEvent('domready', function() {
					//$('BlockBandeau0').setStyle('display','block');
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
			
				function activePub( idx) {
	
					if(idx!=current) {
						//var currentsel= current+1;
						if (idx<[!Index2!]) {
							CatIndex1.addClass('AnimActive');
							//$('sommdiv').setStyle('background-position','0 0');
							$('sommdiv').set('tween', {duration: '1500'});
							$('sommdiv').tween('background-position','0 0' );	
	
							CatIndex2.removeClass('AnimActive');
							CatIndex3.removeClass('AnimActive');
						}
						if (idx>=[!Index2!]&&idx<[!Index3!]) {
							CatIndex2.addClass('AnimActive');
	
							//$('sommdiv').setStyle('background-position','0 54px');
							$('sommdiv').set('tween', {duration: '1500'});
							$('sommdiv').tween('background-position','0 54px' );	
	
							CatIndex1.removeClass('AnimActive');
							CatIndex3.removeClass('AnimActive');
						}
	
						if (idx>=[!Index3!]) {
							CatIndex3.addClass('AnimActive');
							
							$('sommdiv').set('tween', {duration: '1500'});
							$('sommdiv').tween('background-position','0 109px' );	

							//$('sommdiv').setStyle('background-position','0 109px');
	
							CatIndex1.removeClass('AnimActive');
							CatIndex2.removeClass('AnimActive');
	
						}
	
						//$('BlockBandeau'+current).setStyle('display','block');
						//$('BlockBandeau'+current).tween('right', '500px');
						//$('BlockBandeau'+current).setStyle('right','-500px');
						//$('BlockBandeau'+current).tween('right', '0');
	
						if (idx==0) {
							//$('BlockFlashDefile').set('tween', {duration: '1000'});
//							$('BlockFlashDefile').set('fade','0.71').setStyle('margin','0px');
 //							$('BlockFlashDefile').set('tween', {duration: 'long', style: 'fade'});
//							$('BlockFlashDefile').tween('margin-left', '0px' );

							$('BlockFlashDefile').setStyle('opacity', 0);
							$('BlockFlashDefile').setStyle('margin-left', '0px' );
							$('BlockFlashDefile').fade('in');


						} else {
							$('BlockFlashDefile').set('tween', {duration: '1000'});
							$('BlockFlashDefile').tween('margin-left', -(idx*[!IMGWIDTH!])+'px' );
						}
						
						current = idx;
	
						
					}
				}
		
				function changeAnim() {
	
					activePub((current == [!NbImg:-1!]) ? 0 : current+1);
				}
		
				var animInterval = setInterval("changeAnim()", 5000);
	
	
				function apercuDiv (quoi ) {
					$('PopContactTotal').setStyle('opacity','0.5');
					if (quoi=='Contact') {
						$('popupContact').setStyle('display','block');
						$('popupContact').setStyle('opacity','1');
					} else {
						$('popupRappel').setStyle('display','block');
						$('popupRappel').setStyle('opacity','1');
					}
	
				}
			
	
			</script>
		[/IF]
	</div>
</div>