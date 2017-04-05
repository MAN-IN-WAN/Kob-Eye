// Modele qui n'affiche pas le contenu des articles en entier
[INFO [!Lien!]|I]
//Recherche du menu racine
[STORPROC [!I::Historique!]|LaRacine|0|1][/STORPROC]


[STORPROC [!Query!]|Cat]
	// AFFICHAGE DE LA CATEGORIE PRINCIPALE
		[IF [!Cat::Nom!]!=Accueil]
			<div class="TitreCategorie">
				<h1 class="title_block">[!Cat::Nom!]</h1>
			</div>
		[/IF]
		[IF [!Cat::Description!]!=]
			<p class="catDesc">[!Cat::Description!]</p>
		[/IF]
[/STORPROC]