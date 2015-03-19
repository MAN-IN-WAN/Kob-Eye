// MAIL Modifcation DU PASS
[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!CONF::MODULE::SYSTEME::SITE!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM][!CONF::MODULE::SYSTEME::LEMAGASIN!]: Modification de votre mot de passe[/PARAM][/METHOD]	
[METHOD LeMail|Body][PARAM]
	Bonjour [!Obj::Prenom!],<br />
	[!CONF::MODULE::SYSTEME::LEMAGASIN!] vous souhaite la bienvenue.
	<hr/>
	Votre mot de passe a été modifié.<br/><br/>
	LOGIN : [!Obj::Pseudonyme!]<br/>
	MOT DE PASSE : [!Pass!]<br/>
	<hr/>
	Toute l'équipe de [!CONF::MODULE::SYSTEME::LEMAGASIN!] vous remercie de votre confiance.<br/><br/>
	Pour nous contacter : [!CONF::MODULE::SYSTEME::CONTACT!]<br/><br/>
[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]


