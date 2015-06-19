//INPUT
//qi_NUM
//session // numéro de session
//equipe  // numéro d'équipe
//reception des réponses

[IF [!CurrentSession::Id!]=[!session!]]
        //enregistrement des résultats
        [IF [!CurrentSession::saveResult([!equipe!])!]]
            {
                success: true
            }
        [ELSE]
            {
                success: false
            }
        [/IF]
[ELSE]
{
    success: false,
    reset: true,
    msg: 'impossible de trouver la session [!session!].'
}
[/IF]