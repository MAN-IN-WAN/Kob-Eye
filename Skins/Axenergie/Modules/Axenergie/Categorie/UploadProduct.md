{"form":{"type":"TitleWindow","id":"UploadProduct","title":"Importation des Produits",
"minWidth":350,"minHeight":200,"localProxy":1,
"kobeyeClass":{"module":"Axenergie","objectClass":"Categorie"},
"components":[
	{"type":"EditContainer",
	"components":[
		{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalgap":5,"paddingLeft":5,"paddingRight":10,"paddingTop":30,"paddingBottom":10},
		"components":[
			{"type":"FormItem","labelWidth":80,"label":"Marque","percentWidth":100,"components":[
				{"type":"ComboBox","dataField":"Marque","percentWidth":100,
				"kobeyeClass":{"module":"Axenergie","query":"Axenergie/Marque","objectClass":"Marque","identifier":"Id","label":"Nom"},
				"actions":[
					{"type":"init","action":"loadData"}
				]}
			]},
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
					"confirm":{"text":"Confirmer l'importation des Produits"},
					"method":"object","data":{"module":"Axenergie","objectClass":"Categorie"},
					"function":"UploadProduct","args":"dv:Marque,dv:File,id:formCreator","closeForm":1}}
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
