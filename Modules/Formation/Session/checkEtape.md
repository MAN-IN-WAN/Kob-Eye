//INPUT
        //session
        //question
        //equipe
[IF [!CurrentSession::checkSession([!session!])!]]
    [IF [!CurrentSession::checkSessionTeam([!equipe!],[!session!],)!]]
        [IF [!CurrentSession::checkEtape([!equipe!],[!session!],[!question!])!]]
            {
                success: true,
                team: true,
                data: true,
                etape: true
            }
        [ELSE]
        //data ok equipe ok mais pas l'etape => retour au début
            {
                success: false,
                team: true,
                etape: false,
                data: true,
                msg: 'L\'étape est erronée. Retour à la première question.'
            }
        [/IF]
    [ELSE]
    //data ok mais par l'equipe ni l'etape => retour au choix de l'equipe
    {
        success: false,
        team: false,
        etape: false,
        data: true,
        msg: 'L\'équipe n\'est pas reconnue. Retour au choix de l\équipe.'
    }
    [/IF]
[ELSE]
//ni data ni equipe ni etape => reset
{
        success: false,
        team: false,
        etape: fasle,
        data: false,
        msg: 'La session a changé et doit être réinitialisée.'
}
[/IF]
