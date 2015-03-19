<!--Boutique/Reference/  c est le detail d  une annonce -->
[!MenuDemande:=!]
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|1|1]
	[!MenuDemande:=[!H::Value!]!]
[/STORPROC]
<!-- Une reference à acheter-->
<!--Boutique/Reference/Default-->
[MODULE Systeme/Structure/Gauche_Boutique]
[INFO [!Query!]|I]
//CONSTRUCTION REQUETE
[!REQUETE:=Boutique!]
[!PRODUIT_LINK:=[!Systeme::CurrentMenu::Url!]!]
//GESTION DES CATEGORIES
[STORPROC [!I::Historique!]|H|0|10]
	[IF [!H::DataSource!]=Categorie]
		[!REQUETE+=/[!H::DataSource!]/[!H::Value!]!]
		[!PRODUIT_LINK+=/[!H::Value!]!]
	[/IF]
	[IF [!H::DataSource!]=Genre][!GENRES:::=[!H::Value!]!][/IF]
[/STORPROC]
[!REQUETE+=/Produit!]

//GESTION DES GENRES
[COUNT [!GENRES!]|NbG]
[IF [!NbG!]>0]
	[!REQUETE+=/(!!]
	[!B:=0!]
	[STORPROC [!GENRES!]|G]
		[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
		[STORPROC Boutique/Genre/[!G!]|Genre][/STORPROC]
		[!REQUETE+=GenreId=[!Genre::Id!]!]
	[/STORPROC]
	[IF [!NbG!]=1]
		//Ajout des enfants dans le cas d'une selection de premier niveau
		[STORPROC Boutique/Genre/[!G!]/Genre|Ge|0|100]
			[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
			[!REQUETE+=GenreId=[!Ge::Id!]!]
		[/STORPROC]
	[/IF]
	[!REQUETE+=!)!]
[/IF]
//GESTION DES MOTS CLEFS
<!--- contenu central -->
[STORPROC [!Query!]|R|0|1]
	[STORPROC Boutique/Produit/Reference/[!R::Id!]|P|0|1][/STORPROC]
	[STORPROC Boutique/Client/Reference/[!R::Id!]|CLI|0|1][/STORPROC]
	[STORPROC Boutique/Genre/Produit/[!P::Id!]|G|0|1][/STORPROC]
	[!CategConsole:=!]
	[STORPROC Boutique/Categorie/*/Categorie/Produit/[!P::Id!]|CP||tmsCreate|ASC]
		[IF [!CategConsole!]=][!CategConsole+= [!CP::Nom!]!][/IF]
	[/STORPROC]

	// RECHERCHE DES INFOS DU PRODUIT et DU VENDEUR
	<div class="centre">
		// BLOCK DESCRIPTION jeu et vendeur
		<div class="BlocProduitPages">
			<div class="CoteProduitPagesG"><img src="/Skins/gamesavenue/Images/block_debut_prod.jpg"></div>
			<div class="CoteProduitPagesD"><img src="/Skins/gamesavenue/Images/block_fin_prod.jpg"></div>
			<div class="ProduitPagesCentre">
				<div class="blocRefDetailImage">
					[IF [P::Image!]!=]
						<a href="/[!P::Image!].limit.1000x1000.jpg" title="[!P::Nom!]" class="mb"  rel="width:400,height:300"><img src="/[!P::Image!]" class="img_detail"></a>
					[ELSE]
						<img src="/Skins/gamesavenue/Images/defaut_image.jpg" class="img_detail"/>
					[/IF]
				</div>
				<div class="blocProduitPagesDescription">
					<div class="blocRefDetailTitreColonnes">
						<div class="blocRefDetailTitrecol1">
							<span class="blocambiance_color blocProduitPagesTitre">[!P::Nom!][IF [!Console!]!=]&nbsp;&nbsp;/&nbsp;&nbsp;[!Console!][/IF]</span>
						</div>
						<div class="blocRefDetailTitrecol2">
							<span class="blocProduitPagesTitre"><a href="/GamesAvenue/Client/[!CLI::Id!]" class="lienColor14 blocambiance_color">Fiche Vendeur</a></span>
						</div>
					</div>
					<div  class="blocProduitPagesDescriptioncol1"  >
						<span class="blocPagestexte">[IF [!P::Note!]!=]<br/>Note :&nbsp;&nbsp;<strong>[!P::Note!]</strong>[/IF]<br/>Nombre d'articles Neuf :&nbsp;&nbsp;<strong>[!P::getNbNeufs()!]</strong><br/>Nombre d'articles Occasions :&nbsp;&nbsp;<strong>[!P::getNbOccasions()!]</strong>[IF [!P::Age!]!=]<br/>Age :&nbsp;&nbsp;<strong>[!P::Age!]</strong>[/IF][IF [!P::Joueur!]!=]<br/>Joueur :&nbsp;&nbsp;<strong>[!P::Joueur!]</strong>[/IF]</span>
					</div>
					<div  class="blocProduitPagesDescriptioncol2" >
						<span class="blocPagestexte">
							[IF [!P::Annee!]!=0&&[!P::Annee!]!=]<br/>Année :&nbsp;&nbsp;<strong>[!P::Annee!]</strong>[/IF][IF [!P::Editeur!]!=]<br/>Editeur :&nbsp;&nbsp;<strong>[!P::Editeur!]</strong>[/IF][IF [!G::Nom!]!=]<br/>Genre :&nbsp;&nbsp;<strong>[!G::Nom!]</strong>[/IF]
						<span>
						<span class="blocProduitPrixAp"><br/>*à partir de</span>
						<span class="blocProduitPrix blocambiance_color">[!P::PrixAPartirDe( )!] €</span>
					</div>
					<div class="blocRefDetailColPrix" >
						<div class="blocRefDetailColPrixLigne" >
							<div class="blocAvatar">
								[IF [CLI::Avatar!]!=]
									<img src="/[!CLI:Avatar!]" style="width:35px;height:40px;>
								[ELSE]
									<img src="/Skins/gamesavenue/Images/defaut_avatar.jpg" style="width:35px;height:40px;"/>
								[/IF]
							</div>
							<div class="blocDescriptionVendeur">
								<span class="blocPagestexte10">
									[!CLI::Pseudonyme!][IF [!CLI::getNbVentes()!]!=]<br/>Nbre ventes :&nbsp;&nbsp;<strong>[!CLI::getNbVentes()!]</strong>[/IF]
								<span>
							</div>
						</div>
						<div class="blocRefDetailColPrixLigne" >
							<span class="blocPagestexte10">Notation</span>
							//[STORPROC Notationduvendeur]
							[STORPROC 4]
								<span class="blocambiance_etoile" style="padding-left:10px;">&nbsp;</span>
							[/STORPROC]
						</div>
						<div class="blocRefDetailColPrixLigne" >
							<span class="blocPagestexte10">
								Localisation : [!CLI::Ville!]&nbsp;[!CLI::Pays!]
							</span>
						</div>
					</div>
				</div>
	
			</div>
		</div>
		// BLOCK DESCRIPTION annonce
		<div class="ContenuPages">
			<div class="ligneCotePageG"><img src="/Skins/gamesavenue/Images/block_debut_ref.jpg"></div>
			<div class="ligneCotePageD"><img src="/Skins/gamesavenue/Images/block_fin_ref.jpg"></div>
			<div class="lignePageCentre"> 
				<div class="blocRefDetailImage">
					[IF [R::Image!]!=]
						<img src="/[!R::Image!]">
					[ELSE]
						<img src="/Skins/gamesavenue/Images/defaut_image.jpg" style="width:64px;height:64px;"/>
					[/IF]
				</div>
				<div class="blocProduitPagesDescription">
					<div  class="blocRefDetailTitrecol"  >
						<span class="blocPagestexte10">
							Vendeur : <a href="/GamesAvenue/Client/[!CLI::Id!]" class="lienColor14 blocambiance_color">[!CLI::Pseudonyme!]</a>
							[IF [!R::Etat!]!=]<br/>Etat :&nbsp;&nbsp;<strong>[IF [!R::Etat!]=1]Neuf[ELSE]Occasion[/IF]</strong>[/IF]
							[IF [!P::Note!]!=]<br/>DESCRIPTION
							<br/><strong>[!R::Description!]</strong>[/IF]
						</span>
						<div class="btnGrisFonce" style="margin:0;padding-top:10px;">
							<div class="btnGrisFonceGauche"></div>
							<div class="btnGrisFonceCentre">
								<a href="/GamesAvenue/Client/[!CLI::Id!]" class="btnGrisFonce" >
									Voir le profil et les avis
								</a>
							</div>
							<div class="btnGrisFonceDroite" ></div>
						</div>
					</div>
					<div class="blocRefDetailColPrix" style="padding-top:20px;" >
						<span class="blocProduitPrix blocambiance_color">[!R::Tarif!] €</span>
						<div class="btnGrisClair" style="width:80px;padding-top:20px;">
							<div class="btnGrisClairGauche">&nbsp;</div>
							<div class="btnGrisClairCentre" >
								<a href="/[!Lien!]/Acheter_Etap1" class="btnGrisClair blocambiance_color">
									Acheter
								</a>
							</div>
							<div class="btnGrisClairDroite">&nbsp;</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>    // fin CONTENU COMPLET

[/STORPROC]