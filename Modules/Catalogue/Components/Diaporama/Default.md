//fiche Produit
[!RequeteFiltre:=Catalogue/Produit!]
[IF [!CHAMPFILTRE!]!=]
	[!RequeteFiltre+=/[!CHAMPFILTRE!]=[!VALFILTRE!]!]
[/IF]
[IF [!VITESSE!]=]
	[!VITESSE:=4000!]
[/IF]


[COUNT [!RequeteFiltre!]|Cpt]
[STORPROC Systeme/Menu/[!MENULIEN!]|Men][/STORPROC]
[!LgDefil:=[!NBAFFICHE!]!]
[!LgDefil*=[!LARGUNEINFO!]!]
[!LgDeCplt:=[!Cpt!]!]
[!LgDeCplt*=[!LARGUNEINFO!]!]

[!MaxML:=[!Cpt:-[!NBAFFICHE!]!]!]
[!MaxML:=[!MaxML:*[!LARGUNEINFO!]!]!]

<div class="[!NOMDIV!]">
	<div class="TitreColonne ">[!TITRECONTENU!]</div>
	<div id="ContenuComplet"  style="width:[!LgDefil!]px;">
		<div id="ContenuCompletDefile" style="width:[!LgDeCplt!]px;" >
			[STORPROC [!RequeteFiltre!]|R|0|[!LIMITAFFICHE!]]
				[!AjoutLien:=!]
				[STORPROC Catalogue/Categorie/Produit/[!R::Id!]|RqUrl|0|1]
					[STORPROC Catalogue/Categorie/Categorie/[!RqUrl::Url!]|UrlP|0|1]
						[IF [!Men::Alias!]!=Catalogue/Categorie/[!UrlP::Id!]][!AjoutLien:=/[!UrlP::Url!]!][/IF]
					[/STORPROC]

				[/STORPROC]
				
				<div class="UnBloc" style="width:[!LARGUNEINFO!]px;height:[!HAUTUNEINFO!]px;">
					<div class="UneInfo" style="height:[!HAUTIMG!]px;">
						<a href="/[!Men::Url!][!AjoutLien!]/[!RqUrl::Url!]/Produit/[!R::Url!]" title="[!R::Titre!]" >
							[IF [!R::[!CHAMPIMG!]!]!=]
								<img src="[!Domaine!]/[!R::[!CHAMPIMG!]!].limit.[!LARGIMG!]x[!HAUTIMG!].jpg" alt="[!R::Titre!]" title="[!R::Titre!]"  />
							[ELSE]
								<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/Img/defautProd.jpg.limit.[!LARGIMG!]x[!HAUTIMG!].jpg" alt="[!R::Titre!]" title="[!R::Titre!]"  />
							[/IF]
						</a>
					</div>
					<div class="UneInfoText">
						<a href="/[!Men::Url!][!AjoutLien!]/[!RqUrl::Url!]/Produit/[!R::Url!]" title="[!R::Titre!]" >[!R::[!CHAMPTXT!]!]</a>
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>
</div>

<script type="text/javascript">
	var divAppsDefile = $('ContenuCompletDefile');
	var fxAppsDefile = new Fx.Tween(divAppsDefile, {property:'margin-left', link:'ignore'});

	window.addEvent('domready', function() {
		//var btnLeft = new Element('button', {'class':'Clignote ImagesbtnLeft'});
		//var btnRight = new Element('button', {'class':'Clignote ImagesbtnRight'});
		
		var btnLeft = new Element('button', {'class':'ImagesbtnLeft'});
		var btnRight = new Element('button', {'class':'ImagesbtnRight'});
		
		var divApps = $('ContenuComplet');
		btnLeft.inject(divApps, 'before');
		btnRight.inject(divApps, 'before');
		divApps.setStyles({'overflow':'hidden', 'width':'[!LgDefil!]'});
		btnLeft.addEvent('click', moveImgApps);
		btnRight.addEvent('click', moveImgApps);
		btnLeft.addEvent('mouseover', moveImgApps);
		btnRight.addEvent('mouseover', moveImgApps);

		//setInterval(clignote, 500);
		// pour faire défiler tout le temps
		setInterval(moveImgApps, [!VITESSE!]);

	});

	function moveImgApps(e) {
		var move = (e&&e.target.hasClass('ImagesbtnLeft')) ? 3 : -3;
		var currentML = divAppsDefile.getStyle('margin-left').toInt();
		var targetML = currentML + move * [!TAILLEUNEINFO!];

		if (Math.abs(targetML)>= Math.abs([!MaxML!])) {
			divAppsDefile.setStyle('margin-left', "-[!MaxML!]px");
			currentML = 0;
			targetML = 0;


		}
		if(targetML > 0) targetML = 0;
		if(targetML < -[!MaxML!]) targetML = -[!MaxML!];
		fxAppsDefile.start(targetML + 'px');
	}

	function moveImgAppsold(e) {
		var move = (e.target.hasClass('ImagesbtnLeft')) ? 3 : -3;
		var currentML = divAppsDefile.getStyle('margin-left').toInt();
		var targetML = currentML + move * [!TAILLEUNEINFO!];
		if(targetML > 0) targetML = 0;
		if(targetML < -[!MaxML!]) targetML = -[!MaxML!];
		fxAppsDefile.start(targetML + 'px');
	}
</script>
