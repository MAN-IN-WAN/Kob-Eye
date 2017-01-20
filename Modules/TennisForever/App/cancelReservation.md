[IF [!Id!]>0]
    [STORPROC TennisForever/Reservation/[!Id!]|R|0|1]
        [IF [!R::Valide!]&&[!R::getTotal()!]>0]
            {
                "success": false,
                "msg": "Impossible de supprimer la réservation. Veuillez contacter le responsable."
            }
        [ELSE]
            [!R::Delete()!]
            {
                "success": true,
                "msg": "La réservation a été supprimée avec succès."
            }
        [/IF]
        [NORESULT]
            {
                "success": false,
                "msg": "Réservation introuvable."
            }
        [/NORESULT]
    [/STORPROC]
[ELSE]
        {
        "success": false,
        "msg": "Problème technique, données incompletes."
        }
[/IF]