{"form":{"type":"VBox","id":"StockLogistique/DestockList","label":"Déstockage","percentHeight":100,
"setStyle":{"paddingLeft":5,"paddingRight":5},"clipContent":0,
"localProxy":1,
"components":[
	{"type":"MenuTab","id":"menuList", "menuItems":[
		{"label":"File", "children":[
			{"label":"Sauver", "icon":"save", "data":"save"},
			{"type":"vseparator"},
			{"label":"Rafraîchir", "icon":"iconNew", "data":"refresh"}
		]}
	]},
	{"type":"HBox","id":"listBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
		{"type":"EditContainer", "id":"searchBox", "width":180, "percentHeight":100,
		"components":[
			{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"verticalGap":0},
			"components":[
				{"type":"Label","text":"Livraison"},
				{"type":"HGroup","gap":2,"components":[
					{"type":"DateField","dataField":"debut","dataGroup":"searchGroup"},
					{"type":"Spacer","width":2},
					{"type":"DateField","dataField":"fin","dataGroup":"searchGroup"}
				]},
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
		"kobeyeClass":{"module":"StockLogistique","objectClass":"Destockage"},"hierarchical":1,"updatedItems":1,
		"events":[
			{"type":"start", "action":"invoke","method":"callMethod",
			"params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},
			"function":"GetDestockage","args":[{"dataValue":["debut","fin","magasin","cp","ville","client"]}]}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"callMethod",
				"params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},
				"function":"SaveDestockage","args":[{"dataValue":["dataGrid"]}]}},
				{"trigger":"refresh","action":"invoke","method":"restart"},
				{"trigger":"clear","action":"invoke","method":"restart"},
				{"trigger":"searchGroup","action":"invoke","method":"restart"}
			]}
		],
		"columns":[
			{"type":"column","dataField":"DateLivraison","headerText":"Livraison","format":"date","width":90},
			{"type":"column","dataField":"Destockage","headerText":"D","format":"checkbox","width":20},
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
//	{"type":"close", "action":"confirmUpdate"}
]}
}


