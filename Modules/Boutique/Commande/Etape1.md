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

// je recalcule systématiquement
[IF [!Continue!]||[!Valider!]||[!Recalcul!]]
	[STORPROC [!Panier::LignesCommandes!]|Pan]
		// Changement de la quantité
		[!refObj:=[!Pan::RefObject!]!]
		[!Cli::ajusterQtePanier([!Pan::Reference!],[!Qte[!refObj::Id!]!])!]
	[/STORPROC]
[/IF]

[IF [!Continue!]]
	[!Men:=[!Systeme::getMenu(Boutique/Magasin/1)!]!]
	[REDIRECT][!Domaine!]/[!Men!][/REDIRECT]
[/IF]

[IF [!Valider!]]
	[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
[/IF]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
//Magasin
[STORPROC Boutique/Magasin|Mag][/STORPROC] 
<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="Step1Active">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="Step2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="Step3">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="Step4">4 - Paiment</a>
</div>
<div id="javascriptehoh">Vous devez activer javascript pour modifier votre panier</div>
<div class="CommandeEtape1">
	<h1>Mon Panier</h1>
	[COUNT [!Panier::LignesCommandes!]|NB]
	[IF [!NB!]>0]
		<form action ="/[!Lien!]" name="Commande" method="post" >
			<table class="tCommande">
				<tr >
					<th class="Produit">Produit(s)</th>
					<th>Quantité</th>
					<th>Prix initial</th>
					<th>Réduction</th>
					<th>Prix à payer</th>
					<th class="SupprimerItem">Sup</th>
				</tr>
				[!MenuBoutique:=[!Systeme::getMenu(Boutique/Categorie)!]!]
				[STORPROC [!Panier::LignesCommandes!]|Pan]
					// Colisage
					[STORPROC Boutique/Produit/Reference/[!Pan::Reference!]|Prod|0|1][/STORPROC]
					[!RefStock:=0!]
					[STORPROC Boutique/Reference/Reference=[!Pan::Reference!]|Re|0|1][/STORPROC]
					[!RefStock:=[!Re::getstockReference!]!]
					[!Emballage:=[!Prod::GetEmballage()!]!]
					[!NbUnite:=[!Prod::GetColisage()!]!]	
					[IF [!Pan::MontantRemiseTTC!]>0]
						[!montantReduc:=[!Pan::MontantTTC!]!]
						[!montantReduc/=[!Pan::MontantHorsPromoTTC!]!]
						[!montantReduc-=1!]
						[!montantReduc*=100!]
	
					[/IF]
					<tr>
						<td class="Produit"><a href="/[!Prod::getUrl!]">[!Pan::Titre!]</a><br />[IF [!Emballage::ConditionnementDefaut!]!=1](Vendu en [!Emballage::TypeEmballage!])[/IF]</td>
						<td class="Quantite" >
							<div class="LaQuantite">
								[!refObj:=[!Pan::RefObject!]!]
								<input name="Qte[!refObj::Id!]" id="Qte[!refObj::Id!]" value="[!Pan::Quantite!]" style="width:30px; text-align:center;" readonly="readonly" >
							</div>
							<div class="LesBoutons">
								<div style="height:10px;"><input type="button" class="InputBtnPlus"  value="+" onclick="CalculQte([!NbUnite!],[!NbUnite!],'Qte[!refObj::Id!]',[!RefStock!]);"></div>
								<div style="height:10px;"><input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-[!NbUnite!],[!NbUnite!],'Qte[!refObj::Id!]',[!RefStock!]);"></div>							</div>
						</td>						<td class="PrixInitial">[!Math::PriceV([!Pan::MontantHorsPromoTTC!])!] [!De::Sigle!]</td>
						<td class="Remise">[IF [!Pan::MontantRemiseTTC!]>0][!Math::PriceV([!montantReduc!])!] %<br /> soit <br /> - [!Math::PriceV([!Pan::MontantRemiseTTC!])!] [!De::Sigle!][/IF]</td>

						<td class="TotalTTC">[!Math::PriceV([!Pan::MontantTTC!])!]  [!De::Sigle!]</td>
						<td class="SupprimerItem" style="border-right:none;text-align: center;"  ><input type="checkbox" name="Sup[]" value="[!Pan::Reference!]" class="Panier_Supr" /></td>
					</tr>
					[!TotGene+=[!Math::PriceV([!Pan::MontantTTC!])!]!]
				[/STORPROC]
			</table>
			<div class="LigneBoutons">
				<div class="BoutonsDroite">
					<input type="submit" class="RecalculerPanier" name="Recalcul" value="Recalculer le panier" />
				</div>
			</div>
			<div class="MontantTotal">
				<table class="TotalCommande">
					<tr>
						<td class="MontantTotalCmde">Montant de votre commande</td>
						<td class="MontantTotalTTC">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
					</tr>
				</table>
			</div>
			[IF [!Mag::TexteLivraison!]!=]
			<div class="TotalNotLines">
				[!Mag::TexteLivraison!]
			</div>
			[/IF]
			<div class="LigneBoutons">
				<div class="BoutonsDroite">
					<input type="submit" class="ValiderCommande" name="Valider" value="Je valide ma commande" />
				</div>
				<div class="BoutonsGauche">
					<input type="submit" class="ContinuerAchats" name="Continue" value="Continuer mes achats" />
				</div>
			</div>
		</form>
	[ELSE]
		<p>
			Votre panier est vide.<br />
			<a href="/">Cliquez ici pour continuer vos achats</a>.
		</p>
	[/IF]
</div>
<script type="text/javascript">
	function CalculQte(PlusMoins,QteMini,monchamp,QteMax) {
		var Quantite= parseInt($(monchamp).value);
		var totQte = Quantite+parseFloat(PlusMoins);
		if (totQte<=QteMax) {
			 $(monchamp).value=totQte;
		}else {
			alert("Quantité en stock atteinte");
		}

		if ($(monchamp).value < QteMini) $(monchamp).value=QteMini;
	}
</script>