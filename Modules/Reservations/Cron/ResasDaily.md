[!DD:=[!Utils::getTodayMorning()!]!]
[!DD+=86400!]
[!DF:=[!Utils::getTodayEvening()!]!]
[!DF+=86400!]


[BASH COLOR|blue] Debut : [!Utils::getDate(d/m/Y H:i:s,[!DD!])!][/BASH]
[BASH COLOR|yellow] Fin : [!Utils::getDate(d/m/Y H:i:s,[!DF!])!][/BASH]
[STORPROC Reservations/Reservation/DateDebut>=[!DD!]&&DateDebut<[!DF!]|R]
    //[!R::sendRappel()!]
    [BASH COLOR|green] Reservation : [!R::Id!] - [!Utils::getDate(d/m/Y H:i:s,[!R::DateDebut!])!][/BASH]
[/STORPROC]