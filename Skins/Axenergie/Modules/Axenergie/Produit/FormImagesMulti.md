[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]{"form":{"type":"TitleWindow","id":"ImagesMulti","title":"Génération des images catalogue","minWidth":500,"width":500,"height":600,
"kobeyeClass":{"module":"Axenergie","objectClass":"Produit"},
"localProxy":1,
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":0,"verticalGap":0},
		"components":[
			{"type":"HBox","percentWidth":100,"setStyle":{"horizontalGap":5,"paddingRight":5,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#eeeeee"},
			"components":[
				{"type":"Spacer","percentWidth":100},
				{"type":"Button","id":"save","label":"Démarrer","width":80,
				"events":[
					{"type":"click","action":[
						{"action":"invoke","method":"uploadImage","objectID":"image"},
						{"action":"invoke","method":"setProperty","params":{"property":"enabled","value":0}}
					]}
				]},
				{"type":"Button","id":"cancel","label":"Annuler","width":80,
				"events":[
					{"type":"click","action":[
						{"action":"invoke","objectID":"image","method":"stop"},
						{"action":"invoke","objectID":"parentForm","method":"closeForm"}
					]}
				]}
			]},
			{"type":"CreateBitmap","id":"image","dataField":"ImageCatalogue","percentHeight":100,"percentWidth":100,
			"uploadURL":"/Systeme/Upload.htm",
			"events":[
				{"type":"proxy","triggers":[
					{"trigger":"save","action":"invoke","method":"callMethod","params":{"method":"object","function":"getImages","args":"sv:formCreator#dataGrid"}}
				]}
			]}
		]}
//	],
//	"events":[
//		{"type":"start","action":"loadValues","params":{"needsId":1}}
	]}
],
"popup":"modal"
}}
