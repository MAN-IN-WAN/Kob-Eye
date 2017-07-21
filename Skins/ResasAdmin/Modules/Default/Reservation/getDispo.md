//CONVERSION DE LA DATE
[!DateTemp:=[!Utils::getDate(d/m/Y,[!date!])!]!]
[!DateDeb:=[!Utils::getTms([!DateTemp!] 00:00)!]!]
[!DateFin:=[!Utils::getTms([!DateTemp!] 23:59)!]!]

{
    "success": true,
    //"query": "Reservations/Court/*/Reservation/DateDebut>[!DateDeb!]&DateFin<[!DateFin!]",
    "data": [
        [STORPROC Reservations/Court/*/Reservation/DateDebut>[!DateDeb!]&DateFin<[!DateFin!]&Valide=1|R|0|1000|Id|DESC||Id]
        [!Flag:=1!]
        [IF [!Pos!]>1],[/IF]{
            "Id":[!R::Id!],
            "HeureDebut": "[DATE H][!R::DateDebut!][/DATE]",
            "HeureFin": "[DATE H][!R::DateFin!][/DATE]",
            "Court": [!R::CourtId!],
            "MinuteDebut": "[DATE i][!R::DateDebut!][/DATE]",
            "MinuteFin": "[DATE i][!R::DateFin!][/DATE]",
            "Service": "[!R::Service!]",
            "Client": "[!R::Nom!] [!R::Prenom!]",
            "Dispo": "0",
            "Type": "Reservation"
        }
        [/STORPROC]
        [OBJ Reservations|Disponibilite|D]
        [STORPROC [!D::getDispo([!DateDeb!],[!DateFin!])!]|R]
            [STORPROC [!R::_courts!]|C]
                [IF [!Flag!]],[/IF]{
                "Id":[!R::Id!],
                "HeureDebut": "[DATE H][!R::Debut!][/DATE]",
                "HeureFin": "[DATE H][!R::Fin!][/DATE]",
                "Court": [!C::Id!],
                "MinuteDebut": "[DATE i][!R::Debut!][/DATE]",
                "MinuteFin": "[DATE i][!R::Fin!][/DATE]",
                "Service": "[!R::Service!]",
                "Client": "[!R::Titre!]",
                "Dispo": "[!R::Dispo!]",
                "Type": "[IF [!R::RecurrenceHebdo!]]Recurrence[ELSE]Disponibilite[/IF]"
                }
                [!Flag:=1!]
            [/STORPROC]
        [/STORPROC]
    ]
}