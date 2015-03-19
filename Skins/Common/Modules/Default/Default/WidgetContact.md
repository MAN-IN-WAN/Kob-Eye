[INFO [!Query!]|I]
[OBJ [!Int::module!]|[!Int::objectClass!]|O]
[!container:="containerID":"tabNav"!]
{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":"invoke","method":"groupState","params":{"group":"selection","property":"enabled","selection":1}}
	}
},
"components":[
	
	{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
		"components":[
			{"type":"ImageButton","id":"edit:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1,"stateGroup":"selection","enabled":0},
			{"type":"ImageButton","id":"new:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
			{"type":"ImageButton","id":"delete:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1,"stateGroup":"selection","enabled":0}
		]
	},
	{"type":"HBox","minHeight":1,"percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"List","id":"WidgetContact",
				"setStyle":{"horizontalGap":2},"percentWidth":100,"percentHeight":100,
				"kobeyeClass":{
					"dirtyParent":1,
					"module":"[!Int::module!]",
					"objectClass":"[!Int::objectClass!]",
					"select":["Id","FullName","JobTitle","Phone","Mobile","HomeNumber","Fax","Email","Birthday","CAContact","FIContact","Comments"],
					"columns":[
						{"type":"horizontal","setStyle":{"paddingTop":10,"paddingBottom":10,"paddingRight":10,"paddingLeft":10},"components":[
							{"type":"background","color":"0xffffff","dropShadow":1,"components":[
								{"type":"vertical","setStyle":{"paddingTop":10,"paddingBottom":10,"paddingRight":10,"paddingLeft":10,"gap":10},"components":[
									{"type":"horizontal","setStyle":{"gap":10},"components":[
										{"type":"image","value":"mwc_contact","width":64,"height":64},
										{"type":"vertical","components":[
											{"type":"varchar","field":"FullName","setStyle":{"fontWeight":"bold"}},
											{"type":"varchar","field":"JobTitle"},
											{"type":"varchar","field":"Phone","prefix":"Tel: "},
											{"type":"varchar","field":"Mobile","prefix":"Mob: "},
											{"type":"varchar","field":"HomeNumber","prefix":"Home: "},
											{"type":"varchar","field":"Fax","prefix":"Fax: "},
											{"type":"date","field":"Birthday","prefix":"Birthday: "},
											{"type":"email","field":"Email"}
										]},
										{"type":"vertical","components":[
											{"type":"boolean","field":"CAContact","prefix":"CA: "},
											{"type":"boolean","field":"FIContact","prefix":"FI: "}
										]}
									]},
									{"type":"text","field":"Comments","prefix":"Comments:"}
								]}
							]}
						]}
					],
					[STORPROC [!O::getElementsByAttribute(iconField,1)!]|Ic]
						[STORPROC [!Ic::elements!]|Id]
							"iconField":"[!Id::name!]"
						[/STORPROC]
					[/STORPROC]
					"icon":"[!O2::getIcon!]"
					,"form":"FormDetail.json"
				}
				,"events":[
					{"type":"start","action":"loadValues","params":{"needsParentId":1}},
					{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{}},
					{"type":"proxy", "triggers":[
						{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"createForm"},
						{"trigger":"edit:[!Int::objectClass!]","action":"invoke","method":"loadFormWithSelection","params":{}},
						{"trigger":"delete:[!Int::objectClass!]","action":"invoke","method":"deleteWithID"}
					]}
				]
			}
		]
	}
]}