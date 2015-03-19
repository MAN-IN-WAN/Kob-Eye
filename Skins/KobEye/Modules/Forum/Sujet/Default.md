[HEADER]
	<style type="text/css">
		#Milieu{
			margin-right:0;
			padding-bottom:20px;
		}
	</style>
[/HEADER]
[MODULE Forum/Ariane]
<div id="Milieu">
	//On genere Le sujet
	[STORPROC [!Query!]|this]
		[METHOD this|Set]
			[PARAM]Vu[/PARAM]
			[PARAM][!this::Vu:+1!][/PARAM]
		[/METHOD]
		[METHOD this|Save][/METHOD]
		//Parametres de la pagination
		[!TypeEnf:=Article!]//Definition des elements a afficher
		[!Page[!TypeEnf!]:=1!]//On definit la page 1 par defaut
		[!MaxLine:=10!]//Nombre d elements qu on veut afficher par page
		[COUNT Forum/Sujet/[!this::Id!]/Post|Test2]//On compte le nombre total d element a afficher
		[!TotalPage:=[!Test2:/[!MaxLine!]!]!]//On calcule le nombre total de page
		[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]//On arrondit au chiffre superieur le nombre total de page
			[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
		[/IF]
		[!Adr:=[!Query!]!]
		[STORPROC Forum/Categorie/Sujet/[!this::Id!]|Cat][/STORPROC]
		//Cas de l ajout
		[IF [!confPost!]=="Ajouter"]
			[MODULE Forum/Post/Ajouter]
		[/IF]
		// Suppression d un post
		[IF [!conf!]="OK"]
			[STORPROC [!chemin!]|S|0|1]
				[!S::Delete!]
			[/STORPROC]
			[REDIRECT][!Lien!][/REDIRECT]	
		[/IF]
		[IF [!act!]=="suppCat"]
			<div class="PopBox">
				[STORPROC [!chemin!]|S|0|1]
				//	<h4>&Ecirc;tes vous s&ucirc;r de vouloir supprimer :<br /><span class="Bold">"[!S::getFirstSearchOrder!]" ?</span></h4>
					<h4>&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce message ?</h4>
					<div>
						<a href="/[!Lien!]?conf=OK&chemin=[!chemin!]">OUI</a>
						<a href="/[!Lien!]">NON</a>
						<div class="Clear"></div>
					</div>
				[/STORPROC]
			</div>
		[/IF]
		//On genere l entete
		[STORPROC [!Query!]|this]
			<h2 class="CatForum">[!this::getFirstSearchOrder!]</h1>
		[/STORPROC]
		[!Requete:=[!Query!]/Post!]
		[STORPROC [!Requete!]|post|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|ASC]
			<table class="TableSuj">
				<thead>
					<tr>
						<td>Auteur</td>
						<td>Messages</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="TdAuteur">
							[MODULE Systeme/User/[!post::userCreate!]/InfoUser]
						</td>
						<td>
							[COUNT [!Requete!]/tmsCreate>[!post::tmsCreate!]|K]
							[IF [!Systeme::User::Admin!]]
								<div class="FRight">
									[IF [!K!]=0]
										[BLOC Bouton|width:140px;||width:105px;]	
											<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Sujet/[!this::Url!]/Post/[!post::Id!]/Modifier?referer=[!Lien!]&Num=[!Pos!]">Modifier</a>
										[/BLOC]
									[/IF]
									[IF [!Pos!]!=1||[!K!]=0]
										[BLOC Bouton|width:140px;||width:105px;]
											<a href="/[!Lien!]?act=suppCat&chemin=[!Requete!]/[!post::Id!]">Supprimer</a>
										[/BLOC]
									[/IF]
								</div>
							[ELSE]
								[IF [!post::userCreate!]==[!Systeme::User::Id!]]
									<div class="FRight">
										[IF [!K!]=0]
											[BLOC Bouton|width:140px;||width:105px;]	
												<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Sujet/[!this::Url!]/Post/[!post::Id!]/Modifier?referer=[!Lien!]&Num=[!Pos!]" title="Modifier le message">Modifier</a>
											[/BLOC]
											[BLOC Bouton|width:140px;||width:105px;]
												<a href="/[!Lien!]?act=suppCat&chemin=[!Requete!]/[!post::Id!]" title="Supprimer le message">Supprimer</a>
											[/BLOC]
										[/IF]
									</div>
								[/IF]
							[/IF]
							<div class="PostForum">
								[IF [!Pos!]=1]
									<p class="TitreSuj">
										<img src="/Skins/KobEye/Img/Forum/toutPetitSujet.gif" alt="sujet" />
										<span class="Bold">Sujet : </span>[!this::Titre!]
									</p>
								[/IF]
								<p>
									<img src="/Skins/KobEye/Img/Forum/toutPetitHeure.gif" alt="sujet" />
									<span class="Bold">Post&eacute; le : </span>[UTIL FULLDATEFR][!post::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!post::tmsCreate!][/UTIL]
								</p>
								<p>
									[IF [!post::Contenu!]]
										<p><span class="Bold">Message : </span>
										[!post::Contenu!]</p>
										[STORPROC [!Requete!]/[!post::Id!]/Fichier|Fich|0|10|Id|DESC]
											[IF [!Fich::URL!]~jpg||[!Fich::URL!]~png]
												<img src="/[!Fich::URL!].limit.500x300.jpg" alt="Image attach&eacute;e au message" title="Image du message" />
												
											[ELSE]
												<p><span class="Bold">Fichier : </span><a href="/[!Fich::URL!]" title="Document du message">[!Fich::URL!]</a></p>
											[/IF]
										[/STORPROC]
									[/IF]
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		[/STORPROC]
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
		//Formulaire ajout post	
		[IF [!this::noResponse!]=1]
			<div class="Infos">Ce sujet n'accepte pas de r&eacute;ponses</div>
		[ELSE]
			[MODULE Forum/Post/AjouterForm?parent=[!this::Id!]&Lien=[!Lien!]&Page=[!PageArticle!]]
		[/IF]
	[/STORPROC]
</div>
<div class="Clear"></div>