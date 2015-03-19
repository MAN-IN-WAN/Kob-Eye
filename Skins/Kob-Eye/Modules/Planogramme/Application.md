[!forms:=!]
{"form":{"type":"MDIWindow", "id":"mdiPlano", "title":"[!Systeme::CurrentMenu::Titre!]", 
"height":650, "width":1100, "popup":"free",
"components":[
	{"type":"DividedBox", "direction":"horizontal", "percentHeight":100, "liveDragging":0,"resizeToContent":0, 
	"components":[
			[STORPROC [!Systeme::CurrentMenu::Menus!]|C]
				[IF [!C::Affiche!]==1]
					[!forms+={"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!].json", "containerLabel":"[!C::Titre!]"}!]
				[/IF]
			[/STORPROC]
		{"type":"TabNavigator", "id":"tabNav", "percentWidth":100, "percentHeight":100, "closePolicy":"close_rollover", "minTabWidth":"150","maxOpenTab":2,
		"setStyle":{"paddingTop":1},
		"components":[
			{"type":"ViewStack", "id":"firstTab", "percentWidth":100, "percentHeight":100,"styleName":"ViewStackStyle", "bindLabel":1,
			"forms":[
				[!forms!]
			]}
		]}
	]}
],
"actions":[
//	{"type":"close", "action":"confirm"},
]
}}
