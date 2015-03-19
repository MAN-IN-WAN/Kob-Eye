[MODULE Systeme/Structure/Droite]
<div id="Milieu">
	[MODULE Systeme/Ariane]
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
	<div id="Options">
		<a href="/Redaction/Affich/SendToFriend?Rubrique=Actualit&eacute;s" title="Envoyer la page &agrave; un ami">Envoyer &agrave; un ami</a>
		<a href="/Actualites/Imprimer.print?Debut=[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]&Fin=[!MaxLine!]" title="Imprimer les actualit&eacute;">Imprimer</a>
	</div>
	<h1>Page Actualit&eacute;s</h1>
	[STORPROC News/Nouvelle/Publier=1|N|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsEdit|DESC]
		<div class="Article">
			[IF [!N::Image!]!=]
				<div class="ImgArt">
					<img src="/[!N::Image!].limit.120x200.jpg" alt="[!N::Titre!]" />
				</div>
			[/IF]
			<div [IF [!N::Image!]!=]class="TextArt"[ELSE]class="Text"[/IF]>
				<h2>[!N::Titre!]</h2>
				[!N::Contenu!]
				[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic|0|100|Id|ASC]
					<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a>
				[/STORPROC]
				[STORPROC News/Nouvelle/[!N::Id!]/Lien|Lie|0|100|Id|ASC]
					<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="Lien">[!Lie::Titre!]</a>
				[/STORPROC]
			</div>
		</div>
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
<div class="Clear"></div>