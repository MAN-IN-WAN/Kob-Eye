{"form":{"type":"VBox","id":"StockLogistique/Reprise","label":"Retour en stock",
"percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0,"paddingLeft":5,"paddingRight":5},
"kobeyeClass":{"module":"StockLogistique","objectClass":"Tournee"},"localProxy":1,
"components":[
	{"type":"MenuTab","id":"menuList", "menuItems":[
		{"label":"$__File__$", "children":[
			{"label":"Sauver", "icon":"save", "data":"save"},
			{"type":"vseparator"},
			{"label":"Rafraîchir", "icon":"", "data":"refresh"}
		]}
	],
	"actions":[
		{"type":"itemClick","actions":{
			"save":{
				"action":"invoke","method":"callMethod","params":{
				"confirm":{"text":"Confirmer l'enregistrement"},
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee"},
				"function":"SaveReprise","args":[{"dataValue":["elements"]}]}
			}
		}}
	]},
	{"type":"Box","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0}, 
	"components":[
		{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"AdvancedDataGrid","dataField":"elements","id":"elements","updatedItems":1,
			"percentHeight":100,"width":470,
			"kobeyeClass":{"module":"StockLogistique","objectClass":"Reprise"},
			"events":[
				{"type":"start","action":"invoke","method":"callMethod",
				"params":{"method":"object","function":"GetReprise","data":{"module":"StockLogistique","objectClass":"Tournee"},"args":[]}},
				{"type":"proxy","triggers":[
					{"trigger":"refresh","action":"invoke","method":"restart"}
				]}
			],
			"columns":[
				{"type":"column","dataField":"Reference","headerText":"Reference","width":150,"setStyle":{"paddingLeft":1,"paddingRight":1}},
				{"type":"column","dataField":"Panne","headerText":"Panne","format":"checkbox","width":50,"setStyle":{"paddingLeft":1,"paddingRight":1}},
				{"type":"column","dataField":"Stock","headerText":"Stock","format":"checkbox","width":50,"setStyle":{"paddingLeft":1,"paddingRight":1}},
				{"type":"column","dataField":"Commentaire","headerText":"Commentaire","width":220,"setStyle":{"paddingLeft":1,"paddingRight":1}},
				{"type":"column","dataField":"ReferenceId","visible":0},
				{"type":"column","dataField":"Id","visible":0},
				{"type":"column","width":0}
			]}
		]}
	]}
],
"actions":[
	{"type":"close","action":"confirmUpdate"}
]}
}
