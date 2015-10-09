[!toUrl:=Home/[!Systeme::User::Id!]!]
[IF [!Module!]!=]
[!toUrl+=/[!Module!]!]
[/IF]
[IF [!obj!]!=]
[!toUrl+=/[!obj!]!]
[/IF]
[OBJ Explorateur|_Fichier|File]
[METHOD File|Set]
[PARAM]Temp[/PARAM]
[PARAM]file_data[/PARAM]
[/METHOD]
[METHOD File|Set]
[PARAM]Url[/PARAM]
[PARAM][!toUrl!][/PARAM]
[/METHOD]
[METHOD File|Save][/METHOD]

//Enresgistrement de la donnée
[STORPROC [!Query!]|F]
        [!F::Fichier:=[!File::Url!]!]
        [!F::Save()!]
        {
        "url"   : "[!File::Url!]",
        "name"  : "[!File::Nom!]"
        }
[/STORPROC]
