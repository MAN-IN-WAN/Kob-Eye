[!title:=[!Systeme::CurrentMenu::Titre!]!][IF [!title!]=][!title:=Mail!][/IF]
{"form":{"type":"MDIWindow","id":"Mail_Application","title":"[!title!]", 
"height":650,"width":1100,"popup":"free","localProxy":1,
"components":[
	{"type":"TabNavigator","id":"tabNav","percentWidth":100,"percentHeight":100,"minTabWidth":"150",
	"setStyle":{"paddingTop":1},
	"components":[
		{"type":"HBox","percentWidth":100,"percentHeight":100,"label":"Mail",
		"components":[
			{"type":"VBox","percentHeight":100,"width":120,"setStyle":{"paddingLeft":5,"paddingRight":0,"paddingTop":5},"components":[
				{"type":"BadgeButton","label":"$__Inbox__$","percentWidth":100,"badge":0,"height":30,
				"proxySet":"setFilter","proxyValue":"Folder=1&MailUserUserId=[!Systeme::User::Id!]","badgeColor":"0x468847",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","filters":"Folder=1&MailUserUserId=[!Systeme::User::Id!]"},
				"events":[
					{"type":"start", "action":"loadData"},
					{"type":"click","action":"invoke","method":"setValue","params":{"dataField":"filterField","value":""}}
				]},
				{"type":"BadgeButton","label":"$__Sent__$","percentWidth":100,"badge":0,"height":30,
				"proxySet":"setFilter","proxyValue":"Folder=2&MailUserUserId=[!Systeme::User::Id!]","badgeColor":"0x3A87AD",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","filters":"Folder=2&MailUserUserId=[!Systeme::User::Id!]"},
				"events":[
					{"type":"start", "action":"loadData"},
					{"type":"click","action":"invoke","method":"setValue","params":{"dataField":"filterField","value":""}}
				]},
				{"type":"BadgeButton","label":"$__Trash__$","percentWidth":100,"badge":0,"height":30,
				"proxySet":"setFilter","proxyValue":"Folder=4&MailUserUserId=[!Systeme::User::Id!]","badgeColor":"0x999999",
				"kobeyeClass":{"module":"Mail","objectClass":"MailUser","filters":"Folder=4&MailUserUserId=[!Systeme::User::Id!]"},
				"events":[
					{"type":"start", "action":"loadData"},
					{"type":"click","action":"invoke","method":"setValue","params":{"dataField":"filterField","value":""}}
				]},
				{"type":"LabelItem","label":"$__Search__$","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-3,"paddingTop":0},"components":[
					{"type":"TextInput","id":"filterField","dataField":"filterField","dataGroup":"searchGroup","percentWidth":100,"setStyle":{"borderAlpha":1}}
				]}
			]},
			{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0,"backgroundColor":"#e8e8e8"},
			"components":[
				{"type":"MenuTab","maxLines":1,
				"menuItems":[
					{"children":[
						{"label":"$__Open__$","icon":"open","data":"open","needFocus":1},
						{"label":"Zimbra","icon":"open","data":"zimbra","needFocus":1},
						{"label":"$__New__$","icon":"new","data":"new"},
						{"label":"$__Delete__$","icon":"iconDelete","data":"delete","needFocus":1},
						{"label":"$__More__$","icon":"down","data":"more","children":[
							{"label":"$__Reply__$","icon":"msgAnswer","data":"reply","needFocus":1},
							{"label":"$__Reply all__$","icon":"msgAnswerAll","data":"replyAll","needFocus":1},
							{"label":"$__Forward__$","icon":"msgForward","data":"forward","needFocus":1}
						]}
						,{"label":"$__Update__$","icon":"refresh","data":"update"}
					]}
				],
				"actions":[
					{"type":"itemClick","actions":{
							"delete":{"action":"invoke", "method":"deleteFromSelection"}
//							,"update":{"action":"invoke","method":"callMethod","params":{"method":"object","data":{"module":"Murphy","objectClass":"Message"},"function":"SynchMail"}}
						}
					}
				]},
				{"type":"DividedBox","direction":"horizontal","percentHeight":100,"percentWidth":100,
				"components":[
					{"type":"List","dataField":"maillist","percentWidth":100,"percentHeight":100,
					"dragEnabled":1,"dropEnabled":1,
					"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json","filters":"Folder=1&MailUserUserId=[!Systeme::User::Id!]",
					"columns":[
						{"type":"vertical","height":40,"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingRight":0,"paddingLeft":0,"gap":0},"components":[
							{"type":"horizontal","setStyle":{"paddingTop":8,"paddingBottom":5,"paddingRight":2,"paddingLeft":2,"gap":6},"components":[
								{"type":"image","field":"StatusIcon","width":16,"height":16},
								{"type":"vertical","components":[
									{"type":"horizontal","components":[
										{"type":"date","field":"Date"}, //,"setStyle":{"fontWeight":"bold"}},
										{"type":"spacer","width":30},
										{"type":"varchar","field":"Subject"}
									]},
									{"type":"horizontal","components":[
										{"type":"varchar","field":"From","prefix":"From: ","setStyle":{"fontWeight":"bold"}},
										{"type":"spacer","width":30},
										{"type":"varchar","field":"To","prefix":"To: ","setStyle":{"fontWeight":"bold"}}
									]},
									{"type":"varchar","field":"MiniBody","setStyle":{"color":"gray","lineBreak":"explicit"}}
								]},
								{"type":"spacer","percentWidth":100},
								{"type":"image","field":"AttachIcon","width":16,"height":16}
							]},
							{"type":"background","color":"0xdddddd","height":1,"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingRight":0,"paddingLeft":0}}
						]}
					]},
					"events":[
						{"type":"start","action":"loadValues"},
//						{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
						{"type":"dblclick","action":"invoke","method":"callMethod",
						"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
						"args":"id:parentForm,v:edit,id:maillist,ob:parentForm"}},

						{"type":"change","action":"invoke","method":"loadFormWithID","params":{"containerID":"preview","clearContainer":1,
						"kobeyeClass":{"dirtyParent":1,"module":"Mail","objectClass":"MailUser","form":"MailPreview.json"}}},
						{"type":"proxy", "triggers":[
							{"trigger":"update","action":"loadValues"},
							{"trigger":"filterField","action":"invoke","method":"filterData","params":{"filter":"filterField"}},
							{"trigger":"zimbra","action":"invoke","method":"callMethod",
							"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
							"args":"id:parentForm,v:edit,id:maillist,ob:parentForm"}},
							{"trigger":"new","action":"invoke","method":"callMethod",
							"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
							"args":"id:parentForm,v:0,id:maillist,ob:parentForm"}},
							{"trigger":"reply","action":"invoke","method":"callMethod",
							"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
							"args":"id:parentForm,v:reply,id:maillist,ob:parentForm"}},
							{"trigger":"replyAll","action":"invoke","method":"callMethod",
							"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
							"args":"id:parentForm,v:replyAll,id:maillist,ob:parentForm"}},
							{"trigger":"forward","action":"invoke","method":"callMethod",
							"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
							"args":"id:parentForm,v:forward,id:maillist,ob:parentForm"}},
							
							{"trigger":"setFilter","action":"invoke","method":"setFilter","params":{"args":"pv:setFilter"}},
							{"trigger":"refresh", "action":"invoke", "method":"restart"},
//							{"trigger":"new","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//							"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//							"proxyValues":{"vars":{"replyMode":"","listId":""}}}}},
							{"trigger":"open","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}}
//							{"trigger":"reply","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//							"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//							"proxyValues":{"vars":{"replyMode":"reply","listId":{"args":"id:maillist"}}}}}},
//							{"trigger":"replyAll","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//							"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//							"proxyValues":{"vars":{"replyMode":"replyAll","listId":{"args":"id:maillist"}}}}}},
//							{"trigger":"forward","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//							"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//							"proxyValues":{"vars":{"replyMode":"forward","listId":{"args":"id:maillist"}}}}}}
						]}
					]},
					{"type":"VBox","id":"preview","percentWidth":100,"percentHeight":100,
					"setStyle":{"paddingLeft":0,"paddingRight":0,"verticalGap":0}}
				]}
			]}
		]}
	]}
]}
}
