[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!container:="containerID":"tabNav"!]

{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
	"components":[
		{"type":"ImageButton","id":"edit:[!Int::objectClass!]","width":10,"height":10,"cornerRadius":5,"image":"mwc_i","borderWidth":1},
		{"type":"ImageButton","id":"new:[!Int::objectClass!]","width":10,"height":10,"cornerRadius":5,"image":"mwc_plus","borderWidth":1},
		{"type":"ImageButton","id":"delete:[!Int::objectClass!]","width":10,"height":10,"cornerRadius":5,"image":"mwc_moins","borderWidth":1}
	]
},
{"type":"CoverFlow","kobeyeClass":{"dirtyParent":1,"module":"[!Int::module!]","objectClass":"[!Int::objectClass!]","form":"FormDetail.json"},"dataField":"DocumentList",
	"events":[
		{"type":"start","action":"loadValues","params":{"needsParentId":1}},
		{"type":"proxy", "triggers":[
			{"trigger":"new:[!Int::objectClass!]","action":"invoke","method":"createForm"},
			{"trigger":"edit:[!Int::objectClass!]","action":"invoke","method":"loadFormWithID","params":{[!container!]}},
			{"trigger":"delete:[!Int::objectClass!]","action":"invoke","method":"deleteWithID"}
		]}
	]
}
