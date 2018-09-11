[!toUrl:=Home/[!Systeme::User::Id!]!]
[IF [!Module!]!=]
    [!toUrl+=/[!Module!]!]
[/IF]
[IF [!obj!]!=]
    [!toUrl+=/[!obj!]!]
[/IF]
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
{
    "status" : 1,
    "url"   : "[!File::Url!]",
    "name"  : "[!File::Nom!]"
}
