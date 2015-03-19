[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"FT:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0,"verticalGap":0},"localProxy":1, 
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1, "menuItems":[
			{"label":"", "children":[
				//{"label":"Ouvrir", "icon":"open", "data":"open","needFocus":1,"needWrite":1},
				{"label":"Modifier", "icon":"open", "data":"open" ,"needFocus":1,"needWrite":1},
				{"label":"Supprimer", "icon":"iconDelete", "data":"delete","needFocus":1,"needWrite":1},
				{"label":"Nouvelle Catégorie", "icon":"iconNew", "data":"newCategorie", "objectClass":["Categorie"],"needWrite":1},
				{"label":"Nouveau Produit", "icon":"productsManagement", "data":"newProduit", "objectClass":["Categorie"],"needFocus":1,"needWrite":1},
				{"label":"Nouveau Modèle", "icon":"modelManagement", "data":"newModele", "objectClass":["Produit"],"needFocus":1,"needWrite":1},
				{"label":"Rafraichir", "icon":"refresh", "data":"refresh"}
				//{"label":"Importation", "icon":"levelDown", "data":"import","needFocus":1,"needWrite":1}
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
		{"type":"HBox","percentWidth":50,"setStyle":{"verticalAlign":"bottom"},"components":[
			{"type":"Label","text":"  Marque"},
			{"type":"TextInput","dataField":"Marque","width":100}
		]},
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,"drag":1,"rights":1,
			"kobeyeClass":{
				"module":"Axenergie",
				"objectClass":"Categorie",
				"label":"Nom",
				"identifier":"Id",
				"icon":"products",
				"iconField":"ImageEntete",
				"form":"FormBase.json",
				"children":["Categorie","Produit"]
			},
			"otherKobeyeClass":{
				"Produit":{"module":"Axenergie","objectClass":"Produit","identifier":"Id","label":"Nom","form":"FormBase.json","iconField":"ImageProduit",
				"applyFilter":1,"extra":{"other":"Description,Marque,PrixHT"},"children":["Modele"],
				"columns":[
					{"field":"Marque","type":"varchar","width":80},
					{"field":"","type":"varchar","width":100},
					{"field":"","type":"price","width":50},
					{"field":"Description","type":"varchar","percentWidth":100}
				]},
				"Modele":{"module":"Axenergie","objectClass":"Modele","identifier":"Id","label":"Nom","form":"FormBase.json","iconField":"ImageCatalogue",
				"extra":{"other":"Description,Reference,PrixHT,Marque"},
				"columns":[
					{"field":"Marque","type":"varchar","width":80},
					{"field":"Reference","type":"varchar","width":100},
					{"field":"PrixHT","type":"price","width":50},
					{"field":"Description","type":"varchar","percentWidth":100}
				]}
			},
			"events":[
				{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
				{"type":"proxy","triggers":[
					{"trigger":"Marque","action":"invoke","method":"filterData","params":{"vars":["Marque"]}},
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