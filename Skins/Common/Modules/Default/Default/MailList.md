{"form":{"type":"VBox","id":"Mail:Folder","label":"","percentWidth":100,"percentHeight":100,
"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0,"backgroundColor":"#e8e8e8"},"clipContent":0,
"kobeyeClass":{"module":"Mail","objectClass":"Folder"},
"localProxy":1,
"components":[
	{"type":"MenuTab","maxLines":1,
	"menuItems":[
		{"children":[
			{"label":"$__New__$","icon":"new","data":"new"},
			{"label":"$__Delete__$","icon":"iconDelete","data":"delete","needFocus":1},
			{"label":"$__Reply__$","icon":"msgAnswer","data":"reply","needFocus":1},
			{"label":"$__Reply all__$","icon":"msgAnswerAll","data":"replyAll","needFocus":1},
			{"label":"$__Forward__$","icon":"msgForward","data":"forward","needFocus":1},
			//{"label":"$__Unread__$","icon":"msgUnread","data":"set to unread","needFocus":1},
			{"label":"$__Update__$","icon":"refresh","data":"update"}
		]}
	],
	"actions":[
		{"type":"itemClick","actions":{
				"delete":{"action":"invoke", "method":"deleteFromSelection"},
				"update":{"action":"invoke","method":"callMethod","params":{"method":"object",
				"data":{"module":"Murphy","objectClass":"Message"},"function":"SynchMail"}}
			}
		}
	]},
	{"type":"DividedBox","direction":"horizontal","percentHeight":100,"percentWidth":100,
	"components":[
		{"type":"AdvancedDataGrid","dataField":"dataGrid","percentWidth":100,"percentHeight":100,"checkBoxes":1,"dragEnabled":1,"dropEnabled":1,
		"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json"},
		"getDataFunction":{"method":"object","function":"GetMessageList","args":"id:parentForm"},
		"columns":[
			{"type":"column","dataField":"StatusIcon","headerText":"S","width":20,"format":"image"},
			{"type":"column","dataField":"Date","headerText":"Date","format":"time","width":100},
			[IF [!folder!]=Inbox||[!folder!]=Trash]
				{"type":"column","dataField":"From","headerText":"From","width":150},
			[ELSE]
				{"type":"column","dataField":"To","headerText":"To/From","width":150},
			[/IF]
			{"type":"column","dataField":"Subject","headerText":"Subject","width":300}
		],
		"events":[
			{"type":"start","action":"loadValues"},
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
			{"type":"change","action":"invoke","method":"loadFormWithID","params":{"containerID":"preview","clearContainer":1,
			"kobeyeClass":{"dirtyParent":1,"module":"Mail","objectClass":"MailUser","form":"MailPreview.json"}}},
			{"type":"proxy", "triggers":[
				{"trigger":"new","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
				"proxyValues":{"vars":{"replyMode":"","listId":""}}}}},
//				{"trigger":"new","action":"invoke","method":"createForm","params":{"containerID":"tabNav"}},
				{"trigger":"open","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
				{"trigger":"refresh", "action":"invoke", "method":"restart"},
				{"trigger":"reply","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
				"proxyValues":{"vars":{"replyMode":"reply","listId":{"args":"id:dataGrid"}}}}}},
				{"trigger":"replyAll","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
				"proxyValues":{"vars":{"replyMode":"replyAll","listId":{"args":"id:dataGrid"}}}}}},
				{"trigger":"forward","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
				"proxyValues":{"vars":{"replyMode":"forward","listId":{"args":"id:dataGrid"}}}}}}
			]}
		]},
		{"type":"VBox","id":"preview","percentWidth":100,"percentHeight":100,
		"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0}}
	]}
]}
}

