{"form":{"type":"TitleWindow","id":"Facturation","title":"Facturation des Devis",
"width":400,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},"localProxy":1,
	"components":[
		{"type":"EditContainer", "id":"edit",
		"components":[
			{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":8,"paddingLeft":6,"paddingRight":6,"paddingTop":6,"paddingBottom":6},"percentWidth":100,
			"components":[
				{"type":"FormItem","label":"Date de facturation","percentWidth":100,"components":[
					{"type":"DateField","dataField":"date"}
				]},
				{"type":"Spacer","height":10},
				{"type":"HGroup","percentWidth":100,"components":[
					{"type":"Spacer"},
					{"type":"Button","label":"Facturer","id":"ok","events":[
						{"type":"click","action":"invoke","method":"callMethod",
						"params":{"method":"object","data":{"module":"Devis","objectClass":"DevisTete"},"function":"CreateInvoices",
						"args":[{"values":[[!_arg0!]]},{"dataValue":["date"]}]}
					]},
					{"type":"Button","label":"Annuler","id":"cancel","events":[
						{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
					]},
					{"type":"Spacer"}
				]}
			]}
		]}
	]}
],
"popup":"modal"
}}
