<!--Boutique/Produit/Liste des produits demandés-->
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
[/STORPROC]
[!REQUETE+=/Produit!]
//REQ [!REQUETE!]<br> Q [!Query!]<br>P [!PRODUIT_LINK!]<br>

// si on est connecté et que l'on vient de cliquer sur ajouter une annonce
[IF [!Systeme::User::Public!]!=1]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1][/STORPROC]
	[IF [!I_Formulaire!]=OK]
		//On verifie les champs du formulaire
		[IF [!I_Tarif!]=][!I_Tarif_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Description!]=][!I_Description_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Etat!]=0][!I_Etat_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Error!]!=1]
			//Si pas d erreur enregistrement 
			// faire l'enregistrement de l'annonce !!!!!!!!!!!!!!!!
			[OBJ Boutique|Reference|Ref]
			[STORPROC [!Ref::Proprietes!]|Prop]
				[METHOD Ref|Set]
				[PARAM][!Prop::Nom!][/PARAM]
				[PARAM][!I_[!Prop::Nom!]!][/PARAM]
				[/METHOD]
			[/STORPROC]
			[METHOD Ref|Set]
				[PARAM]Image[/PARAM]
				[PARAM][!Form_Image_Upload!][/PARAM]
			[/METHOD]			
			[METHOD Ref|Set]
				[PARAM]Actif[/PARAM]
				[PARAM]1[/PARAM]
			[/METHOD]
			[IF [!Ref::Verify(1)!]]
				[METHOD Ref|AddParent][PARAM]Boutique/Client/[!I_ClientConnecte!][/PARAM][/METHOD]
				[METHOD Ref|AddParent][PARAM]Boutique/Produit/[!I_ProduitEnCours!][/PARAM][/METHOD]
				[METHOD Ref|Save][/METHOD]
			[/IF]
			[!Reset:=0!]
			//Q : [!Query!]  L : [!Lien!] R : [!REQUETE!]
			[REDIRECT]/Mon_Compte/Gestion_Annonces[/REDIRECT]
		[/IF]
	[/IF]
	[MODULE Systeme/Structure/Gauche]
	<!--- contenu central -->
	<div class="centre">
		// BLOCK DESCRIPTION DU PRODUIT
		[STORPROC [!Query!]|PAV|0|1]
			[NORESULT]
				<div class="ligneSelectGrisCentreBlocResults" style="padding:10px;">
					<span class="blocProduitPrix blocambiance_color" >Erreur Produit non trouvé veuillez  réessayer</span>
				</div> 
			[/NORESULT]
			[STORPROC Boutique/Genre/Produit/[!PAV::Id!]|G|0|1][/STORPROC]
			[!CategConsole:=!]
			[STORPROC Boutique/Categorie/*/Categorie/Produit/[!PAV::Id!]|CP||tmsCreate|ASC]
				[IF [!CategConsole!]=][!CategConsole+= [!CP::Url!]!][/IF]
			[/STORPROC]
			<div class="ligneSelectGris" style="height:30px;border-bottom:1px solid #cccccc;">
				<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCentre"  style="overflow:hidden; padding-top:2px;">
					<div class="ligneSelectGrisLeftElements" style="width:100%;">
						<span class="titreligneselect">DETAILS DU PRODUIT</span>
					</div>
				</div>
			</div> // fin ligne selection	
			<div class="BlocProduitPages">
				<div class="ligneSelectGrisCentreBlocResults" style="height:100%;padding-top:10px;padding-bottom:10px;">
					<div class="blocProduitPagesImage">
						[IF [!PAV::Image!]!=]
							<a href="/[!PAV::Image!].limit.1000x1000.jpg" title="[!PAV::Nom!]" class="mb"  rel="width:400,height:300"><img src="/[!PAV::Image!]" class="img_detail"/></a>
						[ELSE]
							<img src="/Skins/gamesavenue/Images/defaut_image.jpg" class="img_detail"/>
						[/IF]
					</div>
				<div class="blocProduitPagesDescription">
						<span class="blocProduitPagesTitre blocambiance_color">[!PAV::Nom!]</span><br/>
						<div  class="blocProduitPagesDescriptioncol1"  >
							<span class="textedescriptif blocambiance_color">
								[STORPROC Boutique/Categorie/[!CategConsole!]|Cat][!Cat::Nom!][/STORPROC]
							</span>
							<span class="textedescriptif">
								[IF [!PAV::Note!]!=]<br/>Note :&nbsp;&nbsp;<strong>[!PAV::Note!]</strong>[/IF]
								[IF [!PAV::PrixAPartirDe( )!]!=]<br/>Nombre d'articles Neuf :&nbsp;&nbsp;<strong>[!PAV::getNbNeufs( )!]</strong><br/>Nombre d'articles Occasions :&nbsp;&nbsp;<strong>[!PAV::getNbOccasions( )!]</strong>[/IF]
								[IF [!PAV::Age!]!=]<br/>Age : &nbsp;&nbsp;<strong>[!PAV::Age!]</strong>[/IF]
								[IF [!PAV::Joueur!]!=]<br/>Joueur(s) :&nbsp;&nbsp;<strong>[!PAV::Joueur!]</strong>[/IF]
							</span>
						</div>
						<div  class="blocProduitPagesDescriptioncol2" >
							<span class="textedescriptif">
								[IF [!PAV::Annee!]!=&&[!PAV::Annee!]!=0]<br/>Année :&nbsp;&nbsp;<strong>[!PAV::Annee!]</strong>[/IF][IF [!PAV::Editeur!]!=]<br/>Editeur :&nbsp;&nbsp;<strong>[!PAV::Editeur!]</strong>[/IF][IF [!G::Nom!]!=]<br/>Genre :&nbsp;&nbsp;<strong>[!G::Nom!]</strong>[/IF]
							</span>
						</div>
						<div class="blocProduitPagesColPrix" >
							[IF [!PAV::PrixAPartirDe( )!]!=]
								<span class="blocProduitPrix blocambiance_color">[!PAV::PrixAPartirDe( )!]  €*</span>
								<span class="blocProduitPrixAp"><br/>*à partir de</span>
								<br/><br/>
								<div class="btnGrisClair" style="width:80px;">
									<div class="btnGrisClairGauche">&nbsp;</div>
									<div class="btnGrisClairCentre"><a href="/[!PRODUIT_LINK!]/Produit/[!PAV::Url!]" class="btnGrisClair blocambiance_color">Acheter</a></div>
									<div class="btnGrisClairDroite">&nbsp;</div>
								</div>
							[ELSE]
								<div class="blocProduitTitre blocambiance_color aumilieu" ><img src="/Skins/gamesavenue/Images/aucune-annonce.png"></div>
							[/IF]
						</div>		
					</div>
				</div>
			</div>
		[/STORPROC]
	
		// BLOCK SAISIE ANNONCE
		<form id="SaisieAnnonce" enctype="application/x-www-form-urlencoded"  method="post">
			<input type="hidden" name="I_ClientConnecte" value="[!Pers::Id!]" />
			<input type="hidden" name="I_ProduitEnCours" value="[!PAV::Id!]" />
			[IF [!I_Error!]=1]
				<div style="margin:5px">
					<span class="blocProduitPagesTitre blocambiance_color">Veuillez remplir les champs obligatoires suivants :</span><br>
					[IF [!I_Tarif!]=]Le prix de vente [/IF]
					[IF [!I_Etat!]=0]<br>L'état[/IF]
					[IF [!I_Description!]=]<br>La description[/IF]
				</div>
			[/IF]
			<div class="ligneSelectGris" style="height:30px;border-bottom:1px solid #cccccc;">
				<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCentre" style="overflow:hidden; padding-top:2px;">
					<div class="ligneSelectGrisLeftElements" style="width:100%;">
						<span class="titreligneselect">VOTRE ANNONCE</span>
					</div>
				</div>
			</div> // fin ligne selection	
			<div class="BlocProduitPages">
			<div class="ligneSelectGrisCentreBlocResults" style="height:100%;padding-top:10px;">
				<div class="blocProduitAnnoncesDescription">
					<div  class="blocProduitAnnoncecol1"  >
						<label>Prix de Vente<span style="color:#ff0000">*</span></label>
						<input type="text" size="15" name="I_Tarif" value="[IF [!Reset!]=][!I_Tarif!][/IF]" class="[IF [!I_Tarif_Error!]]Error[/IF]"/>
						<br>Cette référence c'est vendue en moyenne&nbsp;&nbsp;[!PAV::getPmpa( )!] €<br> N'oubliez pas de tenir compte des frais de livraison
					</div>
					<div  class="blocProduitAnnoncecol2" >
						<label>Etat<span style="color:#ff0000">*</span></label>
						<select name="I_Etat" value="[IF [!Reset!]=][!I_Etat!][/IF]" class="[IF [!I_Etat_Error!]]Error[/IF]"/>
							<option [IF [!I_Etat!]=0||[!I_Reset!]=1]selected[/IF]value=0>Etat du produit vendu</option>
							<option [IF [!I_Etat!]=1&&[!I_Reset!]=0]selected[/IF] value=1>Neuf</option>
							<option [IF [!I_Etat!]=2&&[!I_Reset!]=0]selected[/IF] value=2>Occasion</option>
						</select>
					</div>
				</div>
				<div class="blocProduitAnnoncesDescription">
					<div  class="blocProduitAnnoncecol1" >
						<label>Photo</label>
						<input type="file" size="40" name="Form_Image_Upload" value="[IF [!Reset!]=][!I_Image!][/IF]" class="[IF [!I_Image_Error!]]Error[/IF]"/>
					</div>
					<div  class="blocProduitAnnoncecol2" >
						<label>Description<span style="color:#ff0000">*</span></label>
						<textarea name="I_Description" cols="40" rows="4" [IF [!I_Description_Error!]]class="Error"[/IF]>[IF [!Reset!]=][!I_Descriptione!][/IF]</textarea>
					</div>
				</div>
				<div class="blocProduitAnnoncesDescription">
					<div  class="blocProduitAnnoncecol1"  >
					</div>
					<div  class="blocProduitAnnoncecol2" >
						<div class="btnRouge" style="padding-right:10px;">
							<div class="btnRougeGauche"></div>
							<div class="btnRougeCentre"><img src="/Skins/gamesavenue/Images/plusimg.png">
								<input type="hidden" name="I_Formulaire" id="I_Formulaire" value="OK" />
								<input type="submit" name="send" value="Ajouter l'annonce" class="btnRougeCentre" />
							</div>
							<div class="btnRougeDroite"></div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
[ELSE]
	[MODULE Systeme/Login]

[/IF]
