[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1][!QueryData:=[!H::Query!]!][/STORPROC]
[COUNT [!I::Historique!]|Hi]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!descr:=[!O::getDescription()!]!][IF [!descr!]!=][!descr:=[!I::TypeChild!]!][/IF]
[!O::setView()!]
[!mnuItm:=!][!mnuAct:=!][!chkbox:=!]
[!funct:=[!O::getFunctions!]!]
[STORPROC [!funct!]|mnu]
	[IF [!mnu::hidden!]!=1&&[!mnu::formOnly!]!=1]
		[IF [!mnu::type!]=vseparator]
			[IF [!mnuItm!]][!mnuItm+=,{"type":"separator"}!][/IF]
		[ELSE]
			[IF [!mnu::interface!]]
			[ELSE]
				[IF [!mnuItm!]][!mnuItm+=,!][/IF]
				[!mnuItm+={"label":"[!mnu::label!]","icon":"[!mnu::icon!]","data":"[!mnu::name!]"}!]
				[!acts:=[!mnu::actions!]!]
				[IF [!acts::0!]][!mnuAct+=,"[!mnu::name!]":[!acts::0!]!][/IF]
				[IF [!mnu::multi!]][!chkbox:=,"checkBoxes":1,"allowMultipleSelection":1!][/IF]
			[/IF]
		[/IF]
	[/IF]
[/STORPROC]
[!class:=[!O::getObjectClass()!]!]
[IF [!class::color!]][!color:=,"fillColors":["[!class::color!]","0x999999"]!][/IF]
[!intf:=[!O::getConfiguration!]!][!intf:=[!intf::Interface!]!][!intf:=[!intf::FormList!]!]
[IF [!intf::formBase!]][!formBase:=[!intf::formBase!]!][ELSE][!formBase:=FormBase!][/IF]
[IF [!chkbox!]]
	[!mnuItm+=,{"type":"separator"},{"label":"$__Check selection__$","data":"checkSel","icon":"select"},{"label":"$__Uncheck selection__$","data":"uncheckSel","icon":"unselect"},{"label":"$__Uncheck all__$","data":"uncheckAll","icon":"unselect"}!]
	[!gridTrig:=,{"trigger":"checkSel","action":"invoke","method":"checkSelected"},{"trigger":"uncheckSel","action":"invoke","method":"uncheckSelected"},{"trigger":"uncheckAll","action":"invoke","method":"uncheckAll"}!]
