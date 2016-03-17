//gère l'enregistrement
[!Data:=[!JsonP::getInput()!]!]
[IF [!Data!]]
    [MODULE Systeme/Utils/saveData]
[ELSE]
    [IF [!Sys::User::Admin!]]
        //resultat requete
        [MODULE Systeme/Utils/getJsonDatatable?ORDER_FILTER=1]
    [ELSE]
        //resultat requete
        [MODULE Systeme/Utils/getJsonDatatable?USER_FILTER=1&ORDER_FILTER=1]
    [/IF]
[/IF]
