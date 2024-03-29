{"form":{"type":"TitleWindow","id":"FormInventaire","title":"INVENTAIRE",
"kobeyeClass":{"module":"Cave","objectClass":"Operation"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}}
		]}
	}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"minWidth":550,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
	"components":[
		{"type":"EditContainer","id":"edit",//"percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"VBox","setStyle":{"verticalGap":2,"paddingLeft":6,"paddingRight":6,"paddingTop":0,"paddingBottom":2},
			"percentWidth":100,"percentHeight":100,
			"components":[
				{"type":"DataField","dataField":"TypeId","defaultValue":5},
				{"type":"FormItem","label":"Operateur","labelWidth":80,"components":[
					{"type":"ComboBox","dataField":"OperateurId","width":222,"required":0,
					"kobeyeClass":{"module":"Cave","objectClass":"Operateur","identifier":"Id","label":"Operateur","select":"Id,Operateur"},
					"actions":[
						{"type":"init","action":"loadData"}
					]}
				]},
				{"type":"FormItem","label":"Date - Heure","labelWidth":80,"components":[
					{"type":"DateTimeField","dataField":"Date","validType":"date","required":0,"defaultValue":"Now","startingHour":7,"increment":10}
				]},
				{"type":"Spacer","height":10},
				{"type":"FormItem","label":"Sous type","labelWidth":80,"components":[
					{"type":"ComboBox","dataField":"SousTypeId","width":222,"required":0,"exoFilters":{"Id":"ProduitId"},
					"kobeyeClass":{"module":"Cave","objectClass":"SousType","setFilter":"TypeId=5","identifier":"Id","label":"SousType","select":"Id,SousType"},
					"actions":[
						{"type":"init","action":"loadData"}
					]}
				]},
				{"type":"Spacer","height":10},
				{"type":"HGroup","gap":2,"components":[
					{"type":"Spacer","width":92},
					{"type":"Label","text":"Cuve","width":62,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Capacité","width":55,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Contenu","width":55,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Lot","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Catégorie","width":150,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Couleur","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}}
				]},
				{"type":"FormItem","labelWidth":80,"label":"","percentWidth":100,"components":[
					{"type":"HGroup","gap":2,"components":[
						{"type":"TextInput","dataField":"Cuve","width":40,"editable":0},
						{"type":"PopupButton","dataField":"CuveId","icon":"dataBase","height":20,
//						"kobeyeClass":{"module":"Cave","objectClass":"Cuve","setFilter":"EnService=1&EtatCuveId=11&EtatLotId<3","form":"PopupList.json"},
						"kobeyeClass":{"module":"Cave","objectClass":"Cuve","setFilter":"","form":"PopupList.json"},
						"exoFields":{"Cuve":"Cuve","Capacite":"Capacite","Volume":"Contenu","CuveLotId":"LotId","Lot":"Lot","Categorie":"Categorie","Couleur":"Couleur"}},
						{"type":"TextInput","dataField":"Capacite","width":55,"setStyle":{"textAlign":"end"},"editable":0},
						{"type":"TextInput","dataField":"Contenu","width":55,"setStyle":{"textAlign":"end"},"editable":0},
						{"type":"TextInput","dataField":"Lot","width":60,"editable":0},
						{"type":"TextInput","dataField":"Categorie","width":150},
						{"type":"TextInput","dataField":"Couleur","width":60},
						{"type":"DataField","dataField":"LotId","defaultValue":0}
					]}
				]},
				{"type":"Spacer","height":10},
				{"type":"FormItem","label":"Volume inventaire","labelWidth":80,"components":[
					{"type":"TextInput","dataField":"Volume","width":75,"maxChars":10,"validType":"float","setStyle":{"textAlign":"end"},"required":0}
				]},
				{"type":"Spacer","height":10},
				{"type":"FormItem","label":"Notes","labelWidth":80,"components":[
					{"type":"TextArea","dataField":"Notes","width":350,"height":70,"required":0}
				]}
				,{"type":"Button","label":"copier","events":[
					{"type":"click","action":"invoke","objectID":"xxx","method":"copyToClipboard"}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":1}}
//				{"trigger":"delete","action":"invoke","method":"deleteData"}
//				{"trigger":"cancel","action":"invoke","method":"cancelEdit"}
			]}
		]},
		{"type":"Spacer","percentHeight":100},
		{"type":"HGroup","percentWidth":100,
		"components":[
			{"type":"Spacer"},
			{"type":"Button","id":"ok","label":"$__Ok__$","width":100,"enabled":0,"stateGroup":"updated",
			"events":[
				{"type":"click","action":"invoke","method":"callMethod","params":{"method":"object","function":"SaveOperation","args":"dv:*","closeForm":1}}
			]},
//			{"type":"Button","id":"delete","label":"$__Delete__$","width":100,
//			"events":[
//				{"type":"click","action":"invoke","objectID":"edit","method":"deleteData"}
//			]},
			{"type":"Button","id":"cancel","label":"$__Cancel__$","width":100,
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
