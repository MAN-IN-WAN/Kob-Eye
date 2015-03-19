[!menu:=[!Systeme::User::Menus!]!]
[!m0:=[!menu::0!]!]
[!menus:=[!m0::Menus!]!]
//[!DEBUG::m0!]
{
"form":{"type":"VGroup","id":"Mas:Structure","percentWidth":100, "percentHeight":100, "setStyle":{"verticalGap":4},
"components":[
	{"type":"ApplicationControlBar","height":35,
	"components":[
		{"type":"HBox","top":0, "label":"[!C::Titre!]","styleName":"BarStyle","height":35,"percentWidth":100,"setStyle":{"paddingBottom":2,"paddingTop":2,"paddingLeft":5,"paddingRight":5},
		"components":[
			{"type":"Button", "id":"mainMenu", "height":"30", "label":"[!m0::Titre!]"},
			{"type":"Spacer","percentWidth":100},
			{"type":"ImageButton","id":"alert","width":30,"height":30,"cornerRadius":15,"image":"msgNew","borderWidth":1,
			"alertIcon":"alertIcon","alertType":"alert_alert","autoOpen":1,
			"kobeyeClass":{"module":"Systeme","objectClass":"AlertUser","form":"FormAlert.json"}},
//			"events":[
//				{"type":"click","action":"loadForm","params":{"kobeyeClass":{"module":"Systeme","objectClass":"AlertUser","form":"FormAlert.json"}}}
//			]},
			{"type":"Button","styleName":"profileButton","height":30, "id":"logout3", "label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]", "events":[
				{"type":"click", "action":"invoke", "method":"logout", "params":{"confirm":"$__confirmLogout__$"}}
			]}
		]}
	]},
	{"type":"VBox","id":"canvas","percentWidth":100,"percentHeight":100,
	"components":[
		{"type":"DividedBox", "direction":"horizontal", "percentHeight":100, "liveDragging":0,"resizeToContent":0,
		"components":[
			{"type":"Accordion", "id":"accordion", "width":190, "percentHeight":100,
			"components":[
				[STORPROC [!menus!]/Affiche=1|C]
					[IF [!Pos!]>1],[!forms+=,!][/IF]
				{"type":"VBox", "label":"[!C::Titre!]","styleName":"AccordionStyle","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"paddingBottom":5,"paddingRight":5,"paddingLeft":5}, "components":[
					[!forms+={"form":"[!m0::Url!]/[!C::Url!].json", "containerLabel":"[!C::Titre!]"}!]
					[STORPROC [!C::Menus!]/Affiche=1|S|0|100|Ordre|ASC]
					{"type":"BorderContainer","percentWidth":100,"components":[
						[LIMIT 0|100]
								[IF [!Pos!]>1],[/IF]
								{"type":"IconButton","label":"[!S::Titre!]","styleName":"buttonFirstLevel","percentWidth":100,"setStyle":{"paddingTop":15,"paddingBottom":15},"events":[
									{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!S::Alias!].json"}, "containerLabel":"[!S::Titre!]","containerID":"tabNav"}}
								]}
								[STORPROC [!S::Menus!]/Affiche=1|S2|0|100|Ordre|ASC]
									,{"type":"VBox", "label":"Parcourir","percentHeight":100,"percentWidth":100,"setStyle":{"paddingLeft":10}, "components":[
										[LIMIT 0|100]
											[IF [!Pos!]>1],[/IF]
											{"type":"IconButton","label":"[!S2::Titre!]","styleName":"buttonSecondLevel","percentWidth":100,"setStyle":{"paddingTop":15,"paddingBottom":15},"events":[
												{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!S2::Alias!].json"}, "containerLabel":"[!S2::Titre!]","containerID":"tabNav"}}
											]}
										[/LIMIT]
									]}
								[/STORPROC]
						[/LIMIT]
					]}
					[/STORPROC]
				]}
				[/STORPROC]
			],
			"events":[
				{"type":"indexChanged", "action":[
					{"action":"invoke", "objectID":"firstTab", "method":"selectIndex", "params":{}},
					{"action":"invoke", "objectID":"tabNav", "method":"selectIndex", "params":{"index":0}}
				]}
			]},
			{"type":"TabNavigator", "id":"tabNav", "percentWidth":100, "percentHeight":100, "minTabWidth":"150",
			"setStyle":{"paddingTop":1},
			"components":[
				{"type":"ViewStack", "id":"firstTab", "percentWidth":100, "percentHeight":100,"styleName":"ViewStackStyle", "bindLabel":1,
				"mainContainer":1,
				"forms":[
					[!forms!]
				]}
			]}
		]}
	]},
	{"type":"Toaster","alertType":"alert_alert","toastPosition":"BR"}
],
"container":"application"}
}
