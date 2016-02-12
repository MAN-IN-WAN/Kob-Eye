//gère l'enregistrement
[!Data:=[!JsonP::getInput()!]!]
[IF [!Data!]]
    [MODULE Systeme/Utils/saveData]
[ELSE]
    //resultat requete
    [MODULE Systeme/Utils/getJsonDatatable?USER_FILTER=1&ORDER_FILTER=1]
[/IF]
