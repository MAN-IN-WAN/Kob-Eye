[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
        [STORPROC [!Query!]|O][/STORPROC]
[ELSE]
        [OBJ [!I::Module!]|[!I::ObjectType!]|O]
[/IF]
//mise à jour des informations
[METHOD O|Set][PARAM]Commentaire[/PARAM][PARAM][!Commentaire!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Livraison[/PARAM][PARAM][!Livraison!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]SachetDose[/PARAM][PARAM][!SachetDose!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Image[/PARAM][PARAM][!Image!][/PARAM][/METHOD]
[METHOD O|Save][/METHOD]
{
    success: true
}