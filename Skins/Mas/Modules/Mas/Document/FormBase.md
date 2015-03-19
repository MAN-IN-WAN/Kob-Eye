[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!chldrn:=[!O::getChildTypes!]!]
[!firstField:=!]

[!mnuItm:=!][!mnuAct:=!][!chkbox:=!][!mnuLin:=1!]
[STORPROC [!O::getFunctions!]|mnu]
	[!acts:=[!mnu::actions!]!]
	[IF [!mnu::hidden!]!=1&[!mnu::listOnly!]!=1]
		[IF [!mnu::type!]=vseparator]
			[IF [!mnuItm!]][!mnuItm+=,{"type":"separator"}!][/IF]
		[ELSE]
			[IF [!mnu::interface!]]
			[ELSE]
				[IF [!mnu::localProxy!]=1]
					[!mnuAct+=,[!acts::0!]!]
				[ELSE]
					[IF [!acts::1!]]
						[IF [!mnuItm!]][!mnuItm+=,!][/IF]
						[!mnuItm+={"label":"[!mnu::label!]","icon":"[!mnu::icon!]","data":"[!mnu::name!]"!]
						[IF [!mnu::stateGroup!]][!mnuItm+=,"stateGroup":"[!mnu::stateGroup!]"!][/IF]
						[!mnuItm+=}!]
						[!acts:=[!mnu::actions!]!]
						[IF [!acts::1!]][!mnuAct+=,"[!mnu::name!]":[!acts::1!]!][/IF]
					[/IF]
				[/IF]
			[/IF]
		[/IF]
		[IF [!mnu::menuLines!]][!mnuLin:=[!mnu::menuLines!]!][/IF]
	[/IF]
[/STORPROC]
[!conf:=[!O::getConfiguration!]!][!conf:=[!conf::Interface!]!][!conf:=[!conf::FormBase!]!]
[!class:=[!O::getObjectClass()!]!]
[IF [!class::color!]][!color:=,"fillColors":["[!class::color!]","0x999999"]!][/IF]

{"form":{"type":"GradientVBox","id":"FB:[!I::TypeChild!]?","label":"[!O::getDescription()!]","percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5[!color!]},"clipContent":0,"tabColor":"[!class::color!]",
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedAdmin","property":"enabled","hasID":1,"admin":1}}
		]}
		[!mnuAct!]
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":[!mnuLin!],"menuItems":[
		{"children":[
			[IF [!conf::showNew!]=1]{"label":"$__New__$","icon":"new","data":"new"},[/IF]
			{"label":"$__Save__$","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"$__Save & Close__$","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"$__Close__$","icon":"close","data":"close"},
			//{"type":"vseparator"},
			{"label":"$__Cancel__$","icon":"refresh","data":"cancel","stateGroup":"updated"}
			[IF [!conf::hideDelete!]!=1],{"label":"$__Delete__$","icon":"iconDelete","data":"delete","stateGroup":[IF [!conf::hideDelete!]=admin]"savedAdmin"[ELSE]"saved"[/IF]}[/IF]
			[IF [!mnuItm!]]
				[IF [!conf::hideMore!]]
					,[!mnuItm!]
				[ELSE]
					,{"label":"$__More__$","icon":"down","data":"more","children":[[!mnuItm!]]}
				[/IF]
			[/IF]
			[IF [!conf::hideAdd!]=]
				[STORPROC [!O::getChildElements!]/access=1|Enf]
					,{"label":"$__Add__$...","icon":"addlevel","data":"add","children":[
						[LIMIT 0|100]
						[IF [!Pos!]>1],[/IF]{"label":"$__Add__$ [!Enf::objectName!]","icon":"","data":"new[!Enf::objectName!]","stateGroup":"saved"}
						[/LIMIT]
					]}
				[/STORPROC]
			[/IF]
		]}
	]},
	{"type":"Box","percentWidth":100,"percentHeight":100,"minHeight":0,
	"components":[
		{"type":"EditContainer","percentHeight":100, "id":"edit",
		"components":[
			{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"vertical",
			"components":[							
				{"type":"DividedBox","percentWidth":100,"percentHeight":100,"direction":"horizontal",
				"components":[							
					{"type":"VBox","percentWidth":100,"percentHeight":100,
					"components":[
						{"type":"VBox","percentWidth":100,"percentHeight":100,"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,"setStyle":{"verticalGap":5},
						"components":[
							{"type":"CollapsiblePanel","percentWidth":100,"title":"Document","layout":{"type":"HorizontalLayout"},"open":1,
							"components":[
								{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2,"paddingLeft":5,"paddingRight":5,"paddingTop":5,"paddingBottom":5},
								"components":[	
									{"type":"FormItem","percentLabel":35,"label":"Titre","percentWidth":100,"components":[
										{"type":"TextInput","dataField":"Titre","percentWidth":100,"validType":"string" ,"required":1}
									]},
									{"type":"FormItem","percentLabel":35,"label":"Description","percentWidth":100,"components":[
										{"type":"TextArea","dataField":"Description","percentWidth":100,"height":70,"validType":"string" }
									]},
									{"type":"FormItem","percentLabel":35,"label":"Date de publication","percentWidth":100,"components":[
										{"type":"DateField","dataField":"DatePublication","validType":"date" ,"required":1,"defaultValue":"Now"}
									]},
									{"type":"FormItem","percentLabel":35,"label":"Date d'expiration","percentWidth":100,"components":[
										{"type":"DateField","dataField":"DateExpiration","validType":"date" }
									]},
									{"type":"FormItem","percentLabel":35,"label":"Document","percentWidth":100,"components":[
										{"type":"Upload","dataField":"Document","percentWidth":100}
									]},
									{"type":"FormItem","percentLabel":35,"label":"CategorieId","percentWidth":100,"components":[
										{"type":"Tree","dataField":"Categorie.CategorieId","id":"CB:CategorieId","checkBoxes":1,"percentWidth":100,"height":160,
											"kobeyeClass":{
												"module":"Mas",
												"objectClass":"Categorie",
												"children":["Categorie"],
												"icon":"/Skins/AdminV2/Img/IconAqua/My-Docs.png"
											},
											"checkKobeyeClass":{
												"module":"Mas",
												"parentClass":"Categorie",
												"dirtyChild":1,
												"icon":"/Skins/AdminV2/Img/IconAqua/My-Docs.png"
											},
											"events":[
												{"type":"init", "action":"loadData"}
												,{"type":"start","action":"invoke","method":"loadCheckData"}
												
											]
										}
									]},
									{"type":"FormItem","percentLabel":35,"label":"Rôles","percentWidth":100,
									"components":[
										{"type":"KeyList","dataField":"Role.RoleId","percentWidth":100,"height":80,
										"kobeyeClass":{"dirtyParent":1,"urlType":"children","objectClass":"Role","keyName":"RoleId","form":"PopupList.json"
										},
										"events":[
											{"type":"start","action":"loadValues"}
										]}
									]}
								]}
							]}
						]}
					]},
					{"type":"Accordion","id":"stats[!Pos!]","percentWidth":100,"percentHeight":100,"stateGroup":"saved",
					"components":[
						{"type":"VBox","percentHeight":100,"percentWidth":100,
						"components":[
							{"type":"AdvancedDataGrid","id":"docs","percentHeight":100,"percentWidth":100,
							"kobeyeClass":{"dirtyParent":1,"objectClass":"UserDocument","view":"UserList"},
							"events":[
								{"type":"start","action":"loadValues","params":{"needsParentId":1}}
							],
							"columns":[
								{"type":"column","dataField":"DateConsultation","headerText":"Consultation","format":"date","width":80},
								{"type":"column","dataField":"Nom","headerText":"Nom","width":250},
								{"type":"column","dataField":"Prenom","headerText":"Prénom"}
							]}
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start","action":"loadValues","params":{"needsId":1}},
			{"type":"proxy","triggers":[
				{"trigger":"saveClose","action":"invoke","method":"saveData","params":{"closeForm":1}},
				{"trigger":"save","action":"invoke","method":"saveData","params":{"closeForm":0}},
				{"trigger":"close","action":"invoke","objectID":"parentForm","method":"closeForm"},
				{"trigger":"delete","action":"invoke","method":"deleteData"},
				{"trigger":"cancel","action":"invoke","method":"cancelEdit"},
				{"trigger":"new","action":[
					{"action":"invoke","method":"clearData"},
					{"action":"invoke","method":"restart"}
				]}
			]}
		]}
	]}
],
[IF [!firstField!]]"focusedID":"[!firstField!]",[/IF]
"actions":[
	{"type":"close", "action":"confirmUpdate"}
]}
}


