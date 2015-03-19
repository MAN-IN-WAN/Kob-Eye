[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"TitleWindow","id":"FD:[!I::Module!]/[!I::TypeChild!]","title":"Ligne Devis","width":450,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]","createParent":1},"focusedID":"FamilleId",
"localProxy":{
	"actions":{
		"TexteId":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"TexteLibre","args":[{"dataValue":["TexteId"]}]}},
		"FamilleId":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"Famille","args":[{"dataValue":["formCreator#ClientId","FamilleId","formCreator#TarifDureeId","Remise","Quantite","formCreator#NombreEcheance","formCreator#Mensualites"]}]}},
		"PrixUnitaire":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"TotalLigne","args":[{"dataValue":["PrixUnitaire","Remise"]}]}},
		"Remise":{"action":"invoke","method":"callMethod","params":{"method":"object","function":"TotalLigne","args":[{"dataValue":["PrixUnitaire","Remise"]}]}}
	}
},
//"swfName":"DevisLigne",
"components":[
	{"type":"VBox","id":"vbox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"components":[
		{"type":"EditContainer","id":"edit",
//		"defaultButtonID":"save",
		"components":[
			{"type":"TextInput","dataField":"Famille","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"CategorieId","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"Transport","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"ModeTarif","includeInLayout":0,"visible":0,"editable":0},
			{"type":"TextInput","dataField":"CodeTVA","includeInLayout":0,"visible":0,"editable":0},
			{"type":"Form","id":"form","setStyle":{"verticalGap":2,"paddingLeft":6,"paddingRight":6,"paddingTop":0,"paddingBottom":2},
			"components":[
				{"type":"FormItem","label":"Famille","labelWidth":80,"components":[
					{"type":"ComboBox","id":"FamilleId","dataField":"FamilleId","width":150,
					"kobeyeClass":{"module":"StockLocatif","objectClass":"Famille","identifier":"Id","label":"Famille"},
					"events":[
						{"type":"init","action":"loadData"}
					]}
				]},
				{"type":"FormItem","label":"Texte","labelWidth":80,"components":[
					{"type":"ComboBox","id":"TexteId","dataField":"TexteId","width":150,
					"kobeyeClass":{"module":"Devis","objectClass":"TexteLibre","identifier":"Id","label":"Code"},
					"events":[
						{"type":"init","action":"loadData"}
					]}
				]},
				{"type":"FormItem","label":"Désignation","labelWidth":80,"components":[
					{"type":"TextArea","dataField":"Designation","percentWidth":100,"height":60,"validType":"string"}
				]},
				{"type":"FormItem","label":"Quantité","labelWidth":80,"components":[
					{"type":"TextInput","dataField":"Quantite","width":100,"maxChars":10,"validType":"int","setStyle":{"textAlign":"end"}}
				]},
				{"type":"FormItem","label":"Prix Unitaire","labelWidth":80,"components":[
					{"type":"TextInput","dataField":"PrixUnitaire","width":100,"maxChars":10,"validType":"float","setStyle":{"textAlign":"end"}}
				]},
				{"type":"FormItem","label":"Remise %","labelWidth":80,"components":[
					{"type":"TextInput","dataField":"Remise","width":100,"maxChars":10,"validType":"float","setStyle":{"textAlign":"end"}}
				]},
				{"type":"FormItem","label":"Prix Net","labelWidth":80,"components":[
					{"type":"TextInput","dataField":"PrixNet","width":100,"maxChars":10,"validType":"float","setStyle":{"textAlign":"end"},"editable":0}
				]}
//				,{"type":"TextArea","id":"dumpProxy","percentWidth":100,"height":"250","events":[{"type":"start","action":"invoke","method":"dumpProxy"}]}
//				{"type":"FormItem","label":"Code TVA","labelWidth":80,"components":[
//					{"type":"ComboBox","dataField":"CodeTVA","width":50,"maxChars":5, //"stringValue":1,
//					"kobeyeClass":{"module":"Devis","objectClass":"TVA","identifier":"Code","label":"Taux"},
//					"actions":[
//						{"type":"init","action":"loadData"}
//					]}
//				]}
			]}
		],
		"events":[
//			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"postItem","params":{"closeForm":1}},
				{"trigger":"delete","action":"invoke","method":"deleteItem"}
//				{"trigger":"cancel","action":"invoke","method":"cancelEdit"}
			]}
		]},
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"save","label":"Valider","width":100},
			{"type":"Button","id":"delete","label":"Supprimer","width":100},
			{"type":"Button","id":"cancel","label":"Annuler","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal"
//,"actions":[{"type":"close","action":"confirmUpdate"}]
}}
