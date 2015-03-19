<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" >
// Un Produit unique à référence unique (une fois vendu il n'apparait plus)
<div class="BlocHaut_[!Prod::TypeProduit!]">
	<div class="BlocGauche_[!Prod::TypeProduit!]">[MODULE Boutique/Produit/Photo]</div>
	<div class="BlocDroit_[!Prod::TypeProduit!]">[MODULE Boutique/Produit/Achat]</div>
	
</div><div style="height:60px;"><a href="/[!Cat::getUrl()!]" class="RetourListe">Retour à la liste des produits</a></div>
[IF [!Prod::Description!]!=]<div class="BlocBas_[!Prod::TypeProduit!]">[MODULE Boutique/Produit/Description]</div>[/IF]
</form>