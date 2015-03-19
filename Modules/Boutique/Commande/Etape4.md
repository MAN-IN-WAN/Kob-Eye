// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
[/IF]

// Redirection etape 1
[IF [!ModifierCommande!]]
	[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
[/IF]
//Magasin
[STORPROC Boutique/Magasin|Mag][/STORPROC] 
// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

// Récupère le panier du client
[!Panier:=[!CLCONN::getPanier()!]!]
//CHECK PANIER
[STORPROC [!Panier::LignesCommandes!]|Pan]
	[NORESULT]
		[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
	[/NORESULT]
[/STORPROC]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]

//VERIFICATION ADRESSE LIVRAISON
[IF [!Panier::getAdresseLivraison()!]=||[!Panier::getAdresseFacturation()!]=]
	[REDIRECT]Boutique/Commande/Etape3[/REDIRECT]
[/IF]
[!MontantTotalCommande:=[!Panier::MontantTTC!]!]
[!ReducOk:=0!]

//GESTION OFFRE SPECIALE
[!OFFRESPECIALE:=[!Panier::getOffreSpeciale()!]!]
[IF [!OFFRESPECIALE!]]
	[!MontantTotalCommande-=[!OFFRESPECIALE::getReducMontant([!Panier::MontantTTC!])!])!]
	[!ReducOk:=1!]
[/IF]


<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="Step1">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="Step2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="Step3">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="Step4Active">4 - Paiment</a>
</div>
<div class="CommandeEtape4">
	<h1>Mon paiement</h1>
	// Erreurs
	[IF [!ConfirmerPaiement!]]

		[IF [!AccepterCGV!]!=1]
			// N'a pas accepté les CGV
			[!Erreur:=1!]
			[!ErreurCGV:=1!]
		[/IF]
	
		[IF [!ModePaiement!]>0]
			[STORPROC Boutique/TypePaiement/[!ModePaiement!]|TP]
				[OBJ Boutique|Paiement|P]
				[!P::AddParent(Boutique/TypePaiement/[!ModePaiement!])!]
				[NORESULT]
					// Mode de paiement non valide
					[!Erreur:=1!]
					[!ErreurModedePaiement:=1!]
				[/NORESULT]
			[/STORPROC]
		[ELSE]
			// Mode de paiement non valide
			[!Erreur:=1!]
			[!ErreurModedePaiement:=1!]
		[/IF]

		[IF [!Erreur!]]
			[BLOC Erreur|Vérifiez les points suivants]
				<ul>
					[IF [!ErreurAdresseLivraison!]]<li>Vous devez choisir une adresse de livraison.</li>[/IF]
					[IF [!ErreurAdresseFacturation!]]<li>Vous devez choisir une adresse de facturation.</li>[/IF]
					[IF [!ErreurModedePaiement!]]<li>Vous devez choisir un mode de paiement.</li>[/IF]
					[IF [!ErreurCGV!]]<li>Vous devez accepter les conditions de vente en bas de page.</li>[/IF]
				</ul>
			[/BLOC]
		[ELSE]
			
			
			//GESTION CODE PROMO
			[IF [!CodePromo!]!=]
				[!TabReducCodePromo:=[!Panier::getReductionCodePromo([!CodePromo!],[!CLCONN::Id!])!]!]
				[!Panier::setCodePromo([!CodePromo!])!]
			[/IF]

			// TOUT EST OK -> ENREGISTREMENT COMMANDE
			// On enregistre en BDD
			[IF [!Panier::checkAndBuild()!]]
				[METHOD Panier|setValid][/METHOD]
			[ELSE]
				[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
			[/IF]

			// Association Paiement
			[METHOD P|Set]
				[PARAM]Montant[/PARAM]
				[PARAM][!Panier::MontantPaye!][/PARAM]
			[/METHOD]
			[!P::AddParent(Boutique/Commande/[!Panier::Id!])!]
			[METHOD P|Save][/METHOD]


			// Vider le COOKIE
			//[METHOD CLCONN|ViderPanier][/METHOD]
	
			// Ensuite on passe au paiement effectif
			[REDIRECT]Boutique/Commande/Etape4b[/REDIRECT]
		[/IF]
	
	[/IF]

	<form action="/[!Lien!]" method="post">
		<div class="Etape4">
			<table class="Logistique">
				<tr>
					<th>Mode de livraison choisi</th>
					<th>Adresse de livraison</th>
					<th style="border-right:none;">Adresse de facturation</th>
				</tr>
				<tr>
					<td class="Adresses">
 						[!BLivr:=[!Panier::getBonLivraison()!]!]
						[!FraisDePort:=[!BLivr::MontantLivraisonTTC!]!]
						[!BLivr::TypeLivraison!]<br />
						[!BLivr::TrancheLivraison!]<br /><br />
						<span class="nom">Tarif :  [!Math::Price([!BLivr::MontantLivraisonTTC!])!] [!De::Sigle!]</span>
					</td>
					<td class="Adresses">
						[!AdrLiv:=[!Panier::getAdresseLivraison()!]!]
						<span class="nom">[!AdrLiv::Civilite!] [!AdrLiv::Prenom!] [!AdrLiv::Nom!]</span><br />
						[IF [!BLivr::AdresseLivraisonAlternative!]]
							<br />[!BLivr::ChoixLivraison!]<br />
						[ELSE]
							[!AdrLiv::Adresse!] <br />
							[!AdrLiv::CodePostal!] [!AdrLiv::Ville!] [!AdrLiv::Pays!]<br />
						[/IF]
					</td>
					<td class="Adresses" style="border-right:none;">
						[!AdrFac:=[!Panier::getAdresseFacturation!]!]
						<span class="nom">[!AdrFac::Civilite!] [!AdrFac::Prenom!] [!AdrFac::Nom!]</span><br />
						<br />[!AdrFac::Adresse!] <br />
						[!AdrFac::CodePostal!] [!AdrFac::Ville!] [!AdrFac::Pays!]<br />
			
					</td>
				</tr>
			</table>
		</div>		
		<div class="Etape4">
			<table class="tCommande">
				<tr >
					<th class="Produit">Récapitulatif de votre commande</th>
					<th>Quantité</th>
					<th>Prix initial</th>
					<th>Réduction</th>
					<th class="TotalTTC">Prix à payer</th>
				</tr>

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
						<td class="Produit"><a href="/[!Prod::getUrl!]">[!Pan::Titre!]</a><br />[IF [!Emballage::ConditionnementDefaut!]!=1](Vendu en [!Emballage::TypeEmballage!])[/IF]</td>
						<td class="Quantite" >
							[!Pan::Quantite!]
						</td>
						<td class="PrixInitial">[!Math::PriceV([!Pan::MontantHorsPromoTTC!])!] [!De::Sigle!]</td>
						<td class="Remise">[IF [!Pan::MontantRemiseTTC!]>0][!Math::PriceV([!montantReduc!])!] %<br /> soit <br /> - [!Math::PriceV([!Pan::MontantRemiseTTC!])!] [!De::Sigle!][/IF]</td>
						<td class="TotalTTC">[!Math::PriceV([!Pan::MontantTTC!])!]  [!De::Sigle!]</td>
					</tr>
					[!TotGene+=[!Math::PriceV([!Pan::MontantTTC!])!]!]


				[/STORPROC]
			</table>
		</div>
		
		
		<div class="Etape4">
			<table class="TCodePromo">
				<tr>
					<th colspan="2" >Code Promo</th>
				</tr>
				<tr>
					<td><input type="text" name="CodePromo" id="CodePromo" value="[!CodePromo!]" /></td>
					<script type="text/javascript">
						window.addEvent('domready', function() {
							[IF [!CodePromo!]!=]
								RenvoieCodePromo( true );
							[/IF]
							$('CodePromo').addEvent('keydown', function(e) {
								if(e.code == 13) {
									new Event(e).stop();
									RenvoieCodePromo();
								}
							});
						});
					</script>
					<td><input type="button" id="inputCodePromo" value="OK" onclick="RenvoieCodePromo();"/></td>
				</tr>
			</table>
		</div>	

		<div class="Etape4">
			<table class="TotalDetaille">
				<tr>
					<td class="label">Total articles (prix initial)</td>
					<td class="val">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
				</tr>
				<tr>
					<td class="label">Frais de port</td>
					<td class="val" id="FraisDePort">[!Math::PriceV([!BLivr::MontantLivraisonTTC!])!] [!De::Sigle!]</td>
				</tr>

				[IF [!Panier::Remise!]>0]
					<tr class="Reduction">
						<td class="label">Total réduction(s)</td>
						<td class="val">- [!Math::PriceV([!Panier::Remise!])!] [!De::Sigle!]</td>
					</tr>
				[/IF]

				<tr id="ReductionCodePromo" style="display:none;">
					<td class="label">{ReducDesc}</td>
					<td class="val">- {ReducMontant} [!De::Sigle!]</td>
				</tr>

//				<tr class="TotalCommande">
//					<td class="label">Total commande</td>
//					<td class="val" id="TotalCommande" rel="[!Panier::MontantTTC!]">[!Math::PriceV([!Panier::MontantTTC:-[!Panier::Remise!]!])!] [!De::Sigle!]</td>
//				</tr>
				
				<tr class="vide">
					<td  colspan="2">&nbsp;</td>
				</tr>


				<tr class="TotalAPayer">
					<td class="label">Total à payer</td>
					[!MontantTotalCde:=[!Panier::MontantPaye!]!]
					//[!MontantTotalCde+=[!BLivr::MontantLivraisonTTC!]!]
					<td class="val"  id="TotalAPayer">[!Math::PriceV([!MontantTotalCde!])!] [!De::Sigle!]</td>
				</tr>
			</table>
		</div>
		[COUNT Boutique/TypePaiement/Actif=1|NBmp]	
		[IF [!NBmp!]>0]
			<div class="Etape4">
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
		<div class="Etape4">
			<div class="AccepterCGV">
				<input type="checkbox" name="AccepterCGV" value="1" [IF [!AccepterCGV!]] checked="checked" [/IF] />
				J'accepte les <a href="[!Mag::LienCGV!]" onclick="window.open(this.href);return false;">conditions générales</a> de vente
			</div>
		</div>
		<div class="Etape4" style="margin-bottom:20px;text-align:center;width:auto;">
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

	var source;
	var t;
	var model;
	window.addEvent('domready', function () {
		//On recupere le bloc promo dans total
		model = $('ReductionCodePromo');
		source=model.get('html');

	}
		

	); 

	/**
	* gestion du code promo on vérifie si le code taper existe et on renvoie le montant de la promotion
	*/
	function RenvoieCodePromo ( noError ) {
		//initialisation
		var total = 0;
		var req = {};
		var CodePromo= $('CodePromo').value;
		req['CodePromo'] = CodePromo;
			//On execute la requete
			var r = new Request.JSON({
			url:'/Boutique/Commande/GetReducCodePromo.json',
			data:req,
			onSuccess: function (json,text){
				//Montant reduction 
				json.ReducMontant = setPrice(json.ReducMontant);
				if (json.PortOffert) json.ReducMontant =Math.round([!BLivr::MontantLivraisonTTC!]*100)/100 ;


				if(json.ReducOk) {
					t = source;
					model.set('html',t.substitute(json));
					model.setStyle('display','table-row');
				}
				else {
					alert(json.Message);
					$('CodePromo').value = "";
					model.setStyle('display','none');
				}

				//Mise a jour du total
				total = parseFloat("[!MontantTotalCde!]");
				// ne sert plus car on met à jour reducmontant au dessus
				if(json.PortOffert) {
					total = [!Panier::MontantTTC:-[!Panier::Remise!]!];
				}else {
					total = total - parseFloat(json.ReducMontant);
				}
				$('TotalAPayer').set('html',setPrice(total) + " [!De::Sigle!]");
			},
			onError: function (error){
				alert('probleme de connexion');
			}
		}).send();
	}


	function setPrice(nStr){
		nStr = parseFloat(nStr);
		nStr =  Math.floor(nStr*Math.pow(10,2))/Math.pow(10,2);
		nStr+='';
		x = nStr.split('.');
		x1 = x[0];
		if (x[1])x[1] = x[1].length==1 ? x[1]+'0':x[1];
		else x[1]="00";
		x2 = x.length > 1 ? ',' + x[1] : '';
		return x1 + x2 ;
	}

</script>