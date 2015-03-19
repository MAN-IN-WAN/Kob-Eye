[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[IF [!Promo!]][!imageId:=imagePromo!][ELSE][!imageId:=imageCatalogue!][/IF]
{"form":{"type":"TitleWindow","id":"ImgCat/[!I::TypeChild!]","title":"Génération de l'image catalogue","width":400,"height":550,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":{"transparent":1},
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":0,"horizontalGap":2},
		"components":[
			{"type":"CreateBitmap","id":"[!imageId!]","dataField":"[IF [!Promo!]]ImagePromo[ELSE]ImageCatalogue[/IF]",
			"percentHeight":100,"percentWidth":100,
			[IF [!I::TypeChild!]=Database]"uploadURL":"/Flipbook/Page/Upload.htm",[/IF]
			"events":[
				{"type":"start","action":"invoke","method":"callMethod","params":{"method":"query","function":"getImage"}}
			]},
			{"type":"HBox","percentWidth":100,"setStyle":{"backgroundColor":"silver","paddingTop":6,"paddingBottom":6,"paddingLeft":6,"paddingRight":6},
			"components":[
				{"type":"Spacer"},
				[IF [!I::TypeChild!]!=Database]
					{"type":"Button","id":"saveImg","label":"Enregister",
					"events":[
						{"type":"click","action":[
							{"action":"invoke","method":"uploadImage","objectID":"[!imageId!]"},
							{"action":"invoke","method":"setProperty","params":{"property":"enabled","value":0}}
						]}
					]},
				[/IF]
				{"type":"Button","id":"cancelImg","label":"Annuler",
				"events":[
					{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
				]}
			]}
		]}
	],
	"events":[
		{"type":"proxy","triggers":[
			{"trigger":"[!imageId!]","action":"closeForm"}
		]}
	]}
],
"popup":"modal"
}}
