{"form":
	{"type":"VBox","percentWidth":100,"percentHeight":100,"id":"GroupTree", "label":"Utilisateurs",
	"setStyle":{"closable":0, "paddingTop":0,"verticalGap":0},"localProxy":1,
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1, "menuItems":[
			{"label":"", "children":[
				{"label":"Modifier", "icon":"open", "data":"open" ,"needFocus":1,"needWrite":1},
				{"label":"Supprimer", "icon":"deleteShelf", "data":"delete","needFocus":1,"needWrite":1},
				{"label":"Nouveau Groupe", "icon":"iconNew", "data":"newGroup", "objectClass":["Group"],"needWrite":1},
				{"label":"Nouvel Utilisateur", "icon":"userManagement", "data":"newUser", "objectClass":["Group"],"needFocus":1,"needWrite":1},
				{"label":"Rafraichir", "icon":"refresh", "data":"refresh"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"resetParent":1}},
				"newGroup":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"asParent":1,"objectClass":"Group"}},
				"newUser":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"asParent":1,"objectClass":"User"}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
			}}
		]},
		[STORPROC Systeme/Group/Group/Nom=MAS_ALESTI|G][/STORPROC]
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,"rights":1,
			"kobeyeClass":{
				"module":"Systeme",
				"objectClass":"Group",
				"parentClass":"Group",
				"parentId":"[!G::Id!]",
				"label":"Nom",
				"identifier":"Id",
				"icon":"products",
				"form":"FormBase.json",
				"children":["Group","User"]
			},
			"otherKobeyeClass":{
				"User":{
					"module":"Systeme",
					"objectClass":"User",
					"identifier":"Id",
					"label":"Nom",
					"icon":"user",
					"form":"FormBase.json",
					"applyFilter":1,
					"extra":{"other":"Login,Prenom"},
					"columns":[
						{"field":"Login","type":"varchar","percentWidth":100},
						{"field":"Prenom","type":"varchar","percentWidth":100}
					]
				}
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