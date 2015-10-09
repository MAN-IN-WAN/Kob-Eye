// MAIL A L'INSCRIPTION
[OBJ Boutique|Magasin|Mag2]
[!Mag:=[!Mag2::getCurrentMagasin()!]!]
[STORPROC Systeme/Site/Magasin/[!Mag::Id!]|S|0|1|Id|ASC][/STORPROC] 

[STORPROC Systeme/User/[!Obj::UserId!]|Pers|0|1|tmsCreate|DESC][/STORPROC]
[LIB Mail|LeMail]
[METHOD LeMail|To][PARAM][!Obj::Mail!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM]noreply@[!S::Domaine!][/PARAM][/METHOD]
[METHOD LeMail|Subject][PARAM][!Mag::Nom!]:Inscription sur notre site[/PARAM][/METHOD]	
[METHOD LeMail|Body]
	[PARAM]
		[BLOC Mail]
			Bonjour [!Obj::Prenom!],<br />
			[!Mag::Nom!] vous souhaite la bienvenue.
			<hr/>
			Vous trouverez ci-dessous un récapitulatif de vos coordonnées, de votre identifiant de connexion et de votre mot de passe.<br />
			[!Obj::Prenom!]&nbsp;&nbsp;[!Obj::Nom!]<br/>
			[!Obj::Adresse!]<br/>
			[!Obj::CodePostal!]&nbsp;&nbsp;[!Obj::Ville!]<br/>
			[!Obj::Telephone!]<br/><br/>
			Identifiant : [!Obj::Pseudonyme!]<br/>
			Mot de passe : [!Pass!]<br/>
			<hr/>
			[!AutoConnexion:=[!Mag::AutoClient!]!]
			[IF [!AutoConnexion!]]
				Toute l'équipe de [!Mag::Nom!] vous remercie de votre confiance.<br/><br/>
				Pour nous contacter [!Mag::EmailContact!]<br/><br/>
			[ELSE]
				Nos équipes vont prendre contact avec vous très rapidement pour finaliser la validation de votre compte<br/><br/>
				Toute l'équipe de [!Mag::Nom!] vous remercie de votre confiance.<br/><br/>
				Pour nous contacter : [!Mag::EmailContact!]<br/><br/>

			[/IF]
		[/BLOC]
	[/PARAM]
[/METHOD]
[METHOD LeMail|Send][/METHOD]

