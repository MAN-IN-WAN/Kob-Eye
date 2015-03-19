[INFO [!Query!]|I]
[OBJ [!Int::module!]|[!Int::objectClass!]|O]
[!container:="containerID":"tabNav"!]
{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":"invoke","method":"groupState","params":{"group":"selection","property":"enabled","selection":1}}
	}
},
"components":[
{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
"components":[
	{"type":"ImageButton","id":"edit:[!Int::objectClass!]","toolTip":"Edit","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1,"stateGroup":"selection","enabled":0},
	{"type":"ImageButton","id":"zimbra:[!Int::objectClass!]","toolTip":"Zimbra","width":16,"height":16,"cornerRadius":8,"image":"mwc_zimbra","borderWidth":1,"stateGroup":"selection","enabled":0},
	{"type":"ImageButton","id":"new:[!Int::objectClass!]","toolTip":"New","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
	{"type":"ImageButton","id":"delete:[!Int::objectClass!]","toolTip":"Delete","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1,"stateGroup":"selection","enabled":0},
	{"type":"ImageButton","id":"reply:[!Int::objectClass!]","toolTip":"Reply","width":16,"height":16,"cornerRadius":8,"image":"mwc_reply","borderWidth":1,"stateGroup":"selection","enabled":0},
	{"type":"ImageButton","id":"replyAll:[!Int::objectClass!]","toolTip":"Reply all","width":16,"height":16,"cornerRadius":8,"image":"mwc_replyAll","borderWidth":1,"stateGroup":"selection","enabled":0},
	{"type":"ImageButton","id":"forward:[!Int::objectClass!]","toolTip":"Forward","width":16,"height":16,"cornerRadius":8,"image":"mwc_forward","borderWidth":1,"stateGroup":"selection","enabled":0}
]},
{"type":"HBox","minHeight":1,"percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"List","dataField":"maillist",
			"setStyle":{"horizontalGap":2},"percentWidth":100,"percentHeight":100,
			[IF [!I::TypeChild!]=Third]
			"getDataFunction":{"method":"object","data":{"module":"Murphy","objectClass":"Third"},
				"function":"getMailList","args":"id:parentForm"
			},
			[/IF]
			"kobeyeClass":{
				"dirtyParent":1,
				"module":"[!Int::module!]",
				"objectClass":"[!Int::objectClass!]",
				"columns":[
					{"type":"horizontal","setStyle":{"paddingTop":5,"paddingBottom":6,"paddingRight":5,"paddingLeft":5,"gap":5},"components":[
						{"type":"image","value":"mail_read","width":16,"height":16},
						{"type":"vertical","components":[
							{"type":"horizontal","components":[
								{"type":"date","field":"Date","setStyle":{"fontWeight":"bold"}},
								{"type":"varchar","field":"Subject"}
							]},
							{"type":"horizontal","components":[
								{"type":"varchar","field":"From","prefix":"From: ","setStyle":{"fontWeight":"bold"}},
								{"type":"varchar","field":"To","prefix":"To: ","setStyle":{"fontWeight":"bold"}}
							]},
							{"type":"varchar","field":"MiniBody","setStyle":{"color":"gray","lineBreak":"explicit"}}
						]}
					]}
				],
				[STORPROC [!O::getElementsByAttribute(iconField,1)!]|Ic]
					[STORPROC [!Ic::elements!]|Id]
						"iconField":"[!Id::name!]"
					[/STORPROC]
				[/STORPROC]
				"icon":"[!O2::getIcon!]",
				"form":"FormBase.json"
				//"form":"FormBasePopup.json"
			}
			,"events":[
				{"type":"start","action":"loadValues"},
//				{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
				{"type":"dblclick","action":"invoke","method":"callMethod",
				"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
				"args":"id:parentForm,v:edit,id:maillist,ob:parentForm"}},
				{"type":"proxy", "triggers":[

//					{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"javascript","params":{"function":"mwc_new_message",
//					"kobeyeClass":{"module":"Mail","objectClass":"MailUser"},"args":"ob:parentForm,dv:Reference,dv:Company,dv:Email"}},

					{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"callMethod",
					"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
					"args":"id:parentForm,v:0,id:maillist,ob:parentForm"}},
					{"trigger":"reply:[!Int::objectClass!]","action":"invoke","method":"callMethod",
					"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
					"args":"id:parentForm,v:reply,id:maillist,ob:parentForm"}},
					{"trigger":"replyAll:[!Int::objectClass!]","action":"invoke","method":"callMethod",
					"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
					"args":"id:parentForm,v:replyAll,id:maillist,ob:parentForm"}},
					{"trigger":"forward:[!Int::objectClass!]","action":"invoke","method":"callMethod",
					"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
					"args":"id:parentForm,v:forward,id:maillist,ob:parentForm"}},
					{"trigger":"zimbra:[!Int::objectClass!]","action":"invoke","method":"callMethod",
					"params":{"method":"object","function":"GetMessage","data":{"module":"Mail","objectClass":"MailUser"},
					"args":"id:parentForm,v:edit,id:maillist,ob:parentForm"}},
					
					{"trigger":"delete:[!Int::objectClass!]","action":"invoke","method":"deleteWithID"},

					{"trigger":"edit:[!Int::objectClass!]","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}}
//					{"trigger":"new:[!Int::objectClass!]","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//					"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//					"proxyValues":{"vars":{"replyMode":"","listId":""}}}}},
//					{"trigger":"reply:[!Int::objectClass!]","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//					"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//					"proxyValues":{"vars":{"replyMode":"reply","listId":{"args":"id:maillist"}}}}}},
//					{"trigger":"replyAll:[!Int::objectClass!]","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//					"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//					"proxyValues":{"vars":{"replyMode":"replyAll","listId":{"args":"id:maillist"}}}}}},
//					{"trigger":"forward:[!Int::objectClass!]","action":"invoke","action":"loadForm","params":{"containerID":"tabNav",
//					"kobeyeClass":{"module":"Mail","objectClass":"MailUser","form":"MailForm.json",
//					"proxyValues":{"vars":{"replyMode":"forward","listId":{"args":"id:maillist"}}}}}}
				]}
			]
		}
	]
}
]}
