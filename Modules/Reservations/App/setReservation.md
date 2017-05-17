[STORPROC Reservations/TypeCourt/Court/[!Court!]|TC|0|1][/STORPROC]
[!Partenaire:=[!Utils::jsonDecode([!Partenaire!])!]!]
[!Service:=[!Utils::jsonDecode([!Service!])!]!]

//création de la réservation
[!RES:=[!Module::Reservations::createReservation([!Date!],[!Court!],[!Heure!]:[!Minute!],[!ServiceDuree!])!]!]
[SWITCH [!TC::GestionInvite!]|=]
    [CASE Quantitatif]
        [!RES::setNombrePartenaires([!NombreParticipant:-1!])!]
    [/CASE]
    [CASE Nominatif]
        [!RES::setPartenaires([!Partenaire!])!]
    [/CASE]
[/SWITCH]

[!RES::setProduits([!Service!])!]


[IF [!RES::Verify()!]]
    [COOKIE Set|RES|RES]
    [!RES::Save()!]
    {
        "success": true,
        "msg": 'Votre réservation a été enregistrée avec succès.',
        "data": [MODULE Reservations/App/jsonReservation?C=[!RES!]]
    }
[ELSE]
    //gestion erreurs
    [IF [!RES::Error!]]
        {
            "success": false,
            "msg": "[STORPROC [!RES::Error!]|E][!E::Message!][/STORPROC]"
        }
    [/IF]
[/IF]
