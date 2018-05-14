[!DD:=[!TMS::Now!]!]
[!DF:=[!DD!]!]
[!DF+=1800!]


[BASH COLOR|blue] Debut : [!Utils::getDate(d/m/Y H:i:s,[!DD!])!][/BASH]
[BASH COLOR|yellow] Fin : [!Utils::getDate(d/m/Y H:i:s,[!DF!])!][/BASH]

[STORPROC Reservations/Reservation/DateDebut>[!DD!]&&DateDebut<[!DF!]|R]
    [!Facture:=[!R::getOneChild(Facture)!]!]
    [!MainPaie:=[!Facture::getOneChild(Paiement/PaiementFractionne=1)!]!]
    [IF [!MainPaie!]!=]
        [IF [!MainPaie::Etat!] = 5]
            [!MainPaie::executionDebitPartiel()!]
            [BASH COLOR|green] Reservation : [!R::Id!] - [!Utils::getDate(d/m/Y H:i:s,[!R::DateDebut!])!] - Paiement initié[/BASH]
        [ELSE]
            [BASH COLOR|red] Reservation : [!R::Id!] - [!Utils::getDate(d/m/Y H:i:s,[!R::DateDebut!])!] - Paiement déjà effectué ou en erreur[/BASH]
        [/IF]
    [ELSE]
        //Blabla
    [/IF]
[/STORPROC]