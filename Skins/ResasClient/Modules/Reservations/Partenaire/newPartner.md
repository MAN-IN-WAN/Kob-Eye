
[OBJ Reservations|Partenaire|P]
[STORPROC Reservations/Client/UserId=[!Sys::User::Id!]|Cli][/STORPROC]
[!Res:=[!P::getPartenaire([!email!],[!nom!],[!prenom!],[!Cli!])!]!]



//[!DEBUG::Cli!]
//[!DEBUG::Res!]

[IF [!Res::Id!]]
{
    "success": true,
    "data": {
        "id": [!Res::Id!],
        "email": "[!Res::Email!]",
        "nom": "[!Res::Nom!]",
        "prenom": "[!Res::Prenom!]",
        "detail": "[!Res::Detail!]"
    }
}
[ELSE]
{
    "success": false,
    "data": {
        "errors": [
            [STORPROC [!Res::Error!]|V]
                [IF [!Pos!]>1],[/IF]
                "[!Utils::cleanJson([!V::Message!])!]"
            [/STORPROC]
        ]
    }
}
[/IF]