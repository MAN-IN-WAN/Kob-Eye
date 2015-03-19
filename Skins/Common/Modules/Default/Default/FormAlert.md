[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"TitleWindow","id":"FormAlert","title":"$__Alerts__$",
"kobeyeClass":{"module":"Systeme","objectClass":"AlertUser"},
"localProxy":1,
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0},
		"components":[
			{"type":"HBox","percentWidth":100,
			"components":[
				{"type":"IconButton","label":"$__All read__$","icone":"read","id":"readAll"},
				{"type":"IconButton","label":"$__All done__$","icone":"done","id":"checkAll"}
			]},
			{"type":"AdvancedDataGrid","id":"choice","dataField":"choice","minHeight":450,"minWidth":490,"percentWidth":100,"percentHeight":100,
			"updatedItems":1,"changeEvent":1,
			"kobeyeClass":{"module":"Systeme","objectClass":"AlertUser","form":"FormBase.json"},
			"events":[
				{"type":"start","action":"loadValues"},
				{"type":"change","action":"invoke","method":"invoke","method":"callMethod",
				"params":{"method":"query","data":{"module":"Systeme","objectClass":"AlertUser"},
				"function":"MarkAsRead","currentSelection":1}},
				{"type":"edit","action":"invoke","method":"invoke","method":"callMethod",
				"params":{"method":"query","data":{"module":"Systeme","objectClass":"AlertUser"},
				"function":"MarkAsDone","currentSelection":1}},
				{"type":"dblclick","action":"invoke","method":"findFormWithItem","params":{"field":"Tag","closeForm":1}},
				{"type":"proxy", "triggers":[
					{"trigger":"readAll","action":"invoke","method":"callMethod",
					"params":{"method":"object","data":{"module":"Systeme","objectClass":"AlertUser"},
					"function":"MarkAllRead"}},
					{"trigger":"checkAll","action":"invoke","method":"callMethod",
					"params":{"method":"object","data":{"module":"Systeme","objectClass":"AlertUser"},
					"function":"MarkAllDone","closeForm":1}}
				]}
			],
			"columns":[
				{"type":"column","dataField":"Icon","headerText":".","width":20,"format":"image"},
				{"type":"column","dataField":"Read","headerText":"$__R__$","width":20,"format":"boolean"},
				{"type":"column","dataField":"Done","headerText":"$__D__$","width":20,"format":"checkbox"},
				{"type":"column","dataField":"Date","headerText":"$__Date__$","format":"time","width":84},
				{"type":"column","dataField":"Title","headerText":"$__Title__$","width":400},
				{"type":"column","dataField":"Author","headerText":"$__Author__$","width":120},
				{"type":"column","dataField":"Id","visible":0},
				{"type":"column","width":0}
			]}
		]}
	]}
],
"popup":"modal"
}}
