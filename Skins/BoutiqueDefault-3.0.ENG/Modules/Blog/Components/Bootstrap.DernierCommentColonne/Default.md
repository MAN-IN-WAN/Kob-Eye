<div class="well [!NOMDIV!]">
	<h4>[!TITRE!]</h4>
	<ul class="nav nav-pills nav-stacked">	
		[STORPROC Blog/Commentaire/Publier=1|Commt|0|100|Date|DESC]
			[LIMIT 0|[!NBCOMMENT!]]
				[STORPROC Blog/Post/Commentaire/[!Commt::Id!]|Pst|0|1][/STORPROC]
				[STORPROC Blog/Categorie/Post/[!Pst::Id!]|Cat][/STORPROC]
				<li>
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Pst::Url!]" title="D&eacute;tail du post [!Pst::Titre!]" class="[IF [!Lien!]~[!Pst::Url!]]active[/IF]">
						[DATE d.m.Y][!Commt::Date!][/DATE]&nbsp;&nbsp;<span>[SUBSTR 10|[...]][!Commt::Comment!][/SUBSTR]</span>
					</a>
				</li>
			[/LIMIT]
		[/STORPROC]
	</ul>
</div>

