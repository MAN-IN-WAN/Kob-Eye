// MAIL A L'INSCRIPTION
[STORPROC Systeme/User/[!Obj::UserId!]|Pers|0|1|tmsCreate|DESC][/STORPROC]
[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!Domaine!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM]Inscription sur notre site [!Domaine!][/PARAM][/METHOD]	
[METHOD LeMail|Body]
	[PARAM]
		[BLOC Mail]
			Bonjour,<br />
			
			[!Domaine!] vous souhaite la bienvenue.
			<hr/>
			Vous trouverez ci-dessous un récapitulatif de vos coordonnées, de votre identifiant de connexion et de votre mot de passe.<br />
			Identifiant : [!Obj::Mail!]<br/>
			Mot de passe : [!Pass!]<br/>
			Téléphone : [!Obj::Tel!]<br/><br/>
			<hr/>
		[/BLOC]
	[/PARAM]
[/METHOD]
[METHOD LeMail|Send][/METHOD]

