{"form":
	{"type":"VBox","percentWidth":100,"percentHeight":100,"id":"DocumentTree", "label":"Documents",
	"setStyle":{"closable":0, "paddingTop":0,"verticalGap":0,"fontSize":12},"localProxy":1,
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1, "menuItems":[
			{"label":"", "children":[
				{"label":"Ouvrir","icon":"open","data":"open","objectClass":["Document"],"needWrite":1},
				{"label":"Nouveau","icon":"iconNew","data":"newDoc","objectClass":["Categorie"],"needWrite":1},
				{"label":"Supprimer","icon":"deleteShelf","data":"delete","objectClass":["Document"],"needWrite":1},
				{"label":"Rafraichir","icon":"refresh","data":"refresh"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","resetParent":1}},
				"newDoc":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"Document"}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
			}}
		]},
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,"rights":1,
		"setStyle":{"fontSize":14},
			"kobeyeClass":{
				"module":"Mas",
				"objectClass":"Categorie",
				"label":"Categorie",
				"identifier":"Id",
				"icon":"products",
				"form":"FormBase.json",
				"children":["Categorie","Document"]
			},
			"otherKobeyeClass":{
				"Document":{"module":"Mas","objectClass":"Document","identifier":"Id","label":"Titre","form":"FormBase.json",
				"applyFilter":0,"view":"DocumentUser","setFilter":"UserDocument1.UserId=[!Systeme::User::Id!]",
				"extra":{"other":"DatePublication,UserDocument1.DateConsultation"},
				"columns":[
					{"field":"DateConsultation","type":"date","percentWidth":30},
					{"field":"DatePublication","type":"date","percentWidth":30}
					//,{"field":"AlertIcon","type":"image","width":20}
				]}
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