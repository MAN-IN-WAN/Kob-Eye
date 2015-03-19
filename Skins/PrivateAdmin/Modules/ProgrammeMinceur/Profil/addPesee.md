[IF [!Poids!]>0]
	//Recuperation du profil
	[STORPROC [!Systeme::User::getChildren(Profil)!]|P]
		//Ajout de la pesée
		[OBJ ProgrammeMinceur|Pesee|Pe]
		[METHOD Pe|Set][PARAM]Poids[/PARAM][PARAM][!Poids!][/PARAM][/METHOD]
		[METHOD Pe|Set][PARAM]Date[/PARAM][PARAM][!TMS::Now!][/PARAM][/METHOD]
		[METHOD Pe|AddParent][PARAM][!P!][/PARAM][/METHOD]
		[IF [!Pe::Verify()!]]
			[METHOD Pe|Save][/METHOD]
			{
				"success":1,
				"message":"Votre poids [!Poids!] kg a été enregistré avec succés !"
			}
		[ELSE]
			{
				"success":0,
				"message":"Une erreur est survenue. Veuillez prévenir minceur-sgl.com"
			}
		[/IF]
		[NORESULT]
			{
				"success":0,
				"message":"Votre profil est introuvable. Veuillez saisir votre profil."
			}
		[/NORESULT]
	[/STORPROC]
[ELSE]
	{
		"success":0,
		"message":"Votre saisie est incorrecte. Veuillez saisir votre poids à nouveau."
	}
[/IF]
