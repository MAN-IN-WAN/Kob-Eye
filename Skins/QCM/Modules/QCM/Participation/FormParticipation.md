{"form":{"type":"TitleWindow","id":"FD:QCM/Participation","title":"Participation au QCM",
"kobeyeClass":{"module":"QCM","objectClass":"Participation"},
"localProxy":1,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"minWidth":550,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"verticalScrollPolicy":"auto","minHeight":0,
	"components":[
		{"type":"EditContainer","id":"edit",
		"components":[
			{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2},
			"components":[
				{"type":"CollapsiblePanel","percentWidth":100,"title":"","layout":{"type":"HorizontalLayout"},"open":1,
				"components":[
					{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2,"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
					"components":[	
						{"type":"DataItem","percentWidth":100,"includeInLayout":0,"visible":0,
						"displayFields":[{"name":"Nom","description":"Nom"}],
						"keyType":"short","keyMandatory":true,"dataField":"Projet.ParticipationProjetId",
						"kobeyeClass":{"dirtyChild":1,"module":"QCM","parentClass":"Projet",
						"select":["Id","Nom"],"icon":"[None]","form":"PopupList.json"},
						"actions":[
							{"type":"start", "action":"loadValues"},
							{"type":"goto", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"Projet","form":"FormBase.json"}},
							{"type":"proxy", "triggers":[
								{"trigger":"linkProjet","action":"invoke","method":"linkParent"},
								{"trigger":"unlinkProjet","action":"invoke","method":"unlinkParent"}
							]}
						]},
						{"type":"FormItem","percentLabel":29,"label":"Utilisateur","percentWidth":100,"components":[
							{"type":"DataItem","percentWidth":100,"displayFields":[
								{"name":"Prenom","description":"Prénom"},
								{"name":"NomFamille","description":"Nom de famille"}
							]
							,"keyType":"short","keyMandatory":true,"dataField":"Host.ParticipationHostId",
							"kobeyeClass":{"dirtyChild":1,"module":"ProxyCas","parentClass":"Host",
							"select":["Id","Prenom","NomFamille"],"icon":"[None]",
							"form":"PopupList.json"
							},
							"actions":[
								{"type":"start", "action":"loadValues"},
								{"type":"goto", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"Utilisateur","form":"FormBase.json"}},
								{"type":"proxy", "triggers":[
									{"trigger":"linkHost","action":"invoke","method":"linkParent"},
									{"trigger":"unlinkHost","action":"invoke","method":"unlinkParent"}
								]}
							]}
						]},
						{"type":"FormItem","percentLabel":29,"label":"Valide","percentWidth":100,"components":[
							{"type":"CheckBox","dataField":"Valide","percentWidth":100 }
						]},
						{"type":"FormItem","percentLabel":29,"label":"Date de validation","percentWidth":100,"components":[
							{"type":"DateField","dataField":"DateValidation","validType":"date" }
						]},
						{"type":"FormItem","percentLabel":29,"label":"Note obtenue","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"Note","validType":"float","width":40 }
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"delete","action":"invoke","method":"deleteData"}

			]}
		]},
		{"type":"HGroup","percentWidth":100,
		"components":[
			{"type":"Spacer","percentWidth":100},
			{"type":"Button","id":"ok","label":"Ok","width":100,
			"events":[
				{"type":"click", "action":"invoke","objectID":"edit","method":"saveData","params":{"closeForm":1}}
			]},
			
			{"type":"Button","id":"delete","label":"Supprimer","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"edit","method":"deleteData"}
			]},
			
			{"type":"Button","id":"cancel","label":"Annuler","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"confirmUpdate"}
]}
}