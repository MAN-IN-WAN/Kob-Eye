[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"VBox","id":"[!I::TypeChild!]?","label":"[!O::getDescription()!]","percentHeight":100,
"setStyle":{"paddingLeft":6,"paddingRight":6},"localProxy":1,
//"query":{"query":"[!Query!]"},
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"components":[
	{"type":"MenuTab", "menuItems":[
		{"label":"File", "children":[
			{"label":"Save", "icon":"save", "data":"save"},
			{"label":"Delete", "icon":"iconDelete", "data":"delete"},
			{"label":"Cancel", "icon":"unselect", "data":"cancel"},
			{"type":"vseparator"},
			{"label":"Print Current", "icon":"print", "data":"printCurrent"},
			{"label":"Print All Displays", "icon":"open", "data":"printAll"},
			{"type":"vseparator"},
			{"label":"Copy"},
			{"label":"Cut"},
			{"label":"Paste"}
		]}
	]},
	{"type":"Scroller", "id":"scroller",
	"viewport":
		{"type":"Group", "percentWidth":100,
		"components":[
			{"type":"EditContainer", "id":"edit",
			"components":[
				{"type":"Group", "percentWidth":100, "layout":{"type":"VerticalLayout","gap":4,"paddingLeft":0,"paddingRight":0,"paddingTop":4,"paddingBottom":4},
				"components":[
					[MODULE Systeme/formElements?I=[!I!]]					
				]}
			],
			"events":[
				{"type":"proxy", "triggers":[
					{"trigger":"save","action":"invoke","method":"saveData"},
					{"trigger":"delete","action":"invoke","method":"deleteData"},
					{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
					{"trigger":"new","action":"invoke","method":"clearData"}
				]}
			]}
		]}
	}
]
,
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}


