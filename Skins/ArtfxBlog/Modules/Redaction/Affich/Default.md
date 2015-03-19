[IF [!Page!]=EMPTY]
	[!Page:=1!]
[/IF]
[!Page[!TypeEnf!]!]
	//Parametres de la pagination
[!TypeEnf:=Article!]
	//Definition des elements a afficher
[!Page[!TypeEnf!]:=1!]
	//On definit la page 1 pa r defaut
[!MaxLine:=2!]
	//Nombre d elements qu on veut afficher par page
[COUNT Blog/Post|Po]
	//On compte le nombre total d element a affciher
[!TotalPage:=[!Test2:/[!MaxLine!]!]!]
	//On calcule le nombre total de page
[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
	//On arrondit au chiffre superieur le nombre total de page
	[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
[/IF]
[STORPROC Blog/Post|Post|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
	[MODULE Blog/Post/[!Post::Id!]/Short]
	[NORESULT]<div class="infosbox">Aucun billet pour cette page</div>[/NORESULT]
[/STORPROC]
<div id="Pagination">
	//PAGINATION
	[IF [!TotalPage!]>1]
		<form id="Pagination" action="/[!Lien!]" method="get">	
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
			[IF [!TotalPage!]>1&&[!Page[!TypeEnf!]:+1!]<[![!TotalPage!]:+1!]]
				<div class="FlechesD">
					<input class="PageSuiv" type="submit" value="[!Page[!TypeEnf!]:+1!]" name="Page[!TypeEnf!]" /> 
					<!--<input class="Page2" type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" /> -->
				</div>		
			[/IF]
		</form>
	[/IF]
</div>