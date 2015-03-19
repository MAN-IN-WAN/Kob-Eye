??je passe dans boutique/interfaces
[STORPROC [!Query!]|Cat|0|1]
	<div class="blocCategorie">
		[STORPROC [!Query!]/Produit/Actif=1|Prod|||Ordre|ASC]
			[!AVendre:=[!Prod::CheckStock!]!]

			// si on a choisi des declinaisons, il faut aller chercher la référence correspondante
			// pour voie romaine pas de souci 1 produit = 1 reference
			[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|RE|0|1][/STORPROC]
			[COUNT Boutique/Conditionnement/Produit/[!Prod::Id!]|NbCond]
			//[!Surcout:=0!]
			[!Emballage:=!]
			[!NbUnite:=1!]
			[STORPROC Boutique/Conditionnement/Produit/[!Prod::Id!]|COND|||Ordre|ASC]
				[NORESULT]
					[STORPROC Boutique/Conditionnement/ConditionnementDefaut=1|CONDD|0|1|tmsEdit|DESC]
						[!Emballage:= [!CONDD::TypeEmballage!]!]
						[!NbUnite:=[!CONDD::Colisage!]!]
						//[!Surcout:=[!CONDD::Surcout!]!]
					[/STORPROC]
				[/NORESULT]
				[!Emballage:= [!COND::TypeEmballage!]!]
				[!NbUnite:=[!COND::Colisage!]!]
				//[!Surcout:=[!COND::Surcout!]!]
			[/STORPROC]

			<div class="blocProduit" >
				<div class="blocProduitavantBouton">
					<div class="BlocContenu" >
						<div class="TitreListeArticle" >
							<h3>[!Prod::Nom!]</h3>
						</div>
						<div class="AccrocheArticle" >
							[!Prod::Accroche!]
						</div>
						<div class="DescArticle" >
							[IF [!Prod::Promo!]]
								[SUBSTR 50| [...]][!Prod::Description!][/SUBSTR]
							[ELSE]
								[SUBSTR 150| [...]][!Prod::Description!][/SUBSTR]
							[/IF]

						</div>
						<div class="BlocPrix">
							[IF [!Prod::Promo!]]
								<div class="PromoArticle">
									<div class="PromoArticleContenu" >
										// ici calculer le pourcentage de promotion
										// P: on veut un pourcentage, 0 : arrondi, Tarif, Tarif Reduit 
										[!Reduction:=[!Utils::Calc_Reduction(P,0,[!RE::Tarif!],[!RE::PrixPromotion!])!]!]
										PROMO [!Reduction!]% 
									</div>
									<div class="PromoArticleImage"  ></div>
								</div>
							[/IF]

							<div class="PrixArticle" >
								// affichage du tarif
								[IF [!Prod::Promo!]]
									[!PrixUniteHT:=[!RE::PrixPromotion!]!]
									<div class="Fichprixbarre" style="text-decoration:line-through;">
										[!PrixUniteN:=[!Utils::getMontantTTC([!Prod::Tarif!],[!RE::TypeTva!])!]!]
										//[!PrixUniteN+=[!SurcoutTTCUnitaire!]!]
										[!PrixUniteN!] € TTC
									</div>
									<div class="Fichprix" >
										[!PrixUnite:=[!Utils::getMontantTTC([!RE::PrixPromotion!],[!RE::TypeTva!])!]!]
										//[!PrixUnite+=[!SurcoutTTCUnitaire!]!]
										[!PrixUnite!] € TTC
									</div>
								[ELSE]
									[!PrixUniteHT:=[!Prod::Tarif!]!]
									<div class="Fichprix" >
										[!PrixUnite:=[!Utils::getMontantTTC([!Prod::Tarif!],[!RE::TypeTva!])!]!]
										//[!PrixUnite+=[!SurcoutTTCUnitaire!]!]
										[!PrixUnite!] € TTC
									</div>
								[/IF]
							</div>
							<div class="UniteArticle" >
								[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/TypeCaracteristique=Bouteille|B|0|1][!B::Valeur!][/STORPROC]
							</div>
							<div class="CalculPRCond" >
								<div class="FichCalculPRCond" ><br />Vendu en [!Emballage!]</div>
							</div>
						</div>

					</div>
					<div class="BlocImage" >
						<img src="/[!Prod::Image!].mini.104x234.jpg" alt="[!Prod::Nom!]" title="[!Prod::Nom!]" />
					</div>
				</div>
				<form method="post" action="[!Domaine!]/[!Lien!]" name="achat">

					<div class="blocProduitBouton" >
						[IF [!AVendre!]!=0]
							<div class="AddPanier" >
								<div class="AddPanierImage" >
								</div>						
								<div class="AddPanierContenu" >
									
									<input class="FormBoutonPanierListe" type="submit"  value="Ajouter panier" name="AddPanier">
									//ajouter panier
								</div>
							</div>
						[ELSE]
							<div class="AddPanier" >
								<div class="AddPanierContenu" >Epuisé
								</div>
							</div>

						[/IF]
						<div class="VoirFicheProduit">
							<div class="VoirFicheProduitImage" >
							</div>						
							<div class="VoirFicheProduitContenu">
								<a href="/[!Prod::getUrl()!]"  >voir détail</a>
							</div>
							<input type="hidden" name="RefProduit" value="[!Prod::Reference!]">
							<input type="hidden" name="Qte" value="1">


						</div>
					</div>
				</form>
			</div>
		[/STORPROC]
	</div>
[/STORPROC]
