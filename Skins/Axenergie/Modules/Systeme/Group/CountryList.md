[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":
	{"type":"VBox", "id":"FL:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0, "paddingLeft":5, "paddingRight":5},"localProxy":1, 
	"components":[
		{"type":"MenuTab", "id":"menuList", "menuItems":[
			{"label":"File", "children":[
				{"label":"Open", "icon":"open", "data":"open","needFocus":1},
				{"label":"Edit", "icon":"open", "data":"open" ,"needFocus":1},
				{"label":"Delete", "icon":"remove", "data":"delete","needFocus":1},
				{"type":"vseparator"},
				{"label":"New Group", "icon":"iconNew", "data":"newGroup", "objectclass":["Group"]},
				{"label":"New User", "icon":"user", "data":"newUser", "objectclass":["Group"],"needFocus":1},
				{"label":"New Menu", "icon":"dataBase", "data":"newMenu", "objectclass":["User","Menu"],"needFocus":1},
				{"type":"vseparator"},
				{"label":"Refresh", "icon":"refresh", "data":"refresh"}
			]}
		],
			"actions":[
				{"type":"itemClick", "actions":{
					"open":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
					"newGroup":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"Group"}},
					"newUser":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"User"}},
					"newMenu":{"type":"click", "action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","asParent":1,"objectClass":"Menu"}},
					"delete":{"type":"click", "action":"invoke", "method":"deleteFromSelection"}
					
				}
			}
		]},
		{"type":"Tree","id":"tree_range", "percentWidth":100, "percentHeight":100,
			"kobeyeClass":{
				"module":"Systeme",
				"objectClass":"Group",
				"label":"Nom",
				"identifier":"Id",
				"icon":"iconNew",
				"form":"FormBase.json",
				"children":["Group","User","Menu"]
			},
			"otherKobeyeClass":{
				"User":{"module":"Systeme","objectClass":"User","identifier":"Id","label":"Login","form":"FormBase.json", "iconField":"Avatar", "children":["Menu"]},
				"Menu":{"module":"Systeme","objectClass":"Menu","identifier":"Id","label":"Titre","form":"FormBase.json", "icon":"user", "children":["Menu"]}
			},
			"events":[
				{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
				{"type":"proxy","triggers":[
					{"trigger":"refresh", "action":"invoke", "method":"loadData"}
				]}
			],
			"actions":[
				{"type":"init", "action":"loadData"}
			]
		}
	],
	"actions":[
	]
	}
}