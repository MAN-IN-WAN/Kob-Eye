[INFO [!Lien!]|F] // F = nom de la fonction
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]

// [!F::ACTION::0] indice 0 du tableau des actions
//
//
{"form":{"type":"TitleWindow","id":"FF:[!I::Module!]/[!I::TypeChild!]","title":"[!F::description!]",
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},"localProxy":1,
"components":[
	{"type":"HBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5}, 
	"components":[
		{"type":"EditContainer","id":"edit","defaultButtonID":"ok","components":[
			{"type":"Form", "percentWidth":100, "layout":{"type":"VerticalLayout", "gap":6, "paddingLeft":6, "paddingRight":6},
			"components":[
				[!item:=0!]
				[STORPROC [!F:PROPERTIES!]|P]
					[MODULE Systeme/formProperty?P=[!P!]&O=[!O!]&item=[!item!]]
					[!item+=1!]
				[/STORPROC]
			]}				
		]},
// boutons valider, annuler   
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"yes","label":"Valider","width":80,
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
