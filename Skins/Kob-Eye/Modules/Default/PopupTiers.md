{"type":"TitleWindow", "title":"Sélection Tiers", "width":600, "height":800,
"components":[
	{"type":"VGroup", 
	"components":[
// formulaire de recherche
		{"type":"EditContainer", "id": "searchBox",
		"components":[
			{"type":"HBox", "setStyle":{"paddingLeft":5},
			"components":[
				{"type":"TextInput", "id":"filter", "percentWidth":80},
				{"type":"Button", "id":"search", "label":"Chercher", "width":80, "actions":[
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
		{"type":"AdvancedDataGrid", "id":"dataGrid", "dataField":"dataGrid", "percentWidth":100, "percentHeight":100,
		"query":{"query":"Repertoire/Tiers", "select":"Id,Civilite,Nom,Prenom,Rayon,COsPostalVille"},
		"events":[
			{"type":"dblclick", "action":"invoke", "objectID":"popupTiers", "method":"textValue", "params":{"objectID":"dataGrid", "property":"Nom"}}
		],
		"actions":[
			{"type":"init", "action":"loadData"}
		],
		"columns":[
			{"type":"column", "dataField":"Id", "headerText":"ID", "width":20, "visible":0},
			{"type":"column", "dataField":"Civilite", "headerText":"Civ", "width":30},
			{"type":"column", "dataField":"Nom", "headerText":"Nom", "width":100},
			{"type":"column", "dataField":"Prenom", "headerText":"Prénom", "width":100},
			{"type":"column", "dataField":"Rayon", "headerText":"Rayon", "width":100},
			{"type":"column", "dataField":"CodPostal", "headerText":"CP", "width":50},
			{"type":"column", "dataField":"Ville", "headerText":"Ville", "width":100}
		]},
// boutons valider, annuler
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button", "id":"ok", "label":"Valider", "width":80,
			"events":[
				{"type":"click", "action":"invoke", "objectID":"popupTiers", "method":"textValue", "params":{"objectID":"dataGrid", "property":"Nom"}}
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
