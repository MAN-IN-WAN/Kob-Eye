[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
[!AutoConnexion:=[!Mag::AutoClient!]!]
////////////////// Si déjà connecté on ne peut plus modifier son mail / idenfiant
[IF [!Systeme::User::Public!]=0]
    [!I_Pseudonyme:=[!Systeme::User::Mail!]!]
    [!I_Mail:=[!Systeme::User::Mail!]!]
[/IF]

///////////////// Deja connecté = Modification | Sinon = Création nouveau client
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1]
    [!ModeCreation:=0!]
    [NORESULT]
        [!ModeCreation:=1!]
        [OBJ Boutique|Client|Pers]
    [/NORESULT]
[/STORPROC]

///////////////// On remplit tous les champs
[STORPROC [!Pers::Proprietes!]|Prop]
[IF [!Prop::Nom!]!=UserId&&[!Prop::Nom!]!=ConnexionLe]
    [METHOD Pers|Set]
        [PARAM][!Prop::Nom!][/PARAM]
        [PARAM][![!Prop::Nom!]!][/PARAM]
    [/METHOD]
[/IF]
[/STORPROC]

///////////////// Mot de passe + Identifiant (uniquement en création)
[IF [!ModeCreation!]=1]
    [METHOD Pers|Set]
        [PARAM]Pseudonyme[/PARAM]
        [PARAM][!Mail!][/PARAM]
    [/METHOD]
    [METHOD Pers|Set]
        [PARAM]Pass[/PARAM]
        [PARAM][!Pass!][/PARAM]
    [/METHOD]
    [METHOD Pers|Set]
        [PARAM]Actif[/PARAM]
        [PARAM][!AutoConnexion!][/PARAM]
    [/METHOD]
[/IF]
[IF [!Pers::Verify(1)!]]
    [METHOD Pers|Save][PARAM]1[/PARAM][/METHOD]
    [IF [!ModeCreation!]=1&&[!AutoConnexion!]]
        [CONNEXION [!Mail!]|[!Pass!]]
        [!MSG:=Votre compte a été créé avec succès!]
    [/IF]
    {
        success: [IF [!Sys::User::Public!]]false[ELSE]true[/IF],
        [IF [!Sys::User::Public!]]
            msg: 'Identifiants incorrects. Vérifier votre nom d\'utilisateur et votre mot de passe',
        [ELSE]
            msg: "[JSON][!MSG!][/JSON]",
        [/IF]
        user_id: [!Sys::User::Id!],
        logtoken: '[!Module::Systeme::getToken()!]',
        name: '[!Sys::User::Civilité!] [!Sys::User::Prenom!] [!Sys::User::Nom!]',
        Nom: '[JSON][!Sys::User::Nom!][/JSON]',
        Prenom: '[JSON][!Sys::User::Prenom!][/JSON]',
        Civilite: '[JSON][!Sys::User::Civilité!][/JSON]',
        Mail: '[JSON][!Sys::User::Mail!][/JSON]',
        Tel: '[JSON][!Sys::User::Tel!][/JSON]',
        Adresse: '[JSON][!Sys::User::Adresse!][/JSON]',
        CodePostal: '[JSON][!Sys::User::CodPos!][/JSON]',
        Ville: '[JSON][!Sys::User::Ville!][/JSON]'
    }
[ELSE]
{
    "msg": "<b>Veuillez corriger les éléments suivants</b>: [STORPROC [!Pers::Error!]|E][JSON][!E::Message!]<br />[/JSON][!I_[!E::Prop!]_Error:=1!][NORESULT]aucune donnée reçue[/NORESULT][/STORPROC]",
    "success": false
}
[/IF]
