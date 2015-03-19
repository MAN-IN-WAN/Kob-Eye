[HEADER]
	<style type="text/css">
		#Milieu{
			margin-right:0;
			padding-bottom:20px;
			min-height:400px;
		}
	</style>
[/HEADER]
[IF [!Lien!]=[!Systeme::CurrentMenu::Url!]]
	[!Adr:=[!Query!]!]
	[MODULE Forum/Ariane]
	<div id="Milieu">
		<div class="AddTheme">
			[IF [!Systeme::User::Admin!]]
				[BLOC Bouton|width:140px;||width:105px;]
					<a href="/[!Systeme::CurrentMenu::Url!]?act=newCat&referer=[!Lien!]">Ajouter un th&egrave;me</a>
				[/BLOC]
			[/IF]
		</div>
		<h1 class="TitreForum">Le forum Kob-EYe</h1>
		[MODULE Forum/Categorie/InfoForum]
		[IF [!act!]=="newCat"]
			//Formulaire ajout categorie
			[MODULE Forum/Categorie/AjouterForm?parent=[!parent!]]
		[/IF]
		//Si il y a ajout de cat
		[IF [!confCategorie!]="Ajouter"]
			[MODULE Forum/Categorie/Ajouter?Adr=[!Query!]]
		[/IF]
		// Suppression d une catégorie
		[IF [!conf!]="OK"]
			[STORPROC [!chemin!]|S|0|1]
				[!S::Delete!]
			[/STORPROC]
			[REDIRECT][!Lien!][/REDIRECT]	
		[/IF]
		[IF [!act!]=="suppCat"]
			<div class="PopBox">
				[STORPROC [!chemin!]|S|0|1]
					<h4>&Ecirc;tes vous s&ucirc;r de vouloir supprimer :<br /><span class="Bold">"[!S::getFirstSearchOrder!]" ?</span></h4>
					<div>
						<a href="/[!Lien!]?conf=OK&chemin=[!chemin!]">OUI</a>
						<a href="/[!Lien!]">NON</a>
						<div class="Clear"></div>
					</div>
				[/STORPROC]
			</div>
		[/IF]
		[!T:=0!]
		[STORPROC Forum/Categorie|Cat|0|100|Id|ASC]
			//[BLOC Rounded|[IF [!T!]=0]background-color:fff;[ELSE]background-color:#fff;[/IF]|margin-top:20px;|padding:5px;]
				[MODULE Forum/Categorie/[!Cat::Id!]/Ligne]
			//[/BLOC]
			[MODULE Forum/Categorie/[!Cat::Id!]/AccueilSujet]

			[IF [!T!]=0]
				[!T:=1!]
			[ELSE]
				[!T:=0!]
			[/IF]
		[/STORPROC]
	</div>
	<div class="Clear"></div>
