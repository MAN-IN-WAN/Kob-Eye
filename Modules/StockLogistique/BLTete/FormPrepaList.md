{"form":{"type":"VBox","id":"StockLogistique/PrepaList","label":"Préparation","percentHeight":100,
"setStyle":{"paddingLeft":5,"paddingRight":5},"clipContent":0,
"localProxy":1,
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"menuItems":[
		{"children":[
			{"label":"Ouvrir", "icon":"open", "data":"open"},
			{"type":"vseparator"},
			{"label":"Rafraîchir", "icon":"refresh", "data":"refresh"}
		]}
	]},

	{"type":"HBox","id":"listBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
		{"type":"EditContainer", "id":"searchBox", "width":180, "percentHeight":100,
		"components":[
			{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"verticalGap":0},
			"components":[
				{"type":"Label","text":"Livraison"},
				{"type":"DateInterval","dataField":"livraison","dataGroup":"searchGroup"},
//				{"type":"HGroup","gap":2,"components":[
//					{"type":"DateField","dataField":"debut","dataGroup":"searchGroup"},
//					{"type":"Spacer","width":2},
//					{"type":"DateField","dataField":"fin","dataGroup":"searchGroup"}
//				]},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"Magasin"},
				{"type":"TextInput","dataField":"magasin","percentWidth":100,"dataGroup":"searchGroup"},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"CP"},
				{"type":"TextInput","dataField":"cp","percentWidth":100,"dataGroup":"searchGroup"},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"Ville"},
				{"type":"TextInput","dataField":"ville","percentWidth":100,"dataGroup":"searchGroup"},
				{"type":"Spacer","height":6},
				{"type":"Label","text":"Client"},
				{"type":"TextInput","dataField":"client","percentWidth":100,"dataGroup":"searchGroup"},
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
		"kobeyeClass":{"module":"StockLogistique","objectClass":"BLTete","form":"FormPrepa.json"},"hierarchical":1,
		"getDataFunction":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},
			"function":"GetLivrList","args":[{"value":[1,"L"]},{"filterValue":["livraison","magasin","cp","ville","client"]}]},
		"events":[
			{"type":"start","action":"loadValues"},
//			{"type":"start", "action":"invoke","method":"callMethod",
//			"params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},
//			"function":"GetLivraison","args":[{"value":[1,"L"]},{"filterValue":["debut","fin","magasin","cp","ville","client"]}]}},
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
			{"type":"proxy","triggers":[
				{"trigger":"refresh","action":"invoke","method":"restart"},
				{"trigger":"searchGroup","action":"invoke","method":"restart"},
				{"trigger":"open","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}}
			]}
		],
		"columns":[
				{"type":"column","dataField":"Reference","headerText":"Numéro","width":85},
				{"type":"column","dataField":"DateLivraison","headerText":"Livraison","format":"date","width":60},
				{"type":"column","dataField":"Prepare","headerText":"P","format":"boolean","width":20},
				{"type":"column","dataField":"Tournee","headerText":"T","format":"boolean","width":20},
				{"type":"column","dataField":"LivraisonId","headerText":"Magasin","width":130},
				{"type":"column","dataField":"CodPostal","headerText":"CP","width":50},
				{"type":"column","dataField":"Ville","headerText":"Ville","width":100},
				{"type":"column","dataField":"ClientId","headerText":"Client","width":100},
				{"type":"column","dataField":"Famille","headerText":"Famille","width":80},
				{"type":"column","dataField":"Designation","headerText":"Désignation","width":150},
				{"type":"column","dataField":"Quantite","headerText":"Qté","width":50,"format":"int"},
				{"type":"column","dataField":"Id","visible":0},
				{"type":"column","width":0}
		]}
	]}
]
,
"actions":[
]}
}


