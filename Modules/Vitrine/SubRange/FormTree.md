[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"FL:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0, "paddingLeft":5, "paddingRight":5},"localProxy":1, 
	"components":[
		{"type":"MenuTab", "id":"menuList", "menuItems":[
			{"label":"File", "children":[
				{"label":"Open", "icon":"open", "data":"open","needFocus":1},
				{"label":"Edit", "icon":"open", "data":"open" ,"needFocus":1},
				{"label":"Delete", "icon":"remove", "data":"delete","needFocus":1},
				{"type":"vseparator"},
				{"label":"Add a Range", "icon":"iconNew", "data":"newCategorie", "objectClass":["Categorie"]},
				{"label":"Add a Product", "icon":"products", "data":"newProduit", "objectClass":["Categorie"],"needFocus":1},
				{"label":"Add a Model", "icon":"packaging", "data":"newModele", "objectClass":["Produit"],"needFocus":1},
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
		{"type":"DividedBox","direction":"horizontal", "id":"listBox", "label":"Liste", "percentWidth":100, "percentHeight":100, "setStyle":{"closable":0, "paddingTop":0}, 
		"components":[
			{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,
				"kobeyeClass":{
					"module":"Vitrine",
					"objectClass":"SubRange",
					"label":"Nom",
					"identifier":"Id",
					"icon":"products",
					"form":"FormBase.json",
					"children":["SubRange","SubProduct"]
				},
				"otherKobeyeClass":{
					"SubProduct":{"module":"Vitrine","objectClass":"SubProduct","identifier":"Id","label":"Nom","form":"FormBase.json", "iconField":"Image", "children":["SubModel"]},
					"SubModel":{"module":"Vitrine","objectClass":"SubModel","identifier":"Id","label":"Nom","form":"FormBase.json", "iconField":"CodeBarre"}
				},
				"events":[
					{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
					{"type":"proxy","triggers":[
						{"trigger":"refresh", "action":"invoke", "method":"loadData"}
					]}
				],
				"actions":[
					{"type":"init", "action":"loadData"}
				]
			},
			{"type":"VBox","id":"obj_state","percentHeight":100,"percentWidth":"auto"}
		]}
	],
	"actions":[
	]
	}
}