{
"form":{"type":"VBox","id":"Locanim:Structure","percentWidth":100, "percentHeight":100, "setStyle":{"verticalGap":4},
"components":[
	{"type":"ApplicationControlBar","height":35,
	"components":[
		{"type":"HBox","top":0, "label":"[!C::Titre!]","styleName":"BarStyle","height":35,"percentWidth":100,"setStyle":{"paddingBottom":2,"paddingTop":2,"paddingLeft":5,"paddingRight":5},
		"components":[
			{"type":"StartButton", "id":"mainMenu", "height":"30", "label":"$__OPEN__$"
			,[MODULE Systeme/MainMenu]
			},
			{"type":"TaskBarTab", "id":"taskBar", "percentWidth":100, "height":"30", "actions":[
				{"type":"MDICanvas", "id":"canvas"}
			]},
//			[IF [!Systeme::User::Developper!]]
//			[/IF]
			{"type":"ImageButton","id":"alert","width":30,"height":30,"cornerRadius":15,"image":"msgNew","borderWidth":1,
			"alertIcon":"alertIcon","alertType":"alert_alert","autoOpen":0,
			"kobeyeClass":{"module":"Systeme","objectClass":"AlertUser","form":"FormAlert.json"}},
			{"type":"Button","styleName":"profileButton","height":30, "id":"logout3", "label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]", "events":[
				{"type":"click", "action":"invoke", "method":"logout", "params":{"confirm":"$__confirmLogout__$"}}
			]}
		]}
	]},
	{"type":"MDICanvas","id":"canvas","percentWidth":100,"percentHeight":100}
],
"container":"application"}
}
