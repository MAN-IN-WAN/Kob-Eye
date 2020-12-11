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
    [PARAM]qqfile[/PARAM]
[/METHOD]
[METHOD File|Set]
    [PARAM]Url[/PARAM]
    [PARAM][!toUrl!][/PARAM]
[/METHOD]
[METHOD File|Save][/METHOD]
{
    "success" : true,
    "url"   : "[!File::Url!]",
    "name"  : "[!File::Nom!]"
}
