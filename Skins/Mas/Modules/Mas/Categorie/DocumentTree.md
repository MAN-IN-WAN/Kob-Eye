{"form":
	{"type":"VBox","percentWidth":100,"percentHeight":100,"id":"DocumentTree", "label":"Documents",
	"setStyle":{"closable":0, "paddingTop":0,"verticalGap":0,"fontSize":12},"localProxy":1,
	"components":[
		{"type":"MenuTab", "id":"menuList","maxLines":1,
		"searchBox":1,"dataField":"filterField","id":"filterField","filterFields":["Titre,Description"],
		"menuItems":[
			{"label":"", "children":[
				{"label":"Consulter", "icon":"oeil3", "data":"watch","objectClass":["Document"],"enabled":0},
				{"label":"Nouveau", "icon":"iconNew", "data":"newDoc","objectClass":["Categorie"],"needWrite":1,"enabled":0},
				{"label":"Modifier", "icon":"open", "data":"open","objectClass":["Document"],"needWrite":1,"enabled":0},
				{"label":"Supprimer", "icon":"deleteShelf", "data":"delete","objectClass":["Document"],"needWrite":1,"enabled":0},
				{"label":"Rafraichir", "icon":"refresh", "data":"refresh"}
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
				"watch":{"type":"click", "action":"invoke", "method":"callMethod",
				"params":{"method":"object","data":{"module":"Mas","objectClass":"Document"},
				"function":"ViewDocument","args":"idv:dataGrid"}},
				"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"resetParent":1,"form":"FormBase.json","containerID":"tabNav"}},
				"newDoc":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"asParent":1,"objectClass":"Document","form":"FormBase.json","containerID":"tabNav"}},
				"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
			}}
		]},
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid","percentWidth":100,"percentHeight":100,"hierarchical":1,
		"setStyle":{"fontSize":14},"autoUpdate":0,
		"kobeyeClass":{"module":"Mas","objectClass":"Document","form":"FormBase.json"},
		"getDataFunction":{"method":"object","data":{"module":"Mas","objectClass":"Document"},
		"function":"GetDocuments"},
		"columns":[
			{"type":"column","dataField":"AlertIcon","headerText":".","width":20,"format":"image"},
			{"type":"column","dataField":"Categorie","headerText":"Catégorie","width":250,"treeColumn":1},
			{"type":"column","dataField":"Titre","headerText":"Titre","width":450,"setStyle":{"fontWeight":"bold"}},
			{"type":"column","dataField":"DatePublication","headerText":"Publication","width":90,"format":"date"},
			{"type":"column","dataField":"DateConsultation","headerText":"Consultation","width":90,"format":"date"}
		],
		
		
		"events":[
			{"type":"start","action":"loadValues"},
			//{"type":"start","action":"invoke","method":"callMethod",
			//"params":{"method":"object","data":{"module":"Mas","objectClass":"Document"},
			//"function":"GetDocuments"}},
			{"type":"dblclick","action":"invoke","objectClass":["Document"],"method":"callMethod",
			"params":{"method":"object","data":{"module":"Mas","objectClass":"Document"},
			"function":"ViewDocument","args":"idv:dataGrid"}},
			{"type":"proxy","triggers":[
				{"trigger":"filterField","action":"invoke","method":"filterData","params":{"filter":"filterField"}},
				{"trigger":"refresh", "action":"invoke", "method":"callMethod",
				"params":{"method":"object","data":{"module":"Mas","objectClass":"Document"},
				"function":"GetDocuments","args":"v:,v:,v:,v:,v:,v:"}}
			]}
		]}
	]}
}