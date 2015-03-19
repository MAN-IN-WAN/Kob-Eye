//[IF [!Systeme::User::Public!]=0]
//	[STORPROC Pink/PkUser/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]
//[/IF]
// Utilisateur (Connecté ou non ?)
[!Expert:=!]
[IF [!Systeme::User::Public!]=1]
	[OBJ Pink|PkUser|Cli]
[ELSE]
	[STORPROC Pink/Expert/UserId=[!Systeme::User::Id!]|Cli|0|1]
		[!Expert:=1!]
		[NORESULT]
			[STORPROC Pink/PkUser/UserId=[!Systeme::User::Id!]|Cli|0|1]
				[NORESULT]
					[OBJ Pink|PkUser|Cli]
				[/NORESULT]
			[/STORPROC]
		[/NORESULT]
	[/STORPROC]
[/IF]
<div class="MenuDroit">
	<div class="MenuDroitHaut">
		<a href="/Account" title="Mon compte" class="moncomptehaut" >Mon compte</a>
		[IF [!Systeme::User::Public!]=1]
			<a href="/Account" title="Connexion" class="deconnexion">Connexion</a>
		[ELSE]
			<a href="/Systeme/Logout" title="déconnexion" class="deconnexion">Déconnexion</a>
		[/IF]
	[IF [!Systeme::User::Public!]!=1]
		<div class="MenuSousDroitHaut">Bienvenue<span class="connecte"> [!Cli::Mail!]</span></div>
	[/IF]
	</div>
</div>