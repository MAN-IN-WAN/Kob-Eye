[STORPROC Blog/Categorie/Post/[!Pst::Id!]|Cat][/STORPROC]
[OBJ Blog|Commentaire|Comments]
[METHOD Comments|Set][PARAM]Pseudo[/PARAM][PARAM][!Auteurs!][/PARAM][/METHOD]
[METHOD Comments|Set][PARAM]Comment[/PARAM][PARAM][!C_Com!][/PARAM][/METHOD]
[METHOD Comments|Set][PARAM]Mail[/PARAM][PARAM][!UserMail!][/PARAM][/METHOD]
[IF [!UserSite!]!=]
	[METHOD Comments|Set][PARAM]Site[/PARAM][PARAM][!UserSite!][/PARAM][/METHOD]
[/IF]
[METHOD Comments|Set][PARAM]Actif[/PARAM][PARAM]0[/PARAM][/METHOD]
[METHOD Comments|AddParent][PARAM]Blog/Post/[!Pst::Id!][/PARAM][/METHOD]
[METHOD Comments|Save][/METHOD]


//Mail de confirmation
[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM]Blog Sable et Jasmin : Commentaire a moderer[/PARAM][/METHOD]
[METHOD LeMail|From][PARAM][!CONF::GENERAL::INFO::ADMIN_MAIL!][/PARAM][/METHOD]
[METHOD LeMail|To][PARAM][!CONF::GENERAL::INFO::ADMIN_MAIL!][/PARAM][/METHOD]
[METHOD LeMail|Body]
	[PARAM]
		//[BLOC Mail]
			Un internaute [!Auteurs!] [!UserMail!],<br /> a posté un commentaire sur le post <br />
			Categorie : [!Cat::Titre!] le post : [!Pst::Titre!]

			[!Domaine!]/CategoriePost/[!Cat::Url!]/Post/[!Pst::Id!]

			Merci de le modérer

		//[/BLOC]
	[/PARAM]
[/METHOD]
[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]



[REDIRECT][!Lien!]?Val=OK[/REDIRECT]


