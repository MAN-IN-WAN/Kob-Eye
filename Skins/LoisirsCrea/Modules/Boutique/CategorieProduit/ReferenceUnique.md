// Un Produit à références uniques
<div class="BlocHaut_[!Prod::TypeProduit!]">
	<div class="BlocGauche_[!Prod::TypeProduit!]">[MODULE Boutique/Produit/Photo]</div>
	<div class="BlocDroit_[!Prod::TypeProduit!]">[MODULE Boutique/Produit/Achat]</div>
	<div style="height:60px;"><a href="/[!Cat::getUrl()!]" class="RetourListe">Retour à la liste des produits</a></div>
</div>
<div class="BlocBas_[!Prod::TypeProduit!]">
	[IF [!Prod::Description!]!=][MODULE Boutique/Produit/Description][/IF]
	[MODULE Boutique/Produit/ListeReferences]
</div>