// Construction de l'URL
[!toUrl:=Home/[!Systeme::User::Id!]!]
[IF [!Module!]!=]
    [!toUrl+=/[!Module!]!]
[/IF]
[IF [!obj!]!=]
    [!toUrl+=/[!obj!]!]
[/IF]

// Enregistrement du fichier
[OBJ Explorateur|_Fichier|File]
[METHOD File|Set]
	[PARAM]Temp[/PARAM]
	[PARAM]Filedata[/PARAM]
[/METHOD]
[METHOD File|Set]
	[PARAM]Url[/PARAM]
	[PARAM][!toUrl!][/PARAM]
[/METHOD]
[METHOD File|Save][/METHOD]

// Renvoyer le nom du fichier
[!File::Url!]
