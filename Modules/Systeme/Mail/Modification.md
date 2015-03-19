// MAIL Validation de la création du compte
[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!CONF::MODULE::SYSTEME::SITE!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM][!CONF::MODULE::SYSTEME::LEMAGASIN!]: Modification de votre compte[/PARAM][/METHOD]	
[METHOD LeMail|Body][PARAM]
	Bonjour [!Obj::Prenom!],<br />
	Votre compte a bien été modifié.
	<hr/>
	Toute l'équipe de [!CONF::MODULE::SYSTEME::LEMAGASIN!] vous remercie de votre confiance.<br/><br/>
	Pour nous contacter : [!CONF::MODULE::SYSTEME::CONTACT!]<br/><br/>
[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]


