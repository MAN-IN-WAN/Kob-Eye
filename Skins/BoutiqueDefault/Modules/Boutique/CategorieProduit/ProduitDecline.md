[STORPROC [!Query!]|P|0|1][/STORPROC]
<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" >
	[MODULE Boutique/Produit/Photo]
	<div class="DescriptionProduit">
		<div class="DescriptionHaut">
			<div class="Rel">
				<div class="DescriptionBas">
					<img src="/[!P::Image!].mini.80x80.jpg"  alt="[!Utils::noHtml([!P::Description!])!]" />
					[STORPROC Boutique/Produit/[!P::Id!]/Donnee/Type=Image|i]
						<img src="/[!i::Fichier!].mini.80x80.jpg" alt="[!Utils::noHtml([!P::Description!])!]" />
					[/STORPROC]
				</div>
			</div>
			<h2>[!P::Nom!]</h2>
			[MODULE Boutique/Produit/Achat]
			<div class="Sociaux inb">
					<a href="http://www.facebook.com/share.php?url=[!Domaine!]/[!Lien!]" title="Facebook" target="_blank" ><span class="BoutonsSociaux1 inb"></span></a>
					<a href="http://www.twitter.fr/share?url=[!Domaine!]/[!Lien!]" title="Twitter" target="_blank" ><span class="BoutonsSociaux2 inb"></span></a>
					<a href="http://www.linkedin.com/shareArticle?mini=true&url=[!Domaine!]/[!Lien!]&title=[!P::Titre!]" title="Linkedin" target="_blank" ><span class="BoutonsSociaux3 inb"></span></a>
					<a href="http://pinterest.com/pin/create/button/?url=[!Domaine!]/[!Lien!]&description=Partager sur Pinterest" title="Pinterest" target="_blank" ><span class="BoutonsSociaux4 inb"></span></a>
			</div>
		</div>
	</div>
	<div class="VousAimerezAussi">
		<div class="AimerezAussi">
			<h3>Vous aimerez aussi</h3>
		</div>
		[STORPROC Boutique/Categorie/Produit/[!P::Id!]|Cat|0|1]
			[STORPROC Boutique/Categorie/[!Cat::Id!]/Produit/Actif=1&Id!=[!P::Id!]|Prod]
				[ORDER Id|RANDOM]
					[LIMIT 0|3]
						<div class="ProduitsAimerezAussi">
							//<a href="/[!Prod::getUrl()!]" title="[!Utils::noHtml([!Prod::Description!])!]"><img src="/[!Prod::Image!].mini.125x123.jpg" alt="[!Utils::noHtml([!Prod::Description!])!]" /></a>
							<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Produit/[!Prod::Url!]" title="[!Utils::noHtml([!Prod::Description!])!]"><img src="/[!Prod::Image!].mini.125x123.jpg" alt="[!Utils::noHtml([!Prod::Description!])!]" /></a>

						</div>
					[/LIMIT]
				[/ORDER]
			[/STORPROC]
		[/STORPROC]
	</div>
</form>

