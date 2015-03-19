<h1 >Résultat de votre recherche</h1> 
<table class="table table-striped">
  	[STORPROC [!Systeme::getSearch([!RechercheMotCle!])!]|TL]
		[IF [!TL::PageModule!]!=Boutique]
			[!Requete:=[!TL::PageModule!]/[!TL::PageObject!]/[!TL::PageId!]!]
			[STORPROC [!Requete!]|R|0|1|Nom|ASC]
				<tr>
					<td>
						[IF [!R::Image!]!=]<a href="/[!Systeme::getMenu([!Requete!])!]" title="Voir la fiche de [!R::Nom!]"><img src="/[!R::Image!].limit.75x55.jpg"  alt ="[!R::Nom!]" title="[!R::Nom!]" /></a>[/IF]
					</td>
					<td>
						<a href="/[!Systeme::getMenu([!Requete!])!]" title="Voir détail">[!R::Nom!]</a>
	
					</td>
					<td>
						<a href="/[!Systeme::getMenu([!Requete!])!]" title="Voir détail" class="voirDetailRecherche">Voir</a>
					</td>
				</tr>
				[NORESULT]
					<tr><td colspan="4">Aucun résultat pour vos critères de recherche</td></tr>
				[/NORESULT]
			[/STORPROC]	
		[ELSE]
			[NORESULT]
				<tr><td colspan="4">Aucun résultat pour vos critères de recherche</td></tr>
			[/NORESULT]
			[IF [!TL::PageObject!]!=Produit]
				[!Requete:=[!TL::PageModule!]/[!TL::PageObject!]/[!TL::PageId!]!]
				[STORPROC [!Requete!]|Prod]
					[!Promo:=[!Prod::EstenPromo()!]!]
					[IF [!Prod::getTarif!]>0]
						<tr>
							<td>
								[IF [!Prod::Image!]!=]<a href="[!TL::Url!]" title="Voir la fiche de [!Prod::Nom!]"><img src="/[!Prod::Image!].limit.75x55.jpg"  alt ="[!Prod::Nom!]" title="[!Prod::Nom!]" /></a>[/IF]
							</td>
							<td>
								<a href="[!TL::Url!]" title="Voir la fiche">[!Prod::Nom!]</a>
			
							</td>
							<td>
								[IF [!Promo!]=1]
									<div class="Fichprixbarre" style="text-decoration:line-through;">
										[!Math::PriceV([!Prod::getTarifHorsPromo!])!]</span> [!CurrentDevise::Sigle!]		
									</div>
									<div class="PrixAchat">[!Math::PriceV([!Prod::getTarif!])!][!CurrentDevise::Sigle!]</div>
								[ELSE]
									<div class="PrixAchat">[!Math::PriceV([!Prod::getTarif!])!][!CurrentDevise::Sigle!]</div>
								[/IF]
							</td>
							<td>
								<a href="[!TL::Url!]" title="Voir la fiche" class="voirDetailRecherche">Voir</a>
							</td>
						</tr>
					[/IF]
				[/STORPROC]	
		
			[ELSE]
				[!Requete:=[!TL::PageModule!]/[!TL::PageObject!]/[!TL::PageId!]!]
				[STORPROC [!Requete!]|Prod|0|1]
					
					[!Promo:=[!Prod::EstenPromo()!]!]
				[/STORPROC]	
		
				[IF [!Prod::getTarif!]>0]
					<tr>
						<td>
							[IF [!Prod::Image!]!=]<a href="[!TL::Url!]" title="Voir la fiche de [!Prod::Nom!]"><img src="/[!Prod::Image!].limit.75x55.jpg"  alt ="[!Prod::Nom!]" title="[!Prod::Nom!]" /></a>[/IF]
						</td>
						<td>
							<a href="[!TL::Url!]" title="Voir la fiche">[!Prod::Nom!]</a>
		
						</td>
						<td>
							[IF [!Promo!]=1]
								<div class="Fichprixbarre" style="text-decoration:line-through;">
									[!Math::PriceV([!Prod::getTarifHorsPromo!])!]</span> [!CurrentDevise::Sigle!]		
								</div>
								<div class="PrixAchat">[!Math::PriceV([!Prod::getTarif!])!][!CurrentDevise::Sigle!]</div>
							[ELSE]
								<div class="PrixAchat">[!Math::PriceV([!Prod::getTarif!])!][!CurrentDevise::Sigle!]</div>
							[/IF]
						</td>
						<td>
							<a href="[!TL::Url!]" title="Voir la fiche" class="voirDetailRecherche">Voir</a>
						</td>
					</tr>
				[/IF]
			[/IF]
			
		[/IF]
		[NORESULT]
			[STORPROC Boutique/Reference/Reference~[!RechercheMotCle!]&Actif=1|R|||Nom|ASC]
				<tr>
					<td>
						[IF [!R::Image!]!=]<a href="/[!Systeme::getMenu([!Requete!])!]" title="Voir la fiche de [!R::Nom!]"><img src="/[!R::Image!].limit.75x55.jpg"  alt ="[!R::Nom!]" title="[!R::Nom!]" /></a>[/IF]
					</td>
					<td>
						<a href="/[!Systeme::getMenu([!Requete!])!]" title="Voir détail">[!R::Nom!]</a>
	
					</td>
					<td>
						<a href="/[!Systeme::getMenu([!Requete!])!]" title="Voir détail" class="voirDetailRecherche">Voir</a>
					</td>
				</tr>
				[NORESULT]
					<tr><td colspan="4">Aucun résultat pour vos critères de recherche</td></tr>
				[/NORESULT]
			[/STORPROC]	
		[/NORESULT]
    	[/STORPROC]

</table>
