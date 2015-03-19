// a partir de fiche vendeur on accède aux produits vendu par un client
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
[COUNT Boutique/Client/[!V::Id!]/Reference|NbRep]
[IF [!NbRep!]>0]
	// BLOCK LISTE DES JEUX
	<div class="VendeurBlocNoirCote">
		<img src="/Skins/gamesavenue/Images/bando-vendeur-gauche.png">
	</div>
	<div class="VendeurBlocNoirCentre">
		<div class="TitreVendeur">[!V::Pseudonyme!] vend :</div>
//		<div class="TitreVendeur">[!C::Nom!] >[!G::Nom!]</div>
//		<div class="NbVendu">x Jeux</div>
	</div>
	<div class="VendeurBlocNoirCote">
		<img src="/Skins/gamesavenue/Images/bando-vendeur-droite.png">
	</div>
	//On compte le nombre total d element a affciher
	[!TotalPage:=[!NbRep:/[!MaxLine!]!]!]
	//On calcule le nombre total de page
	[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
		//On arrondit au chiffre superieur le nombre total de page
		[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
	[/IF]
	[STORPROC Boutique/Client/[!V::Id!]/Reference|R|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
		[STORPROC Boutique/Produit/Reference/[!R::Id!]|P|0|1][/STORPROC]
		[STORPROC Boutique/Categorie/Produit/[!P::Id!]|C|0|1][/STORPROC]
		[STORPROC Boutique/Genre/Produit/[!P::Id!]|G|0|1][/STORPROC]
		[!CategConsole:=!]
		[!GenreEncours:=!]
		[!CategEncours:=!]
		[STORPROC Boutique/Categorie/*/Categorie/Produit/[!P::Id!]|CP||tmsCreate|ASC]
			[IF [!CategConsole!]=][!CategConsole+= [!CP::Nom!]!][/IF]
			[!CategEncours+=/[!CP::Url!]!]
			[!GenreEncours:=[!CP::Nom!]!]
		[/STORPROC]
		[!REFLINK:=/GamesAvenue[!CategEncours!]/Produit/[!P::Url!]/Reference/[!R::Reference!]!]

		[IF [!Utils::isPair([!AnnoncePair!])!]]  
			<div class="coinNoirFondBlanccontent">
		[ELSE]
			<div class="coinNoirFondBlanccontent" style="background:#ebebeb;">
		[/IF]
			[!AnnoncePair+=1!]
			<div class="blocVendeurAnnonce">	
				<div class="blocProduitImageMini" >
					[IF [!R::Image!]!=]
						<img src="/[!R::Image!].mini.64x72.jpg" style="width:64px;height:72px;"/>
					[ELSE]
						<img src="/Skins/gamesavenue/Images/defaut_image.jpg" style="width:64px;height:64px;"/>
					[/IF]
				</div>
				<div class="blocReferenceDescriptif" style="width:230px;">
					<span class="blocVendeurAnnonceDescriptif blocambiance_color">[!CategConsole!]/[!GenreEncours!]<br></span>
					<span class="blocProduitPagesTitre blocambiance_color">[!P::Nom!]<br></span>
					<span class="blocVendeurAnnonceDescriptif blocambiance_color">[!R::Reference!]</span>
					<span class="blocPagestexte">
						[IF [!R::Etat!]!=]<br/>Etat :&nbsp;&nbsp;<strong>[IF [!R::Etat!]=1]Neuf[ELSE]Occasion[/IF]</strong>[/IF]
						[IF [!R::Description!]!=]<br/>DESCRIPTION 
						<br/><strong>[!R::Description!]</strong>[/IF]
					</span>
				</div>
				<div class="blocReferenceAchatVente" style="width:80px;" >
					<span class="blocProduitPrix blocambiance_color"  >
						[!R::Tarif!] €
					</span>
				</div> // fin contenu
			</div>
			<div class="blocVendeurAnnonce">	
				<div class="blocProduitImageMini" >&nbsp;</div>
				<div class="blocVendeurAnnonceDescriptif" style="border:none;margin-top:10px;">
				<div class="btnGrisFonce">
					<div class="btnGrisFonceGauche"></div>
						<div class="btnGrisFonceCentre"">
							<a href="[!REFLINK!]" class="btnGrisFonce" >
								Voir le détail de l'annonce
							</a>
							</div>
							<div class="btnGrisFonceDroite" ></div>
						</div>
					</div>
					<div class="blocReferenceAchatVente"  >
						<div class="btnGrisClair" style="width:80px;">
							<div class="btnGrisClairGauche"></div>
							<div class="btnGrisClairCentre">
								<a href="/GamesAvenue[!CategEncours!]/Produit/[!P::Url!]/Reference/[!R::Url!]/Acheter_Etap1" class="btnGrisClair" >
								Acheter
							</a>
						</div>
						<div class="btnGrisClairDroite" ></div>
					</div>
				</div>
			</div> // FIN coinNoirFondBlanccontent
		</div> // fin a propos vendeur
	[/STORPROC]
	<b class="coinNoirFondBlancborderbottom">
		<b class="coinNoirFondBlanc4">&nbsp;</b>
		<b class="coinNoirFondBlanc3">&nbsp;</b>
		<b class="coinNoirFondBlanc2">&nbsp;</b>
		<b class="coinNoirFondBlanc1">&nbsp;</b>
	</b>	
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
		</div>
	[/IF]

[ELSE]
	<span class="blocProduitPrix blocambiance_color">Pas de produit pour ce vendeur</span>

[/IF]