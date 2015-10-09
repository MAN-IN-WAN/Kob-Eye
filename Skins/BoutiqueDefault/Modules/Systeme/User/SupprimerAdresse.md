// Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Ce n'est pas une de mes adresses
[STORPROC Boutique/Client/Adresse/[!Id!]|Cli][/STORPROC]
[IF [!Systeme::User::Id!]!=[!Cli::UserId!]][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Suppression
[STORPROC Boutique/Adresse/[!Id!]|Adr]
	[NORESULT][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/NORESULT]
[/STORPROC]
[METHOD Adr|Delete][/METHOD]
[REDIRECT][!Systeme::CurrentMenu::Url!]/Adresses?Type=[!Adr::Type!][/REDIRECT]