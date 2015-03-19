[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"FL:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0, "paddingLeft":5, "paddingRight":5},"localProxy":1, 
	"components":[
		{"type":"MenuTab", "id":"menuList", "menuItems":[
			{"label":"File", "children":[
				{"label":"Edit name", "icon":"open", "data":"open","needFocus":1,"objectClass":["SubRange","SubProduct","SubModel"]},
				{"label":"Delete", "icon":"remove", "data":"delete","needFocus":1,"objectClass":["SubRange","SubProduct","SubModel"]},
				{"label":"Update to current version", "icon":"open","needFocus":1, "data":"update","objectClass":["SubRange","SubProduct","SubModel"]},
				{"type":"vseparator"},
				{"label":"Refresh", "icon":"refresh", "data":"refresh"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"resetParent":1}},
				"update":{"type":"click","action":"invoke","method":"callMethod","params":{"method":"object","function":"update","currentSelection":1}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
				
			}}
		]},
		{"type":"HBox", "percentWidth":100, "height":13, 
			"components":[
				{"type":"Label","text":"Global database","percentWidth":50,"setStyle":{"align":"center","fontSize":15}},
				{"type":"Label","text":"My local database","percentWidth":50,"setStyle":{"align":"center","fontSize":15}}
			]
		},
		{"type":"DividedBox","direction":"horizontal", "id":"listBox", "label":"Liste", "percentWidth":100, "percentHeight":100, "setStyle":{"closable":0, "paddingTop":0}, 
			"components":[
				{"type":"CheckTree","id":"global_tree", "percentWidth":100, "percentHeight":100,
					"kobeyeClass":{
						"module":"Axenergie",
						"objectClass":"Categorie",
						"label":"Nom",
						"identifier":"Id",
						"icon":"products",
						"filters":"Affiche=0",
						"form":"FormBase.json",
						"children":["Categorie","Produit"]
					},
					"otherKobeyeClass":{
						"Produit":{"module":"Axenergie","objectClass":"Produit","identifier":"Id","label":"Nom", "iconField":"Image", "children":["Modele"]},
						"Modele":{"module":"Axenergie","objectClass":"Modele","identifier":"Id","label":"Nom", "iconField":"CodeBarre"}
					},
					"checkKobeyeClass":{
						"kobeyeClass":{
							"module":"Axenergie",
							"objectClass":"SubRange",
							"parentClass":"Database",
							"parentId":"[!I::LastId!]",
							"label":"Nom",
							"identifier":"Id",
							"icon":"products",
							"form":"FormDetail.json",
							"extra":{
								"linkObject":"Categorie",
								"linkField":"CategorieId",
								"versionField":"Version",
								"subVersionField":"SubVersion"
							},
							"children":["SubRange","SubProduct"]
						},
						"otherKobeyeClass":{
							"SubProduct":{"module":"Axenergie","objectClass":"SubProduct","extra":{"linkObject":"Produit","linkField":"ProduitId","versionField":"Version","subVersionField":"SubVersion"},"identifier":"Id","label":"Nom","form":"FormDetail.json", "iconField":"Image", "children":["SubModel"]},
							"SubModel":{"module":"Axenergie","objectClass":"SubModel","extra":{"linkObject":"Modele","linkField":"ModeleId","versionField":"Version","subVersionField":"SubVersion"},"identifier":"Id","label":"Nom","form":"FormDetail.json", "iconField":"CodeBarre"}
						}
					},
					"events":[
						{"type":"proxy","triggers":[
							{"trigger":"refresh", "action":"invoke", "method":"refreshData"}
						]},
						{"type":"check","action":"invoke","method":"callMethod","params":{"method":"object","module":"Axenergie","objectClass":"Database","id":"[!I::LastId!]","function":"addSub","args":[]}},
						{"type":"uncheck","action":"invoke","method":"callMethod","params":{"method":"object","module":"Axenergie","objectClass":"Database","id":"[!I::LastId!]","function":"removeSub","args":[]}}
					],
					"actions":[
						{"type":"init", "action":"loadData"}
					]
				}
			]
		}
	],
	"actions":[
	]
	}
}