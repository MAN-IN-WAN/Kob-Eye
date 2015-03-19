{"form":{"type":"VBox","id":"StockLogistique/Tournee","label":"Tournée","labelPrefix":"TR:","icon":"refresh",
"percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0,"paddingLeft":5,"paddingRight":5},
"kobeyeClass":{"module":"StockLogistique","objectClass":"Tournee"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}}
		]},
		"Etat":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"valide","property":"enabled","conditions":[{"compare":"Etat=30"}]}},
			{"action":"invoke","method":"groupState","params":{"group":"retour","property":"enabled","conditions":[{"compare":"Etat=31"}]}}
		]}
	}
},
//"focusedID":"reference",
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"menuItems":[
		{"children":[
			{"label":"$__Save__$", "icon":"save", "data":"save","stateGroup":"updated"},
			{"label":"$__Save & Close__$", "icon":"save", "data":"saveClose","stateGroup":"updated"},
			{"label":"$__More__$","icon":"down","data":"more","children":[
				{"label":"Valider départ", "icon":"validate","data":"valideDepart","stateGroup":"valide"},
				{"label":"Imprimer","icon":"print","data":"printTournee","stateGroup":"savedIdle"},
//				{"label":"Contrôle tournée", "icon":"validate","data":"controle"},
				{"label":"Tournée effectuée", "icon":"select","data":"trnEffectue","stateGroup":"retourXXX"},
				{"label":"Tournée non effectuée", "icon":"unselect","data":"trnNonEffectue","stateGroup":"retourXXX"}
			]}
		]}
	],
	"actions":[
		{"type":"itemClick","actions":{
			"valideDepart":{"action":"invoke","method":"callMethod","params":{
				"interface":1,
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/valideTournee.json",
				"proxyValues":{"vars":{"Date":{"args":[{"dataValue":["formCreator#Date"]}]}}}},
				"function":"ValideTournee","selectionRequired":0,"args":[{"id":["parentForm"]},{"interfaceValues":["Date"]},{"value":[0]}]}
			},
//			"valideRetour":{"action":"invoke","method":"callMethod","params":{
//				"confirm":{"text":"Contrôle retour"},
//				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee",
//				"function":"ControleReprise","args":"id:parentForm"}
//			},
			"printTournee":{"action":"invoke","method":"callMethod","params":{
				"interface":1,
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/printTournee.json"},
				"function":"PrintDocuments","args":[{"id":["parentForm"]},{"interfaceValues":["Tournee","BL","Fond"]},{"value":[0]}]}
			},
			"trnEffectue":{
				"action":"invoke","method":"callMethod","params":{
				"confirm":{"text":"Tournée effectuée"},
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee"},
				"function":"SauveTournee","args":"v:1,dv:*"}
			},
			"trnNonEffectue":{
				"action":"invoke","method":"callMethod","params":{
				"confirm":{"text":"Tournée non effectuée"},
				"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee"},
				"function":"SauveTournee","args":"v:0,dv:*"}
			},
			"retour":{
				"action":"invoke","method":"callMethod","params":{
				"interface":1,
				"method":"query","data":{"dirtyChild":1,"module":"StockLogistique","objectClass":"Tournee","form":"Functions/valideRetour.json",
				"proxyValues":{"vars":{"Date":{"args":"dv:formCreator#Date"}}}},
				"function":"ValideRetour","args":[{"value":[null]},{"interfaceValues":["Date"]}]}
			},
			"control":{
				"action":"invoke","method":"callMethod","params":{
				"method":"query","data":{"dirtyChild":1,"module":"StockLogistique","objectClass":"Tournee"},
				"function":"ControleReprise","args":"dv:elements"}
			}
		}}
	]},

	{"type":"Box","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0}, 
	"components":[
		{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100, 
		// "defaultButtonID":"search",
		"components":[
			{"type":"VGroup","percentWidth":100,"percentHeight":100,
			"components":[
				{"type":"TitledBorderBox","title":"Feuille de Route",
				"components":[
					{"type":"Group","percentWidth":100,"layout":{"type":"HorizontalLayout","verticalAlign":"baseline","gap":4,"paddingLeft":6,"paddingRight":0,"paddingTop":0,"paddingBottom":4},
					"components":[
						{"type":"Label","text":"Référence"},
						{"type":"TextInput","dataField":"Reference","width":50,"formLabel":1,"editable":0},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Date"},
						{"type":"DateField","dataField":"Date"},
						//{"type":"Spacer","width":4},
						//{"type":"Label","text":"Validée"},
						//{"type":"CheckBox","dataField":"Valide","editable":0,"forceEvent":1},
						//{"type":"Spacer","width":4},
						//{"type":"Label","text":"Effectué"},
						//{"type":"CheckBox","dataField":"Effectue","forceEvent":1},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Etat"},
						{"type":"ComboBox","dataField":"Etat","width":85,"editable":0,"defaultValue":30,"forceEvent":1,
						"kobeyeClass":{"module":"StockLogistique","objectClass":"Status","identifier":"Code","label":"Etat","filters":"Type=T"},
						"actions":[
							{"type":"init","action":"loadData"}
						]},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Chauffeur"},
						{"type":"ComboBox","dataField":"ChauffeurId","width":150,"requireSelection":0,
						"kobeyeClass":{"module":"Repertoire","objectClass":"Tiers","identifier":"Id","label":"Intitule","filters":"Livreur=1+Transporteur=1"},
						"actions":[
							{"type":"init","action":"loadData"}
						]},
						{"type":"Spacer","width":4},
						{"type":"Label","text":"Véhicule"},
						{"type":"ComboBox","dataField":"VehiculeId","width":150,"requireSelection":0,
						"kobeyeClass":{"module":"StockLogistique","objectClass":"Vehicule","identifier":"Id","label":"Designation"},
						"actions":[
							{"type":"init","action":"loadData"}
						]}
					]}
				]},
				{"type":"VGroup","percentHeight":100,"percentWidth":100,
				"components":[
					{"type":"AdvancedDataGrid","dataField":"lignes","updatedItems":0,
					"percentWidth":100,"height":200,"rowHeight":20,"variableRowHeight":1,"hierarchical":1,
					//"kobeyeClass":{"module":"StockLogistique","objectClass":"lignes"},
					"events":[
						{"type":"start", "action":"invoke","method":"callMethod",
						"params":{"method":"object",
						"function":"GetLignes","args":[{"id":["parentForm"]}]}},
						{"type":"proxy","triggers":[
							{"trigger":"valide","action":"invoke","method":"restart"},
							{"trigger":"livre","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"Status::Livré,Status_Color::0x00ff00,Etat::12,_etat::0,ActionTexte::,Action::"}}
							]},
							{"trigger":"nonLivre","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"Status::Non livré,Status_Color::0xff0000,Etat::13,_etat::1"}}
							]},
							{"trigger":"remiseLivr","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"ActionTexte::Remise en livraison,Action::1,Status::Non livré,Status_Color::0xff0000,Etat::23,_etat::1"}}
							]},
							{"trigger":"repris","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"Status::Repris,Status_Color::0x00ff00,Etat::22,_etat::0,ActionTexte::,Action::"}}
							]},
							{"trigger":"nonRepris","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"Status::Non repris,Status_Color::0xff0000,Etat::23,_etat::1"}}
							]},
							{"trigger":"remiseRepr","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"ActionTexte::Remise en reprise,Action::1,Status::Non repris,Status_Color::0xff0000,Etat::23,_etat::1"}}
							]},
							{"trigger":"efface0","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"_etat::0,ActionTexte::,Action::"}}
							]}
						]}
					],
					"columns":[
						{"type":"column","dataField":"Reference","headerText":"BL/BR","width":55},
						{"type":"column","dataField":"Type","headerText":"T","width":20},
						{"type":"column","dataField":"DateLR","headerText":"Date","format":"date","width":60},
						{"type":"column","dataField":"Status","headerText":"Etat","width":70},
						{"type":"column","dataField":"LivraisonId","headerText":"Magasin","width":150},
						{"type":"column","dataField":"CodPostal","headerText":"CP","width":50},
						{"type":"column","dataField":"Ville","headerText":"Ville","width":100},
						{"type":"column","dataField":"ClientId","headerText":"Client","width":150},
						{"type":"column","dataField":"ActionTexte","headerText":"Action","width":200},
						{"type":"column","dataField":"ActionTraite","headerText":"A","width":20,"format":"boolean"},
						{"type":"column","width":0}
					],
					"contextMenu":[
						{"label":"Livré","data":"livre","icon":"select","compare":"Type=L&ActionTraite=0"},
						{"label":"Non Livré","data":"nonLivre","icon":"unselect","compare":"Type=L&ActionTraite=0"},
						{"label":"Remettre en livraison","data":"remiseLivr","icon":"back","compare":"Type=L&ActionTraite=0"},
						{"label":"Repris","data":"repris","icon":"select","compare":"Type=R&ActionTraite=0"},
						{"label":"Non Repris","data":"nonRepris","icon":"unselect","compare":"Type=R&ActionTraite=0"},
						{"label":"Remettre en reprise","data":"remiseRepr","icon":"back","compare":"Type=R&ActionTraite=0"},
						{"label":"Effacer l'action","data":"efface0","icon":"iconDelete","compare":"Action>0&ActionTraite=0"}
					]},
					{"type":"AdvancedDataGrid","dataField":"elements","id":"elements","updatedItems":0,
					"percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,"checkBoxes":0,
					//"kobeyeClass":{"module":"StockLogistique","objectClass":"elements"},
					"events":[
						//{"type":"start","action":"loadValues","params":{"needsParentId":1}},
						{"type":"start", "action":"invoke","method":"callMethod",
						"params":{"method":"object","function":"GetReprises","args":[{"id":["parentForm"]}]}},
						{"type":"proxy","triggers":[
							{"trigger":"valide","action":"invoke","method":"restart"},
							{"trigger":"remiseLivr1","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"ActionTexte::Remise en livraison,Controle::1,Action::1"}}
							]},
							{"trigger":"remiseRepr1","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"ActionTexte::Remise en reprise,Controle::1,Action::1"}}
							]},
							{"trigger":"remiseStock","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"ActionTexte::Remise en stock,Controle::1,Action::5"}}
							]},
							{"trigger":"efface1","action":[
								{"action":"invoke","method":"setItemValues","params":{"values":"ActionTexte::,Controle::0,Action::0"}}
							]}
						]}
					],
					"columns":[
						{"type":"column","dataField":"Article","headerText":"Article","width":100},
						{"type":"column","dataField":"Reference","headerText":"Reference","width":130},
						{"type":"column","dataField":"Quantite","headerText":"Qte","width":40,"format":"0dec"},
						{"type":"column","dataField":"Livraison","headerText":"Magasin","width":140},
						{"type":"column","dataField":"Client","headerText":"Client","width":140},
						{"type":"column","dataField":"Commentaire","headerText":"Commentaire","width":220},
						{"type":"column","dataField":"Controle","headerText":"C","format":"checkbox","width":20},
						{"type":"column","dataField":"ActionTexte","headerText":"Action","width":200},
						{"type":"column","dataField":"ActionTraite","headerText":"A","width":20,"format":"boolean"},
						{"type":"column","width":0}
					],
					"contextMenu":[
						{"label":"Remettre en livraison","data":"remiseLivr1","icon":"back","compare":"Anomalie=1&ActionTraite=0"},
						{"label":"Remettre en reprise","data":"remiseRepr1","icon":"back","compare":"Anomalie=3&ActionTraite=0"},
						{"label":"Echanger avec...","data":"echange","icon":"refresh","compare":"Anomalie=3&ActionTraite=0"},
						{"label":"Remettre en stock","data":"remiseStock","icon":"addlevel","compare":"Anomalie=3&ActionTraite=0"},
						{"label":"Effacer l'action","data":"efface1","icon":"iconDelete","compare":"Action>0&ActionTraite=0"}
					]},
					{"type":"LocEchange","id":"locEchange",
					"events":[
						{"type":"proxy","triggers":[
							// _action = 2 et _elementId = ElementId de l'element a echanger 
							{"trigger":"echange","action":"invoke","method":"show","params":{"dataGrid":"elements"}}
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"saveClose","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"save","action":"invoke","method":"saveData"},
				{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
				{"trigger":"cancel","action":"invoke","method":"cancelEdit"}
			]},
			{"type":"save","action":"invoke","method":"callMethod",
			"params":{"method":"object","function":"SaveRetour","args":[{"dataValue":["*"]}]}}
		]}
	]}
],
"actions":[
	{"type":"close","action":"confirmUpdate"}
]}
}
