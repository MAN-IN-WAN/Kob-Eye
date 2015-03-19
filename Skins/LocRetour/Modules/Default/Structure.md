{
"form":{"type":"VBox","id":"LocRetour:Structure","percentWidth":100, "percentHeight":100, "setStyle":{"verticalGap":0,"backgroundColor":"#dedede"},
"components":[
	{"type":"ApplicationControlBar","height":35,
	"components":[
		{"type":"HBox","top":0,"styleName":"BarStyle","height":35,"percentWidth":100,"setStyle":{"paddingBottom":2,"paddingTop":2,"paddingLeft":5,"paddingRight":5},
		"components":[
			{"type":"Label","text":"RETOUR","setStyle":{"fontSize":24,"fontWeight":"bold","color":"white"}},
			{"type":"Spacer","percentWidth":100},
			{"type":"Button","styleName":"profileButton","height":30, "id":"logout3", "label":"Déconnexion", "events":[
				{"type":"click", "action":"invoke", "method":"logout", "params":{"confirm":"$__confirmLogout__$"}}
			]}
		]}
	]},
	{"type":"LocRetour","dataField":"locRetour","params":{
		"readMethod":{"method":"object","data":{"module":"StockLogistique","objectClass":"RepriseTete"},"function":"ReadRetour"},
		"removeMethod":{"method":"object","data":{"module":"StockLogistique","objectClass":"RepriseTete"},"function":"RemoveRetour"},
		"saveMethod":{"method":"object","data":{"module":"StockLogistique","objectClass":"RepriseTete"},"function":"SaveRetour"},
		"validateMethod":{
			"confirm":{"text":"Valider la tournée"},
			"method":"object","data":{"module":"StockLogistique","objectClass":"Tournee","form":"Functions/valideRetour.json"},
			"function":"ValideRetour","args":"dv:locRetour"
		}
	}}
],
"container":"application"}
}
