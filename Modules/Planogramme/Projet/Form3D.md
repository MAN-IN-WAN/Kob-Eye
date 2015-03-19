[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1][!QueryData:=[!H::Query!]!][/STORPROC]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
//RECHERCHE DU DBID DE L'UTILISATEUR
[STORPROC Vitrine/Database/Pays=[!Systeme::User::Pays!]|DB|0|1][/STORPROC]

{"form":
{"type":"VBox","id":"FL:[!I::Module!]/[!I::TypeChild!]","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"paddingTop":0,"paddingLeft":0,"paddingRight":0,"verticalGap":0},"localProxy":{
	"actions":{
		"proxy_kobeye_id":{"action":"invoke","method":"groupState","params":{"group":"children","property":"enabled","checkID":1} }
	}
},"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"}, 
"components":[
	{"type":"EditContainer", "id":"searchBox", "percentWidth":100, "percentHeight":100,
		"components":[
			{"type":"VBox","id":"listBox2","label":"Liste","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0}, 
				"components":[
					{"type":"MenuTab", "id":"menuListLayoutManagement",
						"menuItems":[
							{"label":"Project", "children":[
								{"label":"Save", "icon":"save", "data":"save","needFocus":1},
								{"label":"Save as", "icon":"saveAs", "data":"saveAs","needFocus":1},
				//				{"label":"Save and close", "icon":"saveAs", "data":"saveClose"},
								{"type":"vseparator"},
								{"label":"Empty", "icon":"empty", "data":"reset","needFocus":1},
				//				{"label":"Delete", "icon":"remove", "data":"delete"},
								{"label":"Close", "icon":"remove", "data":"close","needFocus":1},
				//				{"label":"Refresh", "icon":"iconNew", "data":"refresh"},
								{"type":"vseparator"},
								{"label":"Print project in 2D", "objectClass":["Level"], "icon":"print", "data":"printProject2d","needFocus":1},
								{"label":"Product list in pdf", "objectClass":["Level"], "icon":"print", "data":"printProject","needFocus":1},
								{"label":"Product list in xls", "objectClass":["Level"], "icon":"print", "data":"xlsexport","needFocus":1}
							]},
							{"label":"Stand", "objectClass":["Shelf"],"children":[
								{"label":"Add a stand", "icon":"addlevel", "data":"addShelf","needFocus":1},
								{"type":"vseparator"},
								{"label":"Empty", "objectClass":["Shelf","Level"], "icon":"empty", "data":"shelfReset","needFocus":1},
								{"label":"Delete", "objectClass":["Shelf","Level"], "icon":"deleteShelf", "data":"shelfDelete","needFocus":1},
								{"type":"vseparator"},
								{"label":"Set width", "objectClass":["Shelf","Level"], "icon":"editWidth", "data":"shelfSetWidth","needFocus":1},
								{"label":"Rename stand", "objectClass":["Shelf", "Level"], "icon":"rename", "data":"setStandName","needFocus":1},
								{"type":"vseparator"},
								{"label":"Print stand in 2D", "objectClass":["Level"], "icon":"print", "data":"printStand","needFocus":1},
								{"label":"Print stand list", "objectClass":["Level"], "icon":"print", "data":"printStandProjectList","needFocus":1}
							]},
							{"label":"3D view", "objectClass":["Level"]
								, "children":[
				//					{"label":"Back to 2D view", "objectClass":["Level"], "icon":"open", "data":"resetCamera","needFocus":1},
				//					{"label":"Free camera", "objectClass":["Level"], "icon":"open", "data":"freeCamera","needFocus":1},
									{"label":"Print your 3D", "objectClass":["Level"], "icon":"print", "data":"printStandDetailed","needFocus":1}
								]
							}
						]
					},
					{"type":"HBox","id":"listBox","label":"Liste","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
					"components":[
						//Vue 3D
						{"type":"RoyalComponent","percentWidth":100,"percentHeight":100,"dataField":"Data","id":"RcDisplay","dataField":"Donnee",[IF [!Systeme::User::isRole(LOCAL_MANAGER)!]]"localAdmin":1,[/IF]
							"kobeyeClass":{
								"module":"Vitrine","dirtyChild":1,"parentClass":"Database","parentId":"[!DB::Id!]"
							},
							"events":[
								{"type":"proxy","triggers":[
									{"trigger":"reset","action":"invoke","method":"reset"},
									{"trigger":"refresh","action":"invoke","method":"refresh"},
									{"trigger":"addShelf","action":"invoke","method":"addShelf"},
									{"trigger":"setStandName","action":"invoke","method":"setStandName"},
									{"trigger":"setProjectName","action":"invoke","method":"setProjectName"},
									{"trigger":"saveAs","action":"invoke","method":"saveAs"},
									{"trigger":"shelfSetWidth","action":"invoke","method":"shelfSetWidth"},
									{"trigger":"shelfReset","action":"invoke","method":"shelfReset"},
									{"trigger":"shelfDelete","action":"invoke","method":"shelfDelete"},
									{"trigger":"shelfAddLevel","action":"invoke","method":"shelfAddLevel"},
									{"trigger":"levelHeightDepth","action":"invoke","method":"levelHeightDepth"},
									{"trigger":"levelAlignRight","action":"invoke","method":"levelAlignRight"},
									{"trigger":"levelAlignLeft","action":"invoke","method":"levelAlignLeft"},
									{"trigger":"levelJustify","action":"invoke","method":"levelJustify"},
									{"trigger":"levelUpper","action":"invoke","method":"levelUpper"},
									{"trigger":"levelLower","action":"invoke","method":"levelLower"},
									{"trigger":"levelDelete","action":"invoke","method":"levelDelete"},
									{"trigger":"levelReset","action":"invoke","method":"levelReset"},
									{"trigger":"resetCamera","action":"invoke","method":"resetCamera"},
									{"trigger":"freeCamera","action":"invoke","method":"freeCamera"},
									{"trigger":"printProject","action":"invoke","method":"printProject"},
									{"trigger":"printProject2d","action":"invoke","method":"printProject2d"},
									{"trigger":"xlsexport","action":"invoke","method":"xlsexport"},
									{"trigger":"printStandDetailed","action":"invoke","method":"printStandDetailed"},
									{"trigger":"printStand","action":"invoke","method":"printStand"},
									{"trigger":"printStandProjectList","action":"invoke","method":"printStandProjectList"}
								]},
								{"type":"changeRange","action":"invoke","method":"setRange","objectID":"RcProduct"}
							]
						},
						{"type":"VBox","percentHeight":100,"width":200,"setStyle":{"paddingTop":5,"verticalGap":0},
							"components":[
								{"type":"RoyalProductComponent","id":"RcProduct","percentWidth":100,"percentHeight":100,
									"kobeyeClass":{
										"module":"Vitrine","objectClass":"Database","Id":"[!DB::Id!]"
									},
									"events":[
										{"type":"over","action":"invoke","method":"showReference","objectID":"RcDisplay"}
									]
								}
							]
						}
					]
				}
			]
		}
	],
	"events":[
		{"type":"start","action":"loadValues","params":{"needsId":1}},
		{"type":"proxy","triggers":[
			{"trigger":"save","action":"invoke","method":"saveData"},
			{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
			{"trigger":"delete","action":"invoke","method":"deleteData","params":{"title":"Delete the project","message":"Do you confirm you want to delete ?","closeForm":1}}
		]}
	]}
],"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}
