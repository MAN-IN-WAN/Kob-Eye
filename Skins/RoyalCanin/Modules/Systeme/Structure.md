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
	//		{"type":"LoadStatus"},
	//		{"type":"Barcode"},
			[!Height:=12!]
//			{"type":"AlertButton", "id":"alertButton","percentHeight":"100",
//				"kobeyeClass":{
//					"module":"Systeme",
//					"objectClass":"Event",
//					"form":"popupEvent.json",
//					"filters":"EventModule=Vitrine&(!EventObjectClass=Produit+EventObjectClass=Categorie+EventObjectClass=Modele!)", 
//					"sortField":"tmsCreate", 
//					"order":"DESC" 
//				},
//				"message":{ 
//					"add":"$__AddAlert__$", 
//					"edit":"$__EditAlert__$", 
//					"delete":"$__DeleteAlert__$"
//				},
//				"events":[
//					{"type":"init", "action":"invoke", "method":"startTimer"}
//				]
//			}
//			,
//			{"type":"ProfileButton", "id":"profileButton","percentHeight":"100","label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]",
//			"components":[
//				{"type":"HBox", "components":[
//					{"type":"Image", "pictureURL":"[IF [!Systeme::User::Avatar!]]/[!Systeme::User::Avatar!][/IF]","width":75,"height":75},
//					{"type":"VBox", "components":[
//						{"type":"Label","height":[!Height!], "text":"[!Systeme::User::Login!]"},
//						{"type":"Label","height":[!Height!], "text":"[!Systeme::User::Nom!] [!Systeme::User::Prenom!]"},
//						{"type":"Label","height": [!Height!], "text":"[!Systeme::User::Mail!]"}
//					]}
//				]},
//				{"type":"TitledBorderBox","title":"$__Profile__$",
//					"components":[
//						{"type":"VBox",
//						"components":[
//							{"type":"Label","height":[!Height!], "text":" [JSON][!Systeme::User::Informations!][/JSON]"},
//							{"type":"Label","height":[!Height!], "text":" tel: [!Systeme::User::Tel!]"},
//							{"type":"Label","height":[!Height!], "text":" fax: [!Systeme::User::Fax!]"},
//							{"type":"Label","height":[!Height!], "text":" [!Systeme::User::Adresse!]"},
//							{"type":"Label", "height":[!Height!],"text":" [!Systeme::User::CodPos!] [!Systeme::User::Ville!]"},
//							{"type":"Label","height":[!Height!], "text":" [!Systeme::User::Pays!]"}
//						]}
//					]
//				},
//				{"type":"TitledBorderBox","title":"$__Permissions__$",
//					"components":[
//						{"type":"VBox","setStyle":{"paddingTop":5,"paddingBottom":5},
//						"components":[
//							[STORPROC [!Systeme::User::Access!]|A]
//							[IF [!Pos!]>1],[/IF]
//							{"type":"Label","height":[!Height!], "text":" - [!A::Titre!]"},
//							{"type":"Label","height":[!Height!], "text":"    [!A::Alias!]"}
//							[/STORPROC]
//						]}
//					]
//				}, 
//				{"type":"Button", "id":"logout3", "label":"$__Logout__$", "events":[
//					{"type":"click", "action":"submit", "url":"Systeme/Deconnexion.json"}
//				]}
//			]}
//			{"type":"FlexSpy"},
			{"type":"Button","styleName":"profileButton","percentHeight":100, "id":"logout3", "label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]", "events":[
				{"type":"click", "action":"submit", "url":"Systeme/Deconnexion.json"}
			]}
		]}
	]},
	{"type":"MDICanvas", "id":"canvas","percentWidth":100, "percentHeight":100}
],
"container":"application"}
}


