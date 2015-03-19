{"form":{"type":"VBox","id":"StockLogistique/LivrList","label":"Feuilles de Route","percentHeight":100,
"setStyle":{"paddingLeft":5,"paddingRight":5},"clipContent":0,
"localProxy":1,
"components":[
	{"type":"MenuTab","id":"menuList", "menuItems":[
		{"label":"File", "children":[
			{"label":"Ouvrir", "icon":"open", "data":"open"},
			{"type":"vseparator"},
			{"label":"Rafraîchir", "icon":"iconNew", "data":"refresh"},
			{"label":"Imprimer", "icon":"print", "data":"printTournee"},
			{"label":"Valider", "icon":"", "data":"valideTournee"}
		]}
	],
	"actions":[
		{"type":"itemClick","actions":{
			"printTournee":{"action":"invoke","method":"callMethod","params":{
				"interface":1,
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/printTournee.json"},
				"function":"PrintDocuments","selectionRequired":1,"args":[{"selectedValues":["dataGrid"]},{"interfaceValues":["Tournee","BL","Fond"]}]}
			},
			"valideTournee":{"action":"invoke","method":"callMethod","params":{
				"interface":1,
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/valideTournee.json"},
				"function":"ValideTournee","selectionRequired":1,"args":[{"selectedValues":["dataGrid"]},{"interfaceValues":["Date","Tournee","BL","Fond"]}]}
			}
		}}
	]},
	{"type":"HBox","id":"listBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
		{"type":"EditContainer", "id":"searchBox", "width":180, "percentHeight":100,
		"components":[
			{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"verticalGap":0},
			"components":[
				{"type":"Label","text":"Réference"},
				{"type":"TextInput","dataField":"Reference","percentWidth":100,"dataGroup":"searchGroup"},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"Livraison"},
				{"type":"HGroup","gap":2,"components":[
					{"type":"DateField","dataField":"debut","dataGroup":"searchGroup"},
					{"type":"Spacer","width":2},
					{"type":"DateField","dataField":"fin","dataGroup":"searchGroup"}
				]},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"Véhicule"},
				{"type":"TextInput","dataField":"VehiculeId","percentWidth":100,"dataGroup":"searchGroup"},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"CP"},
				{"type":"TextInput","dataField":"ChauffeurId","percentWidth":100,"dataGroup":"searchGroup"},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"Ville"},
				{"type":"Boolean","dataField":"Effectuee","dataGroup":"searchGroup"},
				{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
					{"type":"Spacer","percentWidth":100},
					{"type":"Button","label":"$__Clear__$","id":"clear","width":70}
				]}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear","action":"invoke","method":"clearData"}
			]}
		]},
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1, 
		"kobeyeClass":{"module":"StockLogistique","objectClass":"Tournee","form":"FormRetour.json"},"checkBoxes":1,
		"events":[
			{"type":"start", "action":"loadValues"},
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
			{"type":"proxy","triggers":[
				{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"save","action":"invoke","method":"callMethod",
				"params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},
				"function":"SaveDestockage","args":[{"dataValue":["dataGrid"]}]}},
				{"trigger":"refresh","action":"invoke","method":"restart"},
				{"trigger":"open","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}}
			]}
		],
		"columns":[
				{"type":"column","dataField":"Reference","headerText":"Numéro","width":70},
				{"type":"column","dataField":"Date","headerText":"Date","format":"date","width":60},
				{"type":"column","dataField":"Valide","headerText":"V","format":"boolean","width":20},
				{"type":"column","dataField":"Effectue","headerText":"E","format":"boolean","width":20},
				{"type":"column","dataField":"ChauffeurId","headerText":"Chauffeur","width":150},
				{"type":"column","dataField":"VehiculeId","headerText":"Véhicule","width":150},
				{"type":"column","width":0}
		]}
	]}
]
,
"actions":[
]}
}


