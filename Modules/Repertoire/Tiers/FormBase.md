[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]

//[STORPROC Systeme/Group/Nom=GESTION|grp][/STORPROC]


{"form":{"type":"VBox","id":"Tiers?","label":"Tiers","percentHeight":100,
"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":0,"verticalGap":2},"clipContent":0,
"kobeyeClass":{"module":"Repertoire","objectClass":"Tiers"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}}
		]}
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"menuItems":[
		{"children":[
			{"label":"$__New__$","icon":"new","data":"new"},
			{"label":"$__Save__$","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"$__Save & Close__$","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"$__Close__$","icon":"close","data":"close"},
			{"type":"vseparator"},
			{"label":"$__Cancel__$","icon":"back", "data":"cancel","stateGroup":"updated"},
			{"label":"$__Delete__$","icon":"iconDelete","data":"delete","stateGroup":"saved"}
		]}
	]},
	{"type":"EditContainer", "id":"edit","percentHeight":100,
	"components":[
		{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
		"components":[							
			{"type":"TabNavigator","percentWidth":65,"percentHeight":100,"popUpButtonPolicy":"off","closePolicy":"close_never","setStyle":{"paddingTop":0},
			"components":[
				{"type":"HBox","label":"Général","percentWidth":100,"percentHeight":100,"minHeight":0,"setStyle":{"horizontalGap":0,"paddingTop":0},
				"components":[							
					{"type":"VBox","percentWidth":100,"setStyle":{"verticalGap":4,"paddingTop":0},
					"components":[
						{"type":"Panel","titleHeight":15,"title":"Description","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
								{"type":"FormItem","labelWidth":100,"label":"Code & intitulé","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":1},"components":[
									{"type":"TextInput","dataField":"CodeTiers","width":90,"maxChars":10,"validType":"string","required":1,"formLabel":1},
									{"type":"TextInput","dataField":"Intitule","percentWidth":100,"validType":"string" ,"required":1}
								]},
								{"type":"FormItem","labelWidth":100,"label":"Type & sous type","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":1},"components":[
									{"type":"ComboBox","dataField":"TypeTiers","width":90 ,"requireSelection":1,
									"kobeyeClass":{"module":"Repertoire","query":"Repertoire/TypeTiers","objectClass":"TypeTiers","identifier":"Id","label":"Designation"},
									"actions":[
										{"type":"init","action":"loadData"}
									]},
									{"type":"ComboBox","dataField":"SousTypeTiers","percentWidth":100 ,
									"kobeyeClass":{"module":"Repertoire","query":"Repertoire/SousTypeTiers","objectClass":"SousTypeTiers","identifier":"Id","label":"Designation"},
									"actions":[
										{"type":"init","action":"loadData"}
									]}
								]},
								{"type":"FormItem","labelWidth":100,"label":"Enseigne","percentWidth":100,"components":[
									{"type":"ComboBox","dataField":"Enseigne","percentWidth":100 ,
									"kobeyeClass":{"module":"Repertoire","query":"Repertoire/Enseigne","objectClass":"Enseigne","identifier":"Id","label":"Designation"},
									"actions":[
										{"type":"init","action":"loadData"}
									]}
								]},
								{"type":"FormItem","labelWidth":100,"label":"Commercial","percentWidth":100,"components":[
									{"type":"ComboBox","dataField":"CommercialId","percentWidth":100 ,
									"kobeyeClass":{"module":"Systeme","query":"Systeme/Group/21/User","objectClass":"User","identifier":"Id","label":"Initiales"},
									"actions":[
										{"type":"init","action":"loadData"}
									]}
								]}
							]}
						]},
						{"type":"Panel","titleHeight":15,"title":"Adresse","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
								{"type":"FormItem","labelWidth":100,"label":"Adresse","percentWidth":100,"setStyle":{"verticalGap":1},"components":[
									{"type":"TextInput","dataField":"Adresse1","percentWidth":100,"validType":"string" },
									{"type":"TextInput","dataField":"Adresse2","percentWidth":100,"validType":"string" }
//									{"type":"TextInput","dataField":"Adresse3","percentWidth":100,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"CP & Ville","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":1},"components":[
									{"type":"TextInput","dataField":"CodPostal","width":65,"validType":"string" },
									{"type":"TextInput","dataField":"Ville","percentWidth":100,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"Pays","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":2},"components":[
//									{"type":"TextInput","dataField":"Cedex","width":65,"validType":"string" },
									{"type":"TextInput","dataField":"Pays","percentWidth":100,"validType":"string"}
								]},
								{"type":"FormItem","labelWidth":100,"label":"GPS","percentWidth":100,"components":[
									{"type":"HGroup","gap":2,"components":[
										{"type":"TextInput","dataField":"GPS","percentWidth":100,"validType":"string"},
										{"type":"Geocoder","width":20,"height":20,"cornerRadius":10,"image":"locate","borderWidth":0,"label":"Localiser",
										"params":{"location":"GPS","defaultCountry":"FRANCE","country":"Pays",
										"postalCode":"CodPostal","city":"Ville","street":["Adresse1","Adresse2"],
										"key":"Fmjtd%7Cluua256ynu%2Cbl%3Do5-962l5w"}}
									]}
								]}
							]}
						]},
						{"type":"Panel","titleHeight":15,"title":"Contact","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
								{"type":"FormItem","labelWidth":100,"label":"Téléphone & fax","percentWidth":100,
								"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":1,"verticalAlign":"bottom"},"components":[
									{"type":"TextInput","dataField":"Telephone","percentWidth":100,"validType":"string" },
									{"type":"TextInput","dataField":"Fax","percentWidth":100,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"Email","percentWidth":100,"components":[
									{"type":"TextInput","dataField":"Email","percentWidth":100,"validType":"email" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"Site Web","percentWidth":100,"components":[
									{"type":"TextInput","dataField":"SiteWeb","percentWidth":100,"validType":"string" }
								]}
							]}
						]},
						{"type":"Panel","titleHeight":15,"title":"Configuration","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Group","percentWidth":100,"layout":{"type":"HorizontalLayout","verticalAlign":"baseline","gap":4,"paddingLeft":6,"paddingRight":6,"paddingTop":0,"paddingBottom":1},
							"components":[
								{"type":"Label","text":"Livreur"},
								{"type":"CheckBox","dataField":"Livreur" },
								{"type":"Spacer","width":30},
								{"type":"Label","text":"Transporteur"},
								{"type":"CheckBox","dataField":"Transporteur" }
							]}
						]},
						{"type":"Panel","titleHeight":15,"title":"Autres","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
								{"type":"FormItem","labelWidth":100,"label":"Commentaires","percentWidth":100,"components":[
									{"type":"TextArea","dataField":"Commentaires","percentWidth":100,"height":65,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"Archive","percentWidth":100,"components":[
									{"type":"CheckBox","dataField":"Archive" }
								]}
							]}
						]}
					]}
				]},
				{"type":"HBox","label":"Administratif","percentWidth":100,"percentHeight":100,"minHeight":0,"setStyle":{"horizontalGap":0,"paddingTop":0},
				"components":[
					{"type":"VBox","percentWidth":100,"setStyle":{"verticalGap":4,"paddingTop":0},
					"components":[
						{"type":"Panel","titleHeight":15,"title":"Facturation","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
								{"type":"FormItem","labelWidth":100,"label":"Adresse","percentWidth":100,"setStyle":{"verticalGap":1},"components":[
									{"type":"TextInput","dataField":"AdresseFac1","percentWidth":100,"validType":"string" },
									{"type":"TextInput","dataField":"AdresseFac2","percentWidth":100,"validType":"string" }
//									{"type":"TextInput","dataField":"AdresseFac3","percentWidth":100,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"CP & Ville","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":1},"components":[
									{"type":"TextInput","dataField":"CodPostalFac","width":65,"validType":"string" },
									{"type":"TextInput","dataField":"VilleFac","percentWidth":100,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":100,"label":"Cedex & Pays","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2,"verticleGap":2},"components":[
//									{"type":"TextInput","dataField":"CedexFac","width":65,"validType":"string" },
									{"type":"TextInput","dataField":"PaysFac","percentWidth":100,"validType":"string"}
								]}
							]}
						]},
						{"type":"Panel","titleHeight":15,"title":"Administratif","setStyle":{"dropShadowVisible":0},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
								{"type":"FormItem","labelWidth":110,"label":"RCS","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2},"components":[
									{"type":"TextInput","dataField":"Rcs","percentWidth":100,"validType":"string" },
									{"type":"Spacer","width":4},
									{"type":"Label","text":"APE","height":"18","setStyle":{"verticalAlign":"bottom"}},
									{"type":"TextInput","dataField":"Ape","width":40,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":110,"label":"Capital","percentWidth":100,"components":[
									{"type":"TextInput","dataField":"Capital","width":100,"maxChars":10,"validType":"float" }
								]},
								{"type":"FormItem","labelWidth":110,"label":"Compte compta","percentWidth":100,"components":[
									{"type":"TextInput","dataField":"CompteComptable","width":100,"validType":"string" }
								]},
								{"type":"FormItem","labelWidth":110,"label":"Réglement","percentWidth":100,"components":[
									{"type":"ComboBox","dataField":"ModeReglement","percentWidth":100 ,
									"kobeyeClass":{"module":"Devis","query":"Devis/ModeReglement","objectClass":"ModeReglement","identifier":"Code","label":"Designation"},
									"actions":[
										{"type":"init","action":"loadData"}
									]}
								]},
								{"type":"FormItem","labelWidth":110,"label":"Code tarif","percentWidth":100,"components":[
									{"type":"ComboBox","dataField":"CodeTarif","percentWidth":100 ,
									"kobeyeClass":{"module":"Devis","query":"Devis/CodeTarif","objectClass":"CodeTarif","identifier":"Id","label":"CodeTarif"},
									"actions":[
										{"type":"init","action":"loadData"}
									]}
								]},
								{"type":"FormItem","labelWidth":110,"label":"Factures groupées","percentWidth":100,"components":[
									{"type":"CheckBox","dataField":"FactureGroupee"}
								]},
								{"type":"FormItem","labelWidth":110,"label":"N° & taux TVA","percentWidth":100,"direction":"horizontal","setStyle":{"horizontalGap":2},"components":[
									{"type":"TextInput","dataField":"NumTva","percentWidth":100,"validType":"string" },
									{"type":"ComboBox","dataField":"CodeTVA","width":60 ,
									"kobeyeClass":{"module":"Devis","query":"Devis/TVA","objectClass":"TVA","identifier":"Code","label":"Taux"},
									"actions":[
										{"type":"init","action":"loadData"}
									]}
								]},
								{"type":"FormItem","labelWidth":110,"label":"Remise","percentWidth":100,"components":[
									{"type":"TextInput","dataField":"Remise","width":100,"maxChars":10,"validType":"float" }
								]}
							]}
						]}
					]}
				]}
			]}
			[OBJ [!I::Module!]|[!I::TypeChild!]|O]
			[!cat:=0!]
			[!OngletNum:=0!]
			[STORPROC [!O::getChildElements!]|Enf]
				[IF [!Enf::hidden!]!=1]
					[IF [!OngletNum!]=0]
						,{"type":"TabNavigator", "id":"objectTabNav", "percentWidth":35, "percentHeight":100, "closePolicy":"close_never", "minTabWidth":"150",
						"setStyle":{"paddingTop":1},"stateGroup":"saved",
						"components":[
					[/IF]
					[IF [!OngletNum!]>0],[/IF]
					[MODULE Systeme/formChildren?I=[!I!]&O=[!O!]&OngletNum=[!OngletNum!]&Enf=[!Enf!]&cat=[!cat!]]
					[!OngletNum+=1!]
				[/IF]
			[/STORPROC]
			[IF [!OngletNum!]>0]
						],
						"events":[
							{"type":"proxy","triggers":[
								{"trigger":"proxy_kobeye_status","action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","checkId":1}}
							]}
						]}
			[/IF]
			
			
		]}
	],
	"events":[
		{"type":"start","action":"loadValues","params":{"needsId":1}},
		{"type":"proxy","triggers":[
			{"trigger":"saveClose","action":"invoke","method":"saveData","params":{"closeForm":1}},
			{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":0}},
			{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
			{"trigger":"delete","action":"invoke","method":"deleteData"},
			{"trigger":"cancel","action":"invoke","method":"restart"},
			{"trigger":"new","action":"invoke","method":"clearData"}
		]}
	]}
],
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}


