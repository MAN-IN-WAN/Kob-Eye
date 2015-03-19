[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1][!QueryData:=[!H::Query!]!][/STORPROC]
[COUNT [!I::Historique!]|Hi]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
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
[IF [!chkbox!]]
	[!mnuItm+=,{"type":"vseparator"},{"label":"$__Check selection__$","data":"checkSel"},{"label":"$__Uncheck selection__$","data":"uncheckSel"},{"label":"$__Uncheck all__$","data":"uncheckAll"}!]
	[!gridTrig:=,{"trigger":"checkSel","action":"invoke","method":"checkSelected"},{"trigger":"uncheckSel","action":"invoke","method":"uncheckSelected"},{"trigger":"uncheckAll","action":"invoke","method":"uncheckAll"}!]
[/IF]
{"form":
{"type":"MDIWindow","id":"FL:[!I::Module!]/[!I::TypeChild!]","title":"$__Alert__$" ,"label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"paddingTop":0,"paddingLeft":5,"paddingRight":5},"localProxy":1, 
"components":[
	{"type":"MenuTab", "id":"menuList",
		"menuItems":[
			{"label":"$__File__$","children":[
				//{"label":"$__Open__$", "icon":"open", "data":"open"},
				//{"label":"$__New__$", "icon":"iconNew", "data":"new"},
				//{"label":"$__Delete__$", "icon":"remove", "data":"delete"},
				//{"type":"vseparator"},
				{"label":"$__Refresh__$", "icon":"refresh", "data":"refresh"}
				[!mnuItm!]
			]}
		]//,
		//"actions":[
		//	{"type":"itemClick", "actions":{
		//			"delete":{"action":"invoke", "method":"deleteFromSelection"}
		//			[!mnuAct!]
		//		}
		//	}
		//]
	},
	{"type":"HBox","id":"listBox","label":"$__List__$","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0},
	"components":[
// formulaire de recherche
		//{"type":"EditContainer", "id":"searchBox", "width":200, "percentHeight":100,
		//"components":[
		//	{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"paddingTop":5,"verticalGap":0},
		//	"components":[
		//		[!PP:=[!O::getSearchOrder()!]!]
		//		[!count:=0!]
		//		[STORPROC [!PP!]|P]
		//			[IF [!count!]>0],[/IF][!count+=1!]	
		//			[SWITCH [!P::Type!]|=]
		//				[CASE date]
		//					{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
		//						{"type":"DateInterval","dataField":"[!P::Nom!]","dataGroup":"searchGroup","percentWidth":100}
		//					]}
		//				[/CASE]
		//				[CASE boolean]
		//					{"type":"CheckBox3","label":"[!P::description!]","allow3StateForUser":1,"dataField":"[!P::Nom!]","dataGroup":"searchGroup"}
		//				[/CASE]
		//				[CASE image]
		//					{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
		//						{"type":"TextInput","dataField":"[!P::Nom!]","dataGroup":"searchGroup","percentWidth":100}
		//					]}
		//				[/CASE]
		//				[CASE price]
		//					{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
		//						{"type":"TextInput","dataField":"[!P::Nom!]","dataGroup":"searchGroup","width":80}
		//					]}
		//				[/CASE]
		//				[DEFAULT]
		//					{"type":"LabelItem","label":"[!P::description!]","labelPercent":100,"percentWidth":100,"layout":{"type":"VerticalLayout","gap":-4,"paddingTop":2},"components":[
		//						{"type":"TextInput","dataField":"[!P::Nom!]","dataGroup":"searchGroup","percentWidth":100,"filterMode":"[!P::filterMode!]"}
		//					]}
		//				[/DEFAULT]
		//			[/SWITCH]
		//		[/STORPROC]
		//		,{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
		//			{"type":"Spacer","percentWidth":100},
		//			{"type":"Button","label":"$__Clear__$","id":"clear","width":70}
		//		]}
		//	]}
		//],
		//"events":[
		//	{"type":"proxy", "triggers":[
		//		{"trigger":"clear","action":"invoke","method":"clearData"}
		//	]}
		//]},
// grille de donnees
		[!select:=Id!]
		[!columns:={"type":"column","dataField":"Id","headerText":"ID","visible":0}!]
		[!EL:=[!O::getElementsByAttribute(list,,1)!]!]
		[IF [!EL!]=][!EL:=[!O::getSearchOrder()!]!][/IF]
		[STORPROC [!EL!]|P]
			[ORDER list|ASC]
				[!select+=,[!P::name!]!]
				[!columns+=,{"type":"column","dataField":"[!P::name!]","headerText":!]
				[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::Nom!]"!][/IF][/IF]
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
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid" [!chkbox!],"percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1,
		"kobeyeClass":{"module":"[!I::Module!]","objectClass":"[!I::TypeChild!]","form":"FormBase.json","filters":"[!Systeme::CurrentMenu::Filters!]"
		[IF [!Hi!]>1]
			//Ajout d'un parent
			[STORPROC [!I::Historique!]|H|[!Hi:-2!]|1]
				,"parentClass":"[!H::DataSource!]","parentId":"[!H::Value!]"
			[/STORPROC]
		[/IF]
		},
		"events":[
			{"type":"start", "action":"loadValues"},
			{"type":"dblclick","action":"invoke","method":"loadFormWithID","params":{"containerID":"tabNav"}},
			{"type":"proxy", "triggers":[
				//{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				//{"trigger":"new","action":"invoke","method":"createForm","params":{"containerID":"tabNav"}},
				//{"trigger":"open","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
				//{"trigger":"clear","action":"invoke","method":"filterData"},
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
