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
					[NORESULT]
						[STORPROC [!Ob::getElementsByAttribute(searchOrder,,1)!]|P]
							,
							"[!P::name!]":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
						[/STORPROC]
					[/NORESULT]
					,
					"[!P::name!]":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
				[/STORPROC]
			}
		[/STORPROC]
	]
}
