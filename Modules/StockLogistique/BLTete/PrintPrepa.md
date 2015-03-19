[INFO [!Lien!]|F] // F = nom de la fonction
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]

//Recherche du nom de la fonction
[!J:=[![!Lien!]://!]!]
[STORPROC [!J!]|L][!name:=[!L!]!][/STORPROC]
[!funcs:=[!F::Functions!]!]
[!func:=[!funcs::[!name!]!]!]

// [!F::ACTION::0] indice 0 du tableau des actions
//
//
{"form":{"type":"TitleWindow","id":"FF:StockLogistique/PrintPrepa","title":"Etiquettes de déstockage","width":450,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":1,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5}, 
	"components":[
		{"type":"EditContainer","id":"edit","defaultButtonID":"ok","percentWidth":100,"components":[
			{"type":"Form", "percentWidth":100,
			"components":[
				{"type":"RadioButtonGroup","layout":{"type":"VerticalLayout","gap":4},"id":"select","dataField":"select",
				"buttons":["Tous les éléments","Eléments sélectionés"]}
			]}
		]},
// boutons valider, annuler   
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"yes","label":"Valider","width":80,"stateGroup":"validated",
			"events":[
				{"type":"click", "action":"invoke","objectID":"parentForm","method":"closeForm"}
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
