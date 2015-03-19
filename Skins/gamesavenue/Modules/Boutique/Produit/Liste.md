<!--Boutique/Produit/Liste des produits demandés-->
// Recup couleur des titre en fonction de l'univers
[MODULE Systeme/Structure/CouleurUnivers]
[INFO [!Chemin!]|I]
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
	[!G:=1!]
	[!REQUETE+=/(!!]
	[!B:=0!]
	[IF [!NbG!]=1]
		//Ajout des enfants dans le cas d une selection de premier niveau
		[STORPROC [!GENRES!]|G]
			[STORPROC Boutique/Genre/[!G!]|Ge|0|100]
				[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
				[!REQUETE+=GenreId=[!Ge::Id!]!]
			[/STORPROC]
		[/STORPROC]
		[STORPROC Boutique/Genre/[!G!]/Genre|Ge|0|100]
			[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
			[!REQUETE+=GenreId=[!Ge::Id!]!]
		[/STORPROC]
	[ELSE]
		[STORPROC [!GENRES!]|G][/STORPROC]
		[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
		[STORPROC Boutique/Genre/[!G!]|Genre][/STORPROC]
		[!REQUETE+=GenreId=[!Genre::Id!]!]
		
	[/IF]
	[!REQUETE+=!)!]
[/IF]

//GESTION DES MOTS CLEFS
[IF [!Recherche!]!=]
	[!Separateur:= !]
	[!Roch:=[![!Recherche!]:/ !]!]
	[STORPROC [!Roch!]|R2]
		[!R:=[!Utils::Canonic([!R2!])!]!]
		[COUNT Boutique/BlackList/Titre~[!R!]|Bl]
		[IF [!Bl!]=0]
			//[STORPROC Boutique/Motclef/Canon~[!Utils::Canonic([!R!])!]|M|0|1]
				[IF [!Details!]!=][!Details+= | !][/IF]
				[!Details+= <a href="?Recherche=[!M::Canon!]">[!M::Nom!]</a>!]
			//[/STORPROC]
			[IF [!Re!]][!Re+=[!Separateur!]!][/IF]
			[!Re+=[!R!]!]
		[/IF]
	[/STORPROC]
	
	
	[IF [!G!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][!G:=1!][/IF]
	[!REQUETE+=MotClef.ProduitId(Canon~[!Re!])!]
[/IF]

//GESTION DES NOTES
[IF [!Popularite!]!=0]
	[IF [!G!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][!G:=1!][/IF]
	[!REQUETE+=Note>=[!Popularite!]!]
[/IF]

// -- Gestion de la pagination
[IF [!Page!]=EMPTY]
	[!Page:=1!]
[/IF]
[!TypeEnf:=Produit!]
//Definition des elements a afficher
[IF [!PagePos!]!=]
	[!Page[!TypeEnf!]:=[!PagePos!]!]
[ELSE]
	[!Page[!TypeEnf!]:=1!]
[/IF]
[!MaxLine:=20!] // nb de produit à afficher
[IF ![!Popularite!]] [!Popularite:=0!][/IF]
[IF ![!Filtre!]] [!Filtre:=0!][/IF]
//[!REQUETE:=Boutique/Produit/ProduitId!]


		[!DEBUG::REQUETE!]


<!--- contenu central -->
<div class="centre">
	<form>
		[MODULE Systeme/Structure/Recherche_top]
	</form>
	[!ProduitPair:=0!]
	[COUNT [!REQUETE!]|NbRep]
	[IF [!NbRep!]>0]
		//On compte le nombre total d element a affciher
		[!TotalPage:=[!NbRep:/[!MaxLine!]!]!]
		//On calcule le nombre total de page
		[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
			//On arrondit au chiffre superieur le nombre total de page
			[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
		[/IF]
	//PAGINATION
	[IF [!TotalPage!]>1]
		<div class="LignePagination">
			<span class="ResultatPagination"><span>
					// Retour à la première page
					<a href="/[!Lien!]?Pos=1" /><img src="/skins/gamesavenue/Images/dble-fleche-gauche.png"></a>
				</span><span>
					// Page précédente
					<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:-1!]<1]1[ELSE][!Page[!TypeEnf!]:-1!][/IF]" /><img src="/skins/gamesavenue/Images/fleche-gauche.png"></a>
				</span><span>
					// Aller à une page précise

					[STORPROC [!TotalPage!]|Pag]
						// Page 1
						[IF [!Pos!]=1]
							<span class="current[IF [!Page[!TypeEnf!]!]=1] blocambiance_color[/IF]"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// ...
						[IF [!Pos!]=1&&[!Page[!TypeEnf!]!]>3]...[/IF]
						// Page n-1
						[IF [!Pos!]=[!Page[!TypeEnf!]:-1!]&&[!Page[!TypeEnf!]!]>2]
							<span class="current"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// Page courante
						[IF [!Pos!]=[!Page[!TypeEnf!]!]&&[!Page[!TypeEnf!]!]>1]
							<span class="current blocambiance_color">[!Pos!]</span>
						[/IF]
						// Page n+1
						[IF [!Pos!]=[!Page[!TypeEnf!]:+1!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-1!]]
							<span class="current"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// ...
						[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-2!]]...[/IF]
						// Page n-1
						// Dernière page
						[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]!=[!TotalPage!]]
							<span class="current"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// [IF [!Pos!]!=[!Page[!TypeEnf!]!]]
						// 	<a href="/[!Lien!]?PagePos=[!Pos!]" class="LesPages" />[!Pos!]</a>
						// [ELSE]
						//	<span class="current blocambiance_color">[!Page[!TypeEnf!]!]</span>
						// [/IF]
					[/STORPROC]
				</span><span>
					// Page suivante
					<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:+1!]>[!TotalPage!]][!TotalPage!][ELSE][!Page[!TypeEnf!]:+1!][/IF]"/><img src="/skins/gamesavenue/Images/fleche-droite.png"></a>
				</span><span>
					// Dernière Page
					<a href="/[!Lien!]?PagePos=[!TotalPage!]"/ ><img src="/skins/gamesavenue/Images/dble-fleche-droite.png"></a>
				</span>
			</span>
		</div>
	[/IF]
		[STORPROC [!REQUETE!]|P|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|Annee|DESC]
			[!Console:=!]
			[STORPROC Boutique/Categorie/*/Categorie/Produit/[!P::Id!]|CP||tmsCreate|ASC]
				[IF [!Pos!]=1][!Console:=[!CP::Nom!]!][/IF]
			[/STORPROC]
			[IF [!Utils::isPair([!ProduitPair!])!]]
				// LIGNE DEUX PRODUIT DE FRONT
				<div class="ligneListeProduits">[!OKdiv:=0!]
			[/IF]
				<div class="ColListeProduits" >
					[IF [!Utils::isPair([!ProduitPair!])!]]
						<div class="bordureProduit blocambiance_border_right">
					[ELSE]
						// PAS DE BORDURE POUR LE PRODUIT DE DROITE
						<div class="bordureProduit" style="border-right:none;padding-left:5px;" >
					[/IF]
						<div class="colProduit">
							[IF [!P::Image!]!=]
								<div class="blocProduitImageMini">
									<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]">
										<img src="/[!P::Image!].mini.64x72.jpg" style="width:64px;height:72px;" />
									</a>
								</div>
							[ELSE]
								<div class="blocProduitImageMini">
									<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]">
										<img src="/Skins/gamesavenue/Images/defaut_image.jpg" style="width:64px;height:64px;" />
									</a>
								</div>
							[/IF]
							<div class="blocProduitDescriptif">
								<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="blocProduitTitre blocambiance_color">[!P::Nom!] [IF [!Console!]!=]&nbsp;&nbsp;/&nbsp;&nbsp;[!Console!][/IF]</a>
								<span class="blocPagestexte">
									[IF [!P::PrixAPartirDe( )!]!=]
										<br />
										Nombre d'articles Neuf :&nbsp;&nbsp;<strong>[!P::getNbNeufs( )!]</strong><br />
										Nombre d'articles Occasions :&nbsp;&nbsp;<strong>[!P::getNbOccasions( )!]</strong>
									[/IF]
									[IF [!P::Annee!]!=&&[!P::Annee!]!=0]<br />Année :&nbsp;&nbsp;<strong>[!P::Annee!]</strong>[/IF]
									[IF [!P::Age!]!=]<br />Age :&nbsp;&nbsp;<strong>[!P::Age!]</strong>[/IF][IF [!P::Joueur!]!=]<br/>Joueur :&nbsp;&nbsp;<strong>[!P::Joueur!]</strong>[/IF]
								<span>
								<div class="btnGrisFonce" style="width:150px;">
									<div style="text-align:right;">
										<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="blocambiance_color" >Voir le détail du produit</a>
									</div>
								</div>
							</div>
						</div>
							<div class="blocProduitAchatVente" >
								[IF [!P::PrixAPartirDe( )!]!=]
									<span class="blocProduitPrix blocambiance_color">[!P::PrixAPartirDe( )!]  €*</span>
									<span class="blocProduitPrixAp"><br/>*à partir de</span>
									<br/><br/>
									<div class="btnGrisClair ">
										<div class="btnGrisClairGauche">&nbsp;</div>
										<div class="btnGrisClairCentre"><a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="btnGrisClair blocambiance_color">Acheter</a></div>
										<div class="btnGrisClairDroite">&nbsp;</div>
									</div>
								[ELSE]
									<div class="blocProduitTitre blocambiance_color aumilieu" ><img src="/Skins/gamesavenue/Images/produit-non-disponible.jpg"></div>
								[/IF]
								<br />
								[BLOC Bouton|center|/[!PRODUIT_LINK!]/Produit/[!P::Url!]/Vendre]Vendre[/BLOC]
							</div>
						</div>
					</div>
				[!ProduitPair+=1!]
				[IF [!Utils::isPair([!ProduitPair!])!]]
					</div>
					[!OKdiv+=1!]
				[/IF]
		[/STORPROC]
		[IF [!OKdiv!]=0]</div>[/IF]
		
	[ELSE]
		<span class="blocProduitPrix blocambiance_color">Aucun produit ne correspond à votre recherche</span>
	[/IF]
	//
	// FIN BOUCLE PRODUIT
	//PAGINATION
	[IF [!TotalPage!]>1]
		<div class="LignePagination">
			<span class="ResultatPagination"><span>
					// Retour à la première page
					<a href="/[!Lien!]?Pos=1" /><img src="/skins/gamesavenue/Images/dble-fleche-gauche.png"></a>
				</span><span>
					// Page précédente
					<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:-1!]<1]1[ELSE][!Page[!TypeEnf!]:-1!][/IF]" /><img src="/skins/gamesavenue/Images/fleche-gauche.png"></a>
				</span><span>
					// Aller à une page précise

					[STORPROC [!TotalPage!]|Pag]
						// Page 1
						[IF [!Pos!]=1]
							<span class="current[IF [!Page[!TypeEnf!]!]=1] blocambiance_color[/IF]"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// ...
						[IF [!Pos!]=1&&[!Page[!TypeEnf!]!]>3]...[/IF]
						// Page n-1
						[IF [!Pos!]=[!Page[!TypeEnf!]:-1!]&&[!Page[!TypeEnf!]!]>2]
							<span class="current"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// Page courante
						[IF [!Pos!]=[!Page[!TypeEnf!]!]&&[!Page[!TypeEnf!]!]>1]
							<span class="current blocambiance_color">[!Pos!]</span>
						[/IF]
						// Page n+1
						[IF [!Pos!]=[!Page[!TypeEnf!]:+1!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-1!]]
							<span class="current"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// ...
						[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-2!]]...[/IF]
						// Page n-1
						// Dernière page
						[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]!=[!TotalPage!]]
							<span class="current"><a href="/[!Lien!]?PagePos=[!Pos!]"/>[!Pos!]</a></span>
						[/IF]
						// [IF [!Pos!]!=[!Page[!TypeEnf!]!]]
						// 	<a href="/[!Lien!]?PagePos=[!Pos!]" class="LesPages" />[!Pos!]</a>
						// [ELSE]
						//	<span class="current blocambiance_color">[!Page[!TypeEnf!]!]</span>
						// [/IF]
					[/STORPROC]
				</span><span>
					// Page suivante
					<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:+1!]>[!TotalPage!]][!TotalPage!][ELSE][!Page[!TypeEnf!]:+1!][/IF]"/><img src="/skins/gamesavenue/Images/fleche-droite.png"></a>
				</span><span>
					// Dernière Page
					<a href="/[!Lien!]?PagePos=[!TotalPage!]"/ ><img src="/skins/gamesavenue/Images/dble-fleche-droite.png"></a>
				</span>
			</span>
		</div>
	[/IF]
</div>   <!-- fin  centrePartieBas -->

