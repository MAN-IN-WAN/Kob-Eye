//gère l'enregistrement
[!Data:=[!JsonP::getInput()!]!]
[IF [!Data!]]
    [MODULE Systeme/Utils/saveData]
[ELSE]
    //resultat requete
    [MODULE Systeme/Utils/getJsonDatatable]
[/IF]
