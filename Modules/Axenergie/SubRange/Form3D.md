{"form":{"type":"VBox", "id":"FL:[!I::Module!]/[!I::TypeChild!]", "label":"[!O::getDescription()!]", "percentWidth":100, "percentHeight":100, 
	"setStyle":{"closable":0, "paddingTop":0, "paddingLeft":5, "paddingRight":5},"localProxy":1, 
	"components":[
	{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5}, 
	"components":[
		{"type":"HGroup",
		"components":[
			{"type":"Spacer"},
			{"type":"ProfileButton", "id":"profileButton","percentHeight":"100","label":"[!Systeme::User::Prenom!] [!Systeme::User::Nom!]", "components":[
				{"type":"HBox", "components":[
					{"type":"Image", "pictureURL":"/[!Systeme::User::Avatar!]","width":75,"height":75},
					{"type":"VBox", "components":[
						{"type":"Label","height":20, "text":"[!Systeme::User::Login!]"},
						{"type":"Label","height":20, "text":"[!Systeme::User::Nom!] [!Systeme::User::Prenom!]"}
					]}
				]},
				{"type":"Label","height":20, "text":"[!Systeme::User::Mail!]"},
				{"type":"Label","height":20, "text":"[JSON][!Systeme::User::Informations!][/JSON]"},
				{"type":"Label","height":20, "text":"tel: [!Systeme::User::Tel!] fax: [!Systeme::User::Fax!]"},
				{"type":"Label","height":20, "text":"[!Systeme::User::Adresse!]"},
				{"type":"Label", "height":20,"text":"[!Systeme::User::CodPos!] [!Systeme::User::Ville!]"},
				{"type":"Label","height":20, "text":"[!Systeme::User::Pays!]"},
				{"type":"Button", "id":"logout3", "label":"Logout", "events":[
					{"type":"click", "action":"submit", "url":"Systeme/Deconnexion.json"}
				]}
			]},
			{"type":"ComboBox","dataField":"Etat","id":"Etat","width":80,"dataProvider":[
				{"data":"1","label":"aaaaaaaaaaa"},
				{"data":"1","label":"aaaaaaaaaaa"},
				{"data":"1","label":"aaaaaaaaaaa"},
				{"data":"1","label":"aaaaaaaaaaa"},
				{"data":"1","label":"aaaaaaaaaaa"},
				{"data":"1","label":"aaaaaaaaaaa"}
			]},
			{"type":"Button","id":"ok","label":"Valider","width":80},
			{"type":"Button","id":"delete","label":"Supprimer","width":80,
			"events":[
				{"type":"click","action":"invoke","method":"callMethod","params":{"method":"form"}}
			]},
			{"type":"Button","id":"cancel","label":"Annuler","width":80,
			"events":[
				{"type":"click","action":"invoke","objectID":"parentForm","method":"closeForm"}
			]}
		]},
		{"type":"View3D","percentWidth":100,"percentHeight":100}
	]}
],
"popup":"",
"actions":[{"type":"close","action":"confirmUpdate"}
]}
}
