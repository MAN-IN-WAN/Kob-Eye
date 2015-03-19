{
"form":{"type":"VGroup","percentWidth":100, "percentHeight":100, "gap":0,
"components":[
	{"type":"ApplicationControlBar", "height":35,
	"components":[
		{"type":"HBox", "label":"[!C::Titre!]","styleName":"BarStyle","percentHeight":100,"percentWidth":100,"setStyle":{"paddingBottom":4,"paddingTop":4,"paddingLeft":5,"paddingRight":5},
		"components":[
	
			{"type":"StartButton", "id":"mainMenu", "percentHeight":"100", "label":"$__OPEN__$"
			,[MODULE Systeme/MainMenu]
			},
			{"type":"TaskBarTab", "id":"taskBar", "percentWidth":100, "percentHeight":"100", "actions":[
				{"type":"MDICanvas", "id":"canvas"}
			]},
			[!Height:=12!]
			{"type":"ImageButton","id":"alert","width":25,"height":25,"cornerRadius":15,"image":"msgNew","borderWidth":1,
			"alertIcon":"alertIcon","alertType":"alert_alert",
			"events":[
				{"type":"click","action":"loadForm","params":{"kobeyeClass":{"module":"Systeme","objectClass":"AlertUser","form":"FormAlert.json"}}}
			]},
			{"type":"Button","styleName":"profileButton","percentHeight":100, "id":"logout3", "label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]", "events":[
				{"type":"click", "action":"submit", "url":"Systeme/Deconnexion.json"}
			]}
		]}
	]},
	{"type":"MDICanvas", "id":"canvas","percentWidth":100, "percentHeight":100}
        ,
	{"type":"Toaster","alertType":"alert_alert","toastPosition":"TR"}
],
"container":"application"}
}


