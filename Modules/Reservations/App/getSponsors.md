{
    "success": true,
    "data": [
        [STORPROC Reservations/Sponsor|S|0|100]
            [IF [!Pos!]>1],[/IF]{
            "Id":[!S::Id!],
            "Titre": "[!S::Titre!]",
            "Logo": "[!Domaine!]/[!S::Logo!]"
            }
        [/STORPROC]
    ]
}