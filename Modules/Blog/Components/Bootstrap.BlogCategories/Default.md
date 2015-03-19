<div class="well [!NOMDIV!]">
	<h4>[!TITRE!]</h4>
	<ul class="nav nav-pills nav-stacked">
	[STORPROC Blog/Categorie|Cat]
		[COUNT Blog/Categorie/[!Cat::Id!]/Post/Actif=1|NbPost]
		<li>
			<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]" class="[IF [!Lien!]~[!Cat::Url!]]active[/IF]">[SUBSTR 18][!Cat::Titre!][/SUBSTR] ([!NbPost!])</a>
		</li>
	[/STORPROC]
	</ul>
</div>
