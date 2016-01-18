<div class="block [!NOMDIV!]">
	<h3 class="title_block">[!TITRE!]</h3>
		[STORPROC Blog/Post/Brouillon=0&Actif=1|Post|0|[!NBPOST!]|Date|DESC]
		[STORPROC Blog/Categorie/Post/[!Post::Id!]|Cat|0|1][/STORPROC]
			<div class="media">
				[STORPROC Blog/Post/[!Post::Id!]/Donnees/Type=Image|I|0|1]
				<a class="pull-left" href="/[!Systeme::getMenu(Blog/Categorie/[!Cat::Id!])!]" title="D&eacute;tail du post [!Post::Titre!]" class="[IF [!Lien!]~[!Post::Url!]]active[/IF]"> <img class="media-object" src="/[!I::Fichier!].mini.45x45.jpg" alt="[SUBSTR 8][!Post::Titre!][/SUBSTR]"> </a>
					[NORESULT]
						<a class="pull-left kuler" href="/[!Systeme::getMenu(Blog/Categorie/[!Cat::Id!])!" title="D&eacute;tail du post [!Post::Titre!]" class="[IF [!Lien!]~[!Post::Url!]]active[/IF]"></a>
					[/NORESULT]
				[/STORPROC]
				<div class="media-body">
					<strong><a href="/[!Systeme::getMenu(Blog/Categorie/[!Cat::Id!])!]" title="D&eacute;tail du post [!Post::Titre!]" class="[IF [!Lien!]~[!Post::Url!]]active[/IF]">[SUBSTR 15][!Post::Titre!][/SUBSTR]</a></strong><br />
					<a href="/[!Systeme::getMenu(Blog/Categorie/[!Cat::Id!])!]" title="D&eacute;tail du post [!Post::Titre!]" class="[IF [!Lien!]~[!Post::Url!]]active[/IF]">
							[DATE d.m.Y][!Post::Date!][/DATE]
					</a>
				</div>
			</div>
		[/STORPROC]
</div>
