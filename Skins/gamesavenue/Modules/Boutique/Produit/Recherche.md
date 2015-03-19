[MODULE Systeme/Structure/Gauche]
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
[!TypeEnf:=Produit!]
//Definition des elements a afficher
[IF [!PagePos!]!=]
	[!Page[!TypeEnf!]:=[!PagePos!]!]
[ELSE]
	[!Page[!TypeEnf!]:=1!]
[/IF]
[!MaxLine:=20!] // nb d'annonces à afficher
<!--- contenu central -->
<div class="centre">
	<form action="">
		// Bloc de selection 
		<div class="ligneSelectGris" style="height:30px;">
			<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCentre">
				<div class="ligneSelectGrisLeftElements">
					<input type="texte" name="R_Recherche" size="20" value="Tapez ici votre recherche" onfocus="this.value='';">
					<div class="btnGrisClair" style="padding-left:10px;margin:0px;overflow:hidden;float:right;">
						<div class="btnGrisClairGauche"></div>
						<div class="btnGrisClairCentre" style="width:20px;">
							<input type="submit" id="search1" name="I_Search" value="Ok" class="btnGrisClairCentre"  style="color:#000000;"/>
						</div>
						<div class="btnGrisClairDroite"></div>
					</div>
				</div>
				<div class="ligneSelectGrisLeftElements" >
					&nbsp;&nbsp;&nbsp;Ou&nbsp;&nbsp;&nbsp;<select name="Console" class="selectfin"><option value="0">Choisissez une console</option>
					[STORPROC Boutique/Categorie/Nom=Jeux Video|CATEG]
						[STORPROC Boutique/Categorie/Categorie/[!CATEG::Id!]|CATEG2]
						<option  [IF [!Console!]=[!CATEG2::Id!]]selected[/IF] value=[!CATEG2::Id!] >[!CATEG2::Nom!]</option>
						[/STORPROC]
					[/STORPROC]
					</select>				
				</div>
				<div class="ligneSelectGrisLeftElements">
					<select name="Genre" class="selectfin">
						<option value="">Choisissez un genre</option>
						[STORPROC Boutique/Genre|G|0|100|Nom|ASC]
							<option  [IF [!Genre!]=[!G::Id!]]selected[/IF] value=[!G::Id!] >[!G::Nom!]</option>

							[STORPROC Boutique/Genre/[!G::Id!]/Genre|G2|0|100|Nom|ASC]
								<option  [IF [!Genre!]=[!G2::Id!]]selected[/IF] value=[!G2::Id!] >--&nbsp;&nbsp;[!G2::Nom!]</option>
							[/STORPROC]
						[/STORPROC]
					</select>
				</div>
				<div class="ligneSelectGrisLeftElements">
					<div class="btnGrisFonce">
						<div class="btnGrisFonceGauche"></div>
						<div class="btnGrisFonceCentre">
							<input type="submit" name="I_Search"  id="search2" value="Rechercher" class="btnGrisFonceCentre" />
						</div>
						<div class="btnGrisFonceDroite"></div>
					</div>
				</div>
			</div>
		</div>
	</form>
	// Bloc de affichage des résultats
	<div style="text-align:right;padding:10px;overflow:hidden">
		<div class="btnRouge" style="padding-right:10px;">
			<div class="btnRougeGauche"></div>
			<div class="btnRougeCentre">
				<a href="/Mon_Compte/Proposer_Produit" class="btnRouge">Proposer un autre Produit</a>
			</div>
			<div class="btnRougeDroite"></div>
		</div>
	</div>
	<div style="text-align:right;padding:10px;overflow:hidden" class="blocambiance_color">Pour saisir une annonce merci de sélectionner le produit qui vous intéresse</div>
	<div class="ligneSelectGris" style="height:30px;border-bottom:1px solid #cccccc;">
		<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
		<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
		<div class="ligneSelectGrisCentre">
			<div class="ligneSelectGrisLeftElements" style="width:60%;">
				<span class="titreligneselect">RESULTATS DE LA RECHERCHE</span>
			</div>
			<div class="ligneSelectGrisCentreElements" style="width:200px;text-align:right;">
				Triés par : <select name="Popularite" class="selectfin">
					<option selected value=0>Par Popularité</option>
					<option value=5>* * * * * </option>
					<option value=4>* * * * </option>
					<option value=3>* * *  </option>
					<option value=2>* *  </option>
					<option value=1>* </option>
				</select>
			</div>
			<div class="colonnevendre blocambiance_color">Vendre</div>
		</div>
	</div> // fin ligne selection
	[!LigneResultats+=0!]
	[COUNT Boutique/Produit|NbRep]
	[IF [!NbRep!]>0]
		//On compte le nombre total d element a affciher
		[!TotalPage:=[!NbRep:/[!MaxLine!]!]!]
		//On calcule le nombre total de page
		[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
			//On arrondit au chiffre superieur le nombre total de page
			[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
		[/IF]
		[STORPROC Boutique/Produit|PR|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
			[!CategEncours:=!]
			[!CategConsole:=!]
			[STORPROC Boutique/Categorie/*/Categorie/Produit/[!PR::Id!]|CP||tmsCreate|ASC]
				[IF [!CategConsole!]=][!CategConsole+= [!CP::Url!]!][/IF]
				[!CategEncours+=/[!CP::Url!]!]
	
			[/STORPROC]
			
			[IF [!Utils::isPair([!LigneResultats!])!]]
				<div class="ligneSelectGrisCentreBlocResultsPair">
			[ELSE]
				<div class="ligneSelectGrisCentreBlocResultsImPair">
			[/IF]
				<div class="ligneSelectGrisElementsResultats" style="border:none;">[!CategConsole!]&nbsp;&nbsp;-&nbsp;&nbsp;[!PR::Reference!]&nbsp;--&nbsp;[!PR::Nom!]&nbsp;[IF [!PR::Type!]!=0][!PR::Type!][/IF]</div>
				<div class="colonnevendre" style="padding-top:1px;padding-left:5px;width:41px;"><a href="/GamesAvenue[!CategEncours!]/Produit/[!PR::Url!]/Vendre"><input type="radio" name="select" value="[!PR::Id!]"></a></div>
			</div>
			[!LigneResultats+=1!]	
		[/STORPROC]
		<div style="text-align:right;padding:10px;" class="blocambiance_color">Pour saisir une annonce merci de sélectionner le produit qui vous intéresse</div>
		//PAGINATION
		[IF [!TotalPage!]>1]
			<div class="LignePagination">
				<span class="ResultatPagination">
					<span>
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
				</span>
			</div>
		[/IF]
	[ELSE]
		Aucun produit ne correspond à votre recherche
	[/IF]
</div>
