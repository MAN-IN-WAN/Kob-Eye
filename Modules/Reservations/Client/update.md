[!CurrentClient:=[!Module::Reservations::getCurrentClient()!]!]
[IF [!CurrentClient!]]
    [!CurrentClient::Civilite:=[!Civilite!]!]
    [!CurrentClient::Nom:=[!Nom!]!]
    [!CurrentClient::Prenom:=[!Prenom!]!]
    [IF [!Pass!]]
        [!CurrentClient::Pass:=[!Pass!]!]
    [/IF]
    //[!CurrentClient::Email:=[!Email!]!]
    [!CurrentClient::Tel:=[!Tel!]!]
    [!CurrentClient::Adresse:=[!Adresse!]!]
    [!CurrentClient::CodePostal:=[!CodePostal!]!]
    [!CurrentClient::Ville:=[!Ville!]!]

    [IF [!CurrentClient::Verify(1)!]]
        [METHOD CurrentClient|Save][/METHOD]
        {
            "success": true,
            "msg": "Profil enregistré avec succès",
            "user_id": [!Sys::User::Id!],
            "logtoken": "[!Module::Systeme::getToken()!]",
            "name": "[!CurrentClient::Civilite!] [!CurrentClient::Prenom!] [!CurrentClient::Nom!]",
            "Nom": "[JSON][!CurrentClient::Nom!][/JSON]",
            "Prenom": "[JSON][!CurrentClient::Prenom!][/JSON]",
            "Civilite": "[JSON][!CurrentClient::Civilite!][/JSON]",
            "Mail": '[JSON][!CurrentClient::Mail!][/JSON]',
            "Tel": '[JSON][!CurrentClient::Tel!][/JSON]',
            "Adresse": '[JSON][!CurrentClient::Adresse!][/JSON]',
            "CodePostal": '[JSON][!CurrentClient::CodePostal!][/JSON]',
            "Ville": '[JSON][!CurrentClient::Ville!][/JSON]'
        }
    [ELSE]
            {
                "success": false,
                "msg": "Une erreur est survenue: [STORPROC [!CurrentClient::Error!]|E][IF [!Pos!]>1],[/IF][!E::Message!][/STORPROC]"
            }
    [/IF]
[ELSE]
    {
        "success": false,
        "msg": "Vous n'êtes pas connecté"
    }
[/IF]