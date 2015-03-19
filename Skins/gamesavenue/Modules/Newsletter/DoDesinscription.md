[COUNT Newsletter/Contact/Email=[!EMAIL!]|Present]
[IF [!Present!]]
    //On regarde si le client existe
    [COUNT Boutique/Client/Mail=[!EMAIL!]|Cli]
    [IF [!Cli!]]
	//il faut modifier le champ newsletter du client
	[STORPROC Boutique/Client/Mail=[!EMAIL!]|Cl]
	    [METHOD Cl|Set]
		[PARAM]Newsletter[/PARAM]
		[PARAM]0[/PARAM]
	    [/METHOD]
	    [METHOD Cl|Save][/METHOD]
	[/STORPROC]
    [/IF]
    [STORPROC Newsletter/Contact/Email=[!EMAIL!]|LeContact|0|100]
	[METHOD LeContact|Delete][/METHOD]
    [/STORPROC]
    //On ajoute dans la liste des desinscrit
    [OBJ Newsletter|Contact|Con]
    [METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!EMAIL!][/PARAM][/METHOD]
    [METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/13[/PARAM][/METHOD]
    [METHOD Con|Save][/METHOD]
Vous &ecirc;tes d&eacute;sormais d&eacute;sinscrit(e) et ne recevrez plus notre newsletter.
[ELSE]
Votre adresse ne figure pas dans notre liste de diffusion.
[/IF]
