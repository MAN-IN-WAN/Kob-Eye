{"form":{"type":"GradientVBox","id":"Shipment?","label":"Shipment","percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5},"clipContent":0,
"kobeyeClass":{"module":"Murphy","objectClass":"Shipment"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}}
		]}
		
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"menuItems":[
		{"children":[
			{"label":"Sauver","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"Sauver & Fermer","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"Fermer","icon":"close","data":"close"},
			{"label":"Annuler","icon":"refresh","data":"cancel","stateGroup":"updated"},
			{"label":"Supprimer","icon":"iconDelete","data":"delete","stateGroup":"saved"}
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
					{"type":"Panel","title":"Shipment","layout":{"type":"HorizontalLayout"},"titleHeight":20,"dividerVisible":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
					"components":[
						{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0,"paddingLeft":10,"paddingRight":10,"paddingTop":0,"paddingBottom":6},
						"components":[	
							{"type":"FormItem","percentLabel":28,"label":"Contract","percentWidth":100,"components":[
								{"type":"DataItem","percentWidth":100,"noControl":1,"displayFields":[{"name":"Reference","description":"Reference"}],
								"keyType":"short","keyMandatory":true,"dataField":"Contract.ShipmentContractId",
								"kobeyeClass":{"dirtyChild":1,"module":"Murphy","parentClass":"Contract","keyName":"ShipmentContractId",
								"select":["Id","Reference"],"icon":"[None]","form":"PopupList.json"},
								"actions":[
									{"type":"start", "action":"loadValues"},
									{"type":"proxy", "triggers":[
										{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
										{"trigger":"linkContract","action":"invoke","method":"linkParent"},
										{"trigger":"unlinkContract","action":"invoke","method":"unlinkParent"}
									]}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Status","percentWidth":100,"components":[
								{"type":"ComboBox","dataField":"Status","percentWidth":100,"defaultValue":"51" ,"required":1,
								"kobeyeClass":{"module":"Murphy","objectClass":"Status","query":"Murphy/Status/Type=5","identifier":"Id","label":"Status"},
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Purchase order","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"PurchaseOrder","percentWidth":100,"validType":"string" }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Loading date","percentWidth":100,"components":[
								{"type":"DateField","dataField":"LoadingDate","validType":"date" ,"required":1}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Volume","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"Volume","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} ,"required":1}
							]}
						]}
					]},
					{"type":"Panel","title":"Delivery","layout":{"type":"HorizontalLayout"},"titleHeight":20,"dividerVisible":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
					"components":[
						{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0,"paddingLeft":10,"paddingRight":10,"paddingTop":0,"paddingBottom":6},
						"components":[	
							{"type":"FormItem","percentLabel":28,"label":"Supplier contract","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"SupplierContract","percentWidth":100,"validType":"string" }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Supplier invoice","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"SupplierInvoice","percentWidth":100,"validType":"string" }
							]},
							{"type":"FormItem","percentLabel":28,"label":"MWC Invoice","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Invoice","percentWidth":100 ,"editable":0,"enabled":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Blocked","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Retained","percentWidth":100 }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Delivery date","percentWidth":100,"components":[
								{"type":"DateField","dataField":"DeliveryDate","validType":"date" }
							]},
							{"type":"FormItem","percentLabel":28,"label":"Invoice date","percentWidth":100,"components":[
								{"type":"DateField","dataField":"NoteDate","validType":"date" }
							]}
						]}
					]}
				]},
				{"type":"ChildrenGrid","dataField":"Container.ContainerShipmentId","percentWidth":100,"height":300,
				"buttons":["add","delete"],
				"kobeyeClass":{"dirtyParent":1,"objectClass":"Container"},
				"columns":[
					{"type":"column","dataField":"Id","headerText":"ID","visible":0},
					{"type":"column","dataField":"ContainerNumber","headerText":"Container number","width":200},
					{"type":"column","dataField":"Volume","headerText":"Volume","width":100},
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