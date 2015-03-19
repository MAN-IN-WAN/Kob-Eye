[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"TitleWindow","id":"FormSampleReq:[!I::TypeChild!]","title":"Sample request","minHeight":450,
"kobeyeClass":{"module":"Murphy","objectClass":"[!I::TypeChild!]"},
"localProxy":1,
"components":[
	{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
	"components":[
		{"type":"EditContainer","percentHeight":100, "id":"edit",
		"components":[
			{"type":"TextInput","dataField":"CountryId","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"VarietalId","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"ColourId","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"AppellationId","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"Vintage","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"FiltrationId","includeInLayout":0,"visible":0,"editable":0},
			{"type":"HGroup","percentWidth":100,"percentHeight":100,"gap":4,
			"components":[
				{"type":"Panel","title":"Sample","width":350,"percentHeight":100,"layout":{"type":"VerticalLayout","gap":2},"titleHeight":20,"dividerVisible":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
				"components":[
					[SWITCH [!I::TypeChild!]]
					[CASE Enquiry]
					{"type":"FormItem","percentLabel":28,"label":"Enquiry","percentWidth":100,"components":[
						{"type":"DataItem","percentWidth":100 ,"displayFields":[{"name":"Reference","description":"Reference"}],
						"keyType":"field","dataField":"SampleRequestEnquiryId",
						"kobeyeClass":{"module":"Murphy","objectClass":"Enquiry",
						"select":["Id","Reference"]},"noControl":1}
					]},
					[/CASE]
					[CASE Proposal]
					{"type":"FormItem","percentLabel":28,"label":"Proposal","percentWidth":100,"components":[
						{"type":"DataItem","percentWidth":100 ,"displayFields":[{"name":"Reference","description":"Reference"}],
						"keyType":"field","dataField":"SampleRequestProposalId",
						"kobeyeClass":{"module":"Murphy","objectClass":"Proposal",
						"select":["Id","Reference"]},"noControl":1}
					]},
					[/CASE]
					[CASE Contract]
					{"type":"FormItem","percentLabel":28,"label":"Contract","percentWidth":100,"components":[
						{"type":"DataItem","percentWidth":100 ,"displayFields":[{"name":"Reference","description":"Reference"}],
						"keyType":"field","dataField":"SampleRequestContractId",
						"kobeyeClass":{"module":"Murphy","objectClass":"Contract",
						"select":["Id","Reference"]},"noControl":1}
					]},
					[/CASE]
					[/SWITCH]
					{"type":"FormItem","percentLabel":28,"label":"Buyer","percentWidth":100,"components":[
						{"type":"DataItem","percentWidth":100 ,"displayFields":[{"name":"Company","description":"Company"}],
						"keyType":"field","dataField":"SampleRequestBuyerId",
						"kobeyeClass":{"module":"Murphy","objectClass":"Third",
						"select":["Id","Company"]},"noControl":1}
					]},
					{"type":"FormItem","percentLabel":28,"label":"Status","percentWidth":100,"components":[
						{"type":"ComboBox","dataField":"Status","percentWidth":100,"defaultValue":61,
						"kobeyeClass":{"module":"Murphy","objectClass":"Status","query":"Murphy/Status/Type=6","identifier":"Id","label":"Status"},
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"FormItem","percentLabel":28,"label":"Date","percentWidth":100,"components":[
						{"type":"DateField","dataField":"Date","validType":"date" ,"defaultValue":"Now"}
					]},
					{"type":"FormItem","percentLabel":28,"label":"Buyer ref","percentWidth":100,"components":[
						{"type":"TextInput","dataField":"BuyerRef","percentWidth":100,"validType":"string" }
					]},
					{"type":"FormItem","percentLabel":28,"label":"Dead line","percentWidth":100,"components":[
						{"type":"DateField","dataField":"DeadLine","validType":"date" }
					]},
					{"type":"FormItem","percentLabel":28,"label":"Alert","percentWidth":100,"components":[
						{"type":"DateField","dataField":"Alert","validType":"date" }
					]},
					{"type":"FormItem","percentLabel":28,"label":"Purpose","percentWidth":100,"components":[
						{"type":"ComboBox","dataField":"Purpose","percentWidth":100,"defaultValue":1,
						"kobeyeClass":{"module":"Murphy","objectClass":"SamplePurpose","query":"Murphy/SamplePurpose","identifier":"Id","label":"Purpose"},
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"FormItem","percentLabel":28,"label":"Delivery","percentWidth":100,"components":[
						{"type":"ComboBox","dataField":"Delivery","percentWidth":100 ,
						"kobeyeClass":{"module":"Murphy","objectClass":"SampleDelivery","query":"Murphy/SampleDelivery","identifier":"Id","label":"Delivery"},
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]},
					{"type":"Spacer","percentHeight":100},
					{"type":"HGroup","percentWidth":100,
					"components":[
						{"type":"Button","id":"save","label":"$__Save__$","width":100},
						{"type":"Button","id":"cancel","label":"$__Cancel__$","width":100,
						"events":[
							{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
						]}
					]}	
				]},
				{"type":"VGroup","percentWidth":100,"percentHeight":100,"gap":2,
				"components":[
					{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
					"components":[
						{"type":"ImageButton","id":"new:1","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
						{"type":"ImageButton","id":"delete:1","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1}
					]},
					{"type":"AdvancedDataGrid","dataField":"samples","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
					"kobeyeClass":{"module":"Murphy","objectClass":"SampleWine","form":"FormSampleWine.json"},
					"events":[
						{"type":"proxy", "triggers":[
							{"trigger":"new:1","action":"loadForm",
							"params":{"kobeyeClass":{"dirtyParent":1,"objectClass":"SampleWine","form":"FormSampleWine.json",
							"proxyValues":{"vars":{
								"CountryId":{"args":"dv:CountryId"},
								"VarietalId":{"args":"dv:VarietalId"},
								"ColourId":{"args":"dv:ColourId"},
								"AppellationId":{"args":"dv:AppellationId"},
								"Vintage":{"args":"dv:Vintage"},
								"FiltrationId":{"args":"dv:FiltrationId"}
							}}}}},
							{"trigger":"delete:1","action":"invoke","method":"deleteItem"}
						]}
					],
					"columns":[
						{"type":"column","dataField":"Id","headerText":"ID","visible":0},
						{"type":"column","dataField":"Acronym","headerText":"Cy","format":"image","width":20},
						{"type":"column","dataField":"Varietal","headerText":"Varietal","width":150},
						{"type":"column","dataField":"ColourIcon","headerText":"Cr","format":"image","width":20},
						{"type":"column","dataField":"Appellation","headerText":"Appellation","width":100},
						{"type":"column","dataField":"Vintage","headerText":"Vintage","width":50},
						{"type":"column","dataField":"Filtration","headerText":"Filtration","width":100},
						{"type":"column","dataField":"Bottles","headerText":"Bottles","format":"0dec","width":40},
						{"type":"column","width":0}
					]},
					{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
					"components":[
						{"type":"ImageButton","id":"new:2","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
						{"type":"ImageButton","id":"delete:2","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1}
					]},
					{"type":"AdvancedDataGrid","dataField":"suppliers","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
					"kobeyeClass":{"module":"Murphy","parentClass":"Third","form":"PopupList.json"},
					"events":[
						{"type":"proxy", "triggers":[
							{"trigger":"new:2","action":"invoke","method":"createItemFromPopup"},
							{"trigger":"delete:2","action":"invoke","method":"deleteItem"}
						]}
					],
					"columns":[
						{"type":"column","dataField":"Id","headerText":"ID","visible":0},
						{"type":"column","dataField":"Company","headerText":"Supplier","width":300},
						{"type":"column","dataField":"Acronym","headerText":"C","format":"image","width":20},
						{"type":"column","dataField":"Town","headerText":"Town","width":150},
						{"type":"column","width":0}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"invoke","method":"callMethod",
			"params":{"method":"query","data":{"dirtyChild":1,"module":"Murphy"},
			"function":"NewSampleRequest"}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"callMethod",
				"params":{"method":"object","data":{"module":"Murphy","objectClass":"SampleRequest"},
				"function":"saveSampleRequest","args":"dv:*","closeForm":1}}
			]}
		]}
	]}
],
"popup":"modal"
}}