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

{"form":{"type":"GradientVBox","id":"FB:[!I::TypeChild!]?","label":"Utilisateurs","percentHeight":100,
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
							{"type":"CollapsiblePanel","title":"Contact","percentWidth":100,"open":1,
							"components":[
								{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"verticalGap":2,"paddingLeft":10,"paddingRight":10,"paddingTop":4,"paddingBottom":6},
								"components":[
									{"type":"DataField","dataField":"UserId"},
									{"type":"FormItem","percentLabel":25,"label":"eMail","percentWidth":100,"components":[
										{"type":"TextInput","dataField":"Mail","percentWidth":100,"validType":"email" ,"maxChars":255,"required":1}
									]},
									{"type":"FormItem","percentLabel":25,"label":"Mot de passe","direction":"horizontal","setStyle":{"horizontalGap":2},"percentWidth":100,"components":[
										{"type":"TextInput","id":"password_Pass","dataField":"Pass","percentWidth":100,"displayAsPassword":1,"passwordForce":0,"required":1}
									]},
									{"type":"FormItem","percentLabel":25,"label":"Téléphone","percentWidth":100,"components":[
										{"type":"TextInput","dataField":"Tel","percentWidth":100}
									]},
									{"type":"FormItem","percentLabel":25,"label":"Actif","direction":"horizontal","setStyle":{"horizontalGap":2},"percentWidth":100,"components":[
										{"type":"CheckBox","dataField":"Actif","defaultValue":1,"percentWidth":100}
									]},
									{"type":"FormItem","percentLabel":25,"label":"Unités","direction":"horizontal","setStyle":{"horizontalGap":2},"percentWidth":100,"components":[
										{"type":"TextInput","dataField":"Units","width":100,"validType":"int","setStyle":{"textAlign":"end"}}
									]},
									{"type":"FormItem","percentLabel":25,"label":"Unités totales","direction":"horizontal","setStyle":{"horizontalGap":2},"percentWidth":100,"components":[
										{"type":"TextInput","dataField":"TotalUnits","width":100,"validType":"int","setStyle":{"textAlign":"end"}}
									]}
								]}
							]}
						]}
					]},
					{"type":"Accordion","id":"stats[!Pos!]","percentWidth":100,"percentHeight":100,"stateGroup":"saved",
					"components":[
						{"type":"VBox","minHeight":1, "percentWidth":100, "percentHeight":100,"label":"Appels","localProxy":1,"setStyle":{"verticalGap":0},
						"components":[
							{"type":"MenuTab","maxLines":1,
								"searchBox":1,"dataField":"filterField","filterFields":[],
								"menuItems":[
									{"children":[
//										{"label":"","icon":"iconNew","data":"new"},
										{"label":"","icon":"open","data":"open","needFocus":1}
//										,{"label":"","icon":"iconDelete","data":"delete","needFocus":1}
									]}
								],
								"actions":[
									{"type":"itemClick"
										, "actions":{
											"delete":{"action":"invoke", "method":"deleteFromSelection"}
										}
									}
								]
							},
							{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":100,"rowHeight":24,  
							"kobeyeClass":{"dirtyParent":1,"formModule":"Pink","objectClass":"Call","keyName":"CallPkUserId","form":"FormDetail.json"},
							"events":[
								{"type":"start","action":"loadValues","params":{"needsParentId":1}},
								{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
								{"type":"proxy", "triggers":[
									{"trigger":"open","action":"invoke","method":"loadFormWithSelection"},
									{"trigger":"new","action":"invoke","method":"createForm"},
									{"trigger":"edit","action":"invoke","method":"loadFormWithID"},
									{"trigger":"delete","action":"invoke","method":"deleteWithID"}
								]}
							],
							"columns":[
								{"type":"column","dataField":"Id","headerText":"ID","visible":0},{"type":"column","dataField":"Initiales","headerText":"Initiales","width":150},{"type":"column","dataField":"Date","headerText":"Date","format":"date","width":60},{"type":"column","dataField":"Duration","headerText":"Duration","width":50},{"type":"column","dataField":"Units","headerText":"Units","width":50},{"type":"column","dataField":"ANumber","headerText":"A Number","width":150},{"type":"column","dataField":"ADuration","headerText":"A Duration","width":50},{"type":"column","dataField":"BNumber","headerText":"B Number","width":150},{"type":"column","dataField":"BDuration","headerText":"B Duration","width":50},{"type":"column","width":0}
							]}
						]},
						{"type":"VBox","minHeight":1, "percentWidth":100, "percentHeight":100,"label":"Messages","localProxy":1,"setStyle":{"verticalGap":0},
						"components":[
							{"type":"MenuTab","maxLines":1,
								"searchBox":1,"dataField":"filterField","filterFields":[],
								"menuItems":[
									{"children":[
//										{"label":"","icon":"iconNew","data":"new"},
										{"label":"","icon":"open","data":"open","needFocus":1}
//										,{"label":"","icon":"iconDelete","data":"delete","needFocus":1}
									]}
								],
								"actions":[
									{"type":"itemClick"
										, "actions":{
											"delete":{"action":"invoke", "method":"deleteFromSelection"}
										}
									}
								]
							},
							{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":100,"rowHeight":24,  
							"kobeyeClass":{"dirtyParent":1,"formModule":"Pink","objectClass":"Message","keyName":"MessagePkUserId","form":"FormDetail.json"},
							"events":[
								{"type":"start","action":"loadValues","params":{"needsParentId":1}},
								{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
								{"type":"proxy", "triggers":[
									{"trigger":"open","action":"invoke","method":"loadFormWithSelection"},
									{"trigger":"new","action":"invoke","method":"createForm"},
									{"trigger":"edit","action":"invoke","method":"loadFormWithID"},
									{"trigger":"delete","action":"invoke","method":"deleteWithID"}
								]}
							],
							"columns":[
								{"type":"column","dataField":"Id","headerText":"ID","visible":0},{"type":"column","dataField":"Initiales","headerText":"Initiales","width":150},{"type":"column","dataField":"Date","headerText":"Date","format":"date","width":60},{"type":"column","dataField":"Message","headerText":"Message","width":300},{"type":"column","width":0}
							]}
						]},
						{"type":"VBox","minHeight":1, "percentWidth":100, "percentHeight":100,"label":"Votes","localProxy":1,"setStyle":{"verticalGap":0},
						"components":[
							{"type":"MenuTab","maxLines":1,
								"searchBox":1,"dataField":"filterField","filterFields":[],
								"menuItems":[
									{"children":[
//										{"label":"","icon":"iconNew","data":"new"},
										{"label":"","icon":"open","data":"open","needFocus":1}
//										,{"label":"","icon":"iconDelete","data":"delete","needFocus":1}
									]}
								],
								"actions":[
									{"type":"itemClick"
										, "actions":{
											"delete":{"action":"invoke", "method":"deleteFromSelection"}
										}
									}
								]
							},
							{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":100,"rowHeight":24,  
							"kobeyeClass":{"dirtyParent":1,"formModule":"Pink","objectClass":"Vote","keyName":"VotePkUserId","form":"FormDetail.json"},
							"events":[
								{"type":"start","action":"loadValues","params":{"needsParentId":1}},
								{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
								{"type":"proxy", "triggers":[
									{"trigger":"open","action":"invoke","method":"loadFormWithSelection"},
									{"trigger":"new","action":"invoke","method":"createForm"},
									{"trigger":"edit","action":"invoke","method":"loadFormWithID"},
									{"trigger":"delete","action":"invoke","method":"deleteWithID"}
								]}
							],
							"columns":[
								{"type":"column","dataField":"Id","headerText":"ID","visible":0},{"type":"column","dataField":"Initiales","headerText":"Initiales","width":150},{"type":"column","dataField":"Date","headerText":"Date","format":"date","width":60},{"type":"column","dataField":"Message","headerText":"Message","width":300},{"type":"column","dataField":"Score","headerText":"Score","width":50},{"type":"column","width":0}
							]}
						]},
						{"type":"VBox","minHeight":1, "percentWidth":100, "percentHeight":100,"label":"Payements","localProxy":1,"setStyle":{"verticalGap":0},
						"components":[
							{"type":"MenuTab","maxLines":1,
								"searchBox":1,"dataField":"filterField","filterFields":[],
								"menuItems":[
									{"children":[
//										{"label":"","icon":"iconNew","data":"new"},
										{"label":"","icon":"open","data":"open","needFocus":1}
//										,{"label":"","icon":"iconDelete","data":"delete","needFocus":1}
									]}
								],
								"actions":[
									{"type":"itemClick",
										"actions":{
											"delete":{"action":"invoke", "method":"deleteFromSelection"}
										}
									}
								]
							},
							{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":100,"rowHeight":24,  
							"kobeyeClass":{"dirtyParent":1,"formModule":"Pink","objectClass":"Payment","keyName":"MessagePkUserId","form":"FormDetail.json"},
							"events":[
								{"type":"start","action":"loadValues","params":{"needsParentId":1}},
								{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
								{"type":"proxy", "triggers":[
									{"trigger":"open","action":"invoke","method":"loadFormWithSelection"},
									{"trigger":"new","action":"invoke","method":"createForm"},
									{"trigger":"edit","action":"invoke","method":"loadFormWithID"},
									{"trigger":"delete","action":"invoke","method":"deleteWithID"}
								]}
							],
							"columns":[
								{"type":"column","dataField":"Id","headerText":"ID","visible":0},{"type":"column","dataField":"Nom","headerText":"Nom","width":150},{"type":"column","dataField":"Prenom","headerText":"Prenom","width":150},{"type":"column","dataField":"Initiales","headerText":"Initiales","width":150},{"type":"column","dataField":"Date","headerText":"Date","format":"date","width":60},{"type":"column","dataField":"Amount","headerText":"Amount","width":80},{"type":"column","dataField":"Transaction","headerText":"Transaction","width":150},{"type":"column","dataField":"Units","headerText":"Units","width":50},{"type":"column","width":0}
							]}
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"start", "action":"invoke","method":"callMethod",
			"params":{"method":"object","data":{"dirtyChild":1,"module":"Pink","objectClass":"PkUser"},
			"function":"GetPkUser"}},
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


