<div class="[!NOMDIV!]" style="padding-bottom:[!PADDINGBOTTOM!]px">
	<div class="EnteteNavigation">
		[!TITRE!]
	</div>
	<div class="ContenuComposantNavigation">	
		<ul>	
			[STORPROC Blog/Post/Brouillon=0&Actif=1|Post|0|[!NBPOST!]|Date|DESC]
				[STORPROC Blog/Categorie/Post/[!Post::Id!]|Cat|0|1][/STORPROC]
				<li>
					<a href="/CategoriePost/[!Cat::Url!]/Post/[!Post::Url!]" title="D&eacute;tail du post [!Post::Titre!]">[DATE d.m.Y][!Post::Date!][/DATE]&nbsp;&nbsp;<span class="titrepost">[SUBSTR 8][!Post::Titre!][/SUBSTR]</span>
					</a>
				</li>
			[/STORPROC]
		</ul>
	</div>
</div>
