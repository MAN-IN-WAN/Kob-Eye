
{"form":{"type":"TitleWindow","id":"FD:Axenergie/SubProduct","title":"Edition Produit","width":650,
"kobeyeClass":{"module":"Axenergie","objectClass":"SubProduct"},
"localProxy":{
	"actions":{
		"PopupImage":{"action":"invoke","method":"loadFormWithID","params":{"kobeyeClass":{"module":"Axenergie","objectClass":"SubProduct","form":"FormImageCatalogue.json"}}},
		"imagePromo":{"action":"invoke","method":"setValue","params":{"dataField":"ImagePromo","args":"pv:ImagePromo"}},
		"Promo":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"promo","property":"visible","conditions":[{"compare":"Promo=1"}]}}
			//,{"action":"invoke","method":"groupState","params":{"group":"normal","property":"visible","conditions":[{"compare":"Promo=0"}]}}
		]}
	}
},"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"components":[
		{"type":"EditContainer","id":"edit","components":[
			{"type":"HGroup","percentWidth":100,"percentHeight":100,
			"components":[
				{"type":"VBox","width":150,"percentHeight":100,"setStyle":{"backgroundColor":"#dedede","paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
				"components":[
					{"type":"Image","dataField":"ImageCatalogue","width":130,"height":150},
					{"type":"Image","dataField":"ImagePromo","width":130,"height":150},
					{"type":"Button","id":"PopupImage","label":"Image Promo","percentWidth":100,"stateGroup":"promo"}
				]},	
				{"type":"VBox","percentWidth":100,"percentHeight":100, "setStyle":{"paddingLeft":10,"paddingRight":10,"paddingTop":10,"paddingBottom":10},
				"components":[	
					{"type":"Form","setStyle":{"verticalGap":1,"paddingLeft":1,"paddingRight":1,"paddingTop":0,"paddingBottom":1},"percentWidth":100,"components":[
						{"type":"FormItem","percentLabel":29,"label":"Nom","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"Nom","percentWidth":100,"validType":"string","formLabel":1,"editable":0,"enabled":0}
						]},
						{"type":"FormItem","percentLabel":29,"label":"Description","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"Description","percentWidth":100,"validType":"string","formLabel":1,"editable":0,"enabled":0}
						]},
						{"type":"FormItem","percentLabel":29,"label":"Prix HT","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"PrixHT","editable":0,"enabled":0,"width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} }
						]},
						{"type":"FormItem","percentLabel":29,"label":"Prix HT Adhérent","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"PrixAdherent","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} }
						]},
						{"type":"FormItem","percentLabel":29,"label":"Promotion","percentWidth":100,"components":[
							{"type":"CheckBox","dataField":"Promo","percentWidth":100,"forceEvent":1}
						]},
						{"type":"FormItem","percentLabel":29,"label":"Prix promo","percentWidth":100,"components":[
							{"type":"TextInput","dataField":"PrixPromo","width":100,"maxChars":11,"validType":"float","setStyle":{"textAlign":"end"} }
						]},
						{"type":"FormItem","percentLabel":29,"label":"Texte promo","percentWidth":100,"components":[
							{"type":"TextArea","dataField":"TextePromo","percentWidth":100,"height":100,"validType":"string" }
						]}
					]},
					{"type":"Spacer","percentHeight":100},
					{"type":"HGroup","percentWidth":100,
					"components":[
						{"type":"Spacer"},
						{"type":"Button","id":"sauver","label":"Sauver","width":100,
						"events":[
							{"type":"click", "action":"invoke","objectID":"edit","method":"saveData"}
						]},
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
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				//{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"delete","action":"invoke","method":"deleteData"}
			]}
		]}
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"confirmUpdate"}
]}
}