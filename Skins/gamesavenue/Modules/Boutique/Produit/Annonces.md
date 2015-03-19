// liste des annonces d un produit
// Recup couleur des titre en fonction de l'univers
[MODULE Systeme/Structure/CouleurUnivers]
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
// -- Gestion de la pagination
[IF [!Page!]=EMPTY]
	[!Page:=1!]
[/IF]
[!TypeEnf:=Annonces!]
//Definition des elements a afficher
[IF [!PagePos!]!=]
	[!Page[!TypeEnf!]:=[!PagePos!]!]
[ELSE]
	[!Page[!TypeEnf!]:=1!]
[/IF]
[!MaxLine:=5!] // nb d'annonces à afficher// -- Gestion de la pagination
[IF [!Page!]=EMPTY]
	[!Page:=1!]
[/IF]
[!TypeEnf:=Annonces!]
//Definition des elements a afficher
[IF [!PagePos!]!=]
	[!Page[!TypeEnf!]:=[!PagePos!]!]
[ELSE]
	[!Page[!TypeEnf!]:=1!]
[/IF]
[!MaxLine:=5!] // nb d'annonces à afficher
[!AnnoncePair:=0!]
<div class="BlocAnnonces" >
	<b class="coinFinGrisbordertop">
		<b class="coinFinGris1">&nbsp;</b>
		<b class="coinFinGris2">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b>
		<b class="coinFinGris4">&nbsp;</b>
	</b>
	[COUNT [!Query!]/Reference/Actif=1|NbRep]
	[IF [!NbRep!]>0]
		//On compte le nombre total d element a affciher
		[!TotalPage:=[!NbRep:/[!MaxLine!]!]!]
		//On calcule le nombre total de page
		[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
			//On arrondit au chiffre superieur le nombre total de page
			[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
		[/IF]
		[STORPROC [!Query!]/Reference/Actif=1|R|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
			[STORPROC Boutique/Client/Reference/[!R::Id!]|CLR|0|1][/STORPROC]
			<div  class="Bloc1Annonce" > 
				[IF [!Utils::isPair([!AnnoncePair!])!]]  
					<div class="coinFinGriscontent">
				[ELSE]
					<div class="coinFinGriscontent" style="background:#ebebeb;">
				[/IF]
					[!AnnoncePair+=1!]
					<div class="blocReferenceAnnonce">	
						<div class="blocProduitImageMini" >
							[IF [!R::Image!]!=]
								<div class="blocProduitImageMini" ><img src="/[!R::Image!].mini.64x72.jpg" style="width:64px;height:72px;"/></div>
							[ELSE]
								<div class="blocProduitImageMini" ><img src="/Skins/gamesavenue/Images/defaut_image.jpg" style="width:64px;height:64px;"/></div>
							[/IF]
						</div>
						<div class="blocReferenceDescriptif">
							<span class="blocPagestexte">
								Vendeur:&nbsp;&nbsp;<strong>[!CLR::Pseudonyme!]</strong>
								[IF [!R::Etat!]!=]<br/>Etat :&nbsp;&nbsp;<strong>[IF [!R::Etat!]=1]Neuf[ELSE]Occasion[/IF]</strong>[/IF]
								[IF [!R::Description!]!=]<br/>DESCRIPTION<br/><strong>[!R::Description!]</strong>[/IF]
								
							</span>
						</div>
						<div class="blocReferenceAchatVente" >
							<span class="blocProduitPrix blocambiance_color" >
								[!R::Tarif!] €
							</span>
						</div> // fin contenu
					</div>
					<div class="blocReferenceAnnonce">	
						<div class="blocProduitImageMini" >&nbsp;</div>
						<div class="blocReferenceDescriptif" style="border:none;margin-top:10px;">
							<div class="btnGrisFonce">
								<div class="btnGrisFonceGauche"></div>
								<div class="btnGrisFonceDroite" ></div>
								<div class="btnGrisFonceCentre">
									<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]/Reference/[!R::Reference!]" class="btnGrisFonce" >
										Voir le détail de l'annonce
									</a>
								</div>
							</div>
						</div>
						<div class="blocReferenceAchatVente">
							<div class="btnGrisClair" style="width:80px">
								<div class="btnGrisClairGauche"></div>
								<div class="btnGrisClairDroite" ></div>
								<div class="btnGrisClairCentre" >
									<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]/Reference/[!R::Reference!]/Acheter_Etap1" class="btnGrisClair blocambiance_color">
										Acheter
									</a>
								</div>
							</div>
						</div>
					</div>
				</div> // FIN coinFinGriscontent
			</div> //Bloc 1 Annonce
		[/STORPROC]
		//PAGINATION
		[IF [!TotalPage!]>1]
			<div  class="Bloc1Annonce" ><div class="coinFinGriscontent">
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
								<span class="current[IF [!Page[!TypeEnf!]!]=1] blocambiance_color[/IF]">[!Pos!]</span>
							[/IF]
							// ...
							[IF [!Pos!]=1&&[!Page[!TypeEnf!]!]>3]...[/IF]
							// Page n-1
							[IF [!Pos!]=[!Page[!TypeEnf!]:-1!]&&[!Page[!TypeEnf!]!]>2]
								<span class="current">[!Pos!]</span>
							[/IF]
							// Page courante
							[IF [!Pos!]=[!Page[!TypeEnf!]!]&&[!Page[!TypeEnf!]!]>1]
								<span class="current blocambiance_color">[!Pos!]</span>
							[/IF]
							// Page n+1
							[IF [!Pos!]=[!Page[!TypeEnf!]:+1!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-1!]]
								<span class="current">[!Pos!]</span>
							[/IF]
							// ...
							[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-2!]]...[/IF]
							// Page n-1
							// Dernière page
							[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]!=[!TotalPage!]]
								<span class="current">[!Pos!]</span>
							[/IF]
						[/STORPROC]
					</span><span>
						// Page suivante
						<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:+1!]>[!TotalPage!]][!TotalPage!][ELSE][!Page[!TypeEnf!]:+1!][/IF]"/><img src="/skins/gamesavenue/Images/fleche-droite.png"></a>
					</span><span>
						// Dernière Page
						<a href="/[!Lien!]?PagePos=[!TotalPage!]"/ ><img src="/skins/gamesavenue/Images/dble-fleche-droite.png"></a>
					</span>
				</span></div>
			</div></div>
		[/IF]
	[ELSE]
		<div  class="Bloc1Annonce" > 
			<div class="coinFinGriscontent">
				<span class="blocProduitPrix blocambiance_color" >Pas d'annonce pour ce produit</span>
			</div> // FIN coinFinGriscontent
		</div> //Bloc 1 Annonce
	[/IF]
	<b class="coinFinGrisborderbottom">
		<b class="coinFinGris4">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b>
		<b class="coinFinGris2">&nbsp;</b>
		<b class="coinFinGris1">&nbsp;</b>
	</b>
</div> // fin BlocAnnonces

