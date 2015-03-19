{"form":{"type":"TitleWindow","id":"FD:Systeme/AlertTask","title":"Task","minWidth":500,
"kobeyeClass":{"module":"Systeme","objectClass":"AlertTask"},
"localProxy":{
	"actions":{
		"StartDate":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"StartDate","args":"dv:StartDate,dv:EndDate"}},
		"EndDate":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"StartDate","args":"dv:StartDate,dv:EndDate"}}
	}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
	"components":[
		{"type":"EditContainer","id":"edit",
		"components":[
			{"type":"VBox","setStyle":{"verticalGap":2,"paddingLeft":4,"paddingRight":4,"paddingTop":0,"paddingBottom":2},
			"percentWidth":100,"percentHeight":100,
			"components":[
				{"type":"Panel","title":"Task","layout":{"type":"HorizontalLayout"},"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},
				"components":[
					{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":0,"paddingLeft":6,"paddingRight":6,"paddingTop":6,"paddingBottom":6},
					"components":[	
						{"type":"TextInput","dataField":"TaskModule","includeInLayout":0,"visible":0,"editable":0},
						{"type":"TextInput","dataField":"TaskObject","includeInLayout":0,"visible":0,"editable":0},
						{"type":"TextInput","dataField":"ObjectId","includeInLayout":0,"visible":0,"editable":0},
						{"type":"FormItem","labelWidth":80,"label":"Title","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"Title","percentWidth":100,"validType":"string" }
						]},
						{"type":"FormItem","labelWidth":80,"label":"Detail","percentWidth":100,"components":[
							{"type":"TextArea","dataField":"Detail","percentWidth":100,"height":80,"validType":"string" }
						]},
						{"type":"FormItem","labelWidth":80,"label":"Starting at","percentWidth":100,"components":[
							{"type":"DateTimeField","dataField":"StartDate","increment":5}
						]},
						{"type":"FormItem","labelWidth":80,"label":"Ending at","percentWidth":100,"components":[
							{"type":"DateTimeField","dataField":"EndDate","increment":5}
						]},
						{"type":"FormItem","labelWidth":80,"label":"Reminder","percentWidth":100,"components":[
							{"type":"ComboBox","dataField":"Reminder","width":100,"defaultValue":900,
							"dataProvider":[
								{"data":"900","label":"15 min"},
								{"data":"1800","label":"30 min"},
								{"data":"2700","label":"45 min"},
								{"data":"3600","label":"1 hour"},
								{"data":"7200","label":"2 hours"},
								{"data":"14400","label":"4 hours"},
								{"data":"43200","label":"12 hours"},
								{"data":"86400","label":"1 day"},
								{"data":"172800","label":"2 days"},
								{"data":"604800","label":"1 week"}
							]}
						]},
						{"type":"FormItem","labelWidth":80,"label":"Type","percentWidth":100,"components":[
							{"type":"ComboBox","dataField":"TypeId","width":100,"defaultValue":1,
							"kobeyeClass":{"module":"Systeme","objectClass":"TaskType","identifier":"Id","label":"Type"},
							"actions":[
								{"type":"init","action":"loadData"}
							]}
						]},
						{"type":"FormItem","labelWidth":80,"label":"Private","percentWidth":100,"components":[
							{"type":"CheckBox","dataField":"Private","percentWidth":100 }
						]},
						{"type":"FormItem","labelWidth":80,"label":"Role","percentWidth":100,"components":[
							{"type":"DataItem","percentWidth":100 ,"displayFields":[{"name":"Title","description":"Title"}],
							"keyType":"short","keyMandatory":true,"dataField":"Role.RoleId",
							"kobeyeClass":{"dirtyChild":1,"module":"Systeme","parentClass":"Role","keyName":"RoleId",
							"select":["Id","Title"],"form":"PopupList.json"},
							"actions":[
								{"type":"start", "action":"loadValues"},
								{"type":"proxy", "triggers":[
									{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
									{"trigger":"linkRole","action":"invoke","method":"linkParent"},
									{"trigger":"unlinkRole","action":"invoke","method":"unlinkParent"}
								]}
							]}
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"delete","action":"invoke","method":"deleteData"}
			]}
		]},
		{"type":"Spacer","percentHeight":100},
		{"type":"HGroup","percentWidth":100,
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"ok","label":"Ok","width":100,
			"events":[
				{"type":"click", "action":"invoke","objectID":"edit","method":"saveData","params":{"closeForm":1}}
			]},
			{"type":"Button","id":"delete","label":"Supprimer","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"edit","method":"deleteData"}
			]},
			{"type":"Button","id":"cancel","label":"Annuler","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"confirmUpdate"}
]}
}
