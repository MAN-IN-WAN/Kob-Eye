[STORPROC [!Query!]|Cat]
	//Parametres de la pagination
	[!TypeEnf:=Article!]//Definition des elements a afficher
	[!Page[!TypeEnf!]:=1!]//On definit la page 1 par defaut
	[!MaxLine:=4!]//Nombre d elements qu on veut afficher par page
	[COUNT Blog/Categorie/[!Cat::Id!]/Post/Actif=1&Brouillon=0|Test2]//On compte le nombre total d element a afficher
	[!TotalPage:=[!Test2:/[!MaxLine!]!]!]//On calcule le nombre total de page
	[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]//On arrondit au chiffre superieur le nombre total de page
		[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
	[/IF]
	<div class="PageCat">
		<h1>[!Cat::Titre!]</h1>
		[STORPROC Blog/Categorie/[!Cat::Id!]/Post/Actif=1&Brouillon=0|Post|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
			[MODULE Blog/Categorie/[!Cat::Id!]/Post/[!Post::Id!]/Short]
			[NORESULT]
				<h4>Il n'y a aucun article dans cette cat&eacute;gorie.</h4>
			[/NORESULT]
		[/STORPROC]
		//PAGINATION
		[IF [!TotalPage!]>1]
			<form id="Pagination" action="/[!Lien!]" method="get">
				[IF [!TotalPage!]>1&&[!Page[!TypeEnf!]:+1!]<[![!TotalPage!]:+1!]]
					<div class="FlechesD">
						<input class="PageSuiv" type="submit" value="[!Page[!TypeEnf!]:+1!]" name="Page[!TypeEnf!]" /> 
						<!--<input class="Page2" type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" /> -->
					</div>		
				[/IF]
				[IF [!Page[!TypeEnf!]!]>1]
					<div class="FlechesG">
						<input class="Page1" type="submit" value="1" name="Page[!TypeEnf!]" />
						<input class="PagePrec" type="submit" value="[!Page[!TypeEnf!]:-1!]" name="Page[!TypeEnf!]" />
					</div>
				[/IF]
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
		[/IF]
	</div>
[/STORPROC]