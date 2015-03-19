{"form":{"type":"TitleWindow","id":"StockLocatif/UploadTarif","title":"Importation de Tarifs",
"minWidth":350,"minHeight":200,"localProxy":1,
"kobeyeClass":{"module":"StockLocatif","objectClass":"Famille"},
"components":[
	{"type":"EditContainer",
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalgap":5,"paddingLeft":5,"paddingRight":10,"paddingTop":30,"paddingBottom":10},
		"components":[
			{"type":"FormItem","labelWidth":80,"label":"Code tarif","percentWidth":100,"components":[
				{"type":"ComboBox","dataField":"CodeTarif","width":150 ,
				"kobeyeClass":{"module":"Devis","query":"Devis/CodeTarif","objectClass":"CodeTarif","identifier":"Id","label":"CodeTarif"},
				"actions":[
					{"type":"init","action":"loadData"}
				]}
			]},
			{"type":"FormItem","labelWidth":80,"label":"Fichier CSV","percentWidth":100,"components":[
				{"type":"Upload","dataField":"Tarif","percentWidth":100}
			]},
			{"type":"Spacer","height":20},
			{"type":"HGroup","percentWidth":100,
			"components":[
				{"type":"Spacer","percentWidth":100},
				{"type":"Button", "id":"ok", "label":"Valider", "width":80,
				"events":[
					{"type":"click","action":"invoke","method":"callMethod","params":{
					"confirm":{"text":"Confirmer l'importation du tarif"},
					"method":"object","data":{"module":"StockLocatif","objectClass":"Famille"},
					"function":"UploadTarif","args":[{"dataValue":["CodeTarif","Tarif"]}]}}
				]},
				{"type":"Button", "id":"cancel", "label":"Annuler", "width":80,
				"events":[
					{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
				]}
			]}		
		]}
	]}
],
"popup":"modal"
}}
