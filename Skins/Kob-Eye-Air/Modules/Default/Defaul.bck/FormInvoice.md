{"form":{"type":"GradientVBox","id":"Invoice?","label":"Invoice","percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5},"clipContent":0,
"kobeyeClass":{"module":"Murphy","objectClass":"Invoice"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}}
		]},
		"VATRate":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"ComputeInvoice","args":[{"dataValue":["TotalTE","VATRate"]}]}},
		"Paid":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"InvoicePaid","args":[{"dataValue":["Paid","PaymentDate"]}]}}
		,"ValidateInvoice":{"action":"invoke","method":"callMethod","params":{
			"confirm":{"text":"Validate invoice"},
			"method":"query","data":{"dirtyChild":1,"module":"Murphy","objectClass":"Invoice"},
			"function":"ValidateInvoice"}
		}
		,"PrintInvoice":{"action":"invoke","method":"callMethod","params":{
			"method":"query","data":{"dirtyChild":1,"module":"Murphy","objectClass":"Invoice"},
			"function":"PrintInvoices"}
		}
		,"SendInvoice":{"action":"invoke","method":"callMethod","params":{
			"method":"query","data":{"dirtyChild":1,"module":"Murphy","objectClass":"Invoice"},
			"function":"SendInvoices"}
		}
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"menuItems":[
		{"children":[
			
			{"label":"Sauver","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"Sauver & Fermer","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"Fermer","icon":"close","data":"close"},
			{"label":"Annuler","icon":"refresh","data":"cancel","stateGroup":"updated"},
			{"label":"Plus...","icon":"down","data":"more","children":[{"label":"Validate invoice","icon":"validate","data":"ValidateInvoice","stateGroup":"savedIdle"},{"label":"Print invoice","icon":"print","data":"PrintInvoice","stateGroup":"savedIdle"},{"label":"Send invoice","icon":"mail","data":"SendInvoice"}]}
		]}
	]},
	{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
	"components":[
		{"type":"EditContainer","percentHeight":100, "id":"edit",
		"components":[
			{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
			"components":[							
				{"type":"VBox","percentWidth":100,"percentHeight":100,"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,"setStyle":{"verticalGap":5},
				"components":[
					{"type":"Panel","title":"Invoice","layout":{"type":"HorizontalLayout"},"titleHeight":20,"dividerVisible":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
					"components":[
						{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0,"paddingLeft":10,"paddingRight":10,"paddingTop":0,"paddingBottom":6},
						"components":[	
							{"type":"FormItem","percentLabel":28,"label":"Reference","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"Reference","percentWidth":100,"validType":"string" ,"maxChars":20,"formLabel":1,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Type","percentWidth":100,"components":[
								{"type":"ComboBox","dataField":"Type","percentWidth":100 ,"dataProvider":[
									{"data":"I","label":"Invoice"},
									{"data":"C","label":"Credit note"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Date","percentWidth":100,"components":[
								{"type":"DateField","dataField":"Date","validType":"date" }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Valid","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Valid","percentWidth":100 ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Printed","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Printed","percentWidth":100 ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Sent","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Sent","percentWidth":100 ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Supplier","percentWidth":100,"components":[
								{"type":"DataItem","percentWidth":100 ,
								"displayFields":[{"name":"Reference","description":"Reference"},{"name":"Company","description":"Company"}],
								"keyType":"short","keyMandatory":true,"dataField":"Third.InvoiceSupplierId",
								"kobeyeClass":{"dirtyChild":1,"module":"Murphy","parentClass":"Third","keyName":"InvoiceSupplierId",
								"select":["Id","Reference","Company"],"icon":"[None]","form":"PopupList.json"},
								"actions":[
									{"type":"start", "action":"loadValues"},
									{"type":"proxy", "triggers":[
										{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
										{"trigger":"linkThird","action":"invoke","method":"linkParent"},
										{"trigger":"unlinkThird","action":"invoke","method":"unlinkParent"}
									]}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Account","percentWidth":100,"components":[
								{"type":"DataItem","percentWidth":100 ,"displayFields":[{"name":"Name","description":"Name"}],
								"keyType":"short","keyMandatory":true,"dataField":"Account.InvoiceAccountId",
								"exoFields":{"VATRate":""},
								"kobeyeClass":{"dirtyChild":1,"module":"Murphy","parentClass":"Account","keyName":"InvoiceAccountId",
								"select":["Id","Name"],"icon":"[None]","form":"PopupList.json"},
								"actions":[
									{"type":"start", "action":"loadValues"},
									{"type":"proxy", "triggers":[
										{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
										{"trigger":"linkAccount","action":"invoke","method":"linkParent"},
										{"trigger":"unlinkAccount","action":"invoke","method":"unlinkParent"}
									]}
								]}
							]}
						]}
					]},
					{"type":"Panel","title":"Finance","layout":{"type":"HorizontalLayout"},"titleHeight":20,"dividerVisible":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
					"components":[
						{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0,"paddingLeft":10,"paddingRight":10,"paddingTop":0,"paddingBottom":6},
						"components":[	
							{"type":"FormItem","percentLabel":28,"label":"Currency","percentWidth":100,"components":[
								{"type":"ComboBox","dataField":"Currency","percentWidth":100 ,
								"kobeyeClass":{"module":"Murphy","objectClass":"Currency","query":"Murphy/Currency","identifier":"Id","label":"Currency"},
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Total tax excluded","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"TotalTE","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"VAT rate","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"VATRate","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} }
							]},
							{"type":"FormItem","percentLabel":28,"label":"VAT amount","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"VATAmount","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Total tax included","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"TotalTI","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Payment","percentWidth":100,"components":[
								{"type":"ComboBox","dataField":"Payment","percentWidth":100 ,
								"kobeyeClass":{"module":"Murphy","objectClass":"PaymentTerm","query":"Murphy/PaymentTerm","identifier":"Id","label":"Payment"},
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Due date","percentWidth":100,"components":[
									{"type":"DateField","dataField":"DueDate","validType":"date" }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Paid","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Paid","percentWidth":100 }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Payment","percentWidth":100,"components":[
									{"type":"DateField","dataField":"PaymentDate","validType":"date" }
							]}
						]}
					]}
				]},
				{"type":"ChildrenGrid","dataField":"Shimpent.ShipmentInvoiceId","percentWidth":100,"height":300,
				"buttons":[],
				"kobeyeClass":{"dirtyParent":1,"objectClass":"Shipment"},
				"columns":[
					{"type":"column","dataField":"Id","headerText":"ID","visible":0},
					{"type":"column","dataField":"Contract","headerText":"Contract","width":120},
					{"type":"column","dataField":"SupplierInvoice","headerText":"Invoice","width":100},
					{"type":"column","dataField":"Buyer","headerText":"Buyer","width":200},
					{"type":"column","dataField":"Volume","headerText":"Volume","format":"0dec","width":70},
					{"type":"column","dataField":"Varietal","headerText":"Varietal","width":120},
					{"type":"column","visible":0}
				],
				"events":[
					{"type":"start","action":"loadValues","params":{"needsParentId":1}}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"saveClose","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":0}},
				{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
				{"trigger":"delete","action":"invoke","method":"deleteData"},
				{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
				{"trigger":"new","action":[
					{"action":"invoke","method":"clearData"},
					{"action":"invoke","method":"restart"}
				]}
			]}
		]}
	]}
],
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}