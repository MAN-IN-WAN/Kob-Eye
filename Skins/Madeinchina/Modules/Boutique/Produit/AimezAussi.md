[STORPROC [!Query!]|P][/STORPROC]
<div class="VousAimerezAussi">
	<div class="AimerezAussi">
		<h3>Vous aimerez aussi</h3>
	</div>
	[STORPROC Boutique/Categorie/Produit/[!P::Id!]|Cat|0|1]
		[STORPROC Boutique/Categorie/[!Cat::Id!]/Produit/Actif=1&Id!=[!P::Id!]|Prod]
			[ORDER Id|RANDOM]
				[LIMIT 0|3]
					<div class="ProduitsAimerezAussi">
						<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Produit/[!Prod::Url!]" title="[!Utils::noHtml([!Prod::Description!])!]"><img src="/[!Prod::Image!].mini.125x123.jpg" alt="[!Utils::noHtml([!Prod::Description!])!]" /></a>

					</div>
				[/LIMIT]
			[/ORDER]
		[/STORPROC]
	[/STORPROC]
</div>