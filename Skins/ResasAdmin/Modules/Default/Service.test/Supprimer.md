[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|O|0|1][/STORPROC]
[ELSE]
    [OBJ [!I::Module!]|[!I::ObjectType!]|O]
[/IF]
[METHOD O|Delete][/METHOD]
{
    "success": true,
    "message": "<div class=\"alert alert-success\">[!O::getFirstSearchOrder()!] a bien été supprimé</div>"
}
