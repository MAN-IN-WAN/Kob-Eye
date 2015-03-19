[OBJ Blog|Commentaire|Comments]
[METHOD Comments|Set][PARAM]Pseudo[/PARAM][PARAM][!Auteurs!][/PARAM][/METHOD]
[METHOD Comments|Set][PARAM]Comment[/PARAM][PARAM][!C_Com!][/PARAM][/METHOD]
[METHOD Comments|Set][PARAM]Mail[/PARAM][PARAM][!UserMail!][/PARAM][/METHOD]
[IF [!UserSite!]!=]
	[METHOD Comments|Set][PARAM]Site[/PARAM][PARAM][!UserSite!][/PARAM][/METHOD]
[/IF]
[METHOD Comments|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
[METHOD Comments|AddParent][PARAM]Blog/Post/[!Post::Id!][/PARAM][/METHOD]
[METHOD Comments|Save][/METHOD]
[REDIRECT][!Lien!]?Val=OK[/REDIRECT]


