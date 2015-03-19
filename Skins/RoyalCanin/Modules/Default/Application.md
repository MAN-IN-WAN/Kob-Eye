[INFO [!Query!]|I]
[STORPROC [!Systeme::Modules!]|mo]
	[IF [!Key!]=[!I::Module!]]
		[!mm:=[!mo!]!]
	[/IF]
[/STORPROC]
[!db:=[!mm::Db!]!]
[!dc:=[!db::Dico()!]!]

[!forms:=!]
{"form":{"type":"MDIWindow", "id":"appli_[!Systeme::CurrentMenu::Url!]", "title":"[!Systeme::CurrentMenu::Titre!]", 
"height":650, "width":1100, "popup":"free","localProxy":1,"noWindowControl":1,
"components":[
	{"type":"DividedBox", "direction":"horizontal", "percentHeight":100, "liveDragging":0,"resizeToContent":0,
	"components":[
		{"type":"Accordion", "id":"accordion", "width":220, "percentHeight":100,
		"components":[
			[STORPROC [!Systeme::CurrentMenu::Menus!]|C]
				[IF [!C::Affiche!]==1]
					[IF [!Pos!]>1],[!forms+=,!][/IF]
					{"type":"VBox", "label":"[!C::Titre!]","styleName":"AccordionStyle","percentHeight":100,"percentWidth":100,"components":[
						[STORPROC [!C::Menus!]|S]
							[IF [!Pos!]>1],[/IF]
							{"type":"LinkButton","label":"[!S::Titre!]","percentWidth":100,"setStyle":{"color":"gray"},"events":[
								{"type":"click","action":"loadForm","params":{"kobeyeClass":{"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!]/[!S::Url!].json"}}}
							]}
						[/STORPROC]
					]}
					[!forms+={"form":"[!Systeme::CurrentMenu::Url!]/[!C::Url!].json", "containerLabel":"[!C::Titre!]"}!]
				[/IF]
			[/STORPROC]
			
			[STORPROC [!dc!]|di]
				,{"type":"VBox", "label":"$__Dictionaries__$","styleName":"AccordionStyle","percentHeight":100,"percentWidth":100,"components":[
				[LIMIT 0|100]
					[IF [!Pos!]>1],[/IF]
					{"type":"LinkButton","label":"[!di::Description!]","percentWidth":100,"setStyle":{"color":"gray"},"events":[
						{"type":"click","action":"loadForm","params":{"kobeyeClass":{"form":"[!mm::Nom!]/[!di::titre!]/FormDico.json"}}}
					]}
				[/LIMIT]
				]}			
				[!forms+=,{"form":"[!mm::Nom!]/FormEmpty.json", "containerLabel":"$__Dictionaries__$"}!]
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
