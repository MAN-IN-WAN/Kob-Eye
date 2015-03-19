[STORPROC [!Query!]|P][/STORPROC]
<div class="row">
	<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" class="col-md-12" >
		<div class="row pointille">
			<div class="col-md-7 fiche_photo">
				// photo produit 
				[MODULE Boutique/Produit/Photo]
			</div>
			<div class="col-md-5">
				<div class="row pointille fiche_nomproduit">
					<div class="col-md-8">
						//Nom produit
						[MODULE Boutique/Produit/NomProduit]
						
					</div>
					<div class="col-md-4 prix">
						//Prix produit
						[MODULE Boutique/Produit/Prix]
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 fiche_accrocheproduit">
                        			//Accroche produit
                        			[MODULE Boutique/Produit/AccrocheProduit]
                  	  		</div>
				</div>

				<div class="row">
					<div class="col-md-12 fiche_descriptionhaut">
						// description fiche produit
						[MODULE Boutique/Produit/Description]
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 fiche_attributs">
						// attribut du produit
						[MODULE Boutique/Produit/Attributs]
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 fiche_achat">
						//bouton qte et ajour panier
						[MODULE Boutique/Produit/Achat]
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 fiche_retourliste">
						//Retour à la liste des produits
						[MODULE Boutique/Produit/RetourListe]
					</div>
				</div>
			</div>
		</div>
		<div class="fiche_photoplus">
			// photo diapo dessous
			[MODULE Boutique/Produit/PhotoPlus]
		</div>
		
	</form>
	
</div>

<div class="row">
	<div class="col-md-12">
		// description supplémentaires produits (données de type caracteristiques)
		[MODULE Boutique/Produit/Caracteristiques]
	</div>
</div>
