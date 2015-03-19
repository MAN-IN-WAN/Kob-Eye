{"form":{"type":"TitleWindow","id":"FormAnalyse","title":"ANALYSE",
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
				{"type":"DataField","dataField":"TypeId","defaultValue":6},
				{"type":"DataField","dataField":"SousTypeId","defaultValue":61},
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
				{"type":"HGroup","gap":2,"components":[
					{"type":"Spacer","width":92},
					{"type":"Label","text":"Cuve","width":62,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Capacité","width":55,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Contenu","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Lot","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Catégorie","width":150,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Couleur","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}}
				]},
				{"type":"FormItem","labelWidth":80,"label":"","percentWidth":100,"components":[
					{"type":"HGroup","gap":2,"components":[
						{"type":"TextInput","dataField":"Cuve","width":40,"editable":0},
						{"type":"PopupButton","dataField":"CuveId","icon":"dataBase","height":20,
						"kobeyeClass":{"module":"Cave","objectClass":"Cuve","setFilter":"EnService=1&EtatCuveId=11&EtatLotId<3","form":"PopupList.json"},
						"exoFields":{"Cuve":"Cuve","Capacite":"Capacite","Volume":"Contenu","CuveLotId":"LotId","Lot":"Lot","Categorie":"Categorie","Couleur":"Couleur"}},
						{"type":"TextInput","dataField":"Capacite","width":55,"setStyle":{"textAlign":"end"},"editable":0},
						{"type":"TextInput","dataField":"Contenu","width":55,"setStyle":{"textAlign":"end"},"editable":0},
						{"type":"TextInput","dataField":"Lot","width":60,"editable":0},
						{"type":"TextInput","dataField":"Categorie","width":150},
						{"type":"TextInput","dataField":"Couleur","width":60},
						{"type":"DataField","dataField":"LotId","defaultValue":0}
					]}
				]},
//				{"type":"Spacer","height":10},
//				{"type":"FormItem","label":"Volume","labelWidth":80,"components":[
//					{"type":"TextInput","dataField":"Volume","width":75,"maxChars":10,"validType":"float","setStyle":{"textAlign":"end"},"required":0}
//				]},
				{"type":"Spacer","height":10},
				{"type":"HGroup","gap":2,"components":[
					{"type":"Spacer","width":92},
					{"type":"Label","text":"TAV","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"AV","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"AT","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"AM","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"IC","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"pH","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"SO2 Lib","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"SO2 Tot","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Turbidité","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"Stab Pro","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}}
				]},
				{"type":"FormItem","label":"","labelWidth":80,"components":[
					{"type":"HGroup","gap":2,"components":[
						{"type":"TextInput","dataField":"TAV","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"AV","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"AT","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"AM","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"IC","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"pH","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"SO2Lib","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"SO2Tot","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"Turbidite","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"ComboBox","dataField":"StabPro","width":60 ,"dataProvider":[
							{"data":"1","label":"Oui"},
							{"data":"0","label":"Non"}
						]}
					]}
				]},
				{"type":"Spacer","height":4},
				{"type":"HGroup","gap":2,"components":[
					{"type":"Spacer","width":92},
					{"type":"Label","text":"CO2","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"D","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"SR<5","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"SR>5","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"GF<5","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"GF>5","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"AL","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"ATar","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}},
					{"type":"Label","text":"IPT","width":60,"setStyle":{"color":"0xffffff","fontWeight":"bold"}}
				]},
				{"type":"FormItem","label":"","labelWidth":80,"components":[
					{"type":"HGroup","gap":2,"components":[
						{"type":"TextInput","dataField":"CO2","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"D","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"SRinf","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"SRsup","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"GFinf","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"GFsup","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"AL","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"ATar","width":60,"setStyle":{"textAlign":"end"}},
						{"type":"TextInput","dataField":"IPT","width":60,"setStyle":{"textAlign":"end"}}
					]}
				]},
				{"type":"Spacer","height":10},
				{"type":"FormItem","label":"Notes","labelWidth":80,"components":[
					{"type":"TextArea","dataField":"Notes","width":350,"height":70,"required":0}
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
