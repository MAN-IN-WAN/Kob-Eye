{"type":"TitleWindow", "title":"Sélection Tiers", "width":500, "height":600,
"components":[
	{"type":"VBox", "percentWidth":100, "percentHeight":100, "setStyle":{"paddingLeft":5, "paddingRight":5, "paddingTop":5, "paddingBottom":5}, 
	"components":[
// formulaire de recherche
		{"type":"EditContainer", "id": "searchBox",
		"components":[
			{"type":"HGroup",
			"components":[
				{"type":"TextInput", "id":"filter", "percentWidth":80},
				{"type":"Button", "id":"search", "label":"Chercher ZOB", "width":80, "actions":[
					{"type":"click", "action":"updateObject", "id":"dataGrid", "editField":"filter"}
				]},
				{"type":"Button", "id":"clear", "label":"Effacer", "width":80, "actions":[
					{"type":"click", "action":[
						{"action":"invoke", "objectID":"filter", "method":"clearData", "params":{}},
						{"action":"updateObject", "id":"dataGrid"}
					]}
				]}
			]}
		]},
// datagrid
		{"type":"Group", "percentWidth":100, "percentHeight":100,
		"components":[
			{"type":"AdvancedDataGrid", "id":"dataGrid", "dataField":"dataGrid", "percentWidth":100, "percentHeight":100,
			"query":{"query":"Repertoire/Tiers", "select":"Id,CodeTiers,Enseigne,Intitule,CodPostal,Ville"},
			"events":[
				{"type":"dblclick", "action":"invoke", "objectID":"popupTiers", "method":"textValue", "params":{"objectID":"dataGrid", "property":"Intitule"}}
			],
			"actions":[
				{"type":"init", "action":"loadData"}
			],
			"columns":[
				{"type":"column", "dataField":"Id", "headerText":"ID", "width":20, "visible":0},
				{"type":"column", "dataField":"CodeTiers", "headerText":"Code", "width":80},
				{"type":"column", "dataField":"Enseigne", "headerText":"Enseigne", "width":80},
				{"type":"column", "dataField":"Intitule", "headerText":"Intitulé", "width":150},
				{"type":"column", "dataField":"CodPostal", "headerText":"CP", "width":50},
				{"type":"column", "dataField":"Ville", "headerText":"Ville", "width":100}
			]}
		]},
// boutons valider, annuler
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button", "id":"ok", "label":"Valider", "width":80,
			"events":[
				{"type":"click", "action":"invoke", "objectID":"popupTiers", "method":"textValue", "params":{"objectID":"dataGrid", "property":"Intitule"}}
			]},
			{"type":"Button", "id":"cancel", "label":"Annuler", "width":80,
			"events":[
				{"type":"click", "action":"invoke", "objectID":"popupTiers", "method":"textValue"}
			]}
		]}		
	]}
]
//,
//"actions":[
//		{"type":"init", "action":"getData", "query":{"query":"Repertoire/Tiers", "select":"Id,Civilite,Nom,Prenom,Ville"}}
//]
}
