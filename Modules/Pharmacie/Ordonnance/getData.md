//gère l'enregistrement
[!Data:=[!JsonP::getInput()!]!]
[IF [!Data!]]
    [MODULE Systeme/Utils/saveData]
[ELSE]
    //resultat requete
    [IF [!Sys::User::Admin!]]
        [MODULE Systeme/Utils/getJsonDatatable?SORT=DESC,DESC&SFIELD=Priorite,tmsEdit]
    [ELSE]
        [MODULE Systeme/Utils/getJsonDatatable?USER_FILTER=1]
    [/IF]
[/IF]
