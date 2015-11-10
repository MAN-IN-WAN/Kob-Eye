{
    success: [IF [!Module::Systeme::isLogged()!]]true,
        user_id: [!Sys::User::Id!],
        logtoken: '[!Module::Systeme::getToken()!]',
        name: '[!Sys::User::Prenom!] [!Sys::User::Nom!]'
    [ELSE]false[/IF]
}