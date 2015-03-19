{
"form":{"type":"VBox","id":"LocPrepa:Structure","percentWidth":100, "percentHeight":100, "setStyle":{"verticalGap":0,"backgroundColor":"#dedede"},
"components":[
	{"type":"ApplicationControlBar","height":35,
	"components":[
		{"type":"HBox","top":0,"styleName":"BarStyle","height":35,"percentWidth":100,"setStyle":{"paddingBottom":2,"paddingTop":2,"paddingLeft":5,"paddingRight":5},
		"components":[
			{"type":"Label","text":"PREPARATION","setStyle":{"fontSize":24,"fontWeight":"bold","color":"white"}},
			{"type":"Spacer","percentWidth":100},
			{"type":"Button","styleName":"profileButton","height":30, "id":"logout3", "label":"Déconnexion", "events":[
				{"type":"click", "action":"invoke", "method":"logout", "params":{"confirm":"$__confirmLogout__$"}}
			]}
		]}
	]},
	{"type":"LocPrepa","dataField":"locPrepa","params":{
		"readMethod":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"ReadPrepa"},
		"removeMethod":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"RemovePrepa"},
		"saveMethod":{"method":"object","data":{"module":"StockLogistique","objectClass":"BLTete"},"function":"SavePrepa"},
		"validateMethod":{
			"interface":1,
			"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/valideTournee.json"},
			"function":"ValideTournee","args":"dv:locPrepa,iv:Date,v:1"
		}
	}}
],
"container":"application"}
}
