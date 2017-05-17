{
    "success": true,
    "data": [
        {
        "Id":0,
        "Nom": "Sélectionnez un adhérent",
        "Prenom": ""
        }
    [!T:=0!]
    [STORPROC Reservations/Client/Abonne=1|S|0|300]
        ,{
            "Id":[!S::Id!],
            "Nom": "[!S::Nom!]",
            "Prenom": "[!S::Prenom!]"
        }
        [!T+=1!]
    [/STORPROC]
    ]
}
