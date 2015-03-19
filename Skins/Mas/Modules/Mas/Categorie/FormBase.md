[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"TitleWindow","id":"FD:[!I::Module!]/[!I::TypeChild!]","title":"Edition [!O::getDescription()!]",
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":1,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"minWidth":500,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
	"components":[
		{"type":"EditContainer","id":"edit",//"percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"VBox","setStyle":{"verticalGap":10,"paddingLeft":5,"paddingRight":5,"paddingTop":0,"paddingBottom":0},
			"percentWidth":100,"percentHeight":100,"minWidth":500,
			"components":[
				{"type":"CollapsiblePanel","title":"Catégorie","layout":{"type":"HorizontalLayout"},"open":1,
				"components":[
					{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2,"paddingLeft":10,"paddingRight":10,"paddingTop":4,"paddingBottom":6},
					"components":[	
						{"type":"FormItem","percentLabel":35,"label":"Catégorie","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"Categorie","percentWidth":100,"validType":"string" ,"required":1}
						]},
						{"type":"FormItem","percentLabel":35,"label":"Description","percentWidth":100,"components":[
							{"type":"TextArea","dataField":"Description","percentWidth":100,"height":70,"validType":"string" }
						]},
						{"type":"FormItem","percentLabel":35,"label":"Icone","percentWidth":100,"components":[
							{"type":"Upload","dataField":"Icone","percentWidth":100}
						
						]},
						{"type":"FormItem","percentLabel":35,"label":"Publier","percentWidth":100,"components":[
							{"type":"CheckBox","dataField":"Publier","percentWidth":100 ,"defaultValue":"1"}
						]},
						//{"type":"FormItem","percentLabel":35,"label":"Rôles","percentWidth":100,"components":[
						//	{"type":"KeyList","dataField":"Role.RoleId","percentWidth":100,"height":80,
						//	"kobeyeClass":{"dirtyChild":1,"urlType":"parents","parentClass":"Role","keyName":"RoleId","form":"PopupList.json"},
						//	"events":[
						//		{"type":"start","action":"loadValues"}
						//	]}
						//]},
						{"type":"FormItem","percentLabel":35,"label":"Catégorie mère","percentWidth":100,"components":[
							{"type":"Tree","dataField":"Categorie.CategorieId","id":"CB:CategorieId","checkBoxes":1,"percentWidth":100,"height":122,
							"kobeyeClass":{
								"module":"Mas",
								"objectClass":"Categorie",
								"children":["Categorie"],
								"icon":"/Skins/AdminV2/Img/IconAqua/My-Docs.png"
							},
							"checkKobeyeClass":{
								"module":"Mas",
								"parentClass":"Categorie",
								"dirtyChild":1,
								"icon":"/Skins/AdminV2/Img/IconAqua/My-Docs.png"
							},
							"events":[
								{"type":"init", "action":"loadData"}
								,{"type":"start","action":"invoke","method":"loadCheckData"}
								
							]}
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				//{"trigger":"save","action":"invoke","method":"callMethod","params":{
				//"method":"object","data":{"module":"Mas","objectClass":"Categorie"},
				//"function":"SaveGroup","args":"dv:*","closeForm":1}},
				{"trigger":"delete","action":"invoke","method":"deleteData"}
			]}
		]},
		{"type":"Spacer","percentHeight":100},
		{"type":"HGroup","percentWidth":100,
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"save","label":"$__Ok__$","width":100,
			"events":[
				{"type":"click", "action":"invoke","objectID":"edit","method":"saveData","params":{"closeForm":1}}
			]},
			{"type":"Button","id":"delete","label":"$__Delete__$","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"edit","method":"deleteData"}
			]},
			{"type":"Button","id":"cancel","label":"$__Cancel__$","width":100,
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
