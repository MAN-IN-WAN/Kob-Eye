{"form":{"type":"TitleWindow","id":"JournalVentes","title":"Journal des Ventes",
"width":400,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},"localProxy":1,
	"components":[
		{"type":"EditContainer", "id":"edit",
		"components":[
			{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":8,"paddingLeft":6,"paddingRight":6,"paddingTop":6,"paddingBottom":6},"percentWidth":100,
			"components":[
				{"type":"FormItem","label":"Date de début","percentWidth":100,"components":[
					{"type":"DateField","dataField":"begin","defaultValue":"First"}
				]},
				{"type":"FormItem","label":"Date de fin","percentWidth":100,"components":[
					{"type":"DateField","dataField":"finish","defaultValue":"Last"}
				]},
				{"type":"FormItem","label":"Société","percentWidth":100,"components":[
					{"type":"ComboBox","dataField":"societe","width":80,"requireSelection":1,"dataProvider":[
						{"data":"L","label":"Locanim"},
						{"data":"B","label":"Bopi"}
					]}
				]},
				{"type":"Spacer","height":10},
				{"type":"HGroup","percentWidth":100,"components":[
					{"type":"Spacer"},
					{"type":"Button","label":"Imprimer","id":"ok","events":[
						{"type":"click","action":[
							{"action":"invoke","method":"callMethod",
							"params":{"method":"object","data":{"module":"Devis","objectClass":"FactureTete"},"function":"SalesBook",
							"args":[{"dataValue":["begin","finish","societe"]}]}},
							{"action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
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
