// Recherche de l'utilisateur pour avoir l'entite en cours
[STORPROC Systeme/User/[!Systeme::User::Id!]/Site|S|0|1]
	[STORPROC Systeme/Site/[!S::Id!]/Entite|Et|0|1]	[/STORPROC]
[/STORPROC]

<div id="headerRefs" class="articleHeader" [IF [!Et::CodeCouleur!]]style="background-color:[!Et::CodeCouleur!];"[/IF] >
	<div class="container"><h1>[!Systeme::CurrentMenu::Titre!]</h1></div>
</div>
//[MODULE Portfolio/Reference/Liste?Chemin=[!Query!]/Reference]
[MODULE Portfolio/Partenaire/Liste?Chemin=[!Query!]/Partenaire]