[/IF]
{"form":
{"type":"GradientVBox","id":"FL:[!I::Module!]/[!I::TypeChild!]","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"verticalGap":5,"paddingTop":5,"paddingLeft":5,"paddingRight":5[!color!]},
"localProxy":1,
"components":[
	{"type":"MenuTab","maxLines":1,"id":"menuList","searchBox":1,"dataField":"filterField","id":"filterField","filterFields":[[STORPROC [!O::getSearchOrder()!]|P][IF [!Pos!]>1],[/IF]"[!P::Nom!]"[/STORPROC]],
		"menuItems":[
			{"children":[
				{"label":"$__Open__$","icon":"open","data":"open","needFocus":1},
				[IF [!intf::hideNew!]!=1]{"label":"$__New__$","icon":"iconNew","data":"new"[IF [!intf::hideNew!]=admin],"admin":1[/IF]},[/IF]
				[IF [!intf::hideDelete!]!=1]{"label":"$__Delete__$","icon":"iconDelete","data":"delete","needFocus":1[IF [!intf::hideDelete!]=admin],"admin":1[/IF]},[/IF]
				{"label":"$__Refresh__$","icon":"refresh", "data":"refresh"}
				[IF [!mnuItm!]]
					[COUNT [!funct!]|cnt]
					[IF [!cnt!]>1]
					,{"label":[IF [!intf::moreLabel!]]"[!intf::moreLabel!]"[ELSE]"$__More__$"[/IF],"icon":"down","data":"more","children":[[!mnuItm!]]}
					[ELSE]
					,[!mnuItm!]
					[/IF]
				[/IF]
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
					"delete":{"action":"invoke", "method":"deleteFromSelection"}
					[!mnuAct!]
				}
			}
		]
	},
	{"type":"HBox","id":"listBox","label":"$__List__$","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
// formulaire de recherche
		{"type":"EditContainer", "id":"searchBox", "width":180, "percentHeight":100,
		"components":[
			{"type":"VBox","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},"verticalScrollPolicy":"auto","minWidth":0,"minHeight":0,
			"components":[
				[STORPROC [!O::getCustomFilters()!]|F]
					[!NbCustomFilters:=[!NbResult!]!]
					[IF [!defFilter!]=][!defFilter:=[!F::filter!]!][/IF]
				{"type":"BadgeButton","label":"[!F::name!]","percentWidth":100,"badge":0,"height":30,"proxySet":"setFilter","proxyValue":"[!F::filter!]",[IF [!F::color!]]"badgeColor":"[!F::color!]",[/IF]
					"kobeyeClass":{
						"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]","filters":"[!F::filter!]","view":"[!F::view!]"
					},"events":[
						{"type":"start", "action":"loadData"}
					]
				},
				[/STORPROC]
				{"type":"CollapsiblePanel","dividerVisible":0,"titleHeight":0,"setStyle":{"backgroundColor":"#d9d9d9","color":"black"},"open":[IF [!NbCustomFilters!]>0]0[ELSE]1[/IF],"title":"$__Advanced search__$",
				"components":[
					{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":2,"paddingLeft":4,"paddingRight":4,"paddingBottom":4,"verticalGap":2},
					"components":[
						[!PP:=[!O::getSearchOrder()!]!]
						[!count:=0!]
						[STORPROC [!PP!]|P]
							[ORDER SearchOrder|ASC]
								[IF [!count!]>0],[/IF][!count+=1!]	
								[SWITCH [!P::Type!]|=]
									[CASE date]
										{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-3,"paddingTop":0},"components":[
											{"type":"DateInterval","dataField":"[!P::Nom!]","displayYear":0,"dataGroup":"searchGroup","percentWidth":100}
										]}
									[/CASE]
									[CASE boolean]
										{"type":"CheckBox3","label":"[!P::description!]","allow3StateForUser":1,"dataField":"[!P::Nom!]","dataGroup":"searchGroup"}
									[/CASE]
									[CASE image]
										{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-3,"paddingTop":0},"components":[
											{"type":"TextInput","dataField":"[!P::Nom!]","dataGroup":"searchGroup","percentWidth":100}
										]}
									[/CASE]
									[CASE price]
										{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-3,"paddingTop":0},"components":[
											{"type":"TextInput","dataField":"[!P::Nom!]","dataGroup":"searchGroup","width":80}
										]}
									[/CASE]
									[DEFAULT]
										{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-3,"paddingTop":0},"components":[
											[IF [!Utils::isArray([!P::Values!])!]]
//[!DEBUG::P::Values!]
												{"type":"ComboBox","dataField":"[!P::Nom!]","percentWidth":100,"dataGroup":"searchGroup","dataProvider":[
													[STORPROC [!P::Values!]|Val]
														//[LIMIT 0|100]
//[!DEBUG::P::Val!]
															[IF [!Pos!]>1],[/IF]
															[!T:=[![!Val!]:/::!]!]	
															[COUNT [!T!]|S]
															[IF [!S!]>1]
																{"data":"[!T::0!]","label":"[!T::1!]"}
															[ELSE]
																{"data":"[!Val!]","label":"[!Val!]"}
															[/IF]
														//[LIMIT]
														//[NORESULT]
														//[/NORESULT]
													[/STORPROC]
												]}
											[ELSE]
												[IF [!P::query!]]
													[INFO [!P::query!]|Q]
													{"type":"ComboBox","dataField":"[!P::Nom!]","percentWidth":100,"dataGroup":"searchGroup","filterMode":"[!P::filterMode!]",
													"kobeyeClass":{"module":"[!Q::Module!]","objectClass":"[!Q::TypeChild!]"[IF [!Q::Identifier!]],"identifier":"[!Q::Identifier!]"[/IF][IF [!Q::Label!]],"label":"[!Q::Label!]"[/IF]
													[IF [!P::masterField!]] 
														[IF [!P::masterObject!]],"parentClass":"[!P::masterObject!]"[/IF]},
														"masterField":"[!P::masterField!]"
													[ELSE]
														,"query":"[!Q::Query!]"},
														"actions":[
															{"type":"init","action":"loadData"}
														]
													[/IF]
													}
												[ELSE]
													{"type":"TextInput","dataField":"[!P::Nom!]","dataGroup":"searchGroup","percentWidth":100,"filterMode":"[!P::filterMode!]"}
												[/IF]
											[/IF]
										]}
									[/DEFAULT]
								[/SWITCH]
							[/ORDER]
						[/STORPROC]
						,{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
							//{"type":"Spacer","percentWidth":100},
							{"type":"Button","label":"$__Clear__$","id":"clear_search","width":80}
						]}
					]}
				]}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear_search","action":"invoke","method":"clearData"}
			]}
		]},
