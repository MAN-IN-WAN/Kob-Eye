{
    "success": true,
    "data": [
        [STORPROC TennisForever/Court|C|0|100]
            [IF [!Pos!]>1],[/IF]{
                "Id":[!C::Id!],
                "Titre": "[!C::Titre!]",
                "TypeCourt": "[!C::TypeCourt!]",
                "TypeCourtId": "[!C::TypeCourtId!]"
            }
        [/STORPROC]
    ]
}
