{
    "success": true,
    "data": [
        [STORPROC TennisForever/Court/Web=1|C|0|100]
            [IF [!Pos!]>1],[/IF]{
                "Id":[!C::Id!],
                "Titre": "[!C::Titre!]",
                "TypeCourt": "[!C::TypeCourt!]"
            }
        [/STORPROC]
    ]
}