[ELSE]
	[!Adr:=[!Query!]!]
	//[MODULE Systeme/Ariane]
	<div id="Milieu">
		[STORPROC [!Query!]|this]
			<h1 class="TitreCat">[!this::getFirstSearchOrder!]</h1>
		[/STORPROC]
		//Parametres de la pagination
		[!TypeEnf:=Article!]//Definition des elements a afficher
		[!Page[!TypeEnf!]:=1!]//On definit la page 1 par defaut
		[!MaxLine:=10!]//Nombre d elements qu on veut afficher par page
		[COUNT Forum/Categorie/[!this::Id!]/Sujet|Test2]//On compte le nombre total d element a afficher
		[!TotalPage:=[!Test2:/[!MaxLine!]!]!]//On calcule le nombre total de page
		[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]//On arrondit au chiffre superieur le nombre total de page
			[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
		[/IF]
		[IF [!confCategorie!]="Ajouter"][MODULE Forum/Categorie/Ajouter][/IF]
		[IF [!confSujet!]=Ajouter][MODULE Forum/Sujet/Ajouter][/IF]
		[IF [!act!]=="newSubject"]
			//Formulaire ajout sujet
			[MODULE Forum/Sujet/AjouterForm?parent=[!this::Id!]&Page=[!PageArticle!]]
		[/IF]
		[IF [!act!]=="newCat"]
			//Formulaire ajout categorie	
			[MODULE Forum/Categorie/AjouterForm?parent=[!this::Id!]]
		[/IF]
		// Suppression d un sujet
		[IF [!conf!]="OK"]
			[STORPROC [!chemin!]|S|0|1]
				[!S::Delete!]
			[/STORPROC]
			[REDIRECT][!Lien!][/REDIRECT]	
		[/IF]
		[IF [!act!]=="suppCat"]
			<div class="PopBox">
				[STORPROC [!chemin!]|S|0|1]
					<h4>&Ecirc;tes vous s&ucirc;r de vouloir supprimer :<br /><span class="Bold">"[!S::getFirstSearchOrder!]" ?</span></h4>
					<div>
						<a href="/[!Lien!]?conf=OK&chemin=[!chemin!]">OUI</a>
						<a href="/[!Lien!]">NON</a>
						<div class="Clear"></div>
					</div>
				[/STORPROC]
			</div>
		[/IF]
		[STORPROC [!Query!]|this]
			<table class="TableSuj">
				[!Requete:=Forum/Categorie/[!this::Id!]/Sujet!]
				[STORPROC [!Requete!]|Suj|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|ASC]
					<thead>
						<tr>
							<td></td>
							<td>Sujets</td>
							<td>R&eacute;ponses</td>
							<td>Vus</td>
							<td>Dernier message</td>
							[IF [!Systeme::User::Admin!]]
								<td></td>
							[/IF]
						</tr>
					</thead>
					<tbody>
						[LIMIT 0|100]
							<tr>
								<td class="TdIco">
									<img src="/Skins/KobEye/Img/Forum/petitSujet.gif" alt="" title=""/>
								</td>
								<td class="TdText">
									<a href="/[!Systeme::CurrentMenu::Url!]/[!this::Url!]/Sujet/[!Suj::Url!]" class="Bold" title="Voir le sujet">[!Suj::Titre!]</a>
									<p>de [STORPROC Systeme/User/[!Suj::userCreate!]|Us]
										[!Us::Login!]
									[/STORPROC]
									le [UTIL FULLDATEFR][!Suj::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!Suj::tmsCreate!][/UTIL]</p>
								</td>
								<td class="TdNbr">
									[STORPROC Forum/Sujet/[!Suj::Id!]/Post|Pst]
										[!NbPost:=[!NbResult!]!]
										[LIMIT 0|1]
											[IF [!NbPost!]!=1]
												[!NbPost!]
											[ELSE]
												0
											[/IF]
										[/LIMIT]
									[/STORPROC]
								</td>
								<td class="TdNbr">[!Suj::Vu!]</td>
								<td class="TdText">
									[STORPROC Forum/Sujet/[!Suj::Id!]/Post|Pst|0|1|tmsCreate|DESC]
										Dernier message le [UTIL NUMERICDATE][!Pst::tmsCreate!][/UTIL] par 
										[STORPROC Systeme/User/[!Pst::userCreate!]|Usr]
											[IF [!Pst::userCreate!]==[!Systeme::User::Id!]]
												<a href="/Espace-perso" title="Mon compte" class="Bold">[!Usr::Login!]</a>
											[ELSE]
												<a href="#nogo" class="Bold">[!Usr::Login!]</a>
											[/IF]
										[/STORPROC]
									[/STORPROC]
								</td>
								[IF [!Systeme::User::Admin!]]
									<td class="TdIco">
										<a href="/[!Lien!]?act=suppCat&chemin=[!Requete!]/[!Suj::Id!]" class="LienSupp" title="Supprimer le sujet"></a>
									</td>
								[/IF]
							</tr>
						[/LIMIT]
					</tbody>
				[/STORPROC]
			</table>
			//PAGINATION
			[IF [!TotalPage!]>1]
				[BLOC Rounded|background-color:#E4E4E4;||height:25px;line-height:25px;]
					<form id="Pagination" action="/[!Lien!]" method="get">
						//Liste des Numeros de pages
						<div class="NumPages">
							[STORPROC [!TotalPage!]|Pag]
								[IF [!Pos!]!=[!Page[!TypeEnf!]!]]
									<input type="submit" value="[!Pos!]" name="Page[!TypeEnf!]" /> 
								[ELSE]
									<span>[!Page[!TypeEnf!]!]</span>
								[/IF]
							[/STORPROC]
						</div>
						<div class="Clear"></div>
					</form>
				[/BLOC]
			[/IF]
			[IF [!Systeme::User::Admin!]]
				<div class="Actions">
					[BLOC Bouton|width:140px;||width:105px;]
						<a href="[!SERVER::HTTP_REFERER!]">Retour</a>
					[/BLOC]
					[COUNT Forum/Categorie/[!this::Id!]/Sujet|NbSuj]
					// On regarde s il y a déjà des sujets dans la catégorie
					[IF [!NbSuj!]=0]
						// S il n y a pas de sujets, alors on peut ajouter une catégorie
						[BLOC Bouton|width:140px;||width:105px;]
							<a href="/[!Query!]?act=newCat&referer=[!Lien!]">Ajouter une Cat&eacute;gorie</a>
						[/BLOC]
					[/IF]
					[COUNT Forum/Categorie/[!this::Id!]/Categorie|NbssCat]
					[IF [!NbssCat!]=0]
						[BLOC Bouton|width:140px;||width:105px;]
							<a href="/[!Lien!]?act=newSubject&referer=[!Lien!]&PageArticle=[!PageArticle!]">Ajouter un Sujet</a>
						[/BLOC]
					[/IF]
				</div>
			[ELSE]
				[COUNT Forum/Categorie/[!this::Id!]/Categorie|NbssCat]
				[IF [!NbssCat!]=0]
					<div class="Actions">
						[BLOC Bouton|width:140px;||width:105px;]
							<a href="/[!Lien!]?act=newSubject&referer=[!Lien!]&PageArticle=[!PageArticle!]">Ajouter un Sujet</a>
						[/BLOC]
					</div>
				[/IF]
			[/IF]
		[/STORPROC]
	</div>
	<div class="Clear"></div>
[/IF]