[!usr:=[!Systeme::User!]!]
[!adhs:=[!usr::getChildren(Adherent)!]!]
[STORPROC [!adhs!]|adh|0|1][/STORPROC]
[!boks:=[!adh::getParents(Book)!]!]
[STORPROC [!boks!]|bok|0|1][/STORPROC]


[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"[!I::Module!]:[!I::TypeChild!]:[!usr::Id!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0, "paddingLeft":5, "paddingRight":5},
	"kobeyeClass":{"module":"Axenergie","objectClass":"Database","id":"[!I::LastId!]"},
	"localProxy":{
		"actions":{
			"proxy_kobeye_status":{"action":[
				{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
				{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
				{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
				{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}}
			]},
			"PopupImage":{"action":"invoke","method":"loadFormWithID","params":{"kobeyeClass":{"module":"Axenergie","objectClass":"Database","form":"FormImageCatalogue.json"}}}
		}
	},
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1,
		"menuItems":[
			{"children":[
				{"label":"Modifier", "icon":"open", "data":"open","needFocus":1,"objectClass":["SubModel"]},
				{"label":"Suppimer", "icon":"remove", "data":"delete","needFocus":1,"objectClass":["SubRange","SubProduct","SubModel"]},
				{"label":"Mettre à jour", "icon":"open","needFocus":1, "data":"update","objectClass":["SubRange","SubProduct","SubModel"]},
				{"label":"Rafraichir", "icon":"refresh", "data":"refresh"},
				{"label":"Générer Catalogue", "icon":"print", "data":"PopupImage"},
				{"label":"Voir Catalogue", "icon":"oeil2", "data":"VoirCatalogue"},
				//{"label":"Catalogue Service", "icon":"oeil2", "data":"VoirService"},
				{"label":"Liens Externes", "icon":"oeil1", "data":"pdf"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"resetParent":1}},
				"update":{"type":"click","action":"invoke","method":"callMethod","params":{"method":"object","function":"update","currentSelection":1}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"},
				//"VoirCatalogue":{"type":"click", "action":"invoke", "method":"goToURL","params":{"url":"Flipbook/Book/[!bok::Id!].htms"}},
				"VoirCatalogue":{"type":"click", "action":"invoke","method":"callMethod","params":{
				"method":"query","data":{"dirtyChild":1,"module":"Axenergie","objectClass":"Database"},"function":"openBook"}},
				//"VoirService":{"type":"click", "action":"invoke", "method":"goToURL","params":{"url":"Flipbook/Book/118.htms"}},
				"pdf":{"action":"invoke","method":"callMethod","params":{
				"method":"object","data":{"module":"Axenergie","objectClass":"Database"},"function":"CreatePDF"}}
			}}
		]},
		{"type":"HBox","percentWidth":100,"setStyle":{"verticalAlign":"bottom"},
		"components":[
			{"type":"HBox","percentWidth":50,"setStyle":{"verticalAlign":"bottom"},"components":[
				{"type":"Label","text":"Global database","setStyle":{"align":"center","fontSize":15}},
				{"type":"Spacer","percentWidth":100},
				{"type":"Label","text":"Marque"},
				{"type":"TextInput","dataField":"Marque","width":100}
			]},
			{"type":"Label","text":"My local database","percentWidth":50,"setStyle":{"align":"center","fontSize":15}}
		]},
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
					"children":["Categorie","Produit"],
					"iconField":"ImageEntete"
				},
				"otherKobeyeClass":{
					"Produit":{"module":"Axenergie","objectClass":"Produit","identifier":"Id","label":"Nom", "iconField":"ImageProduit",
					"applyFilter":1,"extra":{"other":"Description,Marque,PrixHT"},"children":["Modele"],
					"columns":[
						{"field":"Marque","type":"varchar","width":70},
						{"field":"","type":"varchar","width":80},
						{"field":"","type":"price","width":40},
						{"field":"Description","type":"varchar","percentWidth":100}
					]},
					"Modele":{"module":"Axenergie","objectClass":"Modele","identifier":"Id","label":"Nom","form":"FormBase.json","iconField":"ImageCatalogue",
					"extra":{"other":"Description,Reference,PrixHT,Marque"},"checked":["SubModel"],
					"columns":[
						{"field":"Marque","type":"varchar","width":70},
						{"field":"Reference","type":"varchar","width":80},
						{"field":"PrixHT","type":"price","width":40},
						{"field":"Description","type":"varchar","percentWidth":100}
					]}
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
						"children":["SubRange","SubProduct"],
						"iconField":"ImageEntete"
					},
					"otherKobeyeClass":{
						"SubProduct":{"module":"Axenergie","objectClass":"SubProduct",
						"identifier":"Id","label":"Nom","children":["SubModel"],
						"iconField":"ImageProduit",
						"extra":{"linkObject":"Produit","linkField":"ProduitId"}},
						
						"SubModel":{"module":"Axenergie","objectClass":"SubModel","iconField":"ImageCatalogue",
						"extra":{"linkObject":"Modele","linkField":"ModeleId","versionField":"Version","subVersionField":"SubVersion",
						"other":"Description,Marque,Reference,PrixAdherent,Promo"},
						"identifier":"Id","label":"Nom","form":"FormDetail.json",
						"columns":[
							{"field":"Marque","type":"varchar","width":70},
							{"field":"Reference","type":"varchar","width":80},
							{"field":"PrixAdherent","type":"price","width":40},
							{"field":"Promo","type":"boolean","width":18},
							{"field":"Description","type":"varchar","percentWidth":100}
						]}
					}
				},
				"events":[
					{"type":"proxy","triggers":[
						{"trigger":"refresh", "action":"invoke", "method":"refreshData"},
						{"trigger":"Marque","action":"invoke","method":"filterData","params":{"vars":["Marque"]}}
					]},
					{"type":"check","action":"invoke","method":"callMethod","params":{"method":"object","module":"Axenergie","objectClass":"Database","id":"[!I::LastId!]","function":"addSub","args":[]}},
					{"type":"uncheck","action":"invoke","method":"callMethod","params":{"method":"object","module":"Axenergie","objectClass":"Database","id":"[!I::LastId!]","function":"removeSub","args":[]}}
				],
				"actions":[
					{"type":"start", "action":"invoke", "method":"refreshData"}
				]
			}
		]}
	],
	"actions":[
	]
}
}