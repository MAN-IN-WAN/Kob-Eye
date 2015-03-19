[INFO [!Query!]|I]
[STORPROC [!Query!]|O|0|1][/STORPROC]
{"form":{"type":"TitleWindow","id":"FD:[!I::Module!]/[!I::TypeChild!]","title":"Edition [!O::getDescription()!]","query":{"query":"[!Query!]"},"setStyle":{"paddingBottom":5, "closable":1},
"components":[
	{"type":"VBox","id":"[!I::TypeChild!]?","label":"[!O::getDescription()!]","percentHeight":100,"setStyle":{"paddingLeft":0,"paddingRight":0},"query":{"query":"[!Query!]"},
	"components":[
		{"type":"Scroller", "id":"scroller",
		"viewport":
			{"type":"Group", "percentWidth":100,"percentHeight":100,
			"components":[
				{"type":"EditContainer", "id":"edit",
				"components":[
					{"type":"VBox", "percentWidth":100,"percentHeight":100,"setStyle":{"paddingBottom":10,"paddingTop":10,"paddingLeft":10,"paddingRight":10 },
					"components":[
						[STORPROC [!O::getOrderedProperties()!]|categ]
							[IF [!cat!]>0],[/IF]
							{"type":"TitledBorderBox","title":"[!Key!]","percentWidth":100,"components":[
								{"type":"Form","setStyle":{"labelWidth":110,"verticalGap":1},"percentWidth":100,"components":[
								[!item:=0!]
								[STORPROC [!categ!]|media]
									[STORPROC [!media!]|element]
										[SWITCH [!element::elementType!]|=]
											[CASE fkey]
											[/CASE]
											[CASE rkey]
											[/CASE]
											[DEFAULT]
												[MODULE Systeme/formProperty?P=[!element!]&O=[!O!]&item=[!item!]]
												[!item+=1!]
											[/DEFAULT]
										[/SWITCH]
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
				]}
			]}
		}
	]}
]
,
"popup":"modal",
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}

