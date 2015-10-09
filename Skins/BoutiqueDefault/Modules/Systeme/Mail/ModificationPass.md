// MAIL Modifcation DU PASS$[OBJ Boutique|Magasin|Mag2]
[!Mag:=[!Mag2::getCurrentMagasin()!]!]
[STORPROC Systeme/Site/Magasin/[!Mag::Id!]|S|0|1|Id|ASC][/STORPROC] 

[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!S::Domaine!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM][!Mag::Nom!] : Modification de votre mot de passe[/PARAM][/METHOD]	
[METHOD LeMail|Body][PARAM]
	Bonjour [!Obj::Prenom!] [!Obj::Nom!],<br />
	[!Mag::Nom!] vous souhaite la bienvenue.
	<hr/>
	Votre mot de passe a été modifié.<br/><br/>
	LOGIN : [!Obj::Pseudonyme!]<br/>
	MOT DE PASSE : [!Pass!]<br/>
	<hr/>
	Toute l'équipe de [!Mag::Nom!] vous remercie de votre confiance.<br/><br/>
	Pour nous contacter : [!Mag::EmailContact!]<br/><br/>
[/PARAM][/METHOD]
[METHOD LeMail|Send][/METHOD]



