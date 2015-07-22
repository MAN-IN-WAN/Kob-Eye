[IF [!CurrentSession::setTeam([!num!])!]]
        {
            success: true,
            currentquestion: [!CurrentSession::getCurrentQuestion([!num!])!]
        }
[ELSE]
        {
            success: false
        }
[/IF]