// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
[/IF]

// Redirection etape 1
[IF [!ModifierCommande!]]
	[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
[/IF]

// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

// Récupère le panier du client
[!Panier:=[!CLCONN::getPanier()!]!]

// Si rien dans le panier redirection etape 1
[STORPROC [!Panier::LignesCommandes!]|Pan]
	[NORESULT]
		[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
	[/NORESULT]
[/STORPROC]

<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="Step1">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="Step2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="Step3Active Active">3 - Paiment</a>
	<a href="/Boutique/Commande/Etape3" class="Step4">4 - Confirmation</a>
</div>
<div class="CommandeEtape3">
	<h1>Mon paiement</h1>
	// Erreurs
	[IF [!ConfirmerPaiement!]]
		[IF [!Livraison!]!]
			[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Id=[!Livraison!]&&Type=Livraison|VerifAdress|0|1]
				[NORESULT]
					// Cette adresse n'est pas à lui
					[!Erreur:=1!]
					[!ErreurAdresseLivraison:=1!]
				[/NORESULT]
				[METHOD VerifAdress|Save][/METHOD]
			[/STORPROC] 
		[ELSE]
			// N'a pas choisi d'adresse de livraison
			[!Erreur:=1!]
			[!ErreurAdresseLivraison:=1!]
		[/IF]
	
		[IF [!Facturation!]!]
			[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Id=[!Facturation!]&&Type=Facturation|VerifAdress|0|1]
				[NORESULT]
					// Cette adresse n'est pas à lui
					[!Erreur:=1!]
					[!ErreurAdresseFacturation:=1!]
				[/NORESULT]
				// On la passe par défaut
				[METHOD VerifAdress|Save][/METHOD]
			[/STORPROC] 
		[ELSE]
			// N'a pas choisi d'adresse de facturation
			[!Erreur:=1!]
			[!ErreurAdresseFacturation:=1!]
		[/IF]
	
	//	[IF [!ModePaiement!]!=CB&&[!ModePaiement!]!=Cheque&&[!ModePaiement!]!=Virement]
		[IF [!ModePaiement!]]
		[ELSE]
			// Mode de paiement invalide
			[!Erreur:=1!]
			[!ErreurModedePaiement:=1!]
		[/IF]
	
		[IF [!AccepterCGV!]!=1]
			// N'a pas accepté les CGV
			[!Erreur:=1!]
			[!ErreurCGV:=1!]
		[/IF]
	
		[IF [!Erreur!]]
			[BLOC Erreur|Vérifiez les points suivants]
				<ul>
					[IF [!ErreurAdresseLivraison!]]<li>Vous devez choisir une adresse de livraison.</li>[/IF]
					[IF [!ErreurAdresseFacturation!]]<li>Vous devez choisir une adresse de facturation.</li>[/IF]
					[IF [!ErreurModedePaiement!]]<li>Vous devez choisir un mode de paiement.</li>[/IF]
					[IF [!ErreurCGV!]]<li>Vous devez accepter les conditions de vente.</li>[/IF]
				</ul>
			[/BLOC]
		[ELSE]
			// Tout est OK -> ENREGISTREMENT COMMANDE
	
			// On raccroche les adresses
			[!Panier::AddParent(Boutique/Adresse/[!Livraison!])!]
			[!Panier::AddParent(Boutique/Adresse/[!Facturation!])!]
	
			// On raccroche le client
			[!Panier::AddParent(Boutique/Client/[!CLCONN::Id!])!]
	
			// On enregistre en BDD
			[METHOD Panier|setValid][/METHOD]
			[METHOD Panier|Save][/METHOD]
	
			// Creation Paiement
			// TODO
	
			// Vider le COOKIE
			[METHOD CLCONN|ViderPanier][/METHOD]
	
			//  Ensuite on passera au paiement POUR L'INSTANT PAS ECRIT DONC DIRECT ETAPE4
			[REDIRECT]Boutique/Commande/Etape4[/REDIRECT]
		[/IF]
	
	[/IF]

	<form action="/[!Lien!]" method="post">
		<table class="Logistique">
			<tr>
				<th class="AdresseLivraison" >Adresse de livraison</th>
				<th class="AdresseFacturation" style="border-right:none;">Adresse de facturation</th>
			</tr>
			<tr>
				<td class="AdresseLivraison">
					[MODULE Boutique/Commande/Adresses?Type=Livraison&CLCONN=[!CLCONN!]]
				</td>
				<td class="AdresseFacturation" style="border-right:none;">
					[MODULE Boutique/Commande/Adresses?Type=Facturation&CLCONN=[!CLCONN!]]
				</td>
			</tr>
		</table>
		<div class="Etape3">
			<table class="LaLivraison">
				<tr>
					<th>Mode de livraison choisi</th>
				</tr>
				<tr>
					<td>
						xxx
					</td>
				</tr>
			</table>
		</div>	
		<div class="Etape3">
			<table class="tCommande">
				<tr >
					<th class="Produit">Récapitulatif de votre commande</th>
					<th>Prix initial</th>
					<th>Réduction</th>
					<th>Quantité</th>
					<th>Prix à payer</th>
					<th class="SupprimerItem">Sup</th>
				</tr>
				[!MenuBoutique:=[!Systeme::getMenu(Boutique/Categorie)!]!]
				[STORPROC [!Panier::LignesCommandes!]|Pan]
					// Colisage
					[STORPROC Boutique/Produit/Reference/[!Pan::Reference!]|Prod|0|1][/STORPROC]
					[!Emballage:=[!Prod::GetEmballage()!]!]
					[!NbUnite:=[!Prod::GetColisage()!]!]	
					[IF [!Pan::MontantRemiseTTC!]>0]
						[!montantReduc:=[!Pan::MontantTTC!]!]
						[!montantReduc/=[!Pan::MontantHorsPromoTTC!]!]
						[!montantReduc-=1!]
						[!montantReduc*=100!]
	
					[/IF]
					<tr>
						<td class="Produit"><a href="/[!MenuBoutique!]/[!Pan::UrlDirecte!]">[!Pan::Titre!]</a><br />[IF [!Emballage!]!=Default](Vendu en [!Emballage!])[/IF]</td>
						<td class="PrixInitial">[!Math::Price([!Pan::MontantHorsPromoTTC!])!] €</td>
						<td class="Remise">[IF [!Pan::MontantRemiseTTC!]>0][!Math::Price([!montantReduc!])!] %<br /> soit <br /> - [!Math::Price([!Pan::MontantRemiseTTC!])!] €[/IF]</td>
						<td class="Quantite" >
							[!Pan::Quantite!]
						</td>
						<td class="TotalTTC">[!Math::Price([!Pan::MontantTTC!])!]  €</td>
						<td class="SupprimerItem" style="border-right:none;text-align: center;"  ><input type="checkbox" name="Sup" value="[!Pan::RefProduit!]" class="Panier_Supr" /></td>
					</tr>
					[!TotGene+=[!Math::Price([!Pan::MontantTTC!])!]!]
				[/STORPROC]
			</table>
		</div>	
		<div class="Etape3">
			<table class="TotalDetaille">
				[!totreduc:=[!Panier::MontantTTCHorsPromo:-[!Panier::MontantTTC!]!]!]
				[IF [!totreduc!]>0]

					<tr>
						<td class="label">Total articles (prix initial)</td>
						<td class="val">[!Math::Price([!Panier::MontantTTCHorsPromo!])!] €</td>
					</tr>
					<tr class="Reduction">
						<td class="label">Total réduction</td>
						<td class="val">[!Math::Price([!Panier::MontantTTCHorsPromo:-[!Panier::MontantTTC!]!])!] €</td>
					</tr>
				[/IF]
				<tr class="TotalCommande">
					<td class="label">Total commande</td>
					<td class="val">[!Math::Price([!Panier::MontantTTC!])!] €</td>
				</tr>
				[!FraisDePort:=0!]
				<tr>
					<td class="label">Frais de port</td>
					<td class="val">[!FraisDePort!] €</td>
				</tr>
				<tr class="vide">
					<td  colspan="2">&nbsp;</td>
				</tr>
				// INCLURE LES FRAIS DE PORT DANS LA COMMANDE
				<tr class="TotalAPayer">
					<td class="label">Total à payer</td>
					<td class="val">[!Math::Price([!Panier::MontantTTC!])!] €</td>
				</tr>
			</table>
		</div>
		[COUNT Boutique/TypePaiement/Actif=1|NBmp]	
		[IF [!NBmp!]>0]
			<div class="Etape3">
				<table class="ModeDePaiement">
					<tr>
						<th  colspan="3">Mode de paiement</th>
					</tr>
					[STORPROC Boutique/TypePaiement/Actif=1|MP|||Ordre|ASC]	
						<tr>
							<td style="border-right:none;">
								<span class="NomPaiement">[!MP::Nom!]</span>
								[IF [!MP::Description!]!=]
									<span class="DescPaiement"><br />[!MP::Description!]</span>
								[/IF]
							</td>
							<td >[IF [!MP::Logo!]!=]<img src="/[!MP::Logo!]" title="[!MP::Nom!]" alt="[!MP::Nom!]" >[/IF]</td>
							<td class="ChoixPaiement" style="text-align:center;border-right:none;"><input type="radio" name="ModePaiement" value="[!MP::Id!]" [IF [!ModePaiement!]=[!MP::Id!]] checked="checked" [/IF] /></td>
						</tr>
					[/STORPROC]
				</table>
			</div>
		[/IF]
		<div class="Etape3">
			<div class="AccepterCGV">
				<input type="checkbox" name="AccepterCGV" value="1" [IF [!AccepterCGV!]] checked="checked" [/IF] />
				J'accepte les <a href="/Informations/_Conditions-generales" onclick="window.open(this.href);return false;">conditions générales</a> de vente
			</div>
		</div>
		<div class="Etape3" style="margin-bottom:20px;text-align:center;width:auto;">
			<div class="BoutonsGauche">
				<input type="submit" class="ModifierCommande" name="ModifierCommande" value="Je modifie ma commande" />
			</div>
			<div class="BoutonsDroite">
				<input type="submit" class="ConfirmerPaiement" name="ConfirmerPaiement" value="Je confirme mon paiement" />
			</div>
	
		</div>
	</form>
</div>	
// Surcouche JS
<script type="text/javascript">
	function showMoreAdresses( lien, type ) {
		$$('div.AdresseType' + type).each( function(div) {
			div.setStyle('display', 'block');
		});
		lien.setStyle('display','none');
	}
	$$('a.ChooseMoreAdresses').setStyle('display','block');

</script>