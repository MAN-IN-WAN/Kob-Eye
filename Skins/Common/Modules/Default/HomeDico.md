[INFO [!Query!]|I]
[STORPROC [!Systeme::Modules!]|mo]
	[IF [!Key!]=[!I::Module!]]
		[!mm:=[!mo!]!]
	[/IF]
[/STORPROC]
[!db:=[!mm::Db!]!]
[!dc:=[!db::Dico()!]!]
{"form":
{"type":"VBox","id":"FL:[!I::Module!]/[!I::TypeChild!]","label":"[!O::getDescription()!]","percentWidth":100,"percentHeight":100, 
"setStyle":{"paddingTop":0,"paddingBottom":0,"paddingLeft":0,"paddingRight":0,"verticalGap":0},"localProxy":1, "minHeight":1,"verticalScrollPolicy":"off",
"components":[
	{"type":"TileGroup","percentWidth":100, "percentHeight":100,"setStyle":{"paddingTop":10,"paddingBottom":10,"paddingLeft":10,"paddingRight":10},
	"components":[
		//[STORPROC [!Systeme::CurrentMenu::Menus!]|M|0|100|Ordre|ASC]
		[STORPROC [!dc!]/hidden=|di]
			[IF [!Pos!]>1],[/IF]
			{"type":"MenuItem","params":{"title":"[IF [!di::Description!]][!di::Description!][ELSE][!di::title!][/IF]","icon":"[!di::Icon!]","width":150},
			"events":[
				{"type":"click", "action":"loadForm", "params":{"kobeyeClass":{"form":"[!mm::Nom!]/[!di::titre!]/FormDico.json"}}}
			]}
		[/STORPROC]
	]}
]}
}
