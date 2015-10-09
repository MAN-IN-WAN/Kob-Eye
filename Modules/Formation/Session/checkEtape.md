//INPUT
        //session
        //question
        //equipe
[IF [!CurrentSession::checkSessionTeam([!equipe!],[!session!])!]]
        [IF [!CurrentSession::checkEtape([!equipe!],[!session!],[!question!])!]]
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
        reset: true
}
[/IF]
