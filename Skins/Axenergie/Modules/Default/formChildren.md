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
	{"type":"HBox","percentWidth":100,
		"setStyle":{"horizontalGap":4,"borderStyle":"none","dropShadowEnabled":0,"backgroundColor":"#dedede","paddingTop":1,"paddingBottom":1,"paddingRight":4,"paddingLeft":4},
		"components":[
			{"type":"ImageButton","image":"iconNew","width":26,"height":26,"cornerRadius":13,"borderWidth":1,"id":"new:[!OngletNum!]"},
			{"type":"ImageButton","image":"open","width":26,"height":26,"cornerRadius":13,"borderWidth":1,"id":"edit:[!OngletNum!]"},
			{"type":"ImageButton","image":"iconDelete","width":26,"height":26,"cornerRadius":13,"borderWidth":1,"id":"delete:[!OngletNum!]"}
//			{"type":"Button","label":"$__New__$","id":"new:[!OngletNum!]","width":80},
//			{"type":"Button","label":"$__Edit__$","id":"edit:[!OngletNum!]","width":80},
//			{"type":"Button","label":"$__Delete__$","id":"delete:[!OngletNum!]","width":80}
		]
	},
	{"type":"AdvancedDataGrid","id":"DG:[!OngletNum!]","percentWidth":100,"percentHeight":100,"rowHeight":20,"variableRowHeight":1,
//	"kobeyeClass":{"dirtyParent":1,"objectClass":"[!Enf::objectName!]"[IF [!Enf::useKeyName!]],"keyName":"[!Enf::name!]"[/IF],"form":"[!interface!]"},
	"kobeyeClass":{"dirtyParent":1,"formModule":"[!Enf::objectModule!]","objectClass":"[!Enf::objectName!]","keyName":"[!Enf::name!]","form":"[!interface!]"},
	"events":[
		{"type":"start","action":"loadValues","params":{"needsParentId":1}},
		{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{[!container!]}},
		{"type":"proxy", "triggers":[
			{"trigger":"new:[!OngletNum!]","action":"invoke","method":"createForm","params":{[!container!]}},
			{"trigger":"edit:[!OngletNum!]","action":"invoke","method":"loadFormWithID","params":{[!container!]}},
			{"trigger":"delete:[!OngletNum!]","action":"invoke","method":"deleteWithID"}
		]}
	],
	"columns":[
		[!columns!]
	]}
]}
[!cat+=1!]