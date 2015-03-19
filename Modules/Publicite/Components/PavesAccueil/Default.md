<div id="PubAnim">
	<div id="PubAnimList" style="width:[!LARGEURCOL!]px; float:[!ALIGNEMENT!]">
		[STORPROC Publicite/Produit/Publier=1|Prod]
			<ul>
				[LIMIT 0|[!NBITEMS!]]
					<li [IF [!Pos!]=1] class="PubAnimActive" [/IF]>
						<a href="[!Prod::Lien!]" style="display:block;width:[!LARGEURPICTO!]px; height:[!HAUTEURPICTO!]px;background-image:url('/[!Prod::Picto!]');"></a>
					</li>
				[/LIMIT]
			</ul>
		[/STORPROC]
	</div>
	<div id="PubAnimApercu" style="height:[!HAUTEURMAINIMAGE!]px; margin-[!ALIGNEMENT!]:[!LARGEURCOL!]px">
		[STORPROC Publicite/Produit/Publier=1|Prod|0|[!NBITEMS!]]
			<img class="PubAnimMainImage" src="/[!Prod::Image!]" alt="" />
			<img class="PubAnimMainTxt"  style="left:[IF [!Pos!]=1]0[ELSE][!LARGEURMAINIMAGE!][/IF]px" src="/[!Prod::ImgTexte!]" alt="" />
		[/STORPROC]
	</div>
</div>

<script type="text/javascript">

	/////////////////////////////// VARS ///////////////////////////////
	clearInterval(PubAnimInterval);
	var PubAnimInterval = setInterval("changePubAnim()", [!DUREEANIM:*1000!]);
	var Active = 0;
	var PubAnimList = [];
	var PubAnimImages = [];
	var PubAnimImages = [];
	var PubAnimFx = [];
	var PubAnimTxtFx = [];

	///////////////////////////// DOMREADY /////////////////////////////
	window.addEvent('domready', function() {
		// Images
		PubAnimImages = $$('.PubAnimMainImage');
		PubAnimTextes = $$('.PubAnimMainTxt');
		if(PubAnimImages.length < 2) return;
		PubAnimImages.each( function(item, index) {
			PubAnimFx[index] = new Fx.Tween(item, { duration: 300, link: 'cancel', property: 'opacity' });
			PubAnimTxtFx[index] = new Fx.Tween(PubAnimTextes[index], { duration: 300, link: 'cancel', property: 'left', transition: Fx.Transitions.Quart.easeInOut }); 
			item.setStyles({
				'position':'absolute',
				'top':'0',
				'left':'0'
			});
			if(index > 0) item.setStyle('opacity', 0);
		});
		// Accès liste
		PubAnimList = $('PubAnimList').getElements('li');
		PubAnimList.each( function(item, index) {
			item.addEvent('mouseover', function() {
				clearInterval(PubAnimInterval);
				PubAnimTo(index);
			});
		}); 
	});
	
	///////////////////////////// FUNCTIONS ////////////////////////////
	function changePubAnim() {
		var Max = PubAnimImages.length;
		PubAnimTo( (Max > Active + 1) ? (Active + 1) : 0 );
	}

	function PubAnimTo( index ) {
		if($('PubAnimApercu') != null && Active != index) {
			PubAnimList.each( function(i) { i.removeClass('PubAnimActive');});
			PubAnimList[index].addClass('PubAnimActive');
			PubAnimTxtFx[Active].start('[!LARGEURMAINIMAGE!]px');
			PubAnimFx[Active].start(0);
			PubAnimTxtFx[index].start(0);
			PubAnimFx[index].start(1);
			Active = index;
		}
	}
</script>