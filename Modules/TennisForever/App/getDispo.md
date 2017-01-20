//CONVERSION DE LA DATE
[IF [!date!]>0][ELSE][!date:=[!TMS::Now!]!][/IF]

[!DateTemp:=[!Utils::getDate(d/m/Y,[!date!])!]!]
[!DateDeb:=[!Utils::getTms([!DateTemp!] 00:00)!]!]
[!DateFin:=[!Utils::getTms([!DateTemp!] 23:59)!]!]

{
    "success": true,
    "query": "TennisForever/Court/*/Reservation/DateDebut>[!DateDeb!]&DateFin<[!DateFin!]&Valide=1",
    "data": [
    [STORPROC TennisForever/Court/*/Reservation/DateDebut>[!DateDeb!]&DateFin<[!DateFin!]&Valide=1|R|0|1000|Id|DESC||Id]
        [!Flag:=1!]
        [IF [!Pos!]>1],[/IF]{
            "Id":[!R::Id!],
            "HeureDebut": "[DATE H][!R::DateDebut!][/DATE]",
            "HeureFin": "[DATE H][!R::DateFin!][/DATE]",
            "Court": [!R::CourtId!],
            "MinuteDebut": "[DATE i][!R::DateDebut!][/DATE]",
            "MinuteFin": "[DATE i][!R::DateFin!][/DATE]",
            "Service": "[!R::Service!]",
            "Nom": "[!R::ClientNom!] [!R::ClientPrenom!]"
        }
    [/STORPROC]
[STORPROC TennisForever/Disponibilite/Debut>[!DateDeb!]&Fin<[!DateFin!]|R|0|1000|Id|DESC||Id]
    [STORPROC TennisForever/Court/Disponibilite/[!R::Id!]|C]
        [IF [!Flag!]],[/IF]{
            "Id":[!R::Id!],
            "HeureDebut": "[DATE H][!R::Debut!][/DATE]",
            "HeureFin": "[DATE H][!R::Fin!][/DATE]",
            "Court": [!C::Id!],
            "MinuteDebut": "[DATE i][!R::Debut!][/DATE]",
            "MinuteFin": "[DATE i][!R::Fin!][/DATE]",
            "Service": "[!R::Service!]",
            "Nom": "NICO TENNIS"
        }
        [!Flag:=1!]
    [/STORPROC]
[/STORPROC]
    //Cas piscine
    [IF [!Flag!]],[/IF]{
        "Titre": "Piscine",
        "TypeCourt": "Piscine",
        "Service": "[!Module::TennisForever::getResaPiscine()!]"
    }
]
}