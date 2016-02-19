{
    success: [IF [!Module::Systeme::isLogged()!]]true,
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
    [ELSE]false[/IF]
}