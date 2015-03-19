{"form":
[IF [!MDI!]]
{"type":"MDIWindow","id":"FormDispo","title":"Disponibilité", 
"height":650,"width":1100,"popup":"free",
"localProxy":{
	"actions":{
		"famille":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
		"begin":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
		"finish":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
		"Disponibilite":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish","Disponibilite"]}]}}
	}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
[ELSE]
	{"type":"VBox","id":"FormDispo","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"localProxy":{
		"actions":{
			"famille":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
			"begin":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
			"finish":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish"]},{"value":[0]}]}},
			"Disponibilite":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"Disponibilite","args":[{"dataValue":["famille","begin","finish","Disponibilite"]}]}}
		}
	},
[/IF]
	"components":[
		{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"Group","percentWidth":100,"percentHeight":100,"layout":{"type":"VerticalLayout","gap":2,"paddingLeft":6,"paddingRight":6,"paddingTop":4,"paddingBottom":4},
			"components":[
				{"type":"Panel","topVisible":0,"setStyle":{"dropShadowVisible":0},
				"components":[
					{"type":"Group","percentWidth":100,"layout":{"type":"HorizontalLayout","verticalAlign":"baseline","gap":4,"paddingLeft":0,"paddingRight":6,"paddingTop":2,"paddingBottom":2},
					"components":[
						{"type":"Label","text":"Famille"},
						{"type":"ComboBox","dataField":"famille","width":150,
						"kobeyeClass":{"module":"StockLocatif","objectClass":"Famille","identifier":"Id","label":"Famille"},
						"events":[
							{"type":"init","action":"loadData"}
						]},
						{"type":"Spacer","width":0},
						{"type":"Label","text":"Début"},
						{"type":"DateField","dataField":"begin","displayYear":0},
						{"type":"Spacer","width":0},
						{"type":"Label","text":"Fin"},
						{"type":"DateField","dataField":"finish","displayYear":0},
						{"type":"Spacer","width":10},
						{"type":"Label","text":"Stock"},
						{"type":"TextInput","dataField":"Stock","width":40,"editable":0},
						{"type":"Spacer","width":0},
						{"type":"Label","text":"Disponible","setStyle":{"fontWeight":"bold"}},
						{"type":"TextInput","dataField":"Disponible","width":40,"editable":0,"setStyle":{"color":"#00a000","fontWeight":"bold","fontSize":14}},
						{"type":"Spacer","width":0},
						{"type":"Label","text":"Réservé","setStyle":{"fontWeight":"bold"}},
						{"type":"TextInput","dataField":"Reserve","width":40,"editable":0,"setStyle":{"color":"blue","fontWeight":"bold","fontSize":14}},
						{"type":"Spacer","width":10},
						{"type":"Label","text":"Jour"},
						{"type":"DateField","dataField":"Jour","displayYear":0,"editable":0,"setStyle":{"arrowButtonWidth":0}},
						{"type":"Spacer","width":0},
						{"type":"Label","text":"Disponible"},
						{"type":"TextInput","dataField":"DisponibleJ","width":40,"editable":0,"setStyle":{"color":"#00a000"}},
						{"type":"Spacer","width":0},
						{"type":"Label","text":"Réservé"},
						{"type":"TextInput","dataField":"ReserveJ","width":40,"editable":0,"setStyle":{"color":"blue"}}
					]}
				]},
//				{"type":"HGroup","percentWidth":100,"percentHeight":100,
//				"components":[
				{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
				"components":[							
					{"type":"LocDispo","dataField":"Disponibilite","percentWidth":100,"percentHeight":100},
					{"type":"AdvancedDataGrid","dataField":"Reservations",
					"width":645,"percentHeight":100,"rowHeight":20,"variableRowHeight":1, 
					"kobeyeClass":{"module":"StockLogistique","objectClass":"BLTete"},
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
						{"type":"column","dataField":"Quantite","headerText":"Qté","width":30,"format":"0dec","setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Confirme","headerText":"C","format":"boolean","width":20,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Livre","headerText":"L","format":"boolean","width":20,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Depart","headerText":"Départ","format":"date","width":55,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Retour","headerText":"Retour","format":"date","width":55,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Livraison","headerText":"Magasin","width":150,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"CodPostal","headerText":"CP","width":40,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Ville","headerText":"Ville","width":100,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Client","headerText":"Client","width":120,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Reference","headerText":"Devis","width":56,"setStyle":{"paddingLeft":1,"paddingRight":1}},
						{"type":"column","dataField":"Id","visible":0},
						{"type":"column","width":0}
					]}
				]}
			]}
		]}
	]}
[IF [!MDI!]]
]}
[/IF]
}
