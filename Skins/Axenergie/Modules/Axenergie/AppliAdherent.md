[!usr:=[!Systeme::User!]!]
[!adhs:=[!usr::getChildren(Adherent)!]!]
[STORPROC [!adhs!]|adh|0|1][/STORPROC]
[!dbas:=[!adh::getChildren(Database)!]!]
[STORPROC [!dbas!]|dba|0|1][/STORPROC]


[INFO [!Query!]|I]
[STORPROC [!Systeme::Modules!]|mo]
	[IF [!Key!]=[!I::Module!]]
		[!mm:=[!mo!]!]
	[/IF]
[/STORPROC]
[!db:=[!mm::Db!]!]
[!dc:=[!db::Dico()!]!]

[!forms:=!]
[!index:=0!]
{"form":{"type":"MDIWindow","id":"Adherent:[!usr::Id!]","title":"Axenergie", 
"height":650,"width":1100,"popup":"free","localProxy":1,
"components":[
//	{"type":"DividedBox", "direction":"horizontal", "percentHeight":100, "liveDragging":0,"resizeToContent":0,
//	"components":[
//		{"type":"Accordion", "id":"accordion", "width":220, "percentHeight":100,
//		"components":[
//			{"type":"VBox", "label":"Gestion des Produits","styleName":"AccordionStyle","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"paddingBottom":5,"paddingRight":5,"paddingLeft":5}, "components":[
//			]}
//		],
//		"events":[
//			{"type":"indexChanged", "action":[
//				{"action":"invoke", "objectID":"firstTab", "method":"selectIndex", "params":{}},
//				{"action":"invoke", "objectID":"tabNav", "method":"selectIndex", "params":{"index":0}}
//			]}
//		]},
//		{"type":"TabNavigator", "id":"tabNav", "percentWidth":100, "percentHeight":100, "closePolicy":"close_rollover", "minTabWidth":"150",
//		"setStyle":{"paddingTop":1},
//		"components":[
			{"type":"ViewStack", "id":"firstTab", "percentWidth":100, "percentHeight":100,"styleName":"ViewStackStyle", "bindLabel":1,
			"forms":[
				{"form":"Axenergie/Database/[!dba::Id!]/DatabaseManagement.json", "containerLabel":"Gestion des Produits"}
			]}
//		]}
//	]}
],
"actions":[
//	{"type":"close", "action":"confirm"},
]
}}
