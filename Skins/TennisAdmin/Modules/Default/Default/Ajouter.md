[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|O|0|1][/STORPROC]
[ELSE]
    [OBJ [!I::Module!]|[!I::ObjectType!]|O]
[/IF]
<h1>Ajouter [!P::getDescription()!]</h1>
[MODULE Systeme/Utils/Form]