[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
        [STORPROC [!Query!]|O|0|1]
            [SWITCH [!Etat!]|=]
                [CASE Prepare]
                    [METHOD O|Set][PARAM]Prepare[/PARAM][PARAM]1[/PARAM][/METHOD]
                    [METHOD O|Save][PARAM][/PARAM][/METHOD]
                [/CASE]
                [CASE Expedie]
                    [METHOD O|Set][PARAM]Expedie[/PARAM][PARAM]1[/PARAM][/METHOD]
                    [METHOD O|Save][PARAM][/PARAM][/METHOD]
                [/CASE]
                [CASE Cloture]
                    [METHOD O|Set][PARAM]Cloture[/PARAM][PARAM]1[/PARAM][/METHOD]
                    [METHOD O|Save][PARAM][/PARAM][/METHOD]
                [/CASE]
            [/SWITCH]
        {
        "success": true,
        "msg": "La commande a bien été modifiée"
        }
        [/STORPROC]
[ELSE]
{
        "success": false,
        "msg": "Url incorrecte"
}
[/IF]