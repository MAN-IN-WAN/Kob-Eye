[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"TitleWindow","id":"PL:[!I::Module!]/[!I::TypeChild!]","title":"Sélection [!O::getDescription()!]",
"minWidth":300,"height":400,"minHeight":200,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},"localProxy":1,
	"components":[
// formulaire de recherche
		{"type":"EditContainer", "id":"searchBox",
		"components":[
			{"type":"HGroup", "percentWidth":100,
			"components":[
				{"type":"TextInput","dataField":"filterField","percentWidth":80},
				{"type":"Button","id":"clear","label":"Effacer","width":80}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear","action":"invoke","method":"clearData"}
			]}
		]},
		[!select:=Id!]
		[!columns:={"type":"column", "dataField":"Id", "headerText":"ID", "width":20, "visible":0}!]
		[STORPROC [!O::SearchOrder()!]|P]
			[!select+=,[!P::Nom!]!]
			[!columns+=,{"type":"column", "dataField":"[!P::Nom!]", "headerText":!]
			[IF [!P::description!]][!columns+="[!P::description!]",!][ELSE][!columns+="[!P::Nom!]",!][/IF]
			[IF [!P::width!]][!columns+= "width":[!P::width!]!][ELSE][!columns+= "width":100!][/IF]
			[!columns+=}!]
		[/STORPROC]
		{"type":"Group", "percentWidth":100, "percentHeight":100,
		"components":[
			{"type":"Tree", "id":"dataGrid", "dataField":"dataGrid", "percentWidth":100, "percentHeight":100,
			"kobeyeClass":{
				"module":"Vitrine",
				"objectClass":"Categorie",
				"label":"Nom",
				"identifier":"Id",
				"icon":"products",
				"children":["Categorie","Produit"]
			},
			"otherKobeyeClass":{
				"Produit":{"module":"Vitrine","objectClass":"Produit","identifier":"Id","label":"Nom", "iconField":"Image"}
			},
			"events":[
				{"type":"init","action":"loadData"},
				{"type":"dblclick","action":[
					{"action":"invoke","method":"formValue","params":{"property":"idValue"}},
					{"action":"invoke","objectID":"parentForm","method":"closeForm"}
				]},
				{"type":"proxy", "triggers":[
					{"trigger":"filterField","action":"invoke","method":"filterData","params":{"filter":"filterField"}},
					{"trigger":"clear","action":"invoke","method":"filterData"},
					{"trigger":"ok","action":[
						{"action":"invoke","method":"formValue","params":{"property":"idValue"}},
						{"action":"invoke","objectID":"parentForm","method":"closeForm"}
					]}
				]}
			]}
		]},
// boutons valider, annuler
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"Button", "id":"ok", "label":"Valider", "width":80},
			{"type":"Button", "id":"cancel", "label":"Annuler", "width":80,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal"
}}
