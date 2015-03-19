<div class="[!NOMDIV!]" style="padding-bottom:[!PADDINGBOTTOM!]px">
	<div class="EnteteNavigation">
		[!TITRE!]
	</div>
	<div class="ContenuComposantNavigation">	
		<ul>	
			[STORPROC Blog/Categorie|Cat]
				[COUNT Blog/Categorie/[!Cat::Id!]/Post/Actif=1|NbPost]
				<li>
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]" title="Acc&egrave;s &agrave; la cat&eacute;gorie [!Cat::Titre!]">[SUBSTR 18][!Cat::Titre!][/SUBSTR] ([!NbPost!])</a>
				</li>
			[/STORPROC]
		</ul>
	</div>
</div>
