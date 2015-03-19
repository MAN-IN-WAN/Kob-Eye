[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
	[!Chemin:=[!Query!]/Post/Actif=1!]
	[MODULE Blog/Post/Liste?Chemin=[!Chemin!]]
[ELSE]
	[STORPROC [!Query!]|Cat]
		<div class="box">		
			[IF [!Cat::Icone!]!=]<div class="ImageCat"><img src="/[!Cat::Icone!]" alt="[!Cat::Titre!]" ></div>[/IF]
			<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]"<h1>[!Cat::Titre!]</h1></a>
			<ul class="nav nav-pills nav-stacked">
			[STORPROC Blog/Categorie/[!Cat::Id!]/Post/Brouillon=0&Actif=1|Post|0|[!NBPOST!]|Date|DESC]
				<li>
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Post::Url!]" title="D&eacute;tail du post [!Post::Titre!]" class="[IF [!Lien!]~[!Post::Url!]]active[/IF]">[DATE d.m.Y][!Post::Date!][/DATE]&nbsp;&nbsp;<span class="titrepost">[SUBSTR 8][!Post::Titre!][/SUBSTR]</span></a>
				</li>
			[/STORPROC]
			</ul>
		</div>
	[/STORPROC]
[/IF]
