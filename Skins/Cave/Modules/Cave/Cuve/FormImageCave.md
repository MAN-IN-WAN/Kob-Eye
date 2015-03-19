{"form":{"type":"GradientVBox","id":"FormImageCave","label":"Image de la Cave","percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5[!color!]},"clipContent":0,"tabColor":"0x999999",
"kobeyeClass":{"module":"Cave","objectClass":"Cuve"},
"localProxy":1,
"components":[
	{"type":"EditContainer","percentHeight":100, "id":"edit",
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"minHeight":0,"setStyle":{"paddingTop":0,"verticalGap":2},
		"components":[
			{"type":"HBox","percentWidth":100,"setStyle":{"backgroundColor":"silver","paddingTop":6,"paddingBottom":4},
			"components":[
				{"type":"FormItem","label":"Date - Heure","labelWidth":80,"width":300,"components":[
					{"type":"DateTimeField","dataField":"Date","validType":"date","defaultValue":"Now","startingHour":7,"increment":10}
				]},
				{"type":"Button","label":"Rechercher","id":"run"},
				{"type":"Button","label":"Imprimer","events":[
					{"type":"click","action":"invoke","method":"callMethod","params":{"method":"object","function":"PrintImageCave","args":"dv:Date"}}
				]},
				{"type":"Button","label":"Fermer","id":"close"}
			]},
			{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1,
			"kobeyeClass":{"module":"Cave","objectClass":"Cuve"},
			"events":[
				{"type":"start","action":"invoke","method":"callMethod","params":{"method":"object","function":"ImageCave","args":"dv:Date"}},
				{"type":"proxy","triggers":[
					{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
					{"trigger":"run","action":"invoke","method":"restart"}
				]}
			],
			"columns":[
				{"type":"column","dataField":"Cuve","headerText":"Cuve","format":"","width":40},
				{"type":"column","dataField":"Capacite","headerText":"Capacité","format":"3dec","width":60},
				{"type":"column","dataField":"Volume","headerText":"Volume","format":"3dec","width":60},
				{"type":"column","dataField":"Opération","headerText":"Date","format":"time","width":84},
				{"type":"column","dataField":"Type","headerText":"Type","format":"","width":70},
				{"type":"column","dataField":"SousType","headerText":"Sous-type","format":"","width":90},
				{"type":"column","dataField":"VolumeOperation","headerText":"Vol. op.","format":"3dec","width":60},
				{"type":"column","dataField":"Lot","headerText":"Lot","format":"","width":60},
				{"type":"column","dataField":"Categorie","headerText":"Catégorie","format":"","width":150},
				{"type":"column","dataField":"Couleur","headerText":"Couleur","format":"","width":60},
				{"type":"column","dataField":"Degre","headerText":"D°","format":"2dec","width":40},
				{"type":"column","width":0}
			]}
		]}
	]}
]}
}
