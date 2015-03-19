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
[IF [!Systeme::User::Public!]!=1]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1][/STORPROC]
	<!--- contenu central -->
	<div class="centre">
		<div class="MonCompte"><h1>Paiement</h1></div>
		<div class="blocProduitPagesDescription"><img src="/Skins/gamesavenue/Images/achat_etap3.png"></div>
		// BLOCK ADRESSES
		<div class="ligneSelectGris">
			<div class="ligneSelectGrisCote"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCentre" >
				<div class="ligneSelectGrisCentreElements" style="padding-left:5px;width:49%;border-right:1px dotted #c4c4c4;text-align:left">
					<span class="titreligneselect" style="text-transform:uppercase">Adresse de livraison</span>
				</div>
				<div class="ligneSelectGrisCentreElements" style="padding-left:5px;width:49%;text-align:left">
					<span class="titreligneselect" style="text-transform:uppercase">Adresse de facturation</span>
				</div>
			</div>
			<div class="ligneSelectGrisCote" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
		</div> // fin ligne selection	
		<div class="BlocProduitPages">
			<div class="ligneSelectGrisCentreBlocResults" style="padding:10px 0 0;">
				<div  class="blocProduitPagesDescriptioncol1" style="padding-left:5px;width:49%;border-right:1px dotted #c4c4c4;">
					<div  class="blocProduitPagesDescriptioncol1" style="padding-left:5px;width:48%;">
						[!Pers::Prenom!]&nbsp;&nbsp;[!Pers::Nom!]
						<br>[!Pers:Adresse!]
						<br>[!Pers:CodePostal!]&nbsp;&nbsp[!Pers:Ville!]
					</div>
					<div  class="blocProduitPagesDescriptioncol2" style="padding-left:5px;width:48%;float:left;padding-right:2px;">
						<div class="btnGrisGrand" style="padding-left:25px">
							<div class="btnGrisGrandGauche"></div>
							<div class="btnGrisGrandCentre">
								<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="btnGrisGrandCentre" />
								Modifier votre adresse<br/>de LIVRAISON	</a>
							</div>
							<div class="btnGrisGrandDroite"></div>
						</div>	
					</div>
				</div>
				<div  class="blocProduitPagesDescriptioncol2" style="padding-left:5px;width:49%;text-align:left">
					<div  class="blocProduitPagesDescriptioncol1" style="padding-left:5px;width:48%;">
						[!Pers::Prenom!]&nbsp;&nbsp;[!Pers::Nom!]
						<br>[!Pers:Adresse!]
						<br>[!Pers:CodePostal!]&nbsp;&nbsp[!Pers:Ville!]
					</div>
					<div  class="blocProduitPagesDescriptioncol2" style="padding-left:5px;width:48%;float:left;padding-right:2px;">
						<div class="btnGrisGrand" style="padding-left:25px">
							<div class="btnGrisGrandGauche"></div>
							<div class="btnGrisGrandCentre">
								<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="btnGrisGrandCentre" />
								Modifier votre adresse<br/>de FACTURATION</a>
							</div>
							<div class="btnGrisGrandDroite"></div>
						</div>	
					</div>
					
				</div>
				
			</div>
		</div>
		// BLOCK DESCRIPTION DU PRODUIT
		<div class="ligneSelectGris" style="padding-top:10px;">
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
						<div  class="blocProduitPagesDescriptioncol2" style="height:20px;" >
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
		<div style="overflow:hidden">
			<div style="float:right;width:350px;">
				// BLOCK PAIEMENT
				<div class="ligneSelectGris" style="padding-top:10px;">
					<div class="ligneSelectGrisCote"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
					<div class="ligneSelectGrisCentre"  style="width:342px;">
						<div class="ligneSelectGrisLeftElements" style="width:100%;">
							<span class="titreligneselect" style="text-transform:uppercase;padding-left:5px;">JE CHOISIS MON MODE DE PAIEMENT</span>
						</div>
					</div>
					<div class="ligneSelectGrisCote" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
				</div> // fin ligne selection	
				<div class="BlocProduitPages">
					<div class="ligneSelectGrisCentreBlocResults" style=";padding-top:10px;">
						<div  class="blocProduitPagesCol1" style="padding-left:5px;border-right:1px dotted #c4c4c4;width:80%;">
							<div  class="blocProduitPagesCol1" style="padding-left:5px;width:70%;">
								CARTE BANCAIRE
							</div>
							<div  class="blocProduitPagesCol2" style="padding-left:5px;float:left;padding-right:2px;">
								<img src="/Skins/gamesavenue/Images/carte-bleue_97.png">
							</div>
						</div>
						<div  class="blocProduitPagesCol2" style="padding-left:25px;text-align:center;">
							<input type="radio" name="MP" value="CB">	
						</div>
						<div  class="blocProduitPagesCol1" style="padding-left:5px;border-right:1px dotted #c4c4c4;width:80%;">
							<div  class="blocProduitPagesCol1" style="padding-left:5px;width:70%;">
								CHEQUE
							</div>
							<div  class="blocProduitPagesCol2" style="padding-left:5px;float:left;padding-right:2px;">
								<img src="/Skins/gamesavenue/Images/cheque.jpg">
							</div>
						</div>
						<div  class="blocProduitPagesCol2" style="padding-left:25px;text-align:center;">
							<input type="radio" name="MP" value="CHQ">	
						</div>		
						<div  class="blocProduitPagesCol1" style="padding-left:5px;border-right:1px dotted #c4c4c4;width:80%;">
							<div  class="blocProduitPagesCol1" style="padding-left:5px;width:70%;">
								VIREMENT BANCAIRE
							</div>
							<div  class="blocProduitPagesCol2" style="padding-left:5px;float:left;padding-right:2px;">
							</div>
						</div>
						<div  class="blocProduitPagesCol2" style="padding-left:25px;text-align:center;padding-bottom:10px;">
							<input type="radio" name="MP" value="VB">	
						</div>	
					</div>	
				</div>	
			</div>
		</div>
		<div style="overflow:hidden">
			<div style="float:right;" class="topp10">	
				<span class="blocPagestexte10"><input type="checkbox" name="CGV">&nbsp;&nbsp;J'accepte les <u>conditions générales</u> de vente</u>
			</div>
		</div>

		<div style="overflow:hidden">
			<div style="float:right;">	
				<div class="btnRouge" style="padding-top:10px;">
					<div class="btnRougeGauche"></div>
					<div class="btnRougeCentre">
						<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]/Reference/[!R::Reference!]/Acheter_Etap4" class="btnRougeCentre" /><img src="/Skins/gamesavenue/Images/fleche-suivant.png">&nbsp;&nbsp;Suivant
						</a>
					</div>
					<div class="btnRougeDroite"></div>
				</div>
				<div class="btnRouge" style="padding-top:10px;">
					<div class="btnRougeGauche"></div>
					<div class="btnRougeCentre">
						<a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]/Reference/[!R::Reference!]/Acheter_Etap1" class="btnRougeCentre" />
						Précédent&nbsp;&nbsp;<img src="/Skins/gamesavenue/Images/fleche-precedent.png">
						</a>
					</div>
					<div class="btnRougeDroite"></div>
				</div>	
			</div>
		</div>
	</div>
[ELSE]
	<div class="MonCompte"><h1>Paiement</h1></div>
	<div class="blocProduitPagesDescription"><img src="/Skins/gamesavenue/Images/achat_etap2.png"></div>
	[MODULE Systeme/Login?Menu=non]

[/IF]
