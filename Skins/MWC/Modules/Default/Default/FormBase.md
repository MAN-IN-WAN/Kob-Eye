[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!chldrn:=[!O::getChildTypes!]!]
[!firstField:=!]

[!mnuItm:=!][!mnuAct:=!][!chkbox:=!][!mnuLin:=1!]
[STORPROC [!O::getFunctions!]|mnu]
	[!acts:=[!mnu::actions!]!]
	[IF [!mnu::listOnly!]!=1]
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
[!intf:=[!O::getConfiguration!]!][!intf:=[!intf::Interface!]!][!intf:=[!intf::FormBase!]!]
[!class:=[!O::getObjectClass()!]!]
[IF [!class::color!]][!color:=,"fillColors":["[!class::color!]","0x999999"]!][/IF]

{"form":{"type":"GradientVBox","id":"[!I::TypeChild!]?","label":"[!O::getDescription()!]","percentHeight":100,
"setStyle":{"paddingTop":5,"paddingLeft":5,"paddingRight":5,"verticalGap":5[!color!]},"clipContent":0,"tabColor":"[!class::color!]",
"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]"},
"localProxy":{
	"actions":{
		"proxy_kobeye_status":{"action":[
			{"action":"invoke","method":"groupState","params":{"group":"saved","property":"enabled","hasID":1}},
			{"action":"invoke","method":"groupState","params":{"group":"idle","property":"enabled","idle":1}},
			{"action":"invoke","method":"groupState","params":{"group":"updated","property":"enabled","updated":1}},
			{"action":"invoke","method":"groupState","params":{"group":"savedIdle","property":"enabled","hasID":1,"idle":1}}
		]}
		[!mnuAct!]
	}
},
"components":[
	{"type":"MenuTab","id":"menuList","maxLines":[!mnuLin!],"menuItems":[
		{"children":[
			[IF [!intf::showNew!]==1]{"label":"$__New__$","icon":"new","data":"new"},[/IF]
			{"label":"$__Save__$","icon":"save","data":"save","stateGroup":"updated"},
			{"label":"$__Save & Close__$","icon":"save","data":"saveClose","stateGroup":"updated"},
			{"label":"$__Close__$","icon":"close","data":"close"},
			{"type":"vseparator"},
			{"label":"$__Cancel__$","icon":"refresh","data":"cancel","stateGroup":"updated"},
			{"label":"$__Delete__$","icon":"iconDelete","data":"delete","stateGroup":"saved"}
			[IF [!mnuItm!]]
				,{"label":"$__More__$","icon":"down","data":"more","children":[[!mnuItm!]]}
			[/IF]
			[STORPROC [!O::getChildElements!]/access=1|Enf]
				,{"label":"$__Add__$...","icon":"addlevel","data":"add","children":[
					[LIMIT 0|100]
					[IF [!Pos!]>1],[/IF]{"label":"$__Add__$ [!Enf::objectName!]","icon":"","data":"new[!Enf::objectName!]","stateGroup":"saved"}
					[/LIMIT]
				]}
			[/STORPROC]
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
							[MODULE Systeme/formElements?I=[!I!]]
						]}
					]}
					[STORPROC [!O::getInterfaces()!]|Int]
					,
					{"type":"Accordion","id":"stats[!Pos!]","percentWidth":100,"percentHeight":100,"stateGroup":"saved",
					"components":[
							[STORPROC [!Int!]|Form]
							[IF [!Pos!]>1],[/IF]{"type":"VBox", "id":"firstTab[!Key!]", "percentWidth":100, "percentHeight":100,"label":"[!Form::name!]","localProxy":1,"setStyle":{"verticalGap":0},"components":[
								[MODULE [!Form::module!]/[!Form::objectClass!]/[!Form::form!]?Int=[!Form!]&Enf=[!Form!]]
							]}
							[/STORPROC]
					]}
					[/STORPROC]
				]}



//						[OBJ [!I::Module!]|[!I::TypeChild!]|O]
//						[!cat:=0!]
//						[!OngletNum:=100!]
//						[STORPROC [!O::getChildElements!]|Enf]
//							[IF [!Enf::hidden!]!=1&&[!Enf::hideChild!]!=1&&[!Enf::position!]=accordion]
//								[IF [!OngletNum!]>100],[/IF]
//								[MODULE Systeme/formChildren?I=[!I!]&O=[!O!]&OngletNum=[!OngletNum!]&Enf=[!Enf!]&cat=[!cat!]]
//								[!OngletNum+=1!]
//							[/IF]
//						[/STORPROC]
//						{"type":"GradientVBox","label":"Others","styleName":"AccordionStyle","percentWidth":100,"percentHeight":100}
//					]}
//				]}
//				[OBJ [!I::Module!]|[!I::TypeChild!]|O]
//				[!cat:=0!]
//				[!OngletNum:=0!]
//				[STORPROC [!O::getChildElements!]|Enf]
//					[IF [!Enf::hidden!]!=1&&[!Enf::hideChild!]!=1&&[!Enf::position!]!=accordion]
//						[IF [!OngletNum!]=0]
//							,{"type":"TabNavigator", "id":"objectTabNav", "percentWidth":100, "percentHeight":70, "closePolicy":"close_never", "minTabWidth":"150",
//							"setStyle":{"paddingTop":1},"stateGroup":"saved",
//							"components":[
//						[/IF]
//						[IF [!OngletNum!]>0],[/IF]
//						[MODULE Systeme/formChildren?I=[!I!]&O=[!O!]&OngletNum=[!OngletNum!]&Enf=[!Enf!]&cat=[!cat!]]
//						[!OngletNum+=1!]
//					[/IF]
//				[/STORPROC]
//				[IF [!OngletNum!]>0]
//							]}
//				[/IF]
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
			[STORPROC [!O::getChildElements!]/access=1|Enf]
				{"trigger":"new[!Enf::objectName!]","action":"invoke","method":"createForm","params":{"containerID":"tabNav","kobeyeClass":{"objectClass":"[!Enf::objectName!]","form":"FormBase.json","module":"[!I::Module!]","dirtyParent":1}}},
			[/STORPROC]
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


