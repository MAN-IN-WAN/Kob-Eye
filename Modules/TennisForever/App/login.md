{
    success: [IF [!Sys::User::Public!]]false[ELSE]true[/IF],
    [IF [!Sys::User::Public!]]
        msg: 'Identifiants incorrects. Vérifier votre nom d\'utilisateur et votre mot de passe',
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
    Abonne: [!CurrentClient::Abonne!],
    CodePostal: '[JSON][!Sys::User::CodPos!][/JSON]',
    Ville: '[JSON][!Sys::User::Ville!][/JSON]'
}