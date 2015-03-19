"components":[
	{"type":"VBox", "components":[
	[STORPROC [!Systeme::User::Menus!]|M]
		[IF [!M::Affiche!]==1]
			[IF [!Pos!]>1],[/IF]
			{"type":"IconButton",[IF [!M::Icone!]]"icone":"/[!M::Icone!]",[/IF] "percentWidth":100,"height":25,"styleName":"startMenu", "label":"[!M::Titre!]", "events":[
				{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!M::Url!].json"}}}
			]}
		[/IF]
	[/STORPROC]
	]}
]
