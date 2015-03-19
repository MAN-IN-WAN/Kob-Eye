{"form":{"type":"TitleWindow","id":"FormProposal","title":"Supplier Proposal",
"kobeyeClass":{"module":"Murphy","objectClass":"Enquiry"},
"localProxy":1,
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"backgroundColor":"#dcdcdc","paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
		"components":[
			{"type":"HBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":0,"horizontalGap":2},
			"components":[
				{"type":"VBox","width":250,"percentHeight":100,
				"components":[	
					{"type":"Panel","title":"Product","layout":{"type":"VerticalLayout"},"dividerVisible":0,"titleHeight":20,
					"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
					"components":[
						{"type":"Form","setStyle":{"verticalGap":2,"paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":0},
						"components":[
							{"type":"FormItem","percentLabel":28,"label":"Dossier","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"TextInput","dataField":"Reference","width":110,"editable":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Country","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"ComboBox","dataField":"CountryWine","percentWidth":100,"dataGroup":"searchGroup",
								"kobeyeClass":{"module":"Murphy","objectClass":"Country","identifier":"Id","label":"Country","query":"Murphy/Country/WineProducer=1"},				
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Varietal","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"ComboBox","dataField":"Varietal","percentWidth":100,"dataGroup":"searchGroup",
								"kobeyeClass":{"module":"Murphy","objectClass":"Varietal","identifier":"Id","label":"Varietal","query":"Murphy/Varietal"},				
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Colour","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"ComboBox","dataField":"Colour","percentWidth":100,"dataGroup":"searchGroup",
								"kobeyeClass":{"module":"Murphy","objectClass":"Colour","identifier":"Id","label":"Colour","query":"Murphy/Colour"},				
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Appellation","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"ComboBox","dataField":"Appellation","percentWidth":100,"dataGroup":"searchGroup",
								"kobeyeClass":{"module":"Murphy","objectClass":"Appellation","identifier":"Id","label":"Appellation","query":"Murphy/Appellation"},				
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Vintage","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"TextInput","dataField":"Vintage","percentWidth":100,"validType":"string","dataGroup":"searchGroup"}
							]},
//							{"type":"FormItem","percentLabel":28,"label":"Quantity","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
//								{"type":"ComboBox","dataField":"Quantity","percentWidth":100,"dataGroup":"searchGroup",
//								"kobeyeClass":{"module":"Murphy","objectClass":"Quantity","identifier":"Id","label":"Quantity","query":"Murphy/Quantity"},				
//								"actions":[
//									{"type":"init","action":"loadData"}
//								]}
//							]},
//							{"type":"FormItem","percentLabel":28,"label":"Filtration","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
//								{"type":"ComboBox","dataField":"Filtration","percentWidth":100,"dataGroup":"searchGroup",
//								"kobeyeClass":{"module":"Murphy","objectClass":"Filtration","identifier":"Id","label":"Filtration","query":"Murphy/Filtration"},				
//								"actions":[
//									{"type":"init","action":"loadData"}
//								]}
//							]},
							{"type":"FormItem","percentLabel":28,"label":"","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"CheckBox","dataField":"Filter","label":"Filter on product","dataGroup":"searchGroup"}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Supplier","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"TextInput","dataField":"Supplier","percentWidth":100,"dataGroup":"searchGroup"}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Country","percentWidth":100,"setStyle":{"indicatorGap":4},"components":[
								{"type":"TextInput","dataField":"Country","percentWidth":100,"dataGroup":"searchGroup"}
							]}
						]},
						{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
							{"type":"Spacer","percentWidth":100},
							{"type":"Button","label":"$__Clear__$","id":"clearFilter","width":80}
						]}
					]},
					{"type":"Spacer","percentHeight":100},
					{"type":"HGroup","percentWidth":100,
					"components":[
						{"type":"Button","id":"send","label":"$__Send__$","width":100},
						{"type":"Button","id":"cancel","label":"$__Cancel__$","width":100,
						"events":[
							{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
					]}	
				]},
				{"type":"AdvancedDataGrid","id":"choice","dataField":"choice","minHeight":450,"minWidth":490,"percentWidth":100,"percentHeight":100,"updatedItems":1,
				"kobeyeClass":{"module":"Murphy","objectClass":"Enquiry"},"changeEvent":1,
				"getDataFunction":{"method":"object","data":{"module":"Murphy","objectClass":"Enquiry"},
				"function":"GetSuppliers","args":[{"dataValue":["Filter","CountryWine","Varietal","Colour","Appellation","Vintage","Supplier","Country"]}]},
				"events":[
					{"type":"start", "action":"loadValues"},
//					{"type":"start", "action":"invoke","method":"callMethod",
//					"params":{"method":"object","data":{"module":"Murphy","objectClass":"Enquiry"},
//					"function":"GetSuppliers","args":[{"dataValue":["Filter","CountryWine","Varietal","Colour","Appellation","Vintage","Supplier","Country"]}]}},
					{"type":"dblclick","action":"invoke","objectID":"right","method":"move"},
					{"type":"proxy", "triggers":[
						{"trigger":"searchGroup","action":"invoke","method":"restart"}
					]}
				],
				"columns":[
					{"type":"column","dataField":"Company","headerText":"Company","width":150},
					{"type":"column","dataField":"Town","headerText":"Town","width":100},
					{"type":"column","dataField":"Country","headerText":"Country","width":100},
					{"type":"column","dataField":"Contact","headerText":"Contact","width":100},
					{"type":"column","dataField":"Mail","headerText":"@","width":20,"format":"boolean","setStyle":{"paddingLeft":1,"paddingRight":1}},
					{"type":"column","dataField":"Id","visible":0},
					{"type":"column","width":0}
				]},
				{"type":"VBox","width":30,"percentHeight":100,"setStyle":{"paddingLeft":1,"paddingRight":1},
				"components":[
					{"type":"MoveItemButton","id":"right","width":30,"height":30,"cornerRadius":15,"image":"right","borderWidth":1,
					"params":{"origineID":"choice","destinations":[{"id":"selection"}]}},
					{"type":"MoveItemButton","id":"left","width":30,"height":30,"cornerRadius":15,"image":"left","borderWidth":1,
					"params":{"origineID":"selection","destinations":[{"id":"choice"}]}}
				]},
				{"type":"AdvancedDataGrid","id":"selection","dataField":"selection","minWidth":580,"percentWidth":100,"percentHeight":100,
				"kobeyeClass":{"module":"Murphy","objectClass":"Third"},
				"events":[
					{"type":"dblclick","action":"invoke","objectID":"left","method":"move"}
				],
				"columns":[
					{"type":"column","dataField":"Company","headerText":"Company","width":150},
					{"type":"column","dataField":"Town","headerText":"Town","width":100},
					{"type":"column","dataField":"Country","headerText":"Country","width":100},
					{"type":"column","dataField":"Contact","headerText":"Contact","width":100},
					{"type":"column","dataField":"Mail","headerText":"@","width":20,"format":"boolean","setStyle":{"paddingLeft":1,"paddingRight":1}},
					{"type":"column","dataField":"ShowBuyer","headerText":"Show buyer","width":80,"format":"checkbox"},
					{"type":"column","dataField":"Id","visible":0},
					{"type":"column","width":0}
				]}
			]}						
		]}
	],
	"events":[
		{"type":"start","action":"loadValues","params":{"needsId":1}},
		{"type":"proxy", "triggers":[
			{"trigger":"clear","action":"invoke","method":"clearData"},
			{"trigger":"send","action":"invoke","method":"callMethod",
			"params":{"method":"query","data":{"dirtyChild":1,"module":"Murphy","objectClass":"Enquiry"},
			"function":"SupplierProposal","args":[{"dataValue":["selection"]}],"closeForm":1}}
		]}
	]}
],
"popup":"modal"
}}
