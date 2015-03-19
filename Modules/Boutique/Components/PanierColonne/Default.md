// Utilisateur (Connecté ou non ?)
[IF [!Systeme::User::Public!]=1]
	[OBJ Boutique|Client|Cli]
[ELSE]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1]
		[NORESULT]
			[OBJ Boutique|Client|Cli]
		[/NORESULT]
	[/STORPROC]
[/IF]
// Devise en cours
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]

[!Panier:=[!Cli::getPanier()!]!]

<form action ="/[!Lien!]" name="Panier" method="post" >
	<div class="EntoureComposant">
		<div class="EnteteComposant EntetePanier">
			[IF [!Panier::Valide!]]
				Ma Commande
			[ELSE]
				Mon Panier
			[/IF]
		</div>
		<div class="ContenuComposantPanier">
			<table class="Panier" cellspacing="0">
				<tr class="panierentete">
					<th class="NomProduit">Produits</th>
					<th class="Quantite">Qté</th>
					[IF [!Panier::Valide!]]
						<th class="SupprimerItem">Total TTC</th>
					[ELSE]
						<th class="TotalTTC">Total TTC</th>
						<th class="SupprimerItem">Sup</th>
					[/IF]
					</tr>
				[!MenuBoutique:=[!Systeme::getMenu(Boutique/Categorie)!]!]
				[STORPROC [!Panier::LignesCommandes!]|Ligne]
					<tr class="panierligne [IF [!Ligne::Reference!]=[!Reference!]] justAdded [/IF]">
						<td class="NomProduit"><a href="/[!Ligne::getUrlProduit()!]">[IF [!Pos!]=[!NbResult!]][!Ligne::Titre!][ELSE][!Ligne::Titre!][/IF]</a></td>
						<td class="Quantite">[!Ligne::Quantite!]</td>
						[IF [!Panier::Valide!]]
							<td class="SupprimerItem">[!Math::PriceV([!Ligne::MontantTTC!])!][!De::Sigle!]</td>
						[ELSE]
							<td class="TotalTTC">[!Math::PriceV([!Ligne::MontantTTC!])!][!De::Sigle!]</td>
							<td class="SupprimerItem"><a class="SupprimerItemPanier" href="/[!Lien!]?Sup[]=[!Ligne::Reference!]">X</a></td>
						[/IF]
					</tr>
					[NORESULT]
						<tr class="panierligne">
							<td class="NomProduit">Panier vide...</td>
							<td class="Quantite"></td>
							<td class="TotalTTC"></td>
							<td class="SupprimerItem" style="background:none;"></td>
						</tr>
					[/NORESULT]
					[!NbArticle+=[!Ligne::Quantite!]!]
				[/STORPROC]
			</table>
			<div class="ActionsPanier">
			[IF [!Panier::Valide!]]
				<div class="ValiderCommande"><a href="/Boutique/Commande/Etape4?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a></div>
				<div class="ModifierCommande"><input type="submit" name="Action" value="Modifier ma commande" ></div>
				<div class="ViderPanier">
					<input type="submit" name="Action"  value="Annuler ma commande" >
				</div>
			[ELSE]	
				<div class="ValiderCommande">
					<input type="submit" name="Action" value="Valider ma commande" />
				</div>
				<div class="ModifierCommande">
					<input type="submit" name="Action" value="Modifier ma commande" >
				</div>
				<div class="ViderPanier">
					<input type="submit" name="Action"  value="Vider mon panier" >
				</div>
			[/IF]
			</div>
		</div>
	</div>
	
	//COMMANDES EN COURS
	[STORPROC [!Cli::getPendingCommandes()!]|Com]
	<div class="EntoureComposant">
		<div class="EnteteComposant EntetePanier">
			Commandes en cours
		</div>
		<div class="ContenuComposantPanier">
			<table class="Panier" cellspacing="0">
				<tr class="panierentete">
					<th class="NomProduit">Commande</th>
					<th class="SupprimerItem">Total TTC</th>
				</tr>
					[LIMIT 0|10]
					<tr class="panierligne">
						<td class="NomProduit">[!Com::RefCommande!]</td>
						<td class="SupprimerItem">[!Math::PriceV([!Com::MontantTTC!])!][!De::Sigle!]</td>
					</tr>
					<tr class="panierligne">
						<td colspan ="3"  class="SupprimerItem">
							[SWITCH [!Com::getStatus()!]|=]
								[CASE 1]
									La commande est réservée. Le paiement n'est pas effectué.<br />
									
									<div class="ValiderCommande"><a href="/Boutique/Commande/Etape4?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a></div>
									<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]&action=annule">Annuler ma commande</a></div>
									<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]">Modifier ma commande</a></div>
								[/CASE]
								[CASE 2]
									Un paiement est en attente de réception.<br />
									
									<div class="ValiderCommande"><a href="/Boutique/Commande/Etape4?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a></div>
									<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]&action=annule">Annuler ma commande</a></div>
									<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]">Modifier ma commande</a></div>
								[/CASE]
								[CASE 3]
									Commande validée, le paiement a echoué.
									Pour finaliser votre commande, veuillez cliquer sur le lien ci-dessous.
									
									<div class="ValiderCommande"><a href="/Boutique/Commande/Etape4?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a></div>
									<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]&action=annule">Annuler ma commande</a></div>
									<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]">Modifier ma commande</a></div>
								[/CASE]
								[CASE 4]
									Commande payée le [!Com::PayeLe!]. En cours d'expédition.
								[/CASE]
								[CASE 5]
									Commande expédiée le [!Com::ExpedieLe!]. En cours de livraison.
								[/CASE]
								[CASE 6]
									Commande archivée.
								[/CASE]
							[/SWITCH] 
						</td>
					</tr>
					[/LIMIT]
			</table>
		</div>
	</div>
	[/STORPROC]
	
	
	//AUTRES PANIERS
	[STORPROC [!Cli::getOtherPanier()!]|Com]
	<div class="EntoureComposant">
		<div class="EnteteComposant EntetePanier">
			Autres paniers
		</div>
		<div class="ContenuComposantPanier">
			<table class="Panier" cellspacing="0">
				<tr class="panierentete">
					<th class="NomProduit">Panier</th>
					<th class="SupprimerItem">Total TTC</th>
				</tr>
					[LIMIT 0|10]
					<tr class="panierligne">
						<td class="NomProduit">[!Com::RefCommande!]</td>
						<td class="SupprimerItem">[!Math::PriceV([!Com::MontantTTC!])!][!De::Sigle!]</td>
					</tr>
					<tr class="panierligne">
						<td colspan ="3"  class="SupprimerItem">
							<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]">Utiliser ce panier</a></div>
							<div class="ValiderCommande"><a href="/[!Lien!]?Com=[!Com::RefCommande!]&action=annule">Supprimer ce panier</a></div>
						</td>
					</tr>
					[/LIMIT]
			</table>
		</div>
	</div>
	[/STORPROC]
</form>
