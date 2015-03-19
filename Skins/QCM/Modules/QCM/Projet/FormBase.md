{"form":{"type":"GradientVBox","id":"FB:Projet?","label":"Projet","percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5},"clipContent":0,
"kobeyeClass":{"module":"QCM","objectClass":"Projet"},
"localProxy":{
	"actions":{
		"datagridpar":{"action":"invoke","method":"restart","objectId":"reponses"},
		"Date":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"DateLimite","args":"dv:Date,dv:Validite"}},
		"Validite":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"DateLimite","args":"dv:Date,dv:Validite"}},
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedAdmin","property":"enabled","hasID":1,"admin":1}}
		]},
		"SendProjet":{"action":"invoke","method":"callMethod","params":{
			"interface":1,
			"method":"query","data":{"dirtyChild":1,"module":"QCM","objectClass":"Projet","form":"Functions/SendProjet.json"},
			"function":"SendProjet","args":"iv:address"}
		},
		"PrintProjet":{"action":"invoke","method":"callMethod","params":{
			"interface":1,
			"method":"query","data":{"dirtyChild":1,"module":"QCM","objectClass":"Projet","form":"Functions/PrintProjet.json"},
			"function":"PrintProjet","args":"iv:reponse,v:0"}
		},
		"PrintRapport":{"action":"invoke","method":"callMethod","params":{
			"confirm":{"text":"Imprimer le rapport"},
			"method":"query","data":{"dirtyChild":1,"module":"QCM","objectClass":"Projet"},
			"function":"PrintRapport"}
		},
		"ApercuProjet":{"action":"invoke","method":"callMethod","params":{
			"method":"query","data":{"dirtyChild":1,"module":"QCM","objectClass":"Projet"},
			"function":"ApercuProjet"}
		}
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":1,"menuItems":[
		{"children":[
			{"label":"$__Save__$","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"$__Save & Close__$","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"$__Close__$","icon":"close","data":"close"},
			{"label":"$__Cancel__$","icon":"refresh","data":"cancel","stateGroup":"updated"},
			{"label":"$__Delete__$","icon":"iconDelete","data":"delete","stateGroup":"saved"},
			{"label":"Envoyer","icon":"sendMail","data":"SendProjet","stateGroup":"savedIdle"},
			{"label":"Imprimer","icon":"print","data":"PrintProjet","stateGroup":"savedIdle"},
			{"label":"Aperçu","icon":"oeil3","data":"ApercuProjet","stateGroup":"savedIdle"},
			{"label":"Rapport","icon":"print","data":"PrintRapport","stateGroup":"savedIdle"}
		]}
	]},
	{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
	"components":[
		{"type":"EditContainer","percentHeight":100, "id":"edit",
		"components":[
			{"type":"DataField","dataField":"Url"},
			{"type":"HBox","percentWidth":100,"percentHeight":100,
			"components":[	
				{"type":"VBox","percentWidth":100,"percentHeight":100,
				"components":[
					{"type":"CollapsiblePanel","title":"Description","layout":{"type":"HorizontalLayout"},"open":1,
					"components":[
						{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2,"paddingLeft":10,"paddingRight":10,"paddingTop":4,"paddingBottom":6},
						"components":[	
							{"type":"FormItem","percentLabel":28,"label":"Nom","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"Nom","percentWidth":100,"validType":"string" ,"maxChars":100,"required":1,"formLabel":1}
				
							]},
							{"type":"FormItem","percentLabel":28,"label":"Type de projet","percentWidth":100,"components":[
								{"type":"ComboBox","dataField":"TypeProjetId","width":100 ,"required":1,
								"kobeyeClass":{"module":"QCM","objectClass":"TypeProjet","query":"QCM/TypeProjet","identifier":"Id","label":"TypeProjet"},
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Titre","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"Description","percentWidth":100,"validType":"string" ,"maxChars":100,"required":1}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Date","percentWidth":100,"components":[
								{"type":"Group","percentWidth":100,"layout":{"type":"HorizontalLayout","verticalAlign":"baseline","gap":4},
								"components":[
									{"type":"DateField","dataField":"Date","validType":"date","defaultValue":"Now"},
									{"type":"Spacer","width":10},
									{"type":"Label","text":"Validité en jours","setStyle":{"color":"white","fontWeight":"bold"}},
									{"type":"TextInput","dataField":"Validite","width":40,"maxChars":4,"validType":"int","setStyle":{"textAlign":"end"}},
									{"type":"Spacer","width":10},
									{"type":"Label","text":"Limite","setStyle":{"color":"white","fontWeight":"bold"}},
									{"type":"DateField","dataField":"DateLimite","editable":0}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Commentaires","percentWidth":100,"components":[
								{"type":"TextArea","dataField":"Commentaires","percentWidth":100,"height":38}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Gestionnaire","percentWidth":100,"components":[
								{"type":"ComboBox","dataField":"GestionnaireId","percentWidth":100 ,
								"kobeyeClass":{"module":"Systeme","objectClass":"User","query":"Systeme/Group/Nom=QCM_ADMIN/User","identifier":"Id","label":"Login"},
								"actions":[
									{"type":"init","action":"loadData"}
								]}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Créateur","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"Createur","percentWidth":100,"validType":"string","maxChars":100,"editable":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Editeur","percentWidth":100,"components":[
								{"type":"TextInput","dataField":"Editeur","percentWidth":100,"validType":"string","maxChars":100,"editable":0}
							]},
							{"type":"FormItem","percentLabel":28,"label":"Archivé","percentWidth":100,"components":[
								{"type":"CheckBox","dataField":"Archive","percentWidth":100,"defaultValue":0}
							]}
						]}
					]},
					{"type":"MenuTab", "id":"menuTree","maxLines":1,"stateGroup":"saved","menuItems":[
						{"label":"", "children":[
							{"label":"Ouvrir", "icon":"open", "data":"open","needFocus":1,"needWrite":1},
							{"label":"Supprimer", "icon":"iconDelete", "data":"deleteItem","needFocus":1,"needWrite":1},
							{"label":"Nouvelle Page", "icon":"iconNew", "data":"newPage", "objectClass":["Page"],"needWrite":1},
							{"label":"Nouvelle Question", "icon":"productsManagement", "data":"newQuestion", "objectClass":["Page"],"needFocus":1,"needWrite":1},
							{"label":"Nouvelle Réponse", "icon":"productsManagement", "data":"newReponse", "objectClass":["Question"],"needFocus":1,"needWrite":1},
							{"label":"Rafraichir", "icon":"refresh", "data":"refresh"}
						]}
					],
					"actions":[
						{"type":"itemClick", "actions":{
							"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{}},
							"newPage":{"type":"click", "action":"invoke","method":"createForm","params":{"kobeyeClass":{"dirtyParent":1,"module":"QCM","objectClass":"Page","form":"FormDetail.json"}}},
							"newQuestion":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"asParent":1,"objectClass":"Question"}},
							"newReponse":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"asParent":1,"objectClass":"Reponse"}},
							"deleteItem":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
						}}
					]},
					{"type":"Tree","id":"tree", "percentWidth":100, "percentHeight":100,"drag":0,"rights":1,
						"kobeyeClass":{
							"dirtyParent":1,
							"module":"QCM",
							"objectClass":"Page",
							"label":"Numero",
							"identifier":"Id",
							"extra":{"other":"Nom"},
							"icon":"mx_borderContainer",
							"form":"FormDetail.json",
							"children":["Question"],
							"columns":[
								{"field":"label","width":30},
								{"field":"Nom","type":"varchar","percentWidth":100}
							]
						},
						"otherKobeyeClass":{
							"Question":{"module":"QCM","objectClass":"Question","identifier":"Id","label":"Numero","form":"FormDetail.json",
							"extra":{"other":"Question,Reponse"},"children":["Reponse"],"iconField":"Icon",
							"columns":[
								{"field":"label","width":30},
								{"field":"Reponse","type":"varchar","percentWidth":30},
								{"field":"Question","type":"varchar","percentWidth":70}
							]},
							"Reponse":{"module":"QCM","objectClass":"Reponse","identifier":"Id","label":"Numero",
							"form":"FormDetail.json",
							"extra":{"other":"Reponse,BonneReponse"},"icon":"mx_richText",
							"columns":[
								{"field":"label","width":30},
								{"field":"BonneReponse","type":"boolean","width":20},
								{"field":"Reponse","type":"varchar","percentWidth":100}
							]}
						},
						"events":[
							{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{}},
							{"type":"proxy","triggers":[
								{"trigger":"refresh", "action":"invoke", "method":"refreshData"}
							]}
						],
						"actions":[
							{"type":"start","action":"invoke","method":"refreshData"}
						]
					}
				]},
				{"type":"VBox","minHeight":1, "width":370, "percentHeight":100,"label":"Article","localProxy":1,"setStyle":{"verticalGap":0},
				"components":[
					{"type":"HBox","percentWidth":100,
					"components":[
						{"type":"Label","text":"Participants","setStyle":{"fontWeight":"bold","color":"white"}},
						{"type":"Label","dataField":"reponses","setStyle":{"fontWeight":"bold","color":"white"},
						"events":[
							{"type":"start", "action":"invoke","method":"callMethod",
							"params":{"method":"query","data":{"dirtyChild":1},
							"function":"Reponses"}}
						]}
					]},
					{"type":"MenuTab","maxLines":1,
					"menuItems":[
						{"children":[
							{"label":"","icon":"iconNew","data":"new","toolTip":"Nouveau"},
							{"label":"","icon":"open","data":"open","needFocus":1,"toolTip":"Ouvrir"},
							{"label":"","icon":"iconDelete","data":"delete","needFocus":1,"toolTip":"Supprimer"},
							{"label":"", "icon":"refresh", "data":"refresh","toolTip":"Rafraichir"},
							{"label":"","icon":"print","data":"printQCM","needFocus":1,"toolTip":"Imprimer le QCM"}
						]}
					],
					"actions":[
						{"type":"itemClick","actions":{"delete":{"action":"invoke", "method":"deleteFromSelection"}}}
					]},
					{"type":"AdvancedDataGrid","dataField":"datagridpar","percentWidth":100,"percentHeight":100,"rowHeight":24,  
					"kobeyeClass":{"dirtyParent":1,"formModule":"QCM","objectClass":"Participation","keyName":"ParticipationProjetId",
					"form":"FormParticipation.json"},"changeEvent":1,"emptyOnZero":0,
					"events":[
						{"type":"start","action":"loadValues","params":{"needsParentId":1}},
						{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
						{"type":"proxy", "triggers":[
							{"trigger":"open","action":"invoke","method":"loadFormWithSelection"},
							{"trigger":"new","action":"invoke","method":"createForm"},
							{"trigger":"edit","action":"invoke","method":"loadFormWithID"},
							{"trigger":"delete","action":"invoke","method":"deleteWithID"},
							{"trigger":"refresh","action":"invoke","method":"restart"},
							{"trigger":"printQCM","action":"invoke","method":"callMethod","params":{
							"confirm":{"text":"Imprimer le QCM"},
							"method":"object","data":{"module":"QCM","objectClass":"Projet"},
							"function":"PrintProjet","args":"v:1,idv:datagridpar"}}
						]}
					],
					"columns":[
						{"type":"column","dataField":"Prenom","headerText":"Prénom","width":100},
						{"type":"column","dataField":"NomFamille","headerText":"Nom","width":150},
						{"type":"column","dataField":"DateValidation","headerText":"Validé","format":"date","width":64},
						{"type":"column","dataField":"Note","headerText":"Note","width":40,"format":"2dec"},
						{"type":"column","width":0}
					]}
				]}
			]}
		],
		"events":[
//			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"start", "action":"invoke","method":"callMethod",
			"params":{"method":"object","data":{"dirtyChild":1,"module":"QCM","objectClass":"Projet"},
			"function":"GetProjet"}},
			{"type":"proxy","triggers":[
				{"trigger":"saveClose","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":0}},
				{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
				{"trigger":"delete","action":"invoke","method":"deleteData"},
				{"trigger":"cancel","action":"invoke","method":"cancelEdit"}
			]}
		]}
	]}
],
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}


