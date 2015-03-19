// MAIL A L'INSCRIPTION
[STORPROC Systeme/User/[!Obj::UserId!]|Pers|0|1|tmsCreate|DESC][/STORPROC]
[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!CONF::MODULE::SYSTEME::SITE!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM][!CONF::MODULE::SYSTEME::LEMAGASIN!]:Inscription sur notre site[/PARAM][/METHOD]	
[METHOD LeMail|Body][PARAM]
	Bonjour [!Obj::Prenom!],<br />
	[!CONF::MODULE::SYSTEME::LEMAGASIN!] vous souhaite la bienvenue.
	<hr/>
	Vous trouverez ci-dessous un récapitulatif de vos coordonnées, de votre identifiant de connexion et de votre mot de passe.<br />
	[!Obj::Prenom!]&nbsp;&nbsp;[!Obj::Nom!]<br/>
	[!Obj::Adresse!]<br/>
	[!Obj::CodePostal!]&nbsp;&nbsp;[!Obj::Ville!]<br/>
	[!Obj::Telephone!]<br/><br/>
	Identifiant : [!Obj::Pseudonyme!]<br/>
	Mot de passe : [!Pass!]<br/>
	<hr/>
	Toute l'équipe de [!CONF::MODULE::SYSTEME::LEMAGASIN!] vous remercie de votre confiance.<br/><br/>
	Pour nous contacter : [!CONF::MODULE::SYSTEME::CONTACT!]<br/><br/>
	[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]

