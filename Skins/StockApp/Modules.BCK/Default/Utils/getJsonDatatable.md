[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Ob]
[!Ob::setView()!]

//CONFIGURATION
[!REQUETE:=[!Query!]!]
[IF [!sSortDir_0!]=desc][!SORT:=DESC!][ELSE][!SORT:=ASC!][/IF]
[IF [!sSearch!]][!REQUETE+=/~[!sSearch!]!][/IF]
[IF [!start!]]
        [!iDisplayStart:=[!start!]!]
[/IF]
[IF [!limit!]]
        //[!iDisplayLength:=[!limit!]!]
        [!iDisplayLength:=10!]
[/IF]

//FILTERS
[!FILTER:=[!Utils::jsonDecode([!filter!])!]!]
[!First:=1!]
[STORPROC [!FILTER!]|f]
    [!El:=[!Ob::getElement([!f::property!])!]!]
    [STORPROC [!El!]|Ele]
        [SWITCH [!Ele::type!]|=]
            [CASE fkey]
                [IF [!First!]][!REQUETE+=/!][!First:=!][ELSE][!REQUETE+=&!][/IF]
                [!REQUETE+=[!Ele::objectName!].[!f::property!]([!f::value!])!]
            [/CASE]
            [DEFAULT]
                [IF [!First!]][!REQUETE+=/!][!First:=!][ELSE][!REQUETE+=&!][/IF]
                [!REQUETE+=[!f::property!]=[!f::value!]!]
            [/DEFAULT]
        [/SWITCH]
        [NORESULT]
        [/NORESULT]
    [/STORPROC]
[/STORPROC]

{
    [COUNT [!REQUETE!]|NB]
    "total": [!NB!],
    "results":
[
    [STORPROC [!REQUETE!]|O|[!iDisplayStart!]|[!iDisplayLength!]|[!mDataProp_[!iSortCol_0!]!]|[!SORT!]]
            [IF [!Pos!]>1],[/IF]
            {
                    "id":"[!O::Id!]",
                    "label":"[!O::getFirstSearchOrder()!]"
                    [STORPROC [!Ob::getElementsByAttribute(type,,1)!]|P]
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
    
