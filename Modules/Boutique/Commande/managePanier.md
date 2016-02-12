{
[SWITCH [!action!]|=]
        [CASE add]
            [IF [!CurrentClient::ajouterAuPanier([!ref!],[!qte!])!]]
                "success":true,
                "msg": "Le produit a été ajouté avec succès."
            [ELSE]
                "success":false,
                "msg": "ERREUR"
            [/IF]
        [/CASE]
        [CASE del]
            [IF [!CurrentClient::enleverDuPanier([!ref!])!]]
                "success":true,
                "msg: "Le produit a été supprimé avec succès."
            [ELSE]
                "success":false,
                "msg": "ERREUR"
            [/IF]
        [/CASE]
        [CASE empty]
            [IF [!CurrentClient::viderPanier()!]]
                "success":true,
                "msg": "Le panier a été vidé avec succès."
            [ELSE]
                "success":false,
                "msg": "ERREUR"
            [/IF]
        [/CASE]
        [CASE valid]
            [!Panier:=[!CurrentClient::getPanier()!]!]
            [!Panier::setValid()!]
            "success":true,
            "msg": "La commande a été soumise. Vous pouvez aller dans le menu commande afin de régler les détails."
        [/CASE]
        [DEFAULT]
            "success": false,
            "msg": "Problème de configuration veuillez contacter l\'administrateur."
        [/DEFAULT]
[/SWITCH]
}