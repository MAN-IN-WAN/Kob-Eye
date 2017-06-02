[!toUrl:=Home/[!Systeme::User::Id!]/MiseEnPage/Image!]
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

{"files": [
  {
    "name": "[!File::Nom!]",
    "url": "[!File::Url!]"
  }
]}