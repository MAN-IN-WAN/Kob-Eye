//[MODULE Systeme/Structure/Droite]
[STORPROC [!Query!]|Cat]
	[INFO [!Query!]|Inf]
	[STORPROC [!Inf::Historique!]|H|0|1]
		[!Niv0:=[!H::Value!]!]
	[/STORPROC]
	[MODULE Systeme/Structure/PhotoEntete]
	[MODULE Systeme/Structure/Droite?Cata=[!Niv0!]]
	<div id="Milieu">
		<div id="Data">
			[!Page[!TypeEnf!]!]
				//Parametres de la pagination
			[!TypeEnf:=Article!]
				//Definition des elements a afficher
			[!Page[!TypeEnf!]:=1!]
				//On definit la page 1 pa r defaut
			[!MaxLine:=4!]
				//Nombre d elements qu on veut afficher par page
			[COUNT News/Nouvelle/Publier=1|Test2]
				//On compte le nombre total d element a affciher
			[!TotalPage:=[!Test2:/[!MaxLine!]!]!]
				//On calcule le nombre total de page
			[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
				//On arrondit au chiffre superieur le nombre total de page
				[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
			[/IF]
<!-- 			<h1>[!Cat::Nom!]</h1> -->
			[MODULE Systeme/Ariane]
			<div class="TitreCat">
				[MODULE Systeme/Options]
				[IF [!Cat::Icone!]=]
					<div class="TextDefault">
						<h1>[!Cat::Nom!]</h1>
						<h2 style="left:0;">[!Cat::Chapo!]</h2>
					</div>
				[ELSE]
					<div class="TextDefault">
						<h1 style="background:url(/[!Cat::Icone!]) no-repeat;height:40px;background-position:-1% 0%; padding-left:65px;">[!Cat::Nom!]</h1>
						[IF [!Cat::Chapo!]!=]
							<h2>[!Cat::Chapo!]</h2>
						[/IF]
					</div>
				[/IF]
			</div>
			<div>
				[IF [!Cat::Chapo!]]
					<p class="Description">[!Cat::Chapo!]</p>
				[/IF]
			</div>
			[STORPROC News/Nouvelle/Publier=1|N|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsEdit|DESC]
				<div class="Article">
					[IF [!N::Image!]!=]
						<div class="ImgArt">
							<img src="/[!N::Image!].limit.120x200.jpg" alt="[!N::Titre!]" />
						</div>
					[/IF]
					<div [IF [!N::Image!]!=]class="TextArt"[ELSE]class="Text"[/IF]>
						<h2><a name="[!N::Url!]">[!N::Titre!]</a></h2>
						[!N::Contenu!]
						[COUNT News/Nouvelle/[!N::Id!]/Fichier|F]
						[IF [!F!]]
							[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic|0|10|Id|ASC]
								<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a>
							[/STORPROC]
						[/IF]
						[COUNT News/Nouvelle/[!N::Id!]/Lien|L]
						[IF [!L!]]
<!-- 							<div id="SousCat"> -->
		<!-- 					<ul> -->
							[STORPROC News/Nouvelle/[!N::Id!]/Lien|Lie|0|10|Id|ASC]
								<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="PrestaLien" style="margin-left:10px;">[!Lie::Titre!]</a>
							[/STORPROC]
		<!-- 					</ul> -->
		<!-- 					</div> -->
						[/IF]
					</div>
				</div>
				[IF [!Pos!]=[!NbResult!]][ELSE]<hr />[/IF]
			[/STORPROC]
			//PAGINATION
			[IF [!TotalPage!]>1]
				<div id="Pagination">
					<form action="/[!Lien!]" method="get">
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
					</form>
				</div>
			[/IF]
		</div>
	</div>
	<div class="Clear"></div>
[/STORPROC]