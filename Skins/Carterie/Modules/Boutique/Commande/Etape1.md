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

[!TotGene:=0!]

// Ajout au panier
[IF [!Reference!]!=&&[!Qte!]>0]
	[!Cli::ajouterAuPanier([!Reference!],[!Qte!])!]
[/IF]

// Vider le panier
[IF [!Action!]=Vider mon panier]
	[!Cli::viderPanier()!]
[/IF]

// Récupère le panier du client
[!Panier:=[!Cli::getPanier()!]!]

[IF [!Continue!]]
	[!Men:=[!Systeme::getMenu(Boutique/Magasin/1)!]!]
	[REDIRECT][!Domaine!]/[!Men!][/REDIRECT]
[/IF]

[IF [!Valider!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
//Magasin

[STORPROC Boutique/Magasin|Mag][/STORPROC] 
[COMPONENT Boutique/MessagePanier/Default?]

<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="FondStep1Active">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="FondStep2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="FondStep3">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="FondStep4">4 - Paiment</a>
</div>
<noscript><div id="javascriptehoh">Vous devez activer javascript pour modifier votre panier</div></noscript>
<div class="CommandeEtape1">
	<h1>Mon Panier</h1>
	[COUNT [!Panier::LignesCommandes!]|NB]
	[IF [!NB!]>0]
		<form action ="/[!Lien!]" name="Commande" method="post" >
			<table class="tCommande">
				<tr>
					<th style="text-align:left; padding-left:10px"; class="Qualite">Produit(s)</th>
					<th class="Qualite">Quantité</th>
					<th class="Qualite">Prix Unitaire</th>
					<th class="Qualite">Réduction</th>
					<th class="Qualite">Prix Total à payer</th>
					<th class="SupprimerItem">Sup</th>
				</tr>
				[!MenuBoutique:=[!Systeme::getMenu(Boutique/Categorie)!]!]
				[STORPROC [!Panier::LignesCommandes!]|Pan]
					// Colisage
					[STORPROC Boutique/Produit/Reference/[!Pan::Reference!]|Prod|0|1][/STORPROC]
					[!NbUnite:=[!Prod::GetColisage()!]!]

					[!RefStock:=0!]
					[STORPROC Boutique/Reference/Reference=[!Pan::Reference!]|Re|0|1][/STORPROC]
					[!RefStock:=[!Re::getstockReference!]!]
					[!Emballage:=[!Prod::GetEmballage()!]!]
					[!refObj:=[!Pan::getReference()!]!]
					[IF [!Pan::MontantRemiseTTC!]>0]
						[!montantReduc:=[!Pan::MontantTTC!]!]
						[!montantReduc/=[!Pan::MontantHorsPromoTTC!]!]
						[!montantReduc-=1!]
						[!montantReduc*=100!]
					[/IF]
					<tr>
						<td class="Produit"><a href="/[!Prod::getUrl!]">[!Pan::Titre!]</a><br />[IF [!Emballage::ConditionnementDefaut!]!=1](Vendu en [!Emballage::TypeEmballage!])[/IF]</td>
						<td class="Quantite" >
							<div class="BoutonMoins"><input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-[!NbUnite!],[!NbUnite!],'Qte[!refObj::Id!]',[!RefStock!]);"></div>
							<div class="LaQuantite">
								[!refObj:=[!Pan::RefObject!]!]
								<input name="Qte[!refObj::Id!]" id="Qte[!refObj::Id!]" value="[!Pan::Quantite!]" style="width:20px; text-align:center;" readonly="readonly" >
							</div>
							<div class="BoutonPlus"><input type="button" class="InputBtnPlus"  value="+" onclick="CalculQte([!NbUnite!],[!NbUnite!],'Qte[!refObj::Id!]',[!RefStock!]);"></div>
							
						</td>						<td class="PrixInitial">[!Math::PriceV([!Pan::MontantUnitaireHorsPromoTTC!])!] [!De::Sigle!]</td>
						<td class="Remise">[IF [!Pan::MontantRemiseTTC!]>0][!Math::PriceV([!montantReduc!])!] %<br /> soit <br /> - [!Math::PriceV([!Pan::MontantRemiseTTC!])!] [!De::Sigle!][/IF]</td>

						<td class="TotalTTC">[!Math::PriceV([!Pan::MontantTTC!])!]  [!De::Sigle!]</td>
						<td class="SupprimerItem" style="border-right:none;text-align: center;"  ><input type="checkbox" name="Sup[]" value="[!Pan::Reference!]" class="Panier_Supr" /></td>
					</tr>
					[!TotGene+=[!Math::PriceV([!Pan::MontantTTC!])!]!]
				[/STORPROC]
			</table>
			<div class="Etape1"><div class="LigneBoutons">

				<div class="BoutonsDroite">
					<p>
						<input class="btn btn-kirigami RecalculerPanier" type="submit" name="Recalcul" value="Recalculer le panier" />
					</p>
				</div>
			</div></div>
			<div class="Etape1"><div class="MontantTotal">
				<span class="MontantTotalCmde">Montant de votre commande</span>
				<span class="MontantTotalTTC">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</span>
			</div></div>
			<br />
			[IF [!Mag::TexteLivraison!]!=]
			<div class="TotalNotLines">
				[!Mag::TexteLivraison!]
			</div>
			[/IF]
			<div class="Etape1"><div class="LigneBoutons">
				<div class="BoutonsDroite">
					<p>
						<input class="btn btn-kirigami" type="submit" name="Valider" value="Je valide ma commande" />
					</p>
				</div>
				<div class="BoutonsGauche">
					<p>
						<input class="btn btn-gris BoutonContinuer" type="submit" name="Continue" value="Continuer mes achats" />
					</p>
				</div>
			</div></div>
		</form>
	[ELSE]
		<p style="color:#5E626B;">
			Votre panier est vide.<br />
			<a href="/" style="color:#5E626B;">Cliquez ici pour continuer vos achats</a>.
		</p>
	[/IF]
</div>
<script type="text/javascript">
	function CalculQte(PlusMoins,QteMini,monchamp,QteMax) {
		var Quantite= parseInt($('#'+monchamp).val());
		var totQte = Quantite+parseFloat(PlusMoins);
		if (totQte<=QteMax||parseFloat(PlusMoins)<0) $('#'+monchamp).val(totQte);
	
		if (totQte>QteMax) {
			toastr.warning("Quantité en stock atteinte");
		}

		if ($('#'+monchamp).val() < QteMini) $('#'+monchamp).val(QteMini);
	}

	




</script>