[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"FT:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0,"verticalGap":0},"localProxy":1, 
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1, "menuItems":[
			{"label":"", "children":[
				{"label":"Open", "icon":"open", "data":"open","needFocus":1,"needWrite":1},
				{"label":"Edit", "icon":"open", "data":"open" ,"needFocus":1,"needWrite":1},
				{"label":"Delete", "icon":"remove", "data":"delete","needFocus":1,"needWrite":1},
				{"type":"vseparator"},
				{"label":"Add a range", "icon":"iconNew", "data":"newCategorie", "objectClass":["Categorie"],"needWrite":1},
				{"label":"Add a product", "icon":"products", "data":"newProduit", "objectClass":["Categorie"],"needFocus":1,"needWrite":1},
				//{"label":"Add a format", "icon":"packaging", "data":"newModele", "objectClass":["Produit"],"needFocus":1,"needWrite":1},
				{"type":"vseparator"},
				{"label":"Refresh", "icon":"refresh", "data":"refresh"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
				"newCategorie":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"Categorie"}},
				"newProduit":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"Produit"}},
				"newModele":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"Modele"}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
				
			}}
		]},
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,"rights":1,
			"kobeyeClass":{
				"module":"Axenergie",
				"objectClass":"Categorie",
				"label":"Nom",
				"identifier":"Id",
				"icon":"products",
				"form":"FormBase.json",
				"children":["Categorie","Produit"]
			},
			"otherKobeyeClass":{
				"Produit":{"module":"Axenergie","objectClass":"Produit","identifier":"Id","label":"Nom","form":"FormBase.json", "iconField":"Image", "children":["Modele"]},
				"Modele":{"module":"Axenergie","objectClass":"Modele","identifier":"Id","label":"Nom","form":"FormBase.json", "iconField":"CodeBarre"}
			},
			"events":[
				{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
				{"type":"proxy","triggers":[
					{"trigger":"refresh", "action":"invoke", "method":"refreshData"}
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