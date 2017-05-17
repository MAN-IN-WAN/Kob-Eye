{
    "success": true,
    "query": "Reservations/Client/[!CurrentClient::Id!]/Reservation",
    "data": [
        [STORPROC Reservations/Client/[!CurrentClient::Id!]/Reservation|C|0|100]
            [IF [!Pos!]>1],[/IF]
            [MODULE Reservations/App/jsonReservation?C=[!C!]]
        [/STORPROC]
    ]
}
