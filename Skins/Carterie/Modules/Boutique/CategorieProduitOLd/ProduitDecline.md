[STORPROC [!Query!]|P][/STORPROC]
<div class="row-fluid">
	<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" class="span12" >
		<div class="row-fluid pointille">
			<div class="span7">
				// photo produit et diapo dessous
				[MODULE Boutique/Produit/Photo]
			</div>
			<div class="span5">
				<div class="row-fluid pointille">
					<div class="span8">
						//Nom produit
						[MODULE Boutique/Produit/NomProduit]
						
					</div>
					<div class="span4">
						//Prix produit
						[MODULE Boutique/Produit/Prix]
					</div>
					<div class="span12">
                        			//Accroche produit
                        			[MODULE Boutique/Produit/AccrocheProduit]
                  	  		</div>
				</div>
				<div class="row-fluid">
					<div class="span12 descriptionhaut">
						// description fiche produit
						[MODULE Boutique/Produit/Description]
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						// attribut du produit
						[MODULE Boutique/Produit/Attributs]
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						//bouton qte et ajour panier
						[MODULE Boutique/Produit/Achat]
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						//Retour à la liste des produits
						[MODULE Boutique/Produit/RetourListe]
					</div>
				</div>
			</div>
		</div>
		
	</form>
</div>
<div class="row-fluid">
	<div class="span12">
		// description supplémentaires produits (données de type caracteristiques)
		[MODULE Boutique/Produit/Caracteristiques]
	</div>
</div>
