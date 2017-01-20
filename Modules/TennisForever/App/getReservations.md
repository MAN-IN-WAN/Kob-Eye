{
    "success": true,
    "query": "TennisForever/Client/[!CurrentClient::Id!]/Reservation",
    "data": [
        [STORPROC TennisForever/Client/[!CurrentClient::Id!]/Reservation|C|0|100]
            [IF [!Pos!]>1],[/IF]
            [MODULE TennisForever/App/jsonReservation?C=[!C!]]
        [/STORPROC]
    ]
}
