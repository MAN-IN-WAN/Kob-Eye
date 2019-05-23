[STORPROC Formation/Projet/[!Formation!]|F|0|1][/STORPROC]
[STORPROC Formation/Region/[!Region!]|R|0|1][/STORPROC]

[OBJ Formation|Session|S]
[METHOD S|Set]
    [PARAM]Nom[/PARAM]
    [PARAM][!F::Nom!][/PARAM]
[/METHOD]
[METHOD S|Set]
    [PARAM]Date[/PARAM]
    [PARAM][!Date!][/PARAM]
[/METHOD]
[METHOD S|Set]
    [PARAM]Titre[/PARAM]
    [PARAM][!Titre!][/PARAM]
[/METHOD]
[METHOD S|addParent]
    [PARAM][!F!][/PARAM]
[/METHOD]
[METHOD S|addParent]
    [PARAM][!R!][/PARAM]
[/METHOD]

[IF [!S::Verify()!]&&[!Error!]=]
        [METHOD S|Save][/METHOD]
        {
            "success": true,
            "id": [!S::Id!]
        }
[ELSE]
{
    "success": false,
    "errors": 'Veuillez vérifier votre saisie.'
}
[/IF]
