{
    success: [IF [!Sys::User::Public!]]false[ELSE]true[/IF],
    [IF [!Sys::User::Public!]]
        msg: 'Identifiants incorrects. Vérifier votre nom d\'utilisateur et votre mot de passe',
    [/IF]
    user_id: [!Sys::User::Id!],
    logtoken: '[!Module::Systeme::getToken()!]',
    name: '[!Sys::User::Prenom!] [!Sys::User::Nom!]'
}