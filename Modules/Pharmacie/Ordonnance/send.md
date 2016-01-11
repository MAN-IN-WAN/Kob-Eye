[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
        [STORPROC [!Query!]|O][/STORPROC]
[ELSE]
        [OBJ [!I::Module!]|[!I::ObjectType!]|O]
[/IF]
//mise à jour des informations
[METHOD O|Set][PARAM]Nom[/PARAM][PARAM][!CurrentClient::Nom!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Prenom[/PARAM][PARAM][!CurrentClient::Prenom!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Commentaire[/PARAM][PARAM][!Commentaire!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Telephone[/PARAM][PARAM][!CurrentClient::Tel!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Email[/PARAM][PARAM][!CurrentClient::Mail!][/PARAM][/METHOD]
[METHOD O|Set][PARAM]Image[/PARAM][PARAM][!Image!][/PARAM][/METHOD]
[METHOD O|Save][/METHOD]
{
    success: true
}