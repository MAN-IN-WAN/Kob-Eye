[!Client:=[!Module::Reservations::getCurrentClient()!]!]
[!Partenaire:=[!Client::getOneParent(Partenaire)!]!]
//construction requete
[!REQ:=Reservations/Partenaire/~[!search!]&Id!=[!Partenaire::Id!]&Disponible=1!]
[STORPROC Reservations/Client/[!Client::Id!]/Partenaire|P|0|10000]
    [!REQ+=&Id!=[!P::Id!]!]
[/STORPROC]
{
"success":1,
"query": "[!REQ!]",
"data":[
[STORPROC [!REQ!]|P|0|100]
    {
      "Id":[!P::Id!],
      "FullName": "[!P::Nom!] [!P::Prenom!]",
      "Description": "[JSON][!P::Details!][/JSON]"
    }[IF [!Pos!]<[!NbResult!]],[/IF]
[/STORPROC]
]
}