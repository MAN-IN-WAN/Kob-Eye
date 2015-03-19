[STORPROC [!Query!]|P]
	<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" >
		<div class="Photo">
			[MODULE Boutique/Produit/Photo]
			[COUNT Boutique/Produit/[!P::Id!]/Donnee/Type=Image|NbImg]
			[IF [!NbImg!]>0]
				<div class="DescriptionBas">
					<a href="/[!P::Image!]" rel="shadowbox;" ><img src="/[!P::Image!].mini.80x80.jpg"  alt="[!Utils::noHtml([!P::Description!])!]" /></a>
					[STORPROC Boutique/Produit/[!P::Id!]/Donnee/Type=Image|i]
						<a href="/[!i::Fichier!]" rel="shadowbox;" ><img src="/[!i::Fichier!].mini.80x80.jpg" alt="[!Utils::noHtml([!P::Description!])!]" /></a>
					[/STORPROC]
				</div>
			[/IF]
		</div>
		<div class="DescriptionProduit">
			<div class="DescriptionHaut">
				<h2>[!P::Nom!]</h2>
				<h3>[!P::Accroche!]</h3>
				[MODULE Boutique/Produit/Achat]
			</div>
			
		</div>
	</form>
[/STORPROC]
[STORPROC Boutique/Categorie/Produit/[!P::Id!]|Cat]
	<div class="RetourListe"><a href="/[!Cat::getUrl()!]" >Retour à la liste des produits</a></div>
[/STORPROC]

