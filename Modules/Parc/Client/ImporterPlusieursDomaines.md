[IF [!Action!]="Modifier"]
	[STORPROC [![!domainlist!]:/%RC%!]|D|0|500]
		[OBJ Parc|Domain|Do]
		[METHOD Do|Set][PARAM]Url[/PARAM][PARAM][!D!][/PARAM][/METHOD]
		[METHOD Do|AddParent][PARAM][!Query!][/PARAM][/METHOD]
        [METHOD Do|Save][/METHOD]
	[/STORPROC]
	{
		"success": true,
		"message": "Tous les domaines ont été intégrés avec succès",
        "controls": {
            "save": false,
            "cancel": false,
            "close": true
        }
	}
[ELSE]
	//Maintenant on ouvre le fichier en ecriture
	<form enctype="multipart/form-data" action="" method="post" name="frm" >
	<div class="Propriete">
		<div class="ProprieteTitre">Liste des domaine (1 par ligne) </div>
		<div class="ProprieteValeur">&nbsp;
			<textarea name="domainlist" style="width:500px;height:400px;"></textarea>
		</div>
	</div>
	<input type="hidden" name="Action" value="Modifier"/>
[/IF]
