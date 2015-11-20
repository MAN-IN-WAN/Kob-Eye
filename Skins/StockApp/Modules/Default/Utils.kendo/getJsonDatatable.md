[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Ob]
[!Ob::setView()!]
//CONFIGURATION
[!REQUETE:=[!Query!]!]
[!S:=[![!$orderby!]:/ !]!]
[!sSortDir_0:=[!S::1!]!]

[IF [!sSortDir_0!]=desc][!SORT:=DESC!][ELSE][!SORT:=ASC!][/IF]
[IF [!sSearch!]][!REQUETE+=/~[!sSearch!]!][/IF]

[COUNT [!REQUETE!]|NB]
[COUNT [!Query!]|NBTOTAL]
[!$callback!]({ 
  "d" : { 
    "results": [
    [STORPROC [!REQUETE!]|O|[!$skip!]|[!$top!]|[!S::0!]|[!SORT!]]
        [IF [!Pos!]>1],[/IF]
        {
            "Id":"[!O::Id!]",
            "title":'[!O::getFirstSearchOrder()!]',
            "url":'/[!Systeme::getMenu([!O::Module!]/[!O::ObjectType!])!]/[!O::Id!]'
            [STORPROC [!Ob::getElementsByAttribute(list,,1)!]|P]
                [NORESULT]
                    [STORPROC [!Ob::getElementsByAttribute(searchOrder,,1)!]|P]
                        ,"[!P::name!]":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
                    [/STORPROC]
                [/NORESULT]
                ,"[!P::name!]":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
            [/STORPROC]
        }
    [/STORPROC]
    ],
    "__count": "[!NB!]",
    "__sort":"[!S::0!]",
    "__sortway":"[!SORT!]"
  } 
})
