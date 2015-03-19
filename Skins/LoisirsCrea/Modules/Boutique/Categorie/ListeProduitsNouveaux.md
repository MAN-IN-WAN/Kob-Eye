[OBJ Boutique|Magasin|Mag]
[!Mg:=[!Mag::getCurrentMagasin()!]!]
<div class="row">
	<div class="Titre"><h1>Nouveautés</h1></div>
	<div class="SelectionProduits">
		[!Cpt:=0!]
		<div class="ListeProduitsCat row">
			[STORPROC Boutique/Magasin/[!Mg::Id!]/Categorie/*/Produit/Actif=1|Prod|0|6|tmsEdit|DESC]
				[!Promo:=[!Prod::GetPromo!]!]
				[!Cpt+=1!]
				[IF [!Cpt!]>3]
					[!Cpt:=1!]
					</div>
					<div class="ListeProduitsCat row" >
				[/IF]
				<div class="col-md-4">
					<div class="NomProduit"><h2>[!Prod::Nom!]</h2></div>
					<div class="AccrocheProduit">[SUBSTR 35|...][!Prod::Accroche!][/SUBSTR]	</div>
					<a href="/[!Prod::getUrl!]" title="[!Utils::noHtml([!Prod::Description!])!]">
						<img src="/[!Prod::Image!].mini.215x174.jpg" />
					</a>
					<div class="LesDetails">
						<div class="Details">
							<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
							[IF [!Promo!]!=0&&[!Promo!]!=]
								<div id="tarifNonPromo">Au lieu de <span class="barre">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!De::Sigle!]</span></div>
							[/IF]
						</div>
						<div class="DetailsSous">
							<a href="/[!Prod::getUrl!]" title="[!Prod::Nom!]" class="loupelien" >Voir le détail</a>
							<a href="/[!Prod::getUrl!]#Qte" title="Panier" class="panierliste">Mettre au panier</a>
						</div>
					</div>
				</div>
			[/STORPROC]
			[IF [!Cpt!]!=1]</div>[/IF]
		</div>
	</div>
</div>
