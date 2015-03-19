[COUNT Newsletter/Contact/Email=[!EMAIL!]|C]
[IF [!C!]]&Retour=-1[ELSE]
	//Il n existe pas donc inscription
	[OBJ Newsletter|Contact|Con]
	[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!EMAIL!][/PARAM][/METHOD]
	[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
	[IF [!Utils::isMail([!EMAIL!])!]]
		//Enregistrement
		[METHOD Con|Save][/METHOD]
		&Retour=1
	[ELSE]&Retour=0[/IF]
[/IF]
