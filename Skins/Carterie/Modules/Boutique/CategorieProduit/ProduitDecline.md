[STORPROC [!Query!]|P][/STORPROC]
<div class="row-fluid">
	<form method="post" action="/[!Lien!]" name="achat" id="FicheProduit" class="span12" >
		<div class="row-fluid pointille">
			<div class="span7 fiche_photo">
				// photo produit 
				[MODULE Boutique/Produit/Photo]
			</div>
			<div class="span5">
				<div class="row-fluid pointille fiche_nomproduit">
					<div class="span7">
						//Nom produit
						[MODULE Boutique/Produit/NomProduit]
						
					</div>
					<div class="span5 prix">
						//Prix produit
						[MODULE Boutique/Produit/Prix]
					</div>
					<div class="span12 fiche_accrocheproduit">
                        			//Accroche produit
	                       			[MODULE Boutique/Produit/AccrocheProduit]
                  	  		</div>
				</div>

				<div class="row-fluid">
					<div class="span12 fiche_descriptionhaut">
						// description fiche produit
						[MODULE Boutique/Produit/Description]
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12 fiche_attributs">
						// attribut du produit
						[MODULE Boutique/Produit/Attributs]
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span9 fiche_photoplus">
				// photo diapo dessous
				[MODULE Boutique/Produit/PhotoPlus]
			</div>
			<div class="span3 fiche_achat">
				//bouton qte et ajour panier
				[MODULE Boutique/Produit/Achat]
				[MODULE Boutique/Produit/RetourListe]
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12 fiche_retourliste">
				//Retour à la liste des produits
				
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
