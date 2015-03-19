[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|P]
[OBJ [!Int::module!]|[!Int::objectClass!]|E]
{"type":"DividedBox","percentHeight":100,"direction":"vertical","percentWidth":100,"setStyle":{"verticalGap":0,"backgroundColor":"#cdcdcd"},
"localProxy":1,"components":[
	{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0,"backgroundColor":"#cdcdcd"},
	"components":[
		{"type":"LineChart","percentHeight":100,"percentWidth":100,
			"getDataFunction":{"method":"object","data":{"module":"ProxyCas","objectClass":"ProxyHit"},
				"function":"[!Int::function!]","args":"v:0,v:0,v:[!Int::module!],v:[!Int::objectClass!],id:parentForm"
			},
			"kobeyeClass":{
				"module":"[!Int::module!]",
				"objectClass":"[!Int::objectClass!]",
				"form":"FormDetail.json"
			}
			,"events":[
				{"type":"start","action":"loadValues"},
				{"type":"proxy", "triggers":[
					{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"createForm"},
					{"trigger":"edit:[!Int::objectClass!]","action":"invoke","method":"loadFormWithSelection","params":{}},
					{"trigger":"delete:[!Int::objectClass!]","action":"invoke","method":"deleteWithID"}
				]}
			]
		}
	]}
]}
