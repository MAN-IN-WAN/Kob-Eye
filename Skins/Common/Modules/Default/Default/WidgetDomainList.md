	{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0,"backgroundColor":"#cdcdcd"},
	"components":[
		[IF [!Chemin!]][ELSE][!Chemin:=[!Query!]!][/IF]
		[INFO [!Chemin!]|I]
		[OBJ [!I::Module!]|[!I::ObjectType!]|P]
		[OBJ [!Int::module!]|[!Int::objectClass!]|O]
		[STORPROC [!P::getChildElements!]|Enf]
			[IF [!Enf::objectName!]=[!Int::objectClass!]]
				[OBJ [!O::Module!]|[!Enf::objectName!]|E]
				[!E::setView()!]
				[IF [!E::Interface!]=FormDetail]
					[!container:=!]
					[!interface:=FormDetail.json!]
				[ELSE]
					[!container:="containerID":"tabNav"!]
					[IF [!E::Interface!]][!interface:=[!E::Interface!].json!][ELSE][!interface:=FormBase.json!][/IF]
				[/IF]
				[!select:=Id!]
				[!columns:={"type":"column","dataField":"Id","headerText":"ID","visible":0}!]
				[!EL:=[!E::getElementsByAttribute(list,,1)!]!]
				[IF [!EL!]=][!EL:=[!E::getSearchOrder()!]!][/IF]
				[STORPROC [!EL!]|P]
					[ORDER list|ASC]
						[!select+=,[!P::name!]!]
						[!columns+=,{"type":"column","dataField":"[!P::name!]","headerText":!]
						[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::name!]"!][/IF][/IF]
						[!cf:=!]
						[SWITCH [!P::type!]|=]
							[CASE date]
								[!cf:=date!][!cw:=60!]
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
							[CASE float]
								[!cw:=80!]
							[/CASE]
							[CASE pourcent]
								[!cf:=progress!][!cw:=80!]
							[/CASE]
							[CASE color]
								[!cf:=color!][!cw:=80!]
							[/CASE]
							[CASE int]
								[!cw:=50!]
							[/CASE]
							[CASE text]
								[!cw:=300!]
							[/CASE]
							[DEFAULT]
								[!cw:=150!]
							[/DEFAULT]
						[/SWITCH]
						[IF [!P::format!]][!cf:=[!P::format!]!][/IF]
						[IF [!cf!]][!columns+=,"format":"[!cf!]"!][/IF]
						[IF [!P::listWidth!]][!columns+=,"width":[!P::listWidth!]!][ELSE][!columns+=,"width":[!cw!]!][/IF]
						[!columns+=}!]
					[/ORDER]
				[/STORPROC]
				[!columns+=,{"type":"column","width":0}!]
				{"type":"MenuTab","maxLines":1,"searchBox":1,"dataField":"filterField","filterFields":[[STORPROC [!O::getSearchOrder()!]|P][IF [!Pos!]>1],[/IF]"[!P::Nom!]"[/STORPROC]],
					"menuItems":[
						{"children":[
							{"label":"","icon":"iconNew","data":"new"},
							{"label":"","icon":"open","data":"open","needFocus":1},
							{"label":"","icon":"iconDelete","data":"delete","needFocus":1}
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
				{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
				[IF [!Int::function!]]
				"getDataFunction":{
					"method":"object","data":{"module":"ProxyCas","objectClass":"ProxyHit"},
					"function":"[!Int::function!]","args":"v:0,v:0,v:[!Int::module!],v:[!Int::objectClass!],id:parentForm"
				},
				[/IF]
				"kobeyeClass":{"dirtyParent":1,"formModule":"[!Enf::objectModule!]","objectClass":"[!Enf::objectName!]","keyName":"[!Enf::name!]","form":"FormDetail.json"},
				"events":[
					{"type":"start","action":"loadValues","params":{"needsParentId":1}},
					{"type":"dblclick","action":"invoke","method":"loadFormWithID"},
					{"type":"proxy", "triggers":[
						{"trigger":"open","action":"invoke","method":"loadFormWithSelection"},
						{"trigger":"new","action":"invoke","method":"createForm"},
						{"trigger":"delete","action":"invoke","method":"deleteWithID"}
					]}
				],
				"columns":[
					{"type":"column","dataField":"domain","headerText":"Domaine","width":200},
					{"type":"column","dataField":"hits","headerText":"Hits","width":100},
					{"type":"column","dataField":"pourcent","headerText":"%","width":150,"format":"progress"}
				]}
			[/IF]
		[/STORPROC]
	]}
