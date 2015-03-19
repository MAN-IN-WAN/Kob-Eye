[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Ob]
[!Ob::setView()!]
//CONFIGURATION
[!REQUETE:=[!Query!]!]
[IF [!sSortDir_0!]=desc][!SORT:=DESC!][ELSE][!SORT:=ASC!][/IF]
[IF [!sSearch!]][!REQUETE+=/~[!sSearch!]!][/IF]

[COUNT [!REQUETE!]|NB]
[COUNT [!Query!]|NBTOTAL]
{
	"sEcho": [!sEcho!],
	"iTotalRecords": "[!NBTOTAL!]",
	"iTotalDisplayRecords": "[!NB!]",
	"iQuery":"[!REQUETE!]",
	"aaData": [
		[STORPROC [!REQUETE!]|O|[!iDisplayStart!]|[!iDisplayLength!]|[!mDataProp_[!iSortCol_0!]!]|[!SORT!]]
			[IF [!Pos!]>1],[/IF]
			{
				"Id":"[!O::Id!]"
				[STORPROC [!Ob::getElementsByAttribute(list,,1)!]|P]
					,
					[SWITCH [!P::type!]|=]
						[CASE date]
							"[!P::name!]":"[DATE d/m/Y H:i:s][!O::[!P::name!]!][/DATE]"
						[/CASE]	
						[DEFAULT]
							"[!P::name!]":"[JSON][!O::[!P::name!]!][/JSON]"
						[/DEFAULT]
					[/SWITCH]	
				[/STORPROC]
			}
		[/STORPROC]
	]
}
