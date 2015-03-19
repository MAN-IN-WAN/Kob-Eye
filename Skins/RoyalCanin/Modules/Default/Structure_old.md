{
"form":{"type":"Form", "layout":{"type":"FormLayout", "paddingLeft":-10, "paddingRight":-10, "paddingTop":-10, "paddingBottom":-10}, "percentWidth":100, "percentHeight":100,
"components":[
	{"type":"ApplicationControlBar", "percentWidth":100, "height":30, "setStyle":{"cornerRadius":0, "background-color":0},
	"components":[
		{"type":"MenuBar", "id":"menuBar", [MODULE Systeme/menu]},
		{"type":"Button", "id":"newForm", "label":"Form", "actions":[
			{"type":"click", "action":"invoke", "method":{"objectID":"wm", "method":"getWindow", "params":{"url":"Locanim/Lumiere/FormTest.json"}}}
		]},
		{"type":"Button", "id":"newList", "label":"List", "actions":[
			{"type":"click", "action":"invoke", "method":
				{"objectID":"wm", "method":"getWindow", "params":{"url":"Locanim/Lumiere/List.json"}}
			}
		]},
		{"type":"ApTaskBar", "id":"taskBar", "percentWidth":100, "actions":[
			{"type":"windowManager", "id":"wm"}
		]},
		{"type":"Button", "id":"logout", "label":"Logout", "icon":"cadFred", "actions":[
			{"type":"click", "action":"submit", "url":"Systeme/Deconnexion.json", "dataFields":[]}
		]}
	]},
	{"type":"ApWindowManager", "id":"wm", "percentWidth":100, "percentHeight":100}
],
"container":"application"}
}


