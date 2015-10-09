//INPUT
        //equipeId
        //sessionId

[IF [!CurrentSession::checkSessionTeam([!equipeId!],[!sessionId!])!]]
    {
        success: true
    }
[ELSE]
    {
        success: false
    }
[/IF]