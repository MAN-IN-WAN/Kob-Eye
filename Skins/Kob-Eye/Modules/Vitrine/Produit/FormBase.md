{"form":{"type":"VBox","id":"Produit?","label":"Produit","percentHeight":100,
"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0},"clipContent":0,
"kobeyeClass":{"module":"Vitrine","objectClass":"Produit"},
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
			{"label":"Nouveau","icon":"new","data":"new"},
			{"label":"Sauver","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"Sauver & Fermer","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"Fermer","icon":"close","data":"close"},
			{"type":"vseparator"},
			{"label":"Annuler","icon":"back","data":"cancel","stateGroup":"updated"},
			{"label":"Supprimer","icon":"iconDelete","data":"delete","stateGroup":"saved"}
			
		]}
	]},
	{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
	"components":[
		{"type":"EditContainer","percentHeight":100,"id":"edit",
		"components":[
			{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
			"components":[							


					{"type":"VBox","percentWidth":65,"percentHeight":100,"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
						"components":[
							
	{"type":"Panel","title":"Product", "layout":{"type":"HorizontalLayout"},"minHeight":127,
	"components":[
//		{"type":"VBox","width":150,"percentHeight":100,"setStyle":{"backgroundColor":"#dedede","paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
//		"components":[
//			
//			
//								{"type":"VBox","percentWidth":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":10,"paddingBottom":10},
//								"components":[
//									{"type":"Label","text":"Front picture","percentWidth":100,
//										"setStyle":{"fontWeight":"bold","color":"#000000"}
//									},
//									{"type":"ImageUpload","dataField":"Image","percentWidth":100,"orientation":"vertical"}
//								]}
//							
//						,
//								{"type":"VBox","percentWidth":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":10,"paddingBottom":10},
//								"components":[
//									{"type":"Label","text":"Bottom picture","percentWidth":100,
//										"setStyle":{"fontWeight":"bold","color":"#000000"}
//									},
//									{"type":"ImageUpload","dataField":"ImageBottom","percentWidth":100,"orientation":"vertical"}
//								]}
//							
//						,
//								{"type":"VBox","percentWidth":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":10,"paddingBottom":10},
//								"components":[
//									{"type":"Label","text":"Full texture","percentWidth":100,
//										"setStyle":{"fontWeight":"bold","color":"#000000"}
//									},
//									{"type":"ImageUpload","dataField":"Texture","percentWidth":100,"orientation":"vertical"}
//								]}
//							
//						
//		]},	
		{"type":"VBox","percentWidth":100,"percentHeight":100, "setStyle":{"paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
		"components":[	
			
			
				{"type":"Form","setStyle":{"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,
					"components":[
						{"type":"FormItem","percentLabel":35,"label":"Name","percentWidth":100,"components":[
						
										{"type":"TextInput","dataField":"Nom","percentWidth":100,"validType":"string" ,"maxChars":250,"required":1,"formLabel":1}
									
						]},
						
						{"type":"FormItem","percentLabel":35,"label":"Service","percentWidth":100,"components":[
						
								{"type":"CheckBox","dataField":"Service" }
						]},
						
						{"type":"FormItem","percentLabel":35,"label":"Tarif","percentWidth":100,"components":[
						
								{"type":"TextInput","dataField":"Tarif","width":100,"maxChars":10,"validType":"float" }
						]},
						{"type":"FormItem","percentLabel":35,"label":"Associated range","percentWidth":100,"components":[
						
								{"type":"DataItem","percentWidth":100,"displayFields":[
									
										{"name":"Nom","description":"Name"}
									]
									,"keyType":"long","keyMandatory":true,"dataField":"CategorieId",
									"kobeyeClass":{"dirtyChild":1,"module":"Vitrine","parentClass":"Categorie",
									"select":["Id"
									
										,"Nom"
									],
									
										"icon":"/Skins/AdminV2/Img/IconAqua/LibraryFolder.png"
										
										,"form":"PopupList.json"
									},
									"actions":[
										{"type":"start", "action":"loadValues"},
										{"type":"proxy", "triggers":[
											{"trigger":"linkCategorie","action":"invoke","method":"linkParent"},
											{"trigger":"unlinkCategorie","action":"invoke","method":"unlinkParent"}
										]}
									]
								}
							
						]}
				]}
		]}
	]},
	{"type":"Panel","title":"Template", "layout":{"type":"HorizontalLayout"},"minHeight":127,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100, "setStyle":{"paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
			"components":[	
			{"type":"FormItem","percentLabel":35,"label":"Select template","percentWidth":100,"components":[
				{"type":"ComboBox","dataField":"TemplateComboxBox", "dataProvider":[
					{"data":"--- Choose one template ---", "label":"--- Choose one template ---"},
					{"data":"test", "label":"test"},
					{"data":"zob", "label":"zob"}]
				,"percentWidth":100,"required":1,"formLabel":1
//				,"events":[
//						{"type":"change","action":"invoke","method":"selectTemplate","params":{"dataField":"Template"},"objectID":"Template"}
//					]
				}
			]},
			{"type":"FormItem","percentLabel":100,"label":"Option & visualisation template","percentWidth":100, 
				"components":[
					{"type":"Button","label":"Sauvegarder","id":"TemplateSave","percentWidth":25, "dataField":"TemplateSave",
					"events":[
						{"type":"click","action":"invoke","method":"drawRealTemplate","objectID":"Template"}
					]}
				]
			},
			{"type":"Template","percentWidth":100,"percentHeight":100,"id":"Template", "setStyle":{"paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10, "background-color":"0xffffff"},
				"events":[
					{"type":"proxy","triggers":[
						{"trigger":"TemplateComboxBox","action":"invoke","method":"selectTemplate","params":{"dataField":"TemplateComboxBox"}}
					]}	
				]	
			}
		]}	
	]}
						]
					}
				
							,{"type":"TabNavigator", "id":"objectTabNav", "percentWidth":35, "percentHeight":100, "closePolicy":"close_never", "minTabWidth":"150",
							"setStyle":{"paddingTop":1},"stateGroup":"saved",
							"components":[
						
{"type":"VBox","minHeight":1,"id":"firstTab1","percentWidth":100,"percentHeight":100,
"setStyle":{"verticalGap":0},
"label":"Associated formats",

"components":[
	{"type":"HBox","percentWidth":100,
		"setStyle":{"horizontalGap":4,"borderStyle":"none","dropShadowEnabled":true,"backgroundColor":"#dedede","paddingTop":4,"paddingBottom":4,"paddingRight":4,"paddingLeft":4},
		"components":[
			{"type":"Button","label":"Nouveau","id":"new:Modele","percentWidth":100},
			{"type":"Button","label":"Modifier","id":"edit:Modele","percentWidth":100},
			{"type":"Button","label":"Supprimer","id":"delete:Modele","percentWidth":100}
		]
	},
	{"type":"AdvancedDataGrid","id":"DG:Modele","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
	"kobeyeClass":{"dirtyParent":1,"objectClass":"Modele","form":"FormBase.json"},
	"events":[
		{"type":"start","action":"loadValues","params":{"needsParentId":1}},
		{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
		{"type":"proxy", "triggers":[
			{"trigger":"new:Modele","action":"invoke","method":"createForm","params":{"containerID":"tabNav"}},
			{"trigger":"edit:Modele","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
			{"trigger":"delete:Modele","action":"invoke","method":"deleteWithID"}
		]}
	],
	"columns":[
		{"type":"column","dataField":"Id","headerText":"ID","visible":0},{"type":"column","dataField":"Nom","headerText":"Name","width":150},{"type":"column","dataField":"GenCode","headerText":"Gencode (EAN)","width":150},{"type":"column","dataField":"CodeBarre","headerText":"Barcode","format":"image","width":60},{"type":"column","dataField":"UcPerBox","headerText":"Unit per box","format":"image","width":50},{"type":"column","width":0}
	]}
]}






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
				{"trigger":"cancel","action":"invoke","method":"restart"},
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