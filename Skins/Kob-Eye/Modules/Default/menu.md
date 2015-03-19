"menuItems":
{"label":"OPEN", "data":"top", "items":[
	[STORPROC [!Systeme::User::Menus!]|M]
		[IF [!Pos!]>1],[/IF]{"label":"[!M::Titre!]", "data":"action[!M::Id!]"
			[STORPROC [!M::Menus!]|M2]
			 ,"items":[
				[LIMIT 0|20]
					[IF [!Pos!]>1],[/IF]{"label":"[!M2::Titre!]", "data":"action[!M2::Id!]"}
				[/LIMIT]
			]
			[/STORPROC]
		}
	[/STORPROC]
]},
"actions":[
	{"type":"itemClick", "actions":{
		[STORPROC [!Systeme::User::Menus!]|M]
		[IF [!Pos!]>1],[/IF]"action[!M::Id!]":{"type":"click", "action":"loadForm", "url":"[!M::Alias!].json"}
			[STORPROC [!M::Menus!]|M2]
				,"action[!M2::Id!]":{"type":"click", "action":"loadForm", "url":"[!M2::Alias!].json"}
			[/STORPROC]
		[/STORPROC]
	}}
]
