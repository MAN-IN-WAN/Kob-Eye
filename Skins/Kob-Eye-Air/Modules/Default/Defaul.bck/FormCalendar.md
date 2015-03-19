[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1][!QueryData:=[!H::Query!]!][/STORPROC]
[COUNT [!I::Historique!]|Hi]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!descr:=[!O::getDescription()!]!][IF [!descr!]!=][!descr:=[!I::TypeChild!]!][/IF]
[!O::setView()!]
[!mnuItm:=!][!mnuAct:=!][!chkbox:=!]
[STORPROC [!O::getFunctions!]|mnu]
	[IF [!mnu::formOnly!]!=1]
		[IF [!mnu::type!]=vseparator]
			[!mnuItm+=,{"type":"vseparator"}!]
		[ELSE]
			[!mnuItm+=,{"label":"[!mnu::label!]","icon":"[!mnu::icon!]","data":"[!mnu::name!]"}!]
			[!acts:=[!mnu::actions!]!]
			[IF [!acts::0!]][!mnuAct+=,"[!mnu::name!]":[!acts::0!]!][/IF]
			[IF [!mnu::multi!]][!chkbox:=,"checkBoxes":1,"allowMultipleSelection":1!][/IF]
		[/IF]
	[/IF]
[/STORPROC]
[!intf:=[!O::getConfiguration!]!][!intf:=[!intf::Interface!]!][!intf:=[!intf::FormCalendar!]!]
[IF [!intf::formBase!]][!formBase:=[!intf::formBase!]!][ELSE][!formBase:=FormBase!][/IF]
[IF [!chkbox!]]
	[!mnuItm+=,{"type":"vseparator"},{"label":"$__Check selection__$","data":"checkSel","icon":"select"},{"label":"$__Uncheck selection__$","data":"uncheckSel","icon":"unselect"},{"label":"$__Uncheck all__$","data":"uncheckAll","icon":"unselect"}!]
	[!gridTrig:=,{"trigger":"checkSel","action":"invoke","method":"checkSelected"},{"trigger":"uncheckSel","action":"invoke","method":"uncheckSelected"},{"trigger":"uncheckAll","action":"invoke","method":"uncheckAll"}!]
[/IF]
{"form":
{"type":"GradientVBox","id":"FL:[!I::Module!]/[!I::TypeChild!]","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"verticalGap":5,"paddingTop":5,"paddingLeft":5,"paddingRight":5},"localProxy":1, 
"components":[
	{"type":"MenuTab","maxLines":1,"id":"menuList",
	"menuItems":[
		{"children":[
			{"label":"$__Refresh__$","icon":"refresh", "data":"refresh"}
			[!mnuItm!]
		]}
	],
	"actions":[
		{"type":"itemClick", "actions":{
			[!mnuAct!]
		}}
	]},
	{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":0,"horizontalGap":5},
	"components":[
		{"type":"Calendar","id":"calendar","dataField":"calendar","percentHeight":100,"percentWidth":100,
		"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]","form":"[!formBase!].json","filters":"[!Systeme::CurrentMenu::Filters!]"
		[IF [!Hi!]>1]
			//Ajout d'un parent
			[STORPROC [!I::Historique!]|H|[!Hi:-2!]|1]
				,"parentClass":"[!H::DataSource!]","parentId":"[!H::Value!]"
			[/STORPROC]
		[/IF]
		},
		[IF [!intf::getDataFunction!]]"getDataFunction":[!intf::getDataFunction!],[/IF]
		"events":[
			{"type":"start", "action":"loadValues"},
			{"type":"click","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav","label":"[!descr!]"}},
			{"type":"proxy", "triggers":[
				{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"clear","action":"invoke","method":"filterData"},
				{"trigger":"refresh", "action":"invoke", "method":"restart"}
				[!gridTrig!]
			]}
		]}
	]}
]}
}
