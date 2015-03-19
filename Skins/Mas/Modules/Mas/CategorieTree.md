{"form":
	{"type":"VBox","percentWidth":100,"percentHeight":100,"id":"CategorieTree", "label":"Catégories",
	"setStyle":{"closable":0, "paddingTop":0,"verticalGap":0},"localProxy":1,
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1, "menuItems":[
			{"label":"", "children":[
				{"label":"Modifier", "icon":"open", "data":"open" ,"needFocus":1,"needWrite":1},
				{"label":"Supprimer", "icon":"deleteShelf", "data":"delete","needFocus":1,"needWrite":1},
				{"label":"Nouvelle Catégorie", "icon":"iconNew", "data":"newCat", "objectClass":["Categorie"],"needWrite":1},
				{"label":"Rafraichir", "icon":"refresh", "data":"refresh"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"resetParent":1}},
				"newCat":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"asParent":1,"objectClass":"Categorie"}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
			}}
		]},
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,"rights":1,
			"kobeyeClass":{
				"module":"Mas",
				"objectClass":"Categorie",
				"label":"Categorie",
				"identifier":"Id",
				"icon":"products",
				"form":"FormBase.json",
				"children":["Categorie"]
			},
			"events":[
				{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
				{"type":"proxy","triggers":[
					{"trigger":"refresh", "action":"invoke", "method":"refreshData"}
				]}
			],
			"actions":[
				{"type":"start","action":"invoke","method":"refreshData"}
			]
		}
	],
	"actions":[
	]
	}
}