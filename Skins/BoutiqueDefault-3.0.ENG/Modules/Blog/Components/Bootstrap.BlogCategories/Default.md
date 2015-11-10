<div class="block [!NOMDIV!]">
	<h4 class="title_block">[!TITRE!]</h4>
	<div class="block_content">
		<ul class="navigation">
		[STORPROC Blog/Categorie|Cat]
			[COUNT Blog/Categorie/[!Cat::Id!]/Post/Actif=1|NbPost]
			<li>
				<a href="/[!Systeme::getMenu(Blog/Categorie/[!Cat::Url!])!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]" class="[IF [!Lien!]~[!Cat::Url!]]active[/IF]">[SUBSTR 18][!Cat::Titre!][/SUBSTR] ([!NbPost!])</a>
			</li>
		[/STORPROC]
		</ul>
	</div>
</div>