// grille de donnees
		[!select:=Id!]
		[!columns:={"type":"column","dataField":"Id","headerText":"ID","visible":0}!]
		[!EL:=[!O::getElementsByAttribute(list,,1)!]!]
		[IF [!EL!]=][!EL:=[!O::getSearchOrder()!]!][/IF]
		[STORPROC [!EL!]|P]
			[ORDER list|ASC]
				[!select+=,[!P::name!]!]
				[!columns+=,{"type":"column","dataField":"[!P::name!]","headerText":!]
				[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::name!]"!][/IF][/IF]
				[IF [!P::listExtra!]]
					[!cptext:=0!]
					[!columns+=,"extra":[ !]
					[!exts:=[![!P::listExtra!]:/,!]!]
					[STORPROC [!exts!]|ext]
						[IF [!cptext!]>0][!columns+=, !][/IF][!cptext+=1!]
						[!columns+="[!ext!]"!]
					[/STORPROC]
					[!columns+= ]!]
				[/IF]
				[SWITCH [!P::type!]|=]
					[CASE date]
						[!cf:=date!][!cw:=60!]
					[/CASE]
					[CASE time]
						[!cf:=time!][!cw:=95!]
					[/CASE]
					[CASE boolean]
						[!cf:=boolean!][!cw:=24!][!columns+=,"setStyle":{"paddingLeft":1,"paddingRight":1}!]
					[/CASE]
					[CASE image]
						[!cf:=image!][!cw:=60!]
					[/CASE]
					[CASE price]
						[!cf:=2dec!][!cw:=80!]
					[/CASE]
					[CASE pourcent]
						[!cf:=progress!][!cw:=80!]
					[/CASE]
					[CASE color]
						[!cf:=color!][!cw:=80!]
					[/CASE]
					[CASE float]
						[!cw:=80!]
					[/CASE]
					[CASE int]
						[!cf:=0dec!][!cw:=50!]
					[/CASE]
					[CASE text]
						[!cf:=!][!cw:=300!]
					[/CASE]
					[DEFAULT]
						[!cf:=!][!cw:=150!]
					[/DEFAULT]
				[/SWITCH]
				[IF [!P::format!]][!cf:=[!P::format!]!][/IF]
				[!columns+=,"format":"[!cf!]"!]
				[IF [!P::listWidth!]][!columns+=,"width":[!P::listWidth!]!][ELSE][!columns+=,"width":[!cw!]!][/IF]
				[!columns+=}!]
			[/ORDER]
		[/STORPROC]
		[!columns+=,{"type":"column","width":0}!]
		[!flt:=[!O::getFilters()!]!][IF [!flt!]=][!flt:=[!defFilter!]!][/IF]
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid" [!chkbox!],"percentHeight":100,"percentWidth":100,"rowHeight":24,"variableRowHeight":0,
		"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]","form":"[!formBase!].json","filters":"[!flt!]"
		[IF [!Hi!]>1]
			//Ajout d'un parent
			[STORPROC [!I::Historique!]|H|[!Hi:-2!]|1]
				,"parentClass":"[!H::DataSource!]","parentId":"[!H::Value!]"
			[/STORPROC]
		[/IF]
		},
		[IF [!intf::hierarchical!]]"hierarchical":[!intf::hierarchical!],[/IF]
		[IF [!intf::getDataFunction!]]"getDataFunction":[!intf::getDataFunction!],[/IF]
		"events":[
			{"type":"start", "action":"loadValues"},
			[IF [!formBase!]!=[None]]{"type":"dblclick","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"[!descr!]"}},[/IF]
			{"type":"proxy", "triggers":[
				{"trigger":"filterField","action":"invoke","method":"filterData","params":{"filter":"filterField"}},
				{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"new","action":"invoke","method":"createForm","params":{"containerID":"tabNav"}},
				{"trigger":"open","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"[!descr!]"}},
				{"trigger":"clear","action":"invoke","method":"filterData"},
				{"trigger":"setFilter","action":"invoke","method":"setFilter","params":{"args":"pv:setFilter"}},
//				{"trigger":"setFilter","action":"invoke","method":"setFilter","params":{"args":[{"proxyValue":["setFilter"]}]}},
				{"trigger":"refresh", "action":"invoke", "method":"restart"}
				[!gridTrig!]
			]}
//			,
//			{"type":"itemFocused", "action":"proxyEvent"}
		],
		"columns":[
			[!columns!]
		]}
	]}
]}
}
