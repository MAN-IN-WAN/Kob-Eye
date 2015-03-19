[!usr:=[!Systeme::User!]!]
[IF [!toUrl!]=]
	[!toUrl:=Home/[!usr::Id!]!]
	[IF [!Module!]!=]
	    [!toUrl+=/[!Module!]!]
	[/IF]
	[IF [!obj!]!=]
	    [!toUrl+=/[!obj!]!]
	[/IF]
[/IF]

[!adhs:=[!usr::getChildren(Adherent)!]!]
[STORPROC [!adhs!]|adh|0|1][/STORPROC]
[!boks:=[!adh::getParents(Book)!]!]
[STORPROC [!boks!]|bok|0|1][/STORPROC]

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

[OBJ Flipbook|Page|P]
[METHOD P|Set][PARAM]Image[/PARAM][PARAM][!File::Url!][/PARAM][/METHOD]
[METHOD P|AddParent][PARAM]Flipbook/Book/[!bok::Id!][/PARAM][/METHOD]
[METHOD P|Save][/METHOD]

{
    "status" : 1,
    "url"   : "[!File::Url!]",
    "name"  : "[!File::Nom!]"
}
