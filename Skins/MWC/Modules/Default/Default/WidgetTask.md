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
	{"type":"ImageButton","id":"edit:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1,"stateGroup":"selection","enabled":0},
	{"type":"ImageButton","id":"new:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
	{"type":"ImageButton","id":"delete:[!Int::objectClass!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1,"stateGroup":"selection","enabled":0}
]},
{"type":"List","dataField":"MailList",
	"setStyle":{"horizontalGap":2},"percentWidth":100,"percentHeight":100,
	"getDataFunction":{"method":"object","data":{"module":"Systeme","objectClass":"AlertTask"},
		"function":"GetObjectTask","args":"v:0,v:0,v:[!Int::module!],v:[!Int::objectClass!],id:parentForm"
	},
	"kobeyeClass":{
		//"dirtyParent":1,
		"module":"Systeme",
		"objectClass":"AlertTask",
		"columns":[
			{"type":"horizontal","setStyle":{"paddingTop":10,"paddingBottom":10,"paddingRight":10,"paddingLeft":5,"gap":5},"components":[
				{"type":"vertical","components":[
					{"type":"varchar","field":"Title","setStyle":{"fontWeight":"bold"}},
					{"type":"horizontal","components":[
						{"type":"date","field":"StartDate","prefix":"Start : ","setStyle":{"fontWeight":"bold"}},
						{"type":"varchar","prefix":"       "},
						{"type":"date","field":"EndDate","prefix":"End : ","setStyle":{"fontWeight":"bold"}}
					]},
					{"type":"text","field":"Detail"}
				]}
			]}
		],
		[STORPROC [!O::getElementsByAttribute(iconField,1)!]|Ic]
			[STORPROC [!Ic::elements!]|Id]
				"iconField":"[!Id::name!]"
			[/STORPROC]
		[/STORPROC]
		"icon":"[!O2::getIcon!]",
		"form":"FormTask.json",
		"proxyValues":{"vars":{"TaskModule":{"args":"mo:formCreator"},"TaskObject":{"args":"ob:formCreator"},"ObjectId":{"args":"id:formCreator"}}}
	}
	,"events":[
		{"type":"start","action":"loadValues"},
		{"type":"dblclick","action":"invoke","method":"loadFormWithSelection"},
		{"type":"proxy", "triggers":[
			{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"createForm"},
			{"trigger":"edit:[!Int::objectClass!]","action":"invoke","method":"loadFormWithSelection"},
			{"trigger":"delete:[!Int::objectClass!]","action":"invoke","method":"deleteWithID"}
		]}
	]
}
]}
