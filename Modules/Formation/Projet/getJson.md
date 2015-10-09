[STORPROC [!Query!]|P|0|1]
    [IF [!P::checkUpdate()!]]
        [!P::getJson()!]
    [ELSE]
        {
            "success": true
        }
    [/IF]
[/STORPROC]