// Utilisateur (Connecté ou non ?)
[IF [!Systeme::User::Public!]=1]
	[OBJ Boutique|Client|Cli]
[ELSE]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1]
		[NORESULT]
			[OBJ Boutique|Client|Cli]
		[/NORESULT]
	[/STORPROC]
[/IF]

//Recupération du panier
[!Panier:=[!Cli::getPanier()!]!]


[STORPROC [!Panier::getErrors()!]|E]
	<div class="Error">[!E::Message!]</div>
[/STORPROC]
[!Panier::resetErrors()!]
[STORPROC [!Panier::getSuccess()!]|S]
	<div class="alert alert-success">[!S::Message!]</div>
[/STORPROC]
[!Panier::resetSuccess()!]
[!Cli::savePanier()!]
