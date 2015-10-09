// MAIL RECUPERATION DU PASS
[!Mag:=[!Mag2::getCurrentMagasin()!]!]
[STORPROC Systeme/Site/Magasin/[!Mag::Id!]|S|0|1|Id|ASC][/STORPROC] 

[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!S::Domaine!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM][!Mag::Nom!]: Votre accès[/PARAM][/METHOD]	
[METHOD LeMail|Body][PARAM]
	Bonjour [!Obj::Prenom!] [!Obj::Nom!],<br />
	[!Mag::Nom!] vous souhaite la bienvenue.
	<hr/>
	Vous avez demandé à recevoir vos informations personnelles :.<br />
	LOGIN&nbsp;&nbsp;:&nbsp;&nbsp;[!Obj::Pseudonyme!]<br/>
	MOT DE PASSE&nbsp;&nbsp;:&nbsp;&nbsp;[!Pass!]<br/>
	<hr/>
	Toute l'équipe de [!Mag::Nom!] vous remercie de votre confiance.<br/><br/>
	Pour nous contacter : [!Mag::EmailContact!]<br/><br/>
[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]


