[COUNT [!Req!]|CO]

[STORPROC [![!Req!]:/::!]|ReqOptions|0|1][/STORPROC]
[STORPROC [![!Req!]:/::!]|OptVisible|1|1][/STORPROC]
[STORPROC [![!Req!]:/::!]|OptEnbase|2|1][/STORPROC]

{
	"success": true,
	"count": "[!CO!]",
	"data": [
		[STORPROC [!ReqOptions!]|Val|0|100]
			[IF [!OptEnbase!]=]
				[IF [!OptVisible!]=]
					// Seulement la requête
					[!OptBase:=[!Val::Id!]!]
					[!OptHTML:=[!Val::getFirstSearchOrder!]!]
				[ELSE]
					// Requête + champ spécifique
					[!OptBase:=[!Val::[!OptVisible!]!]!]
					[!OptHTML:=[!Val::[!OptVisible!]!]!]
				[/IF]
			[ELSE]
				// Valeurs stockée et affichée définies
				[!OptBase:=[!Val::[!OptEnbase!]!]!]
				[!OptHTML:=[!Val::[!OptVisible!]!]!]
			[/IF]
			// On affiche l'option
			{
				"value": "[!OptBase!]",
				"html": "[!OptHTML!]"
			},
		[/STORPROC]
	]
}



