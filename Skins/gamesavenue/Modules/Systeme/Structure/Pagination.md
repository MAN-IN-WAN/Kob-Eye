// -- Gestion de la pagination
[IF [!Page!]=EMPTY]
	[!Page:=1!]
[/IF]
[IF [!MaxLine!]=]
[!MaxLine:=20!]
[/IF]
//Nombre d elements qu on veut afficher par page
