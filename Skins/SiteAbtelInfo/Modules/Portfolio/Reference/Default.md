// EN FONCTION C'EST CELUI LÀ OU CELUI QUI EST À LA RACINE DU MODULE QUI EST EXÉCUTÉ
// lecture de la première categorie
[STORPROC Portfolio/Categorie/Publier=1|MaCat|0|1|Ordre|ASC][/STORPROC]

// Recherche de l'utilisateur pour avoir l'entite en cours
[STORPROC Systeme/User/[!Systeme::User::Id!]/Site|S|0|1]
	[STORPROC Systeme/Site/[!S::Id!]/Entite|Et|0|1]	[/STORPROC]
[/STORPROC]

// Recherche si on veut une liste de référence ou une référence particuliere
[COUNT [!Query!]|Refs]
[IF [!Refs!]=1]
	<div id="headerRefs" class="articleHeader" [IF [!Et::CodeCouleur!]]style="background-color:[!Et::CodeCouleur!];"[/IF] >
		[STORPROC [!Query!]|R|0|1|Ordre|ASC][/STORPROC]
		<div class="container"><h1>[!R::Titre!]</h1></div>
		
	</div>
	[MODULE Portfolio/Reference/Fiche?Chemin=[!Query!]]
[ELSE]
	<div id="headerRefs" class="articleHeader" [IF [!Et::CodeCouleur!]]style="background-color:[!Et::CodeCouleur!];"[/IF] >
		<div class="container"><h1>[!Systeme::CurrentMenu::Titre!]</h1></div>
	</div>

	[MODULE Portfolio/Reference/Liste?Chemin=Portfolio/Categorie/[!MaCat::Id!]/Reference]
[/IF]
