[COUNT [!Systeme::User::Groups!]|C]
[STORPROC [!Systeme::User::Groups!]|G|[!C:-1!]|[!C!]][/STORPROC]
//[!FILTER:=gid=[!G::Id!]!]
{
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
"list":
[ELSE]
"data":
[/IF]
[MODULE Systeme/Interfaces/JsonCall?Chemin=[!Query!]&FILTER=[!FILTER!]]
}