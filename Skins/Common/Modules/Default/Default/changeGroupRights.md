[INFO [!Query!]|I]
{"form":{"type":"TitleWindow","id":"FF:[!I::Module!]/[!I::TypeChild!]","title":"Edit object's rights","width":550,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]","select":"Id"},
"localProxy":1,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5}, 
	"components":[
		{"type":"EditContainer","id":"edit","defaultButtonID":"ok","percentWidth":100,"rights":1,"components":[
			{"type":"Form", "percentWidth":100,
			// "layout":{"type":"VerticalLayout", "gap":6, "paddingLeft":6, "paddingRight":6},
			"components":[
				{"type":"FormItem","percentLabel":30,"label":"[!Query!]","percentWidth":100},
				{"type":"VBox","percentWidth":100,"components":[
					{"type":"HBox","percentWidth":100,"components":[
						{"type":"FormItem","percentLabel":30,"label":"User","percentWidth":100},
						{"type":"FormItem","percentLabel":30,"label":"Read","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"sys_ur"}
						]},
						{"type":"FormItem","percentLabel":30,"label":"Write","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"sys_uw"}
						]}
					]},
					{"type":"HBox","percentWidth":100,"components":[
						{"type":"FormItem","percentLabel":30,"label":"Group","percentWidth":100},
						{"type":"FormItem","percentLabel":30,"label":"Read","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"sys_gr"}
						]},
						{"type":"FormItem","percentLabel":30,"label":"Write","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"sys_gw"}
						]}
					]},
					{"type":"HBox","percentWidth":100,"components":[
						{"type":"FormItem","percentLabel":30,"label":"Other","percentWidth":100},
						{"type":"FormItem","percentLabel":30,"label":"Read","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"sys_or"}
						]},
						{"type":"FormItem","percentLabel":30,"label":"Write","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"sys_ow"}
						]}
					]}
				]},
				{"type":"FormItem","percentLabel":30,"label":"Owner group","percentWidth":100,"components":[
				
						{"type":"DataItem","percentWidth":100,"displayFields":[{"name":"Nom","description":"Name"}],"keyType":"field","keyMandatory":true,"dataField":"sys_gid",
							"kobeyeClass":{"module":"Systeme","objectClass":"Group","select":["Id","Nom"],"icon":"/Skins/AdminV2/Img/IconAqua/LibraryFolder.png","form":"PopupList.json"},
							"actions":[
								//{"type":"start", "action":"loadValues"},
								{"type":"proxy", "triggers":[
								]}
							]
						}
					
				]},
				{"type":"FormItem","percentLabel":30,"label":"Owner user","percentWidth":100,"components":[
				
						{"type":"DataItem","percentWidth":100,"displayFields":[{"name":"Nom","description":"Name"},{"name":"Prenom","description":"First name"},{"name":"Login","description":"Login"}],"keyType":"field","keyMandatory":true,"dataField":"sys_uid",
							"kobeyeClass":{"module":"Systeme","objectClass":"User","select":["Id","Login","Nom","Prenom"],"iconField":"Avatar","form":"PopupList.json"},
							"actions":[
								//{"type":"start", "action":"loadValues"},
								{"type":"proxy", "triggers":[
								]}
							]
						}
					
				]},
				{"type":"FormItem","percentLabel":30,"label":"Apply recursively:","percentWidth":100,"components":[
						{"type":"CheckBox","dataField":"recursive","defaultValue":1}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy", "triggers":[
				{"trigger":"yes","action":"invoke","method":"linkParent","method":"saveRights","params":{"closeForm":1}}
			]}
		]},
// boutons valider, annuler   
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"yes","label":"Valider","width":80},
			{"type":"Button","id":"no","label":"Annuler","width":80,
			"events":[
				{"type":"click", "action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"dispatchValues"}
]}
}
