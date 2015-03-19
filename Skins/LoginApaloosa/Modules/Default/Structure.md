{
"form":{"type":"Panel","id":"LoginForm","title":"$__Login__$", "width":350,"height":200, "horizontalCenter":0, "verticalCenter":0,"focusedID":"loginField","localProxy":1,"styleName":"PanelLogin",
"components":[
	{"type":"EditContainer","id":"edit","percentWidth":100,"percentHeight":100,"defaultButtonID":"CONNEXION","addForm":0,
	"components":[
		{"type":"Form", "percentWidth":100, "percentHeight":100, "setStyle":{"verticalGap":8},
		"components":[
			{"type":"FormItem","percentLabel":45, "label":"$__Username__$","styleName":"PanelLogin", "components":[
				{"type":"TextInput", "id":"loginField", "dataField":"login","percentWidth":100}
			]},
			{"type":"FormItem","percentLabel":45, "label":"$__Password__$","styleName":"PanelLogin", "components":[
				{"type":"TextInput", "dataField":"pass", "displayAsPassword":1,"percentWidth":100}
			]},
			{"type":"FormItem","percentLabel":35,"styleName":"PanelLogin", "components":[
				{"type":"Button", "label":"Connexion","styleName":"PanelLogin", "id":"CONNEXION", "x":300, "y":100, "width":100, "events":[
					{"type":"click", "action":"invoke", "objectID":"edit", "method":"submit", "params":{"url":"Systeme/Login.json"}}
				]}
			]}
		]}
	]}
],
"container":"application"
}
}