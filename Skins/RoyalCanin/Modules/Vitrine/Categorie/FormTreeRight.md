[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"FL:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0,"verticalGap":0},"localProxy":1, 
	"components":[
		{"type":"MenuTab", "id":"menuList", "maxLines":1,
			"menuItems":[
				{"label":"", "children":[
					{"label":"Change ownership", "icon":"iconNew", "data":"changegroupright"},
					{"label":"Refresh", "icon":"refresh", "data":"refresh"}
				]}
			],
			"events":[
				{"type":"proxy",
					"triggers":[
						{"trigger":"changegroupright", "action":"invoke", "method":"loadFormWithSelection","params":{"form":"changeGroupRights.json"}}
					]
				}
			]
		},
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,"rights":1,
			"kobeyeClass":{
				"module":"Vitrine",
				"objectClass":"Categorie",
				"label":"Nom",
				"identifier":"Id",
				"icon":"products",
				"form":"changeGroupRights.json",
				"children":["Categorie","Produit"]
			},
			"otherKobeyeClass":{
				"Produit":{"module":"Vitrine","objectClass":"Produit","identifier":"Id","label":"Nom","form":"changeGroupRights.json", "iconField":"Image", "children":["Modele"]},
				"Modele":{"module":"Vitrine","objectClass":"Modele","identifier":"Id","label":"Nom","form":"changeGroupRights.json", "iconField":"CodeBarre"}
			},
			"events":[
				{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{"form":"changeGroupRights.json"}},
				{"type":"proxy","triggers":[
					{"trigger":"refresh", "action":"invoke", "method":"loadData"}
				]}
			],
			"actions":[
				{"type":"init", "action":"loadData"}
			]
		}
	],
	"actions":[
	]
	}
}