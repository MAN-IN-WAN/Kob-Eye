[STORPROC [!Query!]/Categorie|Cat|0|10|tmsCreate|DESC]
	<div class="PageCat">
		<div class="Titre[!Cat::Type!]">
			<h1><a href="/Blog/Categorie/[!Cat::Link!]" title="Lire tous les posts de la cat&eacute;gorie [!Cat::Titre!]">[!Cat::Titre!]</a></h1>
		</div>
		[STORPROC Blog/Categorie/[!Cat::Id!]/Post|Post|0|1|tmsCreate|DESC]
			[MODULE Blog/Categorie/[!Cat::Id!]/Post/[!Post::Id!]/Short]
			[NORESULT]<h4>Il n'y a aucun article dans cette cat&eacute;gorie.</h4>[/NORESULT]
		[/STORPROC]
	</div>
[/STORPROC]