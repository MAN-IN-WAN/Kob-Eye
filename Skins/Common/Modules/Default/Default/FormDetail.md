[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!conf:=[!O::getConfiguration!]!][!conf:=[!conf::Interface!]!][!conf:=[!conf::FormDetail!]!]
{"form":{"type":"TitleWindow","id":"FD:[!I::Module!]/[!I::TypeChild!]","title":"Edition [!O::getDescription()!]",
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":1,
"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"minWidth":550,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
	"verticalScrollPolicy":"auto","minHeight":0,
	"components":[
		{"type":"EditContainer","id":"edit",//"percentWidth":100,"percentHeight":100,
		"components":[
			{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2},
			"components":[
				[MODULE Systeme/formElements?I=[!I!]]
			]}				
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"delete","action":"invoke","method":"deleteData"}
//				{"trigger":"cancel","action":"invoke","method":"cancelEdit"}
			]}
		]},
		{"type":"HGroup","percentWidth":100,
		"components":[
			{"type":"Spacer","percentWidth":100},
			{"type":"Button","id":"ok","label":"$__Ok__$","width":100,
			"events":[
				{"type":"click", "action":"invoke","objectID":"edit","method":"saveData","params":{"closeForm":1}}
			]},
			[IF [!conf::hideDelete!]!=1]
			{"type":"Button","id":"delete","label":"$__Delete__$","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"edit","method":"deleteData"}
			]},
			[/IF]
			{"type":"Button","id":"cancel","label":"$__Cancel__$","width":100,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]}		
	]}
],
"popup":"modal",
"actions":[{"type":"close","action":"confirmUpdate"}
]}
}
