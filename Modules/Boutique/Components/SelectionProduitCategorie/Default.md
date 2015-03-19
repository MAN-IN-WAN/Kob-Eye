//Composant d'affichage d'un sélection de produit
[!Requete:=[!Query!]!]
[IF [!SOUSCATEGORIE!]=1]
	[!Requete+=/Categorie/*!]
[/IF]
[!LesSpan:=12!]
[!LesSpan/=[!NBCOLS!]!]
<div class="[!NOMDIV!]">
	<div class="row-fluid CentrageProduit">
		<div class="Titre">[!TITRE!]</div>
		<div class="ListeProduitsCat">
		[STORPROC [!Requete!]/Produit/Actif=1|Prod|||tmsCreate|ASC]
			
			<div class="span[!LesSpan!]">
				<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="[!Utils::noHtml([!Prod::Description!])!]">
					<span class="ProduitCat" style="background-image: url('/[!Prod::Image!].mini.327x285.jpg');"></span>
				</a>
				<div class="Details">
					<div class="DetailsGauche">
						<h2>[!Prod::Nom!]</h2>
						<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="">En savoir plus</a>
					</div>
					<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
				</div>
			</div>
		[/STORPROC]
		</div>
	</div>
</div>
