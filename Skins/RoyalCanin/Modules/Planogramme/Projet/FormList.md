[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|10][!QueryData:=[!H::Query!]!][/STORPROC]
[COUNT [!I::Historique!]|Hi]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
[!O::setView()!]
[!mnuItm:=!][!mnuAct:=!][!chkbox:=!]
[STORPROC [!O::getFunctions!]|mnu]
	[IF [!mnu::type!]=vseparator]
		[!mnuItm+=,{"type":"vseparator"}!]
	[ELSE]
		[!mnuItm+=,{"label":"[!mnu::label!]","icon":"[!mnu::icon!]","data":"[!mnu::name!]"}!]
		[!acts:=[!mnu::actions!]!]
		[IF [!acts::0!]][!mnuAct+=,"[!mnu::name!]":[!acts::0!]!][/IF]
		[IF [!mnu::multi!]][!chkbox:=,"checkBoxes":1!][/IF]
	[/IF]
[/STORPROC]
{"form":
{"type":"VBox","id":"FL:[!I::Module!]/[!I::TypeChild!]","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"paddingTop":0,"verticalGap":0},"localProxy":1, 
"components":[
	{"type":"MenuTab", "id":"menuList","maxLines":1,
		"menuItems":[
			{"label":"", "children":[
				{"label":"$__Open__$", "icon":"open", "data":"open","needFocus":1},
				{"label":"$__New__$", "icon":"iconNew", "data":"new"},
				{"label":"$__Delete__$", "icon":"remove", "data":"delete","needFocus":1},
				{"type":"vseparator"},
				{"label":"$__Refresh__$", "icon":"refresh", "data":"refresh"}
				[!mnuItm!]
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
					"delete":{"action":"invoke", "method":"deleteFromSelection","params":{"title":"Delete the project","message":"Do you confirm you want to delete ?"}}
					[!mnuAct!]
				}
			}
		]
	},
	{"type":"HBox","id":"listBox","label":"$__List__$","percentWidth":100,"percentHeight":100,"setStyle":{"paddingTop":0,"horizontalGap":0,"fontWeight":"bold"},
	"components":[
// formulaire de recherche
		{"type":"EditContainer", "id":"searchBox", "width":200, "percentHeight":100,
		"components":[
			{"type":"VBox","percentHeight":100,"percentWidth":100,"setStyle":{"verticalGap":0,"paddingLeft":0,"paddingRight":0},
			"components":[
					{"type":"Label","text":"SEARCH","percentWidth":100, "setStyle":{"color":"0xffffff","backgroundColor":"0x808080", "paddingTop":14, "paddingLeft":5,"fontWeight":"bold"}, "height":28},
					{"type":"Spacer","height":10},
					{"type":"Label","text":"By range"},						
						{"type":"ComboBox","dataField":"POS","dataGroup":"searchGroup","percentWidth":100,"dataProvider":[
							{"data":"","label":"All"},
							{"data":"1&VET=0","label":"POS"},
							{"data":"0&VET=1","label":"VET"},
							{"data":"1&VET=1","label":"POS&VET"}
						]},
						{"type":"Spacer","height":10},
					{"type":"Label","text":"By project name"},
						{"type":"TextInput","dataField":"Nom","dataGroup":"searchGroup","percentWidth":100},
						{"type":"Spacer","height":10},
					{"type":"Label","text":"By country"},
						{"type":"TextInput","dataField":"Country","dataGroup":"searchGroup","percentWidth":100},
						{"type":"Spacer","height":10},
					{"type":"Label","text":"By owner"},
						{"type":"TextInput","dataField":"AutorName","dataGroup":"searchGroup","percentWidth":100},
						{"type":"Spacer","height":10},
					{"type":"HBox","percentWidth":100,"setStyle":{"paddingTop":4},"components":[
						{"type":"Spacer","percentWidth":100},
						{"type":"Button","label":"$__Clear__$","id":"clear","width":70}
				]}
			]}
		],
		"events":[
			{"type":"proxy", "triggers":[
				{"trigger":"clear","action":"invoke","method":"clearData"}
			]}
		]},
// grille de donnees
		[!select:=Id!]
		[!columns:={"type":"column","dataField":"Id","headerText":"ID","visible":0}!]
		[!EL:=[!O::getElementsByAttribute(list,,1)!]!]
		[IF [!EL!]=]
			[!PP:=[!O::getSearchOrder()!]!]
//[!DEBUG::PP!]
			[STORPROC [!PP!]|P]
				[!select+=,[!P::Nom!]!]
				[!columns+=,{"type":"column","dataField":"[!P::Nom!]","headerText":!]
				[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::Nom!]"!][/IF][/IF]
				[SWITCH [!P::Type!]|=]
					[CASE date]
						[!cf:=date!][!cw:=60!]
					[/CASE]
					[CASE boolean]
						[!cf:=boolean!][!cw:=20!]
					[/CASE]
					[CASE image]
						[!cf:=image!][!cw:=60!]
					[/CASE]
					[CASE price]
						[!cf:=2dec!][!cw:=80!]
					[/CASE]
					[DEFAULT]
						[!cf:=!][!cw:=150!]
					[/DEFAULT]
				[/SWITCH]
				[!columns+=,"format":"[!cf!]"!]
				[IF [!P::listWidth!]][!columns+=,"width":[!P::listWidth!]!][ELSE][!columns+=,"width":[!cw!]!][/IF]
				[!columns+=}!]
			[/STORPROC]
		[ELSE]
//[!DEBUG::EL!]
			[STORPROC [!EL!]|CAT]
				[!PP:=[!CAT::elements!]!]
				[STORPROC [!PP!]|P]
					[!select+=,[!P::name!]!]
					[!columns+=,{"type":"column","dataField":"[!P::name!]","headerText":!]
					[IF [!P::listDescr!]][!columns+="[!P::listDescr!]"!][ELSE][IF [!P::description!]][!columns+="[!P::description!]"!][ELSE][!columns+="[!P::name!]"!][/IF][/IF]
					[SWITCH [!P::type!]|=]
						[CASE date]
							[!cf:=date!][!cw:=60!]
						[/CASE]
						[CASE boolean]
							[!cf:=boolean!][!cw:=20!]
						[/CASE]
						[CASE image]
							[!cf:=image!][!cw:=60!]
						[/CASE]
						[CASE price]
							[!cf:=2dec!][!cw:=80!]
						[/CASE]
						[DEFAULT]
							[!cf:=!][!cw:=150!]
						[/DEFAULT]
					[/SWITCH]
					[!columns+=,"format":"[!cf!]"!]
					[IF [!P::listWidth!]][!columns+=,"width":[!P::listWidth!]!][ELSE][!columns+=,"width":[!cw!]!][/IF]
					[!columns+=}!]
				[/STORPROC]
			[/STORPROC]
		[/IF]
		[!columns+=,{"type":"column","width":0}!]
		{"type":"AdvancedDataGrid","id":"dataGrid","dataField":"dataGrid" [!chkbox!],"percentHeight":100,"percentWidth":100,"rowHeight":20,"variableRowHeight":1, 
		"kobeyeClass":{"module":"Planogramme","objectClass":"Projet","filters":"userCreate=[!Systeme::User::Id!]","form":"Form3D.json"
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
				{"trigger":"searchGroup","action":"invoke","method":"filterData","params":{"group":"searchGroup"}},
				{"trigger":"new","action":"invoke","method":"createForm","params":{"containerID":"tabNav"}},
				{"trigger":"open","action":"invoke","method":"loadFormWithSelection","params":{"containerID":"tabNav"}},
				{"trigger":"clear","action":"invoke","method":"filterData"},
				
				{"trigger":"refresh", "action":"invoke", "method":"restart"}
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
