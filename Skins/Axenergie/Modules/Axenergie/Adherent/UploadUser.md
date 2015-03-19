{"form":{"type":"TitleWindow","id":"UploadUser","title":"Importation des Adhérents",
"minWidth":350,"minHeight":200,"localProxy":1,
"kobeyeClass":{"module":"Axenergie","objectClass":"Adherent"},
"components":[
	{"type":"EditContainer",
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalgap":5,"paddingLeft":5,"paddingRight":10,"paddingTop":30,"paddingBottom":10},
		"components":[
			{"type":"FormItem","labelWidth":80,"label":"Fichier CSV","percentWidth":100,"components":[
				{"type":"Upload","dataField":"File","viewVisible":0,"percentWidth":100}
			]},
			{"type":"Spacer","height":20},
			{"type":"HGroup","percentWidth":100,
			"components":[
				{"type":"Spacer","percentWidth":100},
				{"type":"Button", "id":"ok", "label":"Valider", "width":80,
				"events":[
					{"type":"click","action":"invoke","method":"callMethod","params":{
					"confirm":{"text":"Confirmer l'importation des Adhérents"},
					"method":"object","data":{"module":"Axenergie","objectClass":"Adherent"},
					"function":"UploadUser","args":"dv:File","closeForm":1}}
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
