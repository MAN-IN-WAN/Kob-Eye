[!OngletNum+=1!]
[OBJ [!O::Module!]|[!Enf::objectName!]|E]
[!E::setView()!]
[IF [!E::Interface!]=FormDetail]
	[!container:=!]
	[!interface:=FormDetail.json!]
[ELSE]
	[!container:="containerID":"tabNav"!]
	[IF [!E::Interface!]][!interface:=[!E::Interface!].json!][ELSE][!interface:=FormBase.json!][/IF]
[/IF]
[!conf:=[!E::getConfiguration!]!][!conf:=[!conf::Interface!]!][!conf:=[!conf::FormChildren!]!]
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


[IF [!cat!]>0],[/IF]
{"type":"VBox","minHeight":1, "percentWidth":100, "percentHeight":100,"label":"[IF [!Enf::childdescription!]][!Enf::childdescription!][ELSE][!Enf::objectName!][/IF]","localProxy":1,"setStyle":{"verticalGap":0},
"components":[
	{"type":"MenuTab","maxLines":1,
		[IF [!conf::hideSearch!]!=1]"searchBox":1,"dataField":"filterField","filterFields":[[STORPROC [!O::getSearchOrder()!]|P][IF [!Pos!]>1],[/IF]"[!P::Nom!]"[/STORPROC]],[/IF]
		"menuItems":[
			{"children":[
				[IF [!conf::hideNew!]!=1]{"label":"","icon":"iconNew","data":"new"},[/IF]
				{"label":"","icon":"open","data":"open","needFocus":1}
				[IF [!conf::hideDelete!]!=1],{"label":"","icon":"iconDelete","data":"delete","needFocus":1}[/IF]
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
//	{"type":"HBox","percentWidth":100,
//		"setStyle":{"horizontalGap":4,"borderStyle":"none","dropShadowEnabled":0,"backgroundColor":"#dedede","paddingTop":1,"paddingBottom":1,"paddingRight":4,"paddingLeft":4},
//		"components":[
//			{"type":"ImageButton","image":"iconNew","width":30,"height":30,"cornerRadius":15,"borderWidth":1},
//			{"type":"ImageButton","image":"open","width":30,"height":30,"cornerRadius":15,"borderWidth":1},
//			{"type":"ImageButton","image":"iconDelete","width":30,"height":30,"cornerRadius":15,"borderWidth":1}
//		]
//	},
	{"type":"AdvancedDataGrid","percentWidth":100,"percentHeight":100,"rowHeight":24,  //"variableRowHeight":1,
	"kobeyeClass":{"dirtyParent":1,"formModule":"[!Enf::objectModule!]","objectClass":"[!Enf::objectName!]","keyName":"[!Enf::name!]","form":"FormDetail.json"},
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
		[!columns!]
	]}
]}
[!cat+=1!]