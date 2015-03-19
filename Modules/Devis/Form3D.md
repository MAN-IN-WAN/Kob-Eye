{"form":{"type":"TitleWindow","id":"3D","title":"View 3D","setStyle":{"backgroundAlpha":0},
"localProxy":{
	"vars":{"zob":"abcd"}
},
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5}, 
	"components":[
// boutons valider, supprimer, annuler   
		{"type":"HGroup",
		"components":[
			{"type":"TextInput","dataField":"zob","width":50},
			{"type":"Button","id":"ok","label":"Valider","width":80},
			{"type":"Button","id":"delete","label":"Supprimer","width":80,
			"events":[
				{"type":"click","action":"invoke","method":"callMethod","params":{"method":"form"}}
			]},
			{"type":"Button","id":"cancel","label":"Annuler","width":80,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]},
		{"type":"View3D","width":600,"height":500}
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"confirmUpdate"}
]}
}
