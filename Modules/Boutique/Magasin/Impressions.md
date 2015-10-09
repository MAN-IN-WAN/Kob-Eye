[IF [!Systeme::User::Public!]]
	[REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT]
[ELSE]
	<div id="Container">
		<div id="Arbo">
			[BLOC Panneau]
			[/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau]
				<div style="display:block;width:100%;" >
					
					[IF [!Systeme::User::Public!]]
						<div id="Entete" style="display:block;margin:20px auto auto auto;width:980px;">
							<a href="/" title="Revenir à la page d'accueil">
								Revenir à l'accueil
							</a>
						</div>
					[ELSE]
						<div id="Entete" style="display:block;margin:20px auto auto auto;width:980px;">
							<a href="[!Domaine!]/Boutique/Magasin/ListeCommande?cde=1" target="_blank" rel="link">Cliquez ici pour obtenir la liste des commandes</a><br /><br />				
							<a href="[!Domaine!]/Boutique/Magasin/ListeCommande?pan=1" target="_blank" rel="link">Cliquez ici pour obtenir la liste des paniers en cours</a><br /><br />
							<a href="[!Domaine!]/Boutique/Magasin/ListeCommande.pdf" target="_blank" rel="link">Cliquez ici pour obtenir la liste des commandes et paniers en cours</a><br /><br />
							<a href="[!Domaine!]/Boutique/Magasin/Personnalisables" target="_blank" rel="link">Cliquez ici pour obtenir la liste des packs personnalisables</a><br /><br />			<a href="[!Domaine!]/Boutique/Magasin/ListeAbonnes" target="_blank" rel="link">Cliquez ici pour obtenir la liste des Abonnés</a><br /><br />
							<a href="[!Domaine!]/Boutique/Magasin/ListeServices" target="_blank" rel="link">Cliquez ici pour obtenir la liste des Services</a><br /><br />
							<a href="[!Domaine!]/#/Boutique/Magasin/DemandeExportEtiquette.htm"   rel="link"   >Exporter les étiquettes</a><br /><br />
<a href="[!Domaine!]/Boutique/Magasin/ListePbLivr"  target="_blank" rel="link"  >Liste des commandes avec double adresse livraison</a><br />
<a href="[!Domaine!]/Boutique/Magasin/ListeCommandeAbonnement.pdf" target="_blank" rel="link">Cliquez ici pour obtenir la liste des commandes avec erreur sur livraison</a><br /><br />

						</div>
					[/IF]
				</div>
			[/BLOC]
		</div>
	</div>
	
[/IF]