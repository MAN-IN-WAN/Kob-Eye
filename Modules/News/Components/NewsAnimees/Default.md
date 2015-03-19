//affichage d'une catégorie de news  dans un block
[COUNT News/Categorie/[!CATEGAFFICH!]/Nouvelle/Publier=1&ALaUne=1|NbNe]
[STORPROC News/Categorie/[!CATEGAFFICH!]|Cat][/STORPROC]
[!Lesens:=ASC!]
[IF [!SENS!]=1][!Lesens:=DESC!][/IF]
[IF [!ORDRE!]=][!ORDRE:=tmsCreate!][/IF]

<div class="[!NOMDIV!]" >
	<div id="Actus" class="BlocAccueil">
		<div style="position:relative;margin:20px 5px;"> 
			<div class="TitreBloc">
				<div class="blocleft">[IF [!TITRE!]]<h2>[!TITRE!]</h2>[ELSE]<h2>Les Actus</h2>[/IF]</div>
				[IF [!NbNe!]>[!NBINFOSVISIBLES!]]
					<div id="paginationAccueilActus" class="paginationAccueilActus">
						<span href="javascript:;" onclick="moveActu('up')" title="Monter" class="precedent" ></span>
						<span href="javascript:;" onclick="moveActu('down')" title="Descendre" class="suivant"></span>
					</div>
				[/IF]
			</div>
			<div id="MaskActu" style="position:relative; overflow:hidden">
				<div id="ActuContent">
					[STORPROC News/Categorie/[!CATEGAFFICH!]/Nouvelle/Publier=1&AlaUne=1|Ne|0|[!NBINFOSTOTALES!]|[!ORDRE!]|[!Lesens!]]
						<div class="BlocActu">
							<div class="TitreNews">
								<a href="/[!MENUACTU!]/[!Ne::Url!]" >
									[IF [!CHAMPTITRE!]=tmsEdit||[!CHAMPTITRE!]=tmsCreate]
										Le [!Utils::getDate(d/m/Y,[!Ne::[!CHAMPTITRE!]!])!]
									[ELSE]
										[SUBSTR [!NBCARACTTITRE!]][!Ne::[!CHAMPTITRE!]!][/SUBSTR]
									[/IF]
									
								</a>
							</div>
							[IF [!CHAMPACCROCHE!]!=]
								<div class="Accroche" > 
									[IF [!CHAMPACCROCHE!]=tmsEdit||[!CHAMPACCROCHE!]=tmsCreate]
										Le [!Utils::getDate(d/m/Y,[!Ne::[!CHAMPACCROCHE!]!])!]
									[ELSE]
										[SUBSTR [!NBCARACTACCROCHE!]][!Ne::[!CHAMPACCROCHE!]!][/SUBSTR]
									[/IF]
								</div>
							[/IF]
							<div class="PartieInfo">
								[IF [!Ne::Image!]!=]
									<div class="affichimage">
										<a href="/[!MENUACTU!]/[!Ne::Url!]"  ><img src="[!Domaine!]/[!Ne::Image!].limit.[!LARGEURUNEIMG!]x[!HAUTEURUNEIMG!].jpg"   alt="[!Ne::Nom!]" width="[!LARGEURUNEIMG!]" height="[!HAUTEURUNEIMG!]" /></a>
									</div>
								[/IF]
								<div class="Desc" >
									[SUBSTR [!NBCARACT!]][!Ne::[!CHAMPTEXTE!]!][/SUBSTR]
								</div>					
							</div>	
							<div class="accueilliennews">
								<a href="/[!MENUACTU!]/[!Ne::Url!]"  >[IF [!TEXTELIENDETAIL!]][!TEXTELIENDETAIL!][ELSE]Lire la suite[/IF]</a>	
							</div>
						</div>
					[/STORPROC]
				</div>
			</div>
		</div>
	</div>
	<div class="touteslesnews"><a href="/[!Systeme::getMenu(News/Nouvelle)!]" >[IF [!TEXTELIENTOUTES!]][!TEXTELIENTOUTES!][ELSE]voir toutes les news[/IF]</a></div>



</div>	

<script type="text/javascript">

	[!Hauteur:=[!HAUTEURUNEINFO!]!]

	[!Hauteurtotale:=[!HAUTEURUNEINFO!]!]

	[!Hauteurtotale:*[!NBINFOSVISIBLES!]!]

	[!InfosVisibles:=[!NBINFOSVISIBLES!]!]
	[!Delai:=[!TEMPSDEFILEMENT!]!]

	// Surcouche JS
	var num = 0;
	var diranim ='down';

	var animInterval2 ;

	// Ajustement de la div conteneur
	window.addEvent('domready', function() {


		if($('ActuContent') == null) return;
		if (animInterval2==null) animInterval2 = setInterval("moveActu()", [!Delai!]);

		$('MaskActu').setStyles({
			'height': '[!Hauteurtotale!]px',
			'position': 'relative',
			'overflow': 'hidden'
		});
		$('ActuContent').setStyles({
			'position':'absolute',
			'top':'0',
			'left':'0',
			'right':'0'
		});
		$$('div.BlocActu').setStyles({
			'margin-bottom': '0',
			'min-height': '[!Hauteur!]px'
		});

		// On affiche les images uniquement si il y en a plus de 1
		var items = $('ActuContent').getElements('div.BlocActu');
		if(items.length > [!InfosVisibles!]) $('Actus').getElements('span').setStyle('display', 'block');
	});

	// Fonction de changement
	function moveActu( direction ) {
		if (direction==null) {
			direction=diranim; 
		} else {
			clearInterval(animInterval2);
		}
		// Nb d'éléments à afficher et items courants
		var nbItems = [!InfosVisibles!];
		var items = $('ActuContent').getElements('div.BlocActu');
		if(direction == 'up' && num > 0) num--;
		if(direction == 'down' && num < items.length - nbItems) num++;

		if(direction == 'up' && num == 0) diranim='down';
		if(direction == 'down' && num == items.length - nbItems) diranim='up';

		// On affiche la div "num" et ses (nbItems - 1) suivantes
		var dest = - num * [!Hauteur!] ;
		dest += 'px';
		$('ActuContent').tween('top', dest);
	}
	

</script>























