<!--Boutique/Reference/Default-->
[MODULE Systeme/Structure/Gauche_Boutique]
[MODULE Systeme/Structure/Bienvenue]
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
	[IF [!NbG!]=1]
		//Ajout des enfants dans le cas d une selection de premier niveau
		[STORPROC [!GENRES!]|G]
			[STORPROC Boutique/Genre/[!G!]/Genre|Ge|0|100]
				[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
				[!REQUETE+=GenreId=[!Ge::Id!]!]
			[/STORPROC]
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
[STORPROC [!Query!]|R|0|1][/STORPROC]
[STORPROC Boutique/Produit/Reference/[!R::Id!]|P|0|1][/STORPROC]
[STORPROC Boutique/Client/Reference/[!R::Id!]|C|0|1][/STORPROC]
[STORPROC Boutique/Genre/Produit/[!P::Id!]|G|0|1][/STORPROC]
<!--- contenu central -->
<div class="centre">
	<div class="MonCompte"><h1>Mon achat</h1></div>
	<div class="blocProduitPagesDescription"><img src="/Skins/gamesavenue/Images/achat_etap1.png"></div>
	// BLOCK DESCRIPTION DU PRODUIT
	<div class="ligneSelectGris">
		<div class="ligneSelectGrisCote"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
		<div class="ligneSelectGrisCentre">
			<div class="ligneSelectGrisLeftElements" style="width:100%;">
				<span class="titreligneselect" style="text-transform:uppercase">Détails du produit sélectionné</span>
			</div>
		</div>
		<div class="ligneSelectGrisCote" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
	</div> // fin ligne selection	
	<div class="BlocProduitPages">
		<div class="ligneSelectGrisCentreBlocResults" style="height:127px;padding-top:10px;">
			<div class="blocProduitPagesImage" >
				[IF [!P::Image!]!=]
					<img src="/[!P::Image!]" class="img_detail"/>
				[ELSE]
					<img src="/Skins/gamesavenue/Images/defaut_image.jpg" class="img_detail"/>
				[/IF]
			</div>
			<div class="blocProduitPagesDescription">
				<span class="blocProduitPagesTitre blocambiance_color">[!P::Nom!]</span><br/>
				<div  class="blocProduitPagesDescriptioncol"  >
					<div  class="blocProduitPagesDescriptioncol1"  style="height:20px;" >
					<span>Etat :&nbsp;&nbsp;[IF [!R:Etat!]=1]Neuf[ELSE]Occasion[/IF]</span>
					</div>
(* 					<div  class="blocProduitPagesDescriptioncol2" style="height:20px;" > *)
						Vendeur:&nbsp;&nbsp;[!C::Prenom!]&nbsp;&nbsp;[!C::Nom!]
					</div>
					<span>Description :&nbsp;&nbsp;[!R:Description!]</span>
				</div>
				<div class="blocProduitPagesColPrix" style="border:none;">
					<span class="blocProduitPrix blocambiance_color">[!R::Tarif!] €</span>
				</div>
			</div>

		</div>
	</div>
	<div style="float:right">	
		<div class="btnRouge" style="padding-top:10px;">
			<div class="btnRougeGauche"></div>
				<div class="btnRougeCentre">
					<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]/Reference/[!R::Reference!]/Acheter_Etap2_3" class="btnRougeCentre" /><img src="/Skins/gamesavenue/Images/fleche-suivant.png">&nbsp;&nbsp;Suivant
					</a>
				</div>
				<div class="btnRougeDroite"></div>
			</div>
		</div>
		<div class="btnRouge" style="padding-top:10px;">
			<div class="btnRougeGauche"></div>
			<div class="btnRougeCentre">
				<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="btnRougeCentre" />
				Précédent&nbsp;&nbsp;<img src="/Skins/gamesavenue/Images/fleche-precedent.png">
				</a>
			</div>
			<div class="btnRougeDroite"></div>
		</div>	
	</div>
</div>