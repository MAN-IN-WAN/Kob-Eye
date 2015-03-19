[!OngletNum+=1!]
[OBJ [!Enf::module!]|[!Enf::objectClass!]|E]
[!E::setView()!]
[IF [!E::Interface!]]
	[!container:=!]
	[!interface:=[!E::Interface!].json!]
[ELSE]
	[!container:="containerID":"tabNav"!]
	[IF [!E::Interface!]][!interface:=[!E::Interface!].json!][ELSE][!interface:=FormBase.json!][/IF]
[/IF]
[!doc:=[!Enf::document!]!]
[!grd:=[!enf::childrenGrid!]!]
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
{"type":"VBox","minHeight":1, "id":"firstTab[!OngletNum!]", "percentWidth":100, "percentHeight":100,"label":"[IF [!Enf::childdescription!]][!Enf::childdescription!][ELSE][!Enf::objectName!][/IF]","localProxy":1,"setStyle":{"verticalGap":0},
"components":[
	{"type":"HBox","percentWidth":100,"setStyle":{"gap":1,"paddingLeft":4,"paddingTop":4,"paddingBottom":4,"backgroundColor":"#d9d9d9"},
		"components":[
			[IF [!doc!]]
				{"type":"ImageButton","id":"edit:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1},
				{"type":"ImageButton","id":"new:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1,
				"kobeyeClass":{"dirtyParent":1,"module":"[!Enf::objectModule!]","objectClass":"[!Enf::objectClass!]","keyName":"[!Enf::name!]"},
				"events":[
					{"type":"click","action":"invoke","method":"selectFile","params":{"property":"[!doc!]"}}
//					{"type":"click","action":"invoke","method":"selectFile","params":{"dataComponentID":"DG:[!OngletNum!]","property":"[!doc!]"}}
				]},
				{"type":"ImageButton","id":"delete:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1}
			[ELSE]
				[IF [!grd!]]
					{"type":"ImageButton","id":"edit:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1}
				[ELSE]
					{"type":"ImageButton","id":"edit:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_i","borderWidth":1},
					{"type":"ImageButton","id":"new:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_plus","borderWidth":1},
					{"type":"ImageButton","id":"delete:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_moins","borderWidth":1}
					//{"type":"ImageButton","id":"up:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_up","borderWidth":1},
					//{"type":"ImageButton","id":"down:[!OngletNum!]","width":16,"height":16,"cornerRadius":8,"image":"mwc_down","borderWidth":1}
				[/IF]
			[/IF]
		]
	},
//[!DEBUG::Enf!]
	{"type":"AdvancedDataGrid","id":"DG:[!OngletNum!]","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
//	"kobeyeClass":{"dirtyParent":1,"objectClass":"[!Enf::objectClass!]"[IF [!Enf::useKeyName!]],"keyName":"[!Enf::name!]"[/IF],"form":"[!interface!]"},
	"kobeyeClass":{"dirtyParent":1,"formModule":"[!Enf::module!]","objectClass":"[!Enf::objectClass!]","keyName":"[!Enf::keyName!]","form":"[!interface!]"},
	"events":[
		{"type":"start","action":"loadValues","params":{"needsParentId":1}},
		[IF [!doc!]]
			{"type":"dblclick","action":"invoke","method":"loadURL","params":{"url":"Doc"}},
		[ELSE]
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{[!container!]}},
		[/IF]
		{"type":"proxy", "triggers":[
			[IF [!doc!]]
				{"trigger":"edit:[!OngletNum!]","action":"invoke","method":"loadURL","params":{"url":"Doc"}},
			[ELSE]
				{"trigger":"new:[!OngletNum!]","action":"invoke","method":"createForm","params":{[!container!]}},
				{"trigger":"edit:[!OngletNum!]","action":"invoke","method":"loadFormWithID","params":{[!container!]}},
			[/IF]
			{"trigger":"delete:[!OngletNum!]","action":"invoke","method":"deleteWithID"}
		]}
	],
	"columns":[
		[!columns!]
	]}
]}
[!cat+=1!]