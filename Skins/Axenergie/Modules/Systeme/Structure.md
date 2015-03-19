{
"form":{"type":"VGroup","percentWidth":100, "percentHeight":100, "gap":0,
"components":[
	{"type":"ApplicationControlBar", "height":35,
	"components":[
		{"type":"HBox", "label":"[!C::Titre!]","styleName":"BarStyle","percentHeight":100,"percentWidth":100,"setStyle":{"paddingBottom":5,"paddingTop":5,"paddingLeft":5,"paddingRight":5},
		"components":[
	
			{"type":"StartButton", "id":"mainMenu", "percentHeight":"100", "label":"$__OPEN__$"
			,[MODULE Systeme/MainMenu]
			},
			{"type":"TaskBarTab", "id":"taskBar", "percentWidth":100, "percentHeight":"100", "actions":[
				{"type":"MDICanvas", "id":"canvas"}
			]},
			[!Height:=12!]
			{"type":"Button","styleName":"profileButton","percentHeight":100, "id":"logout3", "label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]", "events":[
				{"type":"click", "action":"submit", "url":"Systeme/Deconnexion.json"}
			]}
		]}
	]},
	{"type":"MDICanvas", "id":"canvas","percentWidth":100, "percentHeight":100}
],
"container":"application"}
}


