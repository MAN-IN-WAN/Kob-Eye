[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
{"form":{"type":"VBox","id":"[!I::TypeChild!]?","label":"[!O::getDescription()!]","percentHeight":100,
"setStyle":{"paddingLeft":0,"paddingRight":0},"localProxy":1,
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"components":[
	{"type":"Panel","dataField":"[!Nom!]","title":"", "percentWidth":100,"percentHeight":100,"setStyle":{"paddingBottom":10,"paddingTop":10,"paddingLeft":0,"paddingRight":0 },
	"components":[
		{"type":"Scroller", "id":"scroller",
		"viewport":
			{"type":"Group", "percentWidth":100,"percentHeight":100,
			"components":[
				{"type":"EditContainer", "id":"edit",
				"components":[
					{"type":"VBox", "percentWidth":100,"percentHeight":100,"setStyle":{"paddingBottom":10,"paddingTop":10,"paddingLeft":10,"paddingRight":10 },
					"components":[
					[STORPROC [!O::getElements()!]|categ]
						[IF [!cat!]>0],[/IF]
						{"type":"TitledBorderBox","title":"[!Key!]","percentWidth":100,
						//"setStyle":{"gap":4, "paddingLeft":0,"paddingRight":0,"paddingTop":0,"paddingBottom":4},
						"components":[
							{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":2,"paddingLeft":6,"paddingRight":6,"paddingTop":0,"paddingBottom":2},"percentWidth":100,"components":[
							[!item:=0!]
							[STORPROC [!categ!]|media]
								[STORPROC [!media!]|element]
									[SWITCH [!element::type!]|=]
										[CASE fkey][/CASE]
										[CASE rkey][/CASE]
										[DEFAULT]
											[MODULE Systeme/formProperty?P=[!element!]&O=[!O!]&item=[!item!]]
										[/DEFAULT]
									[/SWITCH]
									[!item+=1!]
								[/STORPROC]
							[/STORPROC]
							]}
						]}
						[!cat+=1!]
					[/STORPROC]
						,{"type":"FormItem","label":"Save changes:", "components":[
							{"type":"Button", "label":"Save", "id":"Save", "percentWidth":100, "events":[
								{"type":"click", "action":"invoke", "objectID":"edit", "method":"saveData"}
							]}
						]}
					]}
				],
				"events":[
					{"type":"start", "action":"loadValues"}
				]}
			]}
		}
	]}
]
,
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}
