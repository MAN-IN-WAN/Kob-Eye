[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|0|1][!QueryData:=[!H::Query!]!][/STORPROC]
[COUNT [!I::Historique!]|Hi]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
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
	[!mnuItm+=,{"type":"vseparator"},{"label":"$__Check selection__$","data":"checkSel","icon":"select"},{"label":"$__Uncheck selection__$","data":"uncheckSel","icon":"unselect"},{"label":"$__Uncheck all__$","data":"uncheckAll","icon":"unselect"}!]
	[!gridTrig:=,{"trigger":"checkSel","action":"invoke","method":"checkSelected"},{"trigger":"uncheckSel","action":"invoke","method":"uncheckSelected"},{"trigger":"uncheckAll","action":"invoke","method":"uncheckAll"}!]
[/IF]
{"form":
{"type":"VBox","id":"FL:[!I::Module!]/[!I::TypeChild!]","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"paddingRight":0,"verticalGap":0},"localProxy":1, "minHeight":1,"verticalScrollPolicy":"off",
"components":[
	{"type":"MenuTab","maxLines":1,"id":"menuList",
		"menuItems":[
			{"children":[
				{"label":"Réindexation des bases de donnée","icon":"iconNew","data":"sync"}
				[!mnuItm!]
			]}
		],
		"actions":[
			{"type":"itemClick", "actions":{
//					"sync":{"action":"invoke", "method":"callMethod","}
					[!mnuAct!]
				}
			}
		]
	},
//	{"type":"VBox","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100,
//	"setStyle":{"paddingTop":10,"paddingBottom":10,"paddingLeft":10,"paddingRight":10},"minHeight":1,
//	"components":[
		{"type":"TileGroup","percentWidth":100, "percentHeight":100,"setStyle":{"paddingTop":10,"paddingBottom":10,"paddingLeft":10,"paddingRight":10},
		"components":[
			[STORPROC [!Systeme::CurrentMenu::Menus!]|M|0|100|Ordre|ASC]
				[IF [!Pos!]>1],[/IF]
				{"type":"MenuItem","params":{"title":"[!M::Titre!]","icon":"/[!M::Icone!]","subtitle":"[!M::SousTitre!]","description":"[!M::Desciption!]","width":150}
				,	"events":[
						{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!Systeme::CurrentMenu::Url!]/[!M::Url!].json"},"containerID":"tabNav"}}
					]
				}
			[/STORPROC]
		]}
//	]}
]}
}
