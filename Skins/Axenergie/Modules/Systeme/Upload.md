[IF [!toUrl!]=]
	[!toUrl:=Home/[!Systeme::User::Id!]!]
	[IF [!Module!]!=]
	    [!toUrl+=/[!Module!]!]
	[/IF]
	[IF [!obj!]!=]
	    [!toUrl+=/[!obj!]!]
	[/IF]
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

[IF [!updateURL!]]
	[STORPROC [!updateURL!]|O]
		[METHOD O|Set]
			[PARAM][!updateField!][/PARAM]
			[PARAM][!File::Url!][/PARAM]
		[/METHOD]
		[METHOD O|Save][/METHOD]
	[/STORPROC]
[/IF]

{
    "status" : 1,
    "url"   : "[!File::Url!]",
    "name"  : "[!File::Nom!]"
}
