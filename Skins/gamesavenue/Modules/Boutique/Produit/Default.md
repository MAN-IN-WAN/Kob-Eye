<!-- ici appel d'un produit et de la liste de ses annonces-->
<!--Boutique/Reference/Default-->
[MODULE Systeme/Structure/Gauche_Boutique]
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
[!Roch:=[![!Recherche!]:/ !]!]
[STORPROC [!Roch!]|R2]
	[!R:=[!Utils::Canonic([!R2!])!]!]
	[COUNT Cataloguedrass/BlackList/Titre~[!R!]|Bl]
	[IF [!Bl!]=0]
		[STORPROC Cataloguedrass/Motclef/Canon~[!Utils::Canonic([!R!])!]|M|0|1]
			[IF [!Details!]!=][!Details+= | !][/IF]
			[!Details+= <a href="?Recherche=[!M::Canon!]">[!M::Nom!]</a>!]
		[/STORPROC]
		[IF [!Re!]][!Re+=[!Separateur!]!][/IF]
		[!Re+=[!R!]!]
	[/IF]
[/STORPROC]


[!DEBUG::Re!]

[STORPROC [!Query!]|P|0|1]
	[!CategConsole:=!]
	[!Console:=!]
	[STORPROC Boutique/Categorie/*/Categorie/Produit/[!P::Id!]|CP||tmsCreate|ASC]
		[IF [!CategConsole!]=][!CategConsole+= [!CP::Url!]!][/IF]
		[IF [!Pos!]=1][!Console:=[!CP::Nom!]!][/IF]
	[/STORPROC]
	[STORPROC Boutique/Genre/Produit/[!P::Id!]|G|0|1][/STORPROC]

<!--- contenu central -->
<div class="centre">
	// BLOCK DESCRIPTION DU PRODUIT
	<div class="BlocProduitPages">
		<div class="CoteProduitPagesG"><img src="/Skins/gamesavenue/Images/block_debut_prod.jpg"></div>
		<div class="CoteProduitPagesD"><img src="/Skins/gamesavenue/Images/block_fin_prod.jpg"></div>
		<div class="ProduitPagesCentre">
			<div class="blocProduitPagesImage" >
				[IF [!P::Image!]!=]
					<a href="/[!P::Image!].limit.1000x1000.jpg" title="[!P::Nom!]" class="mb"  rel="width:400,height:300"><img src="/[!P::Image!]" class="img_detail"/></a>
				[ELSE]
					<img src="/Skins/gamesavenue/Images/defaut_image.jpg" class="img_detail"/>
				[/IF]
			</div>
			<div class="blocProduitPagesDescription">
				<div class="blocProduitPagesTitre blocambiance_color"><span class="blocProduitPagesTitre blocambiance_color">[!P::Nom!] [IF [!Console!]!=]&nbsp;&nbsp;/&nbsp;&nbsp;[!Console!][/IF]</span></div>
				<div  class="blocProduitPagesDescriptioncol1">
					
					<span class="textedescriptif">[IF [!P::Note!]!=&&[!P::Note!]!=0]<br/>Note :&nbsp;&nbsp;<strong>[!P::Note!]</strong>[/IF][IF [!P::PrixAPartirDe( )!]!=]<br/>Nombre d'articles Neuf :&nbsp;&nbsp;<strong>[!P::getNbNeufs( )!]</strong><br/>Nombre d'articles Occasions :&nbsp;&nbsp;<strong>[!P::getNbOccasions( )!]</strong>[/IF][IF [!P::Age!]!=]<br/>Age :&nbsp;&nbsp;<strong>[!P::Age!]</strong>[/IF][IF [!P::Joueur!]!=]<br/>Joueur(s) :&nbsp;&nbsp;<strong>[!P::Joueur!]</strong>[/IF]</span>
				</div>
				<div  class="blocProduitPagesDescriptioncol2" >
					<br/>
					<span class="textedescriptif">
						[IF [!P::Annee!]!=0&&[!P::Annee!]!=]Année :&nbsp;&nbsp;<strong>[!P::Annee!]</strong>[/IF]
						[IF [!P::Editeur!]!=]<br/>Editeur :&nbsp;&nbsp;<strong>[!P::Editeur!]</strong>[/IF]
						[IF [!G::Nom!]!=]<br/>Genre :&nbsp;&nbsp;<strong>[!G::Nom!]</strong>[/IF]
					<span>
					[IF [!Systeme::User::Public!]!=1]
						<div class="btnNoir">
							<div class="btnNoirGauche"></div>
							<div class="btnNoirCentre">
								<a href="/[!Lien!]?Vue=Avis" class="btnNoir"  >
									Noter ce produit
								</a>
							</div>
							<div class="btnNoirDroite" ></div>
						</div>
					[/IF]
				</div>
				[IF [!P::PrixAPartirDe( )!]!=]
					<div class="blocProduitPagesColPrix" >
						<span class="blocProduitPrix blocambiance_color">[!P::PrixAPartirDe( )!] €</span>
						<span class="blocProduitPrixAp"><br/>*à partir de</span><br/><br/>
						<div class="btnGrisFonce"  style="width:80px">
							<div class="btnGrisFonceGauche"></div>
							<div class="btnGrisFonceDroite" ></div>
							<div class="btnGrisFonceCentre">
								<a href="/[!Lien!]/Vendre" class="btnGrisFonce" >Vendre</a>
							</div>
						</div>
				[ELSE]
					<div class="blocProduitPagesColPrix" style="margin-top:0px">
						<div class="blocambiance_color aumilieu"><img src="/Skins/gamesavenue/Images/produit-non-disponible.jpg"></div>
						<div class="btnGrisFonce" >
							<div class="btnGrisFonceGauche"></div>
							<div class="btnGrisFonceDroite" ></div>
							<div class="btnGrisFonceCentre">
								<a href="/[!Lien!]/Vendre" class="btnGrisFonce" >
									Vendre
								</a>
							</div>
						</div>
					</div>
				[/IF]
				</div>
			</div>
		</div>
	</div>
	[IF [!Systeme::User::Public!]!=1]
		[IF [!Vue!]=Avis]
			[!ReloadUrl:[!Query!]?Vue=Avis!]
			[!I_Error:=!]
			//  LA GESTION D'ERREUR
			[IF [!N_Valid!]!=]
				//On verifie les champs du formulaire
				[IF [!I_Commentaire!]=][!I_Commentaire_Error:=1!][!I_Error:=1!][/IF]
				[IF [!I_Error!]=1]
					<div class="BlocError">Le commentaire est obligatoire</div>
					[!Reset:=!]
				[ELSE]
					// Faire la mise à jour
					[OBJ Boutique|NoteProduit|No] 
					[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CL|0|1][/STORPROC]

					[METHOD No|Set]
						[PARAM]Note[/PARAM]
						[PARAM][!I_Note!][/PARAM]
					[/METHOD]
					[METHOD No|Set]
						[PARAM]Commentaires[/PARAM]
						[PARAM][!I_Commentaire!][/PARAM]
					[/METHOD]
					[METHOD No|AddParent][PARAM]Boutique/Client/[!CL::Id!][/PARAM][/METHOD]
					[METHOD No|AddParent][PARAM]Boutique/Produit/[!P::Id!][/PARAM][/METHOD]
					[METHOD No|Save][/METHOD]
					[!Reset:=1!]
					[!I_Commentaire:=!]
					[!I_Note:=0!]
				[/IF]
			[/IF]
			[IF [!N_Valid!]!=&&[!I_Error!]!=1]
				<div class="blocProduitPagesDescription">Votre avis a bien été enregistré</div>
			[ELSE]
				<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
				<div class="ligneSelectGrisCentre" >
					<div class="ligneSelectGrisLeftElements">
						<span class="titreligneselect">NOTER CE PRODUIT</span>
					</div>	
				</div>
				<div class="ligneSelectGrisCentreBlocResults" style="height:120px;padding-top:10px;">
					<form name="notation" action="[!ReloadUrl!]" method="POST">
						<div class="blocProduitPagesDescription">
							<div class="blocColonneAvis">Note</div>
							<div class="blocColonneAvis">
								<select name="I_Note">
									<option value=0 [IF [!I_Note!]=0] selected [/IF]>0/10</option>				
									<option value=1 [IF [!I_Note!]=1] selected [/IF]>1/10</option>				
									<option value=2 [IF [!I_Note!]=2] selected [/IF]>2/10</option>				
									<option value=3 [IF [!I_Note!]=3] selected [/IF]>3/10</option>				
									<option value=4 [IF [!I_Note!]=4] selected [/IF]>4/10</option>				
									<option value=5 [IF [!I_Note!]=5] selected [/IF]>5/10</option>				
									<option value=6 [IF [!I_Note!]=6] selected [/IF]>6/10</option>				
									<option value=7 [IF [!I_Note!]=7] selected [/IF]>7/10</option>				
									<option value=8 [IF [!I_Note!]=8] selected [/IF]>8/10</option>				
									<option value=9 [IF [!I_Note!]=9] selected [/IF]>9/10</option>				
									<option value=10 [IF [!I_Note!]=10] selected [/IF]>10/10</option>				
								</select>
							</div>
							<div class="blocColonneAvis">Commentaires</div>
							<div class="blocColonneAvis">
								<textarea  name="I_Commentaire" cols="60" rows="4" 
								[IF [!I_Commentaire_Error!]] class="Error"[/IF]></textarea>
							</div>
						</div>
						<div class="blocProduitPagesDescription" style="text-align:right;padding-right:40px">
							<div class="btnRouge" >
								<div class="btnRougeGauche"></div>
								<div class="btnRougeDroite"></div>
								<div class="btnRougeCentre">
									<input type="submit" name="N_Valid" value="Valider" class="btnRougeCentre" />
								</div>
							</div>
						</div>
					</form>
				</div>
			[/IF]
		[/IF]
	[/IF]
	// BLOCK CENTRAL
		<div class="ContenuPages">
			<div class="ligneCotePageG"><img src="/Skins/gamesavenue/Images/block_debut_ref.jpg"></div>
			<div class="ligneCotePageD"><img src="/Skins/gamesavenue/Images/block_fin_ref.jpg"></div>
			<div class="lignePageCentre"> 
			// BLOCK DE GAUCHE
			<div class="ReferenceBlocGauche">
				<div class="BoutonsReference" >
					<div class="Bouton3colonnes">
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent aumilieu">
							<a class="[IF [!Vue!]!=]btndessines[ELSE]btndessinescurrent blocambiance_color[/IF]"  href="/[!Lien!]">ANNONCES</a></div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
					<div class="Bouton3colonnes">
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent aumilieu"><a class="[IF [!Vue!]!=Avis]btndessines[ELSE]btndessinescurrent blocambiance_color[/IF]" href="/[!Lien!]?Vue=Avis">AVIS</a></div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
					<div class="Bouton3colonnes">
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent aumilieu"><a class="[IF [!Vue!]!=Galerie]btndessines[ELSE]btndessinescurrent blocambiance_color[/IF]" href="/[!Lien!]?Vue=Galerie">FICHE</a></div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
				</div>
				// BLOCK DES ANNONCES
				<div class="BlocAnnonces" >
					[SWITCH [!Vue!]|=]
						[CASE Avis][MODULE [!Query!]/Avis][/CASE]
						[CASE Galerie][MODULE [!Query!]/Galerie][/CASE]
						[DEFAULT][MODULE [!Query!]/Annonces][/DEFAULT]
					[/SWITCH]

				</div> // fin BlocAnnonces
			</div> // fin BLOCK DE GAUCHE
			// BLOCK DE DROITE
			<div class="ReferenceBlocDroite">
				// BLOCK BOUTON NOTATION
				<div class="BoutonsReference">
					<div class="Bouton1colonne" >
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent">
							<span style="padding-left:5px;padding-right:10px;">NOTATION </span>
							[STORPROC [!Math::Floor([!P::Note!])!]]
								<span class="blocambiance_etoile" style="padding-left:10px;">&nbsp;</span>
							[/STORPROC]
						</div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
				</div> // fin BLOCK NOTATION
				// BLOCK DERNIERS AVIS
				<div class="BlocDernierAvisReference">
					<div class="BlocDernierAvisContenuReference" >
						<b class="coinfondFinGrisbordertop">
							<b class="coinfondFinGris1">&nbsp;</b>
							<b class="coinfondFinGris2">&nbsp;</b>
							<b class="coinfondFinGris3">&nbsp;</b>
							<b class="coinfondFinGris4">&nbsp;</b>
						</b>
						<div class="coinfondFinGriscontent">
							<div class="titreBlocDernierAvisReference">LES DERNIERS AVIS</div>

							[STORPROC Boutique/Produit/[!P::Id!]/NoteProduit|NP|0|4]
								[STORPROC Boutique/Client/NoteProduit/[!NP::Id!]|CLNP|0|1][/STORPROC]
								<div class="identifiantBlocDernierAvisReference blocambiance_color blocambiance_border_bottom" >[!CLNP::Pseudonyme!]</div>
								<div class="descriptionBlocDernierAvisReference">
									[!NP::Commentaires!]
								</div>
							[/STORPROC]
						</div>
						<b class="coinfondFinGrisborderbottom">
							<b class="coinfondFinGris4">&nbsp;</b>
							<b class="coinfondFinGris3">&nbsp;</b><b class="coinfondFinGris2">&nbsp;</b><b class="coinfondFinGris1">&nbsp;</b>
						</b>
					</div>
				</div>  // fin BLOC DERNIERS AVIS
				// BLOCK GALERIE PHOTOS
				<div class="BlocGaleriesReference" >
					<b class="coinNoirbordertop">
						<b class="coinNoir1">&nbsp;</b>
						<b class="coinNoir2">&nbsp;</b>
						<b class="coinNoir3">&nbsp;</b>
						<b class="coinNoir4">&nbsp;</b>
					</b>
					<div class="coinNoircontent">
						<div class="BlocGaleriesTitre" >Galerie</div>
	
						<div class="GaleriesReference" >
							[STORPROC Boutique/Produit/[!P::Id!]/Photo|PH|0|3|tmsCreate|DESC]
								<a href="/[!PH::Image!].limit.1000x1000.jpg" title="[!PH::Nom!]" class="mb" id="mb_Galerie[!Pos!]">
									<img src="/[!PH::Image!].mini.65x65.jpg">
								</a>
								<div class="multiBoxDesc mb_Galerie[!Pos!]" style="display: none">[!PH::Nom!]</div>
							[/STORPROC]
						</div>
					</div>
	
						<b class="coinNoirborderbottom">
							<b class="coinNoir4">&nbsp;</b>
							<b class="coinNoir3">&nbsp;</b><b class="coinNoir2">&nbsp;</b><b class="coinNoir1">&nbsp;</b>
						</b>
				</div> // fin BLOCK GALERIE PHOTOS
				//BLOCK CARRE PUB
				<div class="bloccarrepubReference"><p class="p10">
					//[MODULE Publicite/PubContenu]
				</p></div> // fin CARRE PUB
			</div> // fin BLOCK DE DROITE
		</div>  // fin ligne centrale
	</div> // fin BLOCK CENTRE
</div>    // fin CONTENU COMPLET
[/STORPROC]
