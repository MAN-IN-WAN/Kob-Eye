[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Ob]
[!Ob::setView()!]
//CONFIGURATION
[IF [!I::TypeSearch!]=Child]
    [!REQUETE:=[!Query!]!]
    [!PARENT:=#!]
[ELSE]
    [!PARENT:=[!I::LastId!]!]
    [!REQUETE:=[!Query!]/[!I::ObjectType!]!]
[/IF]
[IF [!sSortDir_0!]=desc][!SORT:=DESC!][ELSE][!SORT:=ASC!][/IF]
[IF [!sSearch!]][!REQUETE+=/~[!sSearch!]!][/IF]

[COUNT [!REQUETE!]|NB]
[COUNT [!Query!]|NBTOTAL]
[
[STORPROC [!REQUETE!]|O|[!iDisplayStart!]|[!iDisplayLength!]|[!mDataProp_[!iSortCol_0!]!]|[!SORT!]]
    [IF [!Pos!]>1],[/IF]
    {
        "id":"[!O::Id!]",
        "parent": "[!PARENT!]",
        "type": "[!O::ObjectType!]",
        "module": "[!O::Module!]",
        [STORPROC [!Ob::getElementsByAttribute(list,,1)!]|P|0|1]
            [NORESULT]
                [STORPROC [!Ob::getElementsByAttribute(searchOrder,,1)!]|P|0|1]
                    "text":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]],
                [/STORPROC]
            [/NORESULT]
            ,
            "text":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
        [/STORPROC]
        "icon": "icon-book",
        "children": [IF [!O::isTail()!]]false[ELSE]true[/IF],
        "state": {
            "opened"    : 0,
            "disabled"  : 0,
            "selected"  : 0
        }
//        ,
//        "li_attr": {},                              //Attributs pour la balise li 
//        "a_attr": {}                              //Attributs pour la balise a 
    }
[/STORPROC]
]