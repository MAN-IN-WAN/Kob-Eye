[INFO [!Query!]|I]
[STORPROC [!Systeme::Modules!]|mo]
	[IF [!Key!]=[!I::Module!]]
		[!mm:=[!mo!]!]
	[/IF]
[/STORPROC]
[!db:=[!mm::Db!]!]
[!dc:=[!db::Dico()!]!]
[!forms:=!]
{"form":{"type":"MDIWindow", "id":"appli_[!Systeme::CurrentMenu::Url!]", "title":"[!Systeme::CurrentMenu::Titre!]","hideMinimized":1,
//"noControl":1,
"height":650, "width":1100, "popup":"free","localProxy":1,
"components":[
	{"type":"DividedBox", "direction":"horizontal","liveDragging":false, "percentHeight":100, "setStyle":{"backgroundAlpha":0,"borderAlpha":0,"paddingTop":5,"paddingBottom":5,"paddingLeft":5,"paddingRight":5},
	"components":[
		{"type":"GradientVBox", "label":"[!C::Titre!]","styleName":"AccordionStyle","percentHeight":100,"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"paddingRight":0},"width":65,"components":[
		[!I:=0!]
		[STORPROC [!Systeme::CurrentMenu::Menus!]/Affiche=1|C]
			//[IF [!C::Affiche!]==1]
				[IF [!Pos!]>1],[!forms+=,!][/IF]
				{"type":"ImageButton","label":"[!C::Titre!]","styleName":"buttonMenuApplication","percentWidth":100,
				"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"paddingRight":0},"image":"[!Domaine!]/[!C::Icone!]",
				"events":[
					{"type":"click","action":[
						{"action":"invoke","objectID":"firstTab","method":"selectIndex","params":{"index":[!I!]}},
						{"action":"invoke", "objectID":"tabNav", "method":"selectIndex", "params":{"index":0}}
						[!I+=1!]
					]}
				]}
				[!forms+={"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!].json", "containerLabel":"[!C::Titre!]"}!]
			//[/IF]
		[/STORPROC]

//		[STORPROC [!dc!]|di]
//			,{"type":"VBox", "label":"$__Dictionaries__$","styleName":"AccordionStyle","percentHeight":100,"percentWidth":100,"components":[
//			[LIMIT 0|100]
//				[IF [!Pos!]>1],[/IF]
//				{"type":"LinkButton","label":"[!di::Description!]","percentWidth":100,"setStyle":{"color":"white"},"events":[
//					{"type":"click","action":"loadForm","params":{"kobeyeClass":{"form":"[!mm::Nom!]/[!di::titre!]/FormDico.json"}}}
//				]}
//			[/LIMIT]
//			]}			
//			[!forms+=,{"form":"[!mm::Nom!]/FormEmpty.json", "containerLabel":"$__Dictionaries__$"}!]
//		[/STORPROC]

		]},
		{"type":"TabNavigator", "id":"tabNav", "percentWidth":100, "percentHeight":100,"minTabWidth":"150",
		"setStyle":{"paddingTop":1,"backgroundAlpha":0,"borderAlpha":0},
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
