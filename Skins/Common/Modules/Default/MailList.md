[INFO [!Query!]|I]
{"form":{"type":"VBox","id":"Mail_[!I::folder!]","label":"[!I::folder!]","percentWidth":100,"percentHeight":100,
"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0},"clipContent":0,
"kobeyeClass":{"module":"Mail","objectClass":"Message"},
"localProxy":1,
"components":[
	{"type":"MenuTab","maxLines":1,
	"menuItems":[
		{"children":[
			{"label":"$__New__$","icon":"new","data":"new"},
			{"label":"$__Delete__$", "icon":"iconDelete", "data":"delete","stateGroup":"enabled"},
			{"type":"vseparator"},
			{"label":"$__Reply__$", "icon":"right", "data":"reply","stateGroup":"enabled"},
			{"label":"$__Reply all__$", "icon":"msgAnswerAll", "data":"replyAll","stateGroup":"enabled"},
			{"label":"$__Forward__$", "icon":"msgForward", "data":"forward","stateGroup":"enabled"}
		]}
	]},
	{"type":"DividedBox","direction":"vertical","percentHeight":100,
	"components":[
		{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":70,"checkBoxes":1,"dragEnabled":1,
		"kobeyeClass":{"module":"Mail","objectClass":"Message"},
		"columns":[
			{"type":"column","dataField":"Status","headerText":"S","width":25},
			{"type":"column","dataField":"Date","headerText":"Date","format":"time","width":95},
			[IF [!folder!]=Inbox||[!folder!]=Trash]
				{"type":"column","dataField":"From","headerText":"From","width":150},
			[ELSE]
				{"type":"column","dataField":"To","headerText":"To","width":150},
			[/IF]
			{"type":"column","dataField":"Subject","headerText":"Subject","width":300}
		],
		"events":[
			{"type":"start", "action":"invoke","method":"callMethod",
			"params":{"method":"object","data":{"module":"Mail","objectClass":"Message"},
			"function":"MessageList","args":[{"value":["[!folder!]"]}]}},
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}}
		]},
		{"type":"VBox","id":"[!folder!]_preview","percentWidth":100,"percentHeight":30,
		"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0},
		"components":[
		]}
	]}
]}
}

