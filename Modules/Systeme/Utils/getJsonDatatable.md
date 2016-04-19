[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Ob]
[!Ob::setView()!]

//CONFIGURATION
[!REQUETE:=[!Query!]!]
[IF [!SORT!]=]
    [IF [!sSortDir_0!]!=asc][!SORT:=DESC!][ELSE][!SORT:=ASC!][/IF]
[/IF]
[IF [!sSearch!]][!REQUETE+=/~[!sSearch!]!][/IF]
[IF [!start!]]
        [!iDisplayStart:=[!start!]!]
[/IF]

[IF [!limit!]]
        //[!iDisplayLength:=[!limit!]!]
        [!iDisplayLength:=10!]
[/IF]

[!SEARCH_TEST:=0!]
[IF [!USER_FILTER!]=1&&[!Sys::User::Admin!]=0]
    [!REQUETE+=/userCreate=[!Sys::User::Id!]!]
    [!SEARCH_TEST:=1!]
[/IF]

[IF [!PRODUCT_FILTER!]=1]
    [IF [!SEARCH_TEST!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][/IF]
    [!REQUETE+=Tarif>0&Actif=1&Display=1!]
    [!SEARCH_TEST:=1!]
[/IF]

[IF [!FILTER_COEUR!]=1]
    [IF [!SEARCH_TEST!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][/IF]
    [!REQUETE+=Coeur=1!]
    [!SEARCH_TEST:=1!]
[/IF]

[IF [!ORDER_FILTER!]=1]
    [IF [!SEARCH_TEST!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][/IF]
    [!REQUETE+=Valide=1!]
    [!SEARCH_TEST:=1!]
[/IF]

[IF [!ORDO_FILTER!]=1]
    [IF [!SEARCH_TEST!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][/IF]
    [!REQUETE+=Etat<4!]
    [!SEARCH_TEST:=1!]
[/IF]

[IF [!search!]]
    [IF [!SEARCH_TEST!]][!REQUETE+=&!][ELSE][!REQUETE+=/!][/IF]
    [!REQUETE+=~[!search!]!]
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
[IF [!SFIELD!]=]
    [IF [!mDataProp_[!iSortCol_0!]!]]
        [!SFIELD:=[!mDataProp_[!iSortCol_0!]!]!]
    [ELSE]
        [!SFIELD:=Id!]
    [/IF]
[/IF]

{
    [COUNT [!REQUETE!]|NB]
    "total": [!NB!],
    "req": '[!REQUETE!] |[!SFIELD!]|[!SORT!]',
    "results":
[
    [STORPROC [!REQUETE!]|O|[!iDisplayStart!]|[!iDisplayLength!]|[!SFIELD!]|[!SORT!]]
            [IF [!Pos!]>1],[/IF]
            {
                    "id":"[!O::Id!]",
                    "label":"[!O::getFirstSearchOrder()!]"
                    [STORPROC [!Ob::getElementsByAttribute(type,,1)!]|P]
                            [NORESULT]
                                    [STORPROC [!Ob::getElementsByAttribute(searchOrder,,1)!]|P]
                                            ,"[!P::name!]":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
                                    [/STORPROC]
                            [/NORESULT],"[!P::name!]":[MODULE Systeme/Utils/getDataType?P=[!P!]&O=[!O!]]
                    [/STORPROC]
            }
    [/STORPROC]
]
}    
    
