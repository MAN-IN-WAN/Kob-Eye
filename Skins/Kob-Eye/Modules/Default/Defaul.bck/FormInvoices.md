{"form":{"type":"TitleWindow","id":"CreateInvoices","title":"Create invoices",
//"kobeyeClass":{"module":"Murphy","objectClass":"Enquiry"},
"localProxy":1,
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"backgroundColor":"#dcdcdc","paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
		"components":[
			{"type":"HBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":0,"horizontalGap":2},
			"components":[
				{"type":"VBox","width":180,"percentHeight":100,"setStyle":{"backgroundColor":"#dcdcdc"},
				"components":[	
					{"type":"Panel","title":"Product","layout":{"type":"VerticalLayout"},"dividerVisible":0,"titleHeight":20,"width":180,
					"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
					"components":[
						{"type":"LabelItem","label":"Loading date","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
							{"type":"DateInterval","dataField":"Date","width":110}
						]},
						{"type":"LabelItem","label":"Contract","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
							{"type":"TextInput","dataField":"Contract","width":110}
						]},
						{"type":"LabelItem","label":"Supplier","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
							{"type":"TextInput","dataField":"Supplier","percentWidth":100,"dataGroup":"searchGroup"}
						]},
						{"type":"LabelItem","label":"Buyer","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
							{"type":"TextInput","dataField":"Buyer","percentWidth":100,"dataGroup":"searchGroup"}
						]},
						{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
							{"type":"Spacer","percentWidth":100},
							{"type":"Button","label":"$__Clear__$","id":"clear","width":80}
						]}
					]},
					{"type":"Spacer","percentHeight":100},
					{"type":"HGroup","percentWidth":100,
					"components":[
						{"type":"Button","id":"send","label":"$__Confirm__$","width":80},
						{"type":"Button","id":"cancel","label":"$__Cancel__$","width":80,
						"events":[
							{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
					]}	
				]},
				{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","minHeight":450,"minWidth":490,"percentWidth":100,"percentHeight":100,
				"kobeyeClass":{"dirtyParent":1,"module":"Murphy","objectClass":"Shipment"},"changeEvent":1,
				"events":[
					{"type":"start", "action":"loadValues"},
					{"type":"proxy", "triggers":[
						{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}}
					]}
				],
				"columns":[
					{"type":"column","dataField":"Id","headerText":"ID","visible":0},
					{"type":"column","dataField":"Retained","headerText":"B","format":"boolean","width":20,"extra":"iconRed"},
					{"type":"column","dataField":"LoadingDate","headerText":"Loading","format":"date","width":60},
					{"type":"column","dataField":"DeliveryDate","headerText":"Delivery","format":"date","width":60},
					{"type":"column","dataField":"Volume","headerText":"Volume","format":"0dec","width":80},
					{"type":"column","dataField":"SupplierContract","headerText":"Supl contract","format":"","width":100},
					{"type":"column","dataField":"SupplierInvoice","headerText":"Supl invoice","format":"","width":100},
					{"type":"column","dataField":"Supplier","headerText":"Supplier","format":"","width":150},
					{"type":"column","dataField":"Buyer","headerText":"Buyer","format":"","width":150},
					{"type":"column","dataField":"Varietal","headerText":"Varietal","format":"","width":100},
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
			"params":{"method":"object","data":{"module":"Murphy","objectClass":"Invoice"},
			"function":"CreateInvoices","selectionRequired":1,"args":[{"selectedValues":["dataGrid"]}],"closeForm":1}}
		]}
	]}
],
"popup":"modal"
}}
