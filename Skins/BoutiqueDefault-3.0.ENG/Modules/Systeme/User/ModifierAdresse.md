// Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Ce n'est pas une de mes adresses
[STORPROC Boutique/Client/Adresse/[!Id!]|Cli][/STORPROC]
[IF [!Systeme::User::Id!]!=[!Cli::UserId!]][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Enregistrement de l'adresse
[STORPROC Boutique/Adresse/[!Id!]|Adr]
	[NORESULT][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/NORESULT]
[/STORPROC]
[IF [!I_Valid!]!=]
	// Proprietes
	[STORPROC [!Adr::Proprietes!]|Prop]
		[METHOD Adr|Set]
			[PARAM][!Prop::Nom!][/PARAM]
			[PARAM][!I_[!Prop::Nom!]!][/PARAM]
		[/METHOD]
	[/STORPROC]
	// Enregistrement
	[METHOD Adr|Save][/METHOD]
	// Redirection
	[REDIRECT][!Systeme::CurrentMenu::Url!]/Adresses?Type=[!Adr::Type!][/REDIRECT]
[/IF]


<h1>Modification d'une adresse de [!Adr::Type!]</h1>
<div class="user">
	[MODULE Boutique/Adresse/Form?Adr=[!Adr!]&Type=[!Adr::Type!]]
</div>