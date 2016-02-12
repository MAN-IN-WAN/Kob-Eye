[!Panier:=[!CurrentClient::getPanier()!]!]
[OBJ Boutique|LigneCommande|Ob]
[!Ob::setView()!]

//gère l'enregistrement
{
    "total": 0,
    "results":[
        [STORPROC [!Panier::LignesCommandes!]|O|0|1000|tmsCreate|DESC]
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

