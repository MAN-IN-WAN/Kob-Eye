<!--Boutique/Produit/Liste des produits demandés-->
// Recup couleur des titre en fonction de l'univers
[MODULE Systeme/Structure/CouleurUnivers]
[INFO [!Query!]|I]
//CONSTRUCTION REQUETE
[!REQUETE:=Boutique/Produit/Reference!]
[STORPROC [!I::Historique!]|H|0|10]
	[!REQUETE+=/[!H::Value!]!]
[/STORPROC]
// si on est connecté et que l'on vient de cliquer sur ajouter une annonce
[IF [!Systeme::User::Public!]!=1]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1][/STORPROC]
	[IF [!I_Formulaire!]=OK]
		[STORPROC [!Query!]|Ref]
//			[!DEBUG::Ref!]
			[METHOD Ref|Set]
				[PARAM]Tarif[/PARAM]
				[PARAM][!I_Tarif!][/PARAM]
			[/METHOD]
			[METHOD Ref|Set]
				[PARAM]Description[/PARAM]
				[PARAM][!I_Description!][/PARAM]
			[/METHOD]
			[IF [!Form_Image_Upload!]!=]
				[METHOD Ref|Set]
					[PARAM]Image[/PARAM]
					[PARAM][!Form_Image_Upload!][/PARAM]
				[/METHOD]	
			[/IF]
			[METHOD Ref|Save][/METHOD]
		[/STORPROC]
		[!Reset:=0!]
		[REDIRECT]Mon_Compte/Gestion_Annonces[/REDIRECT]
	[/IF]
	[MODULE Systeme/Structure/Gauche]
	<!--- contenu central -->
	<div class="centre">
		// BLOCK DESCRIPTION DU PRODUIT
		[STORPROC [!REQUETE!]|PAV|0|1]

			[NORESULT]
				<div class="ligneSelectGrisCentreBlocResults" style="padding:10px;">
					<span class="blocProduitPrix blocambiance_color" >Erreur Produit non trouvé veuillez  réessayer</span>
				</div> 
			[/NORESULT]
			[!Console:=!]
			[STORPROC Boutique/Categorie/*/Categorie/Produit/[!PAV::Id!]|CP||tmsCreate|ASC]
				[IF [!Console!]=][!Console+= [!CP::Nom!]!][/IF]
			[/STORPROC]
			[STORPROC Boutique/Genre/Produit/[!PAV::Id!]|G|0|1][/STORPROC]
			<div class="ligneSelectGris">
				<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCentre">
					<div class="ligneSelectGrisLeftElements" style="width:100%;">
						<span class="titreligneselect">DETAILS DU PRODUIT</span>
					</div>
				</div>
				
			</div> // fin ligne selection	
			<div class="BlocProduitPages">
			<div class="ligneSelectGrisCentreBlocResults" style="height:127px;padding-top:10px;">
				<div class="blocProduitPagesImage">
					[IF [!PAV::Image!]!=]
						<a href="/[!PAV::Image!].limit.1000x1000.jpg" title="[!PAV::Nom!]" class="mb"  rel="width:400,height:300"><img src="/[!PAV::Image!]" class="img_detail"/></a>
					[ELSE]
						<img src="/Skins/gamesavenue/Images/defaut_image.jpg" class="img_detail"/>
					[/IF]
				</div>
				<div class="blocProduitPagesDescription">
					<span class="blocProduitPagesTitre blocambiance_color">[!PAV::Nom!][IF [!Console!]!=]&nbsp;&nbsp;/&nbsp;&nbsp;[!Console!][/IF]</span><br/>
					<div  class="blocProduitPagesDescriptioncol1"  >
						<span class="textedescriptif">[IF [!PAV::Note!]!=&&[!PAV::Note!]!=0]<br/>Note :&nbsp;&nbsp;<strong>[!PAV::Note!]</strong>[/IF][IF [!PAV:::getNbNeufs( )!]!=]<br/>Nombre d'articles Neuf :&nbsp;&nbsp;<strong>[!PAV::getNbNeufs( )!]]</strong>[/IF][IF [!PAV::getNbOccasions()!]!=] <br/>Nombre d'articles Occasions :&nbsp;&nbsp;<strong>[!PAV::getNbOccasions( )!]</strong>[/IF][IF [!PAV::Age!]!=]<br/>Age : &nbsp;&nbsp;<strong>[!PAV::Age!]]</strong>[/IF][IF [!PAV::Joueur!]!=]<br/>Joueur(s) :&nbsp;&nbsp;<strong>[!PAV::Joueur!]</strong>[/IF]</span>
					</div>
					<div  class="blocProduitPagesDescriptioncol2" >
						<span class="textedescriptif">[IF [!PAV::Annee!]!=&&[!PAV::Annee!]!=0]<br/>Année :&nbsp;&nbsp;<strong>[!PAV::Annee!]</strong>[/IF][IF [!PAV::Editeur!]!=]<br/>Editeur :&nbsp;&nbsp;<strong>[!PAV::Editeur!]</strong>[/IF][IF [!G::Nom!]!=]<br/>Genre :&nbsp;&nbsp;<strong>[!G::Nom!]</strong>[/IF]<span>
					</div>
					<div class="blocProduitPagesColPrix" >
						[IF [!PAV::PrixAPartirDe( )!]!=]
							<span class="blocProduitPrix blocambiance_color">[!PAV::PrixAPartirDe( )!]  €*</span>
							<span class="blocProduitPrixAp"><br/>*à partir de</span>
							<br/><br/>
							<div class="btnGrisClair" style="width:80px;">
								<div class="btnGrisClairGauche">&nbsp;</div>
								<div class="btnGrisClairCentre"><a href="/[!PRODUIT_LINK!]/Produit/[!P::Url!]" class="btnGrisClair blocambiance_color">Acheter</a></div>
								<div class="btnGrisClairDroite">&nbsp;</div>
							</div>
						[ELSE]
							<div class="blocProduitTitre blocambiance_color aumilieu" style="margin-bottom:30px;">Aucune annonce<br> pour ce produit</div>
						[/IF]
						
					</div>
				</div>
			</div>
		[/STORPROC]
	
		// BLOCK SAISIE ANNONCE
		[STORPROC [!Query!]|RM|0|1][/STORPROC]
		<form id="SaisieAnnonce" enctype="application/x-www-form-urlencoded"  method="post">
			<input type="hidden" name="I_ClientConnecte" value="[!Pers::Id!]" />
			<input type="hidden" name="I_ClientConnecte" value="[!Pers::Id!]" />
			[IF [!I_Error!]=1]
				<div style="margin:5px">
					<span class="blocProduitPagesTitre blocambiance_color">Veuillez remplir les champs obligatoires suivants :</span><br>
					[IF [!I_Tarif!]=]Le prix de vente [/IF]
					[IF [!I_Etat!]=0]<br>L'état[/IF]
					[IF [!I_Description!]=]<br>La description[/IF]
				</div>
			[/IF]
			<div class="ligneSelectGris" style="height:30px;border-bottom:1px solid #cccccc;">
				<div class="ligneSelectGrisCote"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCentre">
					<div class="ligneSelectGrisLeftElements" style="width:100%;">
						<span class="titreligneselect">VOTRE ANNONCE</span>
					</div>
				</div>
				<div class="ligneSelectGrisCote" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
			</div> // fin ligne selection	<div class="BlocProduitPages">
			<div class="ligneSelectGrisCentreBlocResults" style="height:100%;padding-top:10px;">
				<div class="blocProduitAnnoncesDescription">
					<div  class="blocProduitAnnoncecol1"  >
						<label>Prix de Vente<span style="color:#ff0000">*</span></label>
						[IF [!I_Tarif!]=][!I_Tarif:=[!RM::Tarif!]!][/IF]
						<input type="text" size="15" name="I_Tarif" value="[IF [!Reset!]=][!I_Tarif!][/IF]" class="[IF [!I_Tarif_Error!]]Error[/IF]"/>
						<br>Cette référence c'est vendue en moyenne&nbsp;&nbsp;[!PAV::getPmpa( )!] €<br> N'oubliez pas de tenir compte des frais de livraison
					</div>
					<div  class="blocProduitAnnoncecol2" >
						<label>Etat<span style="color:#ff0000">*</span></label>
						[IF [!I_Etat!]=][!I_Etat:=[!RM::Etat!]!][/IF]
						<select name="I_Etat" value="[IF [!Reset!]=][!I_Etat!][/IF]" class="[IF [!I_Etat_Error!]]Error[/IF]"/>
							<option [IF [!I_Etat!]=0||[!I_Reset!]=1]selected[/IF]value=0>Etat du produit vendu</option>
							<option [IF [!I_Etat!]=1&&[!I_Reset!]!=1]selected[/IF] value=1>Neuf</option>
							<option [IF [!I_Etat!]=2&&[!I_Reset!]!=1]selected[/IF] value=2>Occasion</option>
						</select>
					</div>
				</div>
				<div class="blocProduitAnnoncesDescription">
					<div  class="blocProduitAnnoncecol1" >
						<label>Photo</label>
						<div class="blocProduitPagesImage">
							[IF [!RM::Image!]!=]
								<img src="/[!RM::Image!]" class="img_liste"/>
							[/IF]
						</div>
						<input type="file" size="40" name="Form_Image_Upload" value=""/>
					</div>
					<div  class="blocProduitAnnoncecol2" >
						<label>Description<span style="color:#ff0000">*</span></label>
						[IF [!I_Description!]=][!I_Description:=[!RM::Description!]!][/IF]
						<textarea name="I_Description" cols="40" rows="4" [IF [!I_Description_Error!]]class="Error"[/IF]>[IF [!Reset!]=][!I_Description!][/IF]</textarea>
					</div>
				</div>
				<div class="blocProduitAnnoncesDescription">
					<div  class="blocProduitAnnoncecol1"  >
					</div>
					<div  class="blocProduitAnnoncecol2" >
						<div class="btnRouge" style="padding-right:10px;">
							<div class="btnRougeGauche"></div>
							<div class="btnRougeCentre">
								<input type="hidden" name="I_Formulaire" id="I_Formulaire" value="OK" />
								<input type="submit" name="send" value="Modifier l'annonce" class="btnRougeCentre" />
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
