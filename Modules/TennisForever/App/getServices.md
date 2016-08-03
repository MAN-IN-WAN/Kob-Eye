{
    "success": true,
    "data": [
    [!T:=0!]
    [STORPROC [!Module::TennisForever::getServices()!]|S|0|300]
        [IF [!T!]>0],[/IF]
        {
            "Id":[!S::Id!],
            "Titre": "[!S::Titre!]",
            "Type": "[!S::Type!]",
            "Tarif": "[!S::Tarif!]",
            "TarifAbonnes": "[!S::TarifAbonnes!]",
            "TarifInvite": "[!S::TarifInvite!]",
            "TarifCreuse": "[!S::TarifCreuse!]",
            "Duree": "[!S::Duree!]",
            "SaisieQuantite": "[!S::SaisieQuantite!]",
            "TypeCourtId": "[!S::TypeCourtId!]",
            "CourtId": "[!S::CourtId!]"
        }
        [!T+=1!]
    [/STORPROC]
    ]
}
