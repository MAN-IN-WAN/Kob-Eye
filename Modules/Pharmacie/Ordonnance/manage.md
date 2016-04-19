[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
        [STORPROC [!Query!]|O|0|1]
            [SWITCH [!Etat!]|=]
                [CASE Prepare]
                    [METHOD O|Set][PARAM]Etat[/PARAM][PARAM]3[/PARAM][/METHOD]
                    [METHOD O|Save][PARAM][/PARAM][/METHOD]
                [/CASE]
                [CASE Expedie]
                    [METHOD O|Set][PARAM]Etat[/PARAM][PARAM]4[/PARAM][/METHOD]
                    [METHOD O|Save][PARAM][/PARAM][/METHOD]
                [/CASE]
                [CASE Cloture]
                    [METHOD O|Set][PARAM]Etat[/PARAM][PARAM]6[/PARAM][/METHOD]
                    [METHOD O|Save][PARAM][/PARAM][/METHOD]
                [/CASE]
            [/SWITCH]
        {
        "success": true,
        "msg": "L'ordonnance a bien été modifiée"
        }
        [/STORPROC]
[ELSE]
{
        "success": false,
        "msg": "Url incorrecte"
}
[/IF]