//fiche Produit
[!ImgHeight:=100!]
<div class="LaFicheProduit">
	[STORPROC [!Query!]/Publier=1|P|0|1]
		[NORESULT]
			[REDIRECT]/[!Systeme::currentMenu::Url!][/REDIRECT]
		[/NORESULT]

		[STORPROC Catalogue/Categorie/Produit/[!P::Id!]|Cat|0|1][/STORPROC]
		[COUNT Catalogue/Produit/[!P::Id!]/Image|Cpt]
		[!Cpt+=1!]
		<div class="FicheProduitHaut">
			<div id="LesImagesProduits" >
				<div id="ContenuComplet">
					<div id="ContenuCompletDefile">
						<img src="/[!P::Image!].limit.66x100.jpg" alt="[!P::Titre!]" title="[!P::Titre!]"  onclick="return apercu('[!Domaine!]/[!P::Image!].limit.256x385.jpg','[UTIL SANSCOTE][!Im::Titre!][/UTIL]',this);"/>
						[STORPROC Catalogue/Produit/[!P::Id!]/Image|Im]
							<img src="/[!Im::URL!].limit.66x100.jpg" alt="[!Im::Titre!]" title="[!Im::Titre!]"  onclick="return apercu('[!Domaine!]/[!Im::URL!].limit.256x385.jpg','[UTIL SANSCOTE][!Im::Titre!][/UTIL]',this);"/>
						[/STORPROC]
					</div>
				</div>
			</div>	
			<div class="BlockPrincipal">
				<div id="PrincipalImage">
					<img src="[IF [!P::Image!]!=]/[!P::Image!].limit.256x385.jpg[ELSE][!Domaine!]/Skins/[!Systeme::Skin!]/Img/defautProd.jpg.limit.256x385.jpg[/IF]" title="[!P::Titre!]" alt="[!P::Titre!]" id="ImagePrincipale"/>
				</div>
				<div class="PrincipalDesc" [IF [!P::DescriptionDansListe!]]style="height:auto;"[/IF]>
					<div class="BlocDesc1">
						<div class="TitreProduit"><h2>[!Cat::Nom!]</h2></div>
						<div class="BlocModelePrix">
							<div class="PrixProduit">
								[IF [!P::Tva!]=0]
								[ELSE]
									//[!TauxTva:=[!P::TauxTva!]!]
									[!TauxTva:=!]
									[STORPROC Catalogue/TypeTaux/Nom=[!P::TypeTaux!]|Ttva|0|1]
										[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
											[!TauxTva:=[!Tx::Taux!]!]
										[/STORPROC]
									[/STORPROC]
									[IF [!P::Tva!]=1]
										// Un taux specifique
										<div class="PPIProduit" ><span class="dontTva" >(dont TVA [!TauxTva!]%)</span>
										[!Utils::getPrice([!P::PrixTTC([!P::PPHT!],[!TauxTva!])!])!] €</div>
									[ELSE]
										// plusieurs taux possibles
										[IF [!P::PPHT!]!=&&[!P::PPHT!]!=0]								
											<div class="PPIProduit" >
												<span class="dontTva" >(dont TVA [!TauxTva!]%)</span>
												[!Utils::getPrice([!P::PrixTTC([!P::PPHT!],[!TauxTva!])!])!] €
											</div>
											[STORPROC Catalogue/TypeTaux/Nom=[!P::TypeTaux2!]|Ttva|0|1]
												<div class="PPIProduit" >
													[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
														[!TauxTva:=[!Tx::Taux!]!]
														<span class="dontTva" >(dont TVA [!TauxTva!]%)</span>
														<span style="font-size: 12px;">[!Utils::getPrice([!P::PrixTTC([!P::PPHT!],[!TauxTva!])!])!] €</span>
													[/STORPROC]
												</div>
											[/STORPROC]
										[/IF]
									[/IF]
								[/IF]
							</div>
							<div class="GauchePrix" >
								[IF [!P::Fabricant!]!=]
									[STORPROC Catalogue/Fabricant/[!P::Fabricant!]|Fab|0|1][/STORPROC]
									<div class="UneInfoTitre">[!Fab::Nom!]</div>
								[/IF]
								//[IF [!P::Titre!]!=]<div class="UneInfoTitre" ><h1>[SUBSTR 50|...][!P::Titre!][/SUBSTR]</h1></div>[/IF]
								[IF [!P::Titre!]!=]<div class="UneInfoTitre" ><h1 >[!P::Titre!]</h1></div>[/IF]
							</div>
						</div>
					</div>
					<div class="BlocDesc1">
						[IF [!P::Chapo!]!=]<div class="UneInfoSousTitre " >[!P::Chapo!]</div>[/IF]
						[IF [!P::DescriptionDansListe!]]
							<div class="UneInfo" style="margin-bottom:40px;">[!P::Description!]</div>[IF [!P::Fabricant!]!=]<br /><br />[/IF]
						[ELSE]
							[IF [!P::Dimensions!]!=]<div class="UneInfo" >
								<span class="Libelle"> Dimensions : </span>[!P::Dimensions!]
							</div>[/IF]
							[IF [!P::SolMurale!]!=]<div class="UneInfo" >
								<span class="Libelle"> Pose : </span>[!P::SolMurale!]
							</div>[/IF]
							[IF [!P::Energie!]!=]<div class="UneInfo" >
								<span class="Libelle"> Energie : </span> 
									[SWITCH [!P::Energie!]|=]
										[CASE 0]
											Gaz Naturel
										[/CASE]
										[CASE 1]
											Propane
										[/CASE]
										[CASE 2]
											Gaz Naturel ou Propane
										[/CASE]
									[/SWITCH]
							</div>[/IF]
							[IF [!P::Service!]!=]<div class="UneInfo" >
								<span class="Libelle"> Service : </span>[!P::Service!]
							</div>[/IF]
							[IF [!P::Evacuation!]!=]<div class="UneInfo" >
								<span class="Libelle"> Evacuation : </span> 
									[SWITCH [!P::Evacuation!]|=]
										[CASE CF]
											Conduit Fumée
										[/CASE]
										[CASE FF]
											Flux forcé
										[/CASE]
										[CASE VMC_Gaz]
											VMC Gaz
										[/CASE]
									[/SWITCH]
							</div>[/IF]
							[IF [!P::Puissance!]!=]<div class="UneInfo" >
								<span class="Libelle"> Puissance : </span>[!P::Puissance!] Kw
							</div>[/IF]
							[IF [!P::Sanitaire!]!=]<div class="UneInfo" >
								<span class="Libelle"> Type sanitaire : </span>[!P::Sanitaire!]
							</div>[/IF]
							[IF [!P::DebitSanitaire!]!=]<div class="UneInfo" >
								<span class="Libelle"> Débit sanitaire : </span>[!P::DebitSanitaire!] l/mn
							</div>[/IF]
						[/IF]
						[STORPROC Catalogue/Produit/[!P::Id!]/Fichier|Pl|0|1]
							<div class="Plaquette">
								<a href="/[!Pl::URL!]" target="_blank" title="[!Pl::Titre!]" alt="[!Pl::Titre!]" >[!Pl::Titre!]</a>
							</div>
						[/STORPROC]
					</div>
					[IF [!P::Fabricant!]!=]
						[STORPROC Catalogue/Fabricant/[!P::Fabricant!]|Fab|0|1]
							<div class="ImgFab">
								<img src="/[!Fab::Logo!]" alt="[!Fab::Nom!]" title="[!Fab::Nom!]" />
							</div>
						[/STORPROC]
					[/IF]
				</div>
				<div class="Boutons">
					<a href="/Contact?Sujet=[!P::Url!]" alt="Etre contacter pour ce produit" class="Contact">Contact</a>
					<a href="/[!Lien!]/ImprimeFiche.pdf" alt="Imprimer cette fiche" class="Impression" target="_blank" >Imprimer</a>
					<a href="/Partager?C_Lien=[!Lien!]" alt="Partager cette page" class="Partage">Partager</a>
					<a href="/Simulateur" alt="Simulateur" class="BtnSimulateur">Partager</a>
				</div>
			</div>
		</div>
		[IF [!P::DescriptionDansListe!]]
		[ELSE]
			[IF [!P::Description!]!=]<div class="FicheProduitCaracterisques">
				<div class="TitreCaracteristiques">Description</div>
				<p>[!P::Description!]</p>
			</div>[/IF]
		[/IF]
		[IF [!P::Avantages!]!=]<div class="FicheProduitCaracterisques">
			<div class="TitreCaracteristiques">Avantages produits</div>
			<p>[!P::Avantages!]</p>
		</div>[/IF]
	
	[/STORPROC]
</div>
<script type="text/javascript">
	/********************************
	*	DECLARATION GLOBALE
	********************************/
	var divAppsDefile = $('ContenuCompletDefile');
	var fxAppsDefile = new Fx.Tween(divAppsDefile, {property:'margin-top', link:'ignore'});
	//recupération des hauteurs
	var hauteurFleche = 70;
	var hauteurImagePrincipale = 0;
	var hauteurScrollImages = 0;

	window.addEvent('domready', function() {
		//initialisation
		hauteurImagePrincipale = $('ImagePrincipale').getHeight();
		hauteurScrollImages = $('ContenuCompletDefile').getHeight()-hauteurFleche;

		//initialisation du scroll si hauteur image principale est inferieure à la liste des images
		if (hauteurImagePrincipale<hauteurScrollImages){
			var divApps = $('ContenuComplet');

			//affichage
			//creation des boutons
			var btnHaut = new Element('button', {'id':'ImagesBtnHaut','class':'ImagesBtnHaut'});
			var btnBas = new Element('button', {'id':'ImagesBtnBas','class':'ImagesBtnBas'});
			btnHaut.inject(divApps, 'before');
			btnBas.inject(divApps, 'after');
			btnHaut.addEvent('click', moveImgApps);
			btnBas.addEvent('click', moveImgApps);

			//mise à jour
			resetScrollMiniature();
		}
	});

	function resetScrollMiniature(balise) {
		//mise à jour de la hauteur de l'image principale
		if (balise != null){
			//calcul de la proportion
			var prop=(balise.getHeight()/balise.getWidth());
			if (prop>1){
				//portrait
				hauteurImagePrincipale=385;
			}else{
				//paysage
				hauteurImagePrincipale=265*prop;
			}
		}else hauteurImagePrincipale = $('ImagePrincipale').getHeight();
		
		if (hauteurImagePrincipale<hauteurScrollImages){
			//on affiche les boutons
			$('ImagesBtnHaut').setStyle('visibility','visible');
			$('ImagesBtnBas').setStyle('visibility','visible');

			//on redimensionne le contenu des miniatures
			var divApps = $('ContenuComplet');
			divApps.setStyles({'overflow':'hidden', 'height':(hauteurImagePrincipale-hauteurFleche)+'px'});
		}else{
			//on cache les boutons
			$('ImagesBtnHaut').setStyle('visibility','hidden');
			$('ImagesBtnBas').setStyle('visibility','hidden');

			//on restaure la nature du conatiner
			var divApps = $('ContenuComplet');
			divApps.setStyles({'overflow':'none', 'height':'auto'});
		}
	}



	function moveImgApps(e) {
		var move = (e.target.hasClass('ImagesBtnHaut')) ? 50 : -50;
		var currentML = divAppsDefile.getStyle('margin-top').toInt();
		var targetML = currentML + move;
		if(targetML > 0) targetML = 0;
		if(targetML < -hauteurScrollImages+hauteurImagePrincipale-(2*hauteurFleche)) targetML = -hauteurScrollImages+hauteurImagePrincipale-(2*hauteurFleche);
		//alert(move+' '+currentML+' '+targetML);
		
		fxAppsDefile.start(targetML + 'px');
	}
	// Clic sur miniature
	function apercu(img,legende,balise) {
		var image = new Element('img', {
			'src': img,
			'alt': legende,
			'title': legende,
			'id': 'ImagePrincipale'
		});
		$('PrincipalImage').empty();
		image.inject($('PrincipalImage'));
		resetScrollMiniature(balise);
		return false;
	}
</script>
