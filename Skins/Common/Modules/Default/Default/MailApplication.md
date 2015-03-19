[!title:=[!Systeme::CurrentMenu::Titre!]!][IF [!title!]=][!title:=Mail!][/IF]
{"form":{"type":"MDIWindow","id":"Mail_Application","title":"[!title!]", 
"height":650,"width":1100,"popup":"free","localProxy":1,
"components":[
	{"type":"DividedBox","direction":"horizontal","percentHeight":100,"liveDragging":0,"resizeToContent":0,
	"components":[
		{"type":"DividedBox","direction":"vertical","width":160,"minWidth":100,"percentHeight":100,
		"components":[
			{"type":"Tree","id":"folders","percentWidth":100,"percentHeight":50,"drag":0,
			"kobeyeClass":{"module":"Mail","objectClass":"Folder","identifier":"Id","label":"Name",
			"children":["Folder"],
//			"children":["Folder","MailUser"],
			"select":"Id,Name,Type,Icon","IconField":"Icon",
			"sortField":"Id","order":"ASC","form":"MailList.json"},
//			"otherKobeyeClass":{
//				"MailUser":{"module":"Mail","objectClass":"MailUser","identifier":"Id","label":"Subject"}
//			},
			"events":[
				{"type":"init","action":"loadData"},
				{"type":"start","action":"invoke","method":"selectIndex","params":{"index":0}},
				{"type":"change","action":[
					{"action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav","clearContainer":1}},
					{"action":"invoke","objectID":"tabNav","method":"selectIndex","params":{"index":0}}
				]}
			]},
			{"type":"VBox","percentWidth":100,"percentHeight":50}
		]},
		{"type":"TabNavigator","id":"tabNav","percentWidth":100,"percentHeight":100,"minTabWidth":"150",
		"setStyle":{"paddingTop":1},
		"components":[
//			{"type":"Box","id":"single","percentWidth":100,"percentHeight":100,"label":"Mail"}
		]}
	]}
],
"actions":[
]
}}
