{"form":
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"localProxy":{
		"actions":{
			"famille":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
			"begin":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
			"finish":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
			"Disponibilite":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"CommandeTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish","Disponibilite"]}]}}
		}
	},
	"components":[
		{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"Group","percentWidth":100,"percentHeight":100,"layout":{"type":"VerticalLayout","gap":2,"paddingLeft":6,"paddingRight":6,"paddingTop":4,"paddingBottom":4},
			"components":[
				{"type":"Panel","topVisible":0,"setStyle":{"dropShadowVisible":0},
				"components":[
					{"type":"Group","percentWidth":100,"layout":{"type":"HorizontalLayout","verticalAlign":"baseline","gap":4,"paddingLeft":6,"paddingRight":6,"paddingTop":2,"paddingBottom":2},
					"components":[
						{"type":"Label","text":"Famille"},
						{"type":"ComboBox","dataField":"famille","width":150,
						"kobeyeClass":{"module":"StockLocatif","objectClass":"Famille","identifier":"Id","label":"Famille"},
						"events":[
							{"type":"init","action":"loadData"}
						]},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Début"},
						{"type":"DateField","dataField":"begin"},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Fin"},
						{"type":"DateField","dataField":"finish"},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Stock"},
						{"type":"TextInput","dataField":"Stock","width":40,"editable":0},
						{"type":"Spacer","width":10},
						{"type":"Label","text":"Disponible"},
						{"type":"TextInput","dataField":"Disponible","width":40,"editable":0,"setStyle":{"color":"#00a000","fontWeight":"bold"}},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Réservé"},
						{"type":"TextInput","dataField":"Reserve","width":40,"editable":0,"setStyle":{"color":"blue","fontWeight":"bold"}},
						{"type":"Spacer","width":10},
						{"type":"Label","text":"Jour"},
						{"type":"DateField","dataField":"Jour","editable":0,"setStyle":{"arrowButtonWidth":0}},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Disponible"},
						{"type":"TextInput","dataField":"DisponibleJ","width":40,"editable":0,"setStyle":{"color":"#00a000"}},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Réservé"},
						{"type":"TextInput","dataField":"ReserveJ","width":40,"editable":0,"setStyle":{"color":"red"}}
					]}
				]},
//				{"type":"HGroup","percentWidth":100,"percentHeight":100,
//				"components":[
				{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
				"components":[							
					{"type":"Disponibilite","dataField":"Disponibilite","percentWidth":100,"percentHeight":100},
					{"type":"AdvancedDataGrid","dataField":"Reservations",
					"width":570,"percentHeight":100,"rowHeight":20,"variableRowHeight":1, 
					"kobeyeClass":{"module":"StockLogistique","objectClass":"CommandeTete"},
					"events":[
//						{"type":"start", "action":"invoke","method":"callMethod","params":{"method":"object",
//						"function":"GetReservation","args":[{"dataValue":["famille","begin","finish","Disponibilite"]}]}},
//						{"type":"proxy","triggers":[
//							{"trigger":"ok","action":"invoke","method":"callMethod","params":{"method":"object",
//							"function":"GetReservation","args":[{"dataValue":["famille","begin","finish"]},{"value":[""]}]}},
//							{"trigger":"Disponibilite","action":"invoke","method":"callMethod","params":{"method":"object",
//							"function":"GetReservation","args":[{"dataValue":["famille","begin","finish","Disponibilite"]}]}}
//						]}
					],
					"columns":[
						{"type":"column","dataField":"Livre","headerText":"L","format":"boolean","width":20,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"DateLivraison","headerText":"Départ","format":"date","width":55,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"DateReprise","headerText":"Retour","format":"date","width":55,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Livraison","headerText":"Magasin","width":150,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"CodPostal","headerText":"CP","width":40,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Ville","headerText":"Ville","width":100,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Client","headerText":"Client","width":120,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Quantite","headerText":"Qté","width":30,"format":"0dec","setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Id","visible":0},
						{"type":"column","width":0}
					]}
				]}
			]}
		]}
	]}
}
