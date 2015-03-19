[IF [!Systeme::User::Public!]!=1]
	[STORPROC Systeme/Group/User/[!Systeme::User::Id!]|G|0|1]
		[IF [!G::Id!]=2]
		[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1|tmsCreate|DESC]
			<div class="Bienvenue"><h2>Vous êtes connecté en tant que <span class="connecte">[!Pers::Pseudonyme!]</span></h2></div>
		[/STORPROC]
		[/IF]
	[/STORPROC]
[/IF]