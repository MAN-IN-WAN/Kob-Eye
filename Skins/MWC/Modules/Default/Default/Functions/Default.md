[INFO [!Lien!]|F] // F = nom de la fonction
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]

//Recherche du nom de la fonction
[!J:=[![!Lien!]://!]!]
[STORPROC [!J!]|L][!name:=[!L!]!][/STORPROC]
[!funcs:=[!F::Functions!]!]
[!func:=[!funcs::[!name!]!]!]

[IF [!func!]=]
	Fonction [!name!] non trouvée
[ELSE]

//recherche propriete json
[!props:=[!func::properties!]!]
[STORPROC [!props!]|P][IF [!P::type!]=json][!json:=[!P!]!][/IF][/STORPROC]

// [!F::ACTION::0] indice 0 du tableau des actions
//
//
{"form":{"type":"TitleWindow","id":"FF:[!I::Module!]/[!I::TypeChild!]:[!name!]","title":"[!func::description!]",
"width":500,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":"invoke","method":"groupState","params":{"group":"validated","property":"enabled","validated":1}}
	}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5}, 
	"components":[
		{"type":"EditContainer","id":"edit","defaultButtonID":"ok","percentWidth":100,"components":[
			{"type":"Form", "percentWidth":100,
			"components":[
				[IF [!json!]]
					[!json::name!]
				[ELSE]
					[!item:=0!]
					[STORPROC [!props!]|P]
						[MODULE Systeme/formProperty?P=[!P!]&O=[!O!]&item=[!item!]]
						[!item+=1!]
					[/STORPROC]
				[/IF]
			]}
		],
		"events":[
//			{"type":"init","action":"invoke","method":"clearData"}
		]},
// boutons valider, annuler   
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"yes","label":"Valider","width":80,"stateGroup":"validated","enabled":0,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]},
			{"type":"Button","id":"no","label":"Annuler","width":80,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"dispatchValues"}
]}
}
[/IF]
