// Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Enregistrement de l'adresse
[OBJ Boutique|Adresse|Adr]
[IF [!I_Valid!]!=]
	// Propriétés
	[STORPROC [!Adr::Proprietes!]|Prop]
		[METHOD Adr|Set]
			[PARAM][!Prop::Nom!][/PARAM]
			[PARAM][!I_[!Prop::Nom!]!][/PARAM]
		[/METHOD]
	[/STORPROC]
	// Client
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli][/STORPROC]
	[METHOD Adr|AddParent][PARAM]Boutique/Client/[!Cli::Id!][/PARAM][/METHOD]
	// Enregistre
	[METHOD Adr|Save][/METHOD]
	// Redirection
	[REDIRECT][!Systeme::CurrentMenu::Url!]/Adresses?Type=[!Adr::Type!][/REDIRECT]
[/IF]

<h1>Ajout d'une adresse de [!Type!]</h1>
[MODULE Boutique/Adresse/Form?Type=[!Type!]]