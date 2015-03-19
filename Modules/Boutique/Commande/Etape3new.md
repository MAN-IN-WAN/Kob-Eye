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
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]

<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="Step1">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="Step2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="Step3 Step3Active">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="Step4">4 - Paiment</a>
</div>
<div class="CommandeEtape3">
	<h1>Mon paiement</h1>
	// Erreurs
	[IF [!ValiderCommande!]]
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
			[!ErreurAdresseFacturation:=1!]
		[/IF]
	
		// on initialise le bon de livraison
		[STORPROC LivraisonStock/TypeLivraison/[!TypeLivraison!]|Liv|0|1]
			[!Panier::setBonLivraison([!TypeLivraison!],[!Liv::TvaLivr!])!]
			[NORESULT]
				[!Erreur:=1!]
				[!ErreurTypeLivraison:=1!]
			[/NORESULT]
		[/STORPROC]

		[IF [!Erreur!]]
			[BLOC Erreur|Vérifiez les points suivants]
				<ul>
					[IF [!ErreurAdresseLivraison!]]<li>Vous devez choisir une adresse de livraison.</li>[/IF]
					[IF [!ErreurAdresseFacturation!]]<li>Vous devez choisir une adresse de facturation.</li>[/IF]
					[IF [!ErreurTypeLivraison!]]<li>Incohérence sur le type de livraison choisi, merci de le sélectionner à nouveau.</li>[/IF]
				</ul>
			[/BLOC]
		[ELSE]
			// Tout est OK 
			// On raccroche les adresses
			[!Panier::setAdresseLivraison([!Livraison!])!]
			[!Panier::setAdresseFacturation([!Facturation!])!]

			// On raccroche le client
			[!Panier::setClient([!CLCONN::Id!])!]
	
			[!CLCONN::savePanier()!]

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
			<table class="tCommande">
				<tr >
					<th class="Produit">Récapitulatif de votre commande</th>
					<th>Quantité</th>
					<th>Prix initial</th>
					<th>Réduction</th>
					<th>Prix à payer</th>
					<th class="SupprimerItem">Sup</th>
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
						<td class="SupprimerItem" style="border-right:none;text-align: center;"  ><input type="checkbox" name="Sup" value="[!Pan::RefProduit!]" class="Panier_Supr" /></td>
					</tr>
					[!TotGene+=[!Math::PriceV([!Pan::MontantTTC!])!]!]



				[/STORPROC]
			</table>
		</div>
		<div class="Etape3">
			<table class="LaLivraison" id="ListeLivraison">
				<thead>
					<tr>
						<th colspan="3">Mode de livraison choisi</th>
					</tr>
				</thead>
				<tbody id="typeLivraisonModel">
					<tr>
						<td>{NomTarif}[IF {Desc}!=]<br /><span class="desc">{Desc}</span>[/IF]</td>
						<td>{Tarif}</td>
						<td rowspan"2"><input type="radio" value="{Id}" name="TypeLivraison" class="radioLivraison" rel="{TarifFR}"/></td>
					</tr>
					
				</tbody>
			</table>
		</div>	
		
		<div class="Etape3">
			<table class="TotalDetaille">
				[!totreduc:=[!Panier::MontantTTCHorsPromo:-[!Panier::MontantTTC!]!]!]
				[IF [!totreduc!]>0]

					<tr>
						<td class="label">Total articles (prix initial)</td>
						<td class="val">[!Math::PriceV([!Panier::MontantTTCHorsPromo!])!] [!De::Sigle!]</td>
					</tr>
					<tr class="Reduction">
						<td class="label">Total réduction</td>
						<td class="val">[!Math::PriceV([!Panier::MontantTTCHorsPromo:-[!Panier::MontantTTC!]!])!] [!De::Sigle!]</td>
					</tr>
				[/IF]
				<tr class="TotalCommande">
					<td class="label">Total commande</td>
					<td class="val" id="TotalCommande" rel="[!Panier::MontantTTC!]">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
				</tr>
				[!FraisDePort:=0!]
				<tr>
					<td class="label">Frais de port</td>
					<td class="val" id="FraisDePort">[!FraisDePort!] [!De::Sigle!]</td>
				</tr>
				
				<tr class="Reduction" id="ReductionCodePromo" style="display:none;">
					<td class="label">{ReducDesc}</td>
					<td class="val">-{ReducMontant} [!De::Sigle!]</td>
				</tr>

				<tr class="vide">
					<td  colspan="2">&nbsp;</td>
				</tr>


				<tr class="TotalAPayer">
					<td class="label">Total à payer</td>
					<td class="val"  id="TotalAPayer">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
				</tr>
			</table>
		</div>
		
		<div class="Etape3" >
			<div class="LigneBoutons" >
				<div class="BoutonsDroite">
					<input type="submit" class="ValiderCommande" name="ValiderCommande" value="Je valide ma commande" />
				</div>
				<div class="BoutonsGauche">
					<input type="submit" class="ModifierCommande" name="ModifierCommande" value="Je modifie ma commande" />
				</div>
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


	function getLivraison () {
		//initialisation
		var sel = $$('.AdresseRadioLivraison');
		var req = {};

		//On va chercher tous les combos et radios d'attributs
		sel.each(function (item,index){
			var lalivraison = item.get('name');
			var valeurlivraison = -1;
			switch (item.get('type')){
				case "radio":
					if (item.get('checked')){
						valeurlivraison = item.get('value');
						//On stocke les informations dans le tableau de requete
						req[lalivraison] = valeurlivraison;
					}
				break;
			}
		});
		
		//On execute la requete
		var r = new Request.JSON({
			url:'/LivraisonStock/TypeLivraison/GetTarifLivraison.json',
			data:req,
			onSuccess: function (json,text){
				//on recupere le container de la liste
				var location = $('ListeLivraison');
				if (!location.initialized){
					//On recupere le modele de ligne de livraison
					var model = $('typeLivraisonModel');
					// On detache et on stocke le modele dans la variable model
					model.dispose();
					location.model = model;
					//on initialise le tableau des lignes
					location.lignes = []
					//on definit la liste comme initialisée
					location.initialized = true;
				}else{
					var model = location.model;
					//on efface les lignes
					location.lignes.each(function (item,index){
						item.dispose();
						item.clear;
					})
					location.lignes = [];
				}
				
				//on boucle sur les types de livraisons et on cree une ligne pour chaque.
				var moinscher = -1;
				json.TypeLivraison.each(function (item,index){
					//on clone le modele
					var u = model.clone();
					var t = u.get('html');
					item.TarifFR = item.Tarif;
					item.Tarif = setPrice(item.Tarif);
					t = u.set('html',t.substitute(item));
					location.adopt(u);
					location.lignes.push(u);
					
					//selection du moins cher
					if (moinscher==-1||moinscher>parseFloat(json.Tarif)){
						u.getElement('input').set('checked',true);
					}
					// enlever pour éviter que ça ce coche le mini chaque fois que l'on change de choix de tarif
					u.getElement('input').addEvent('click',refreshTotal);
				})

				//on recalcule le total
				refreshTotal();
			},
			onError: function (error){
				alert('probleme de connexion');
			}
		}).send();
	}


	/**
	* met a jour les totaux de la commande avec les frais de port	
	*/
	function refreshTotal(){
		var rads = $$('.radioLivraison');
		var valeurlivraison = 0;
		rads.each(function (item,index) {
			if (item.get('checked')){
				valeurlivraison = parseFloat(item.get('rel'));
			}
		})
		//Mise a jour du total
		var tc = parseFloat($('TotalCommande').get('rel'));
		$('FraisDePort').set('html',setPrice(valeurlivraison));
		$('TotalAPayer').set('html',setPrice(tc+valeurlivraison));
/*		
		$('ReducMontantCodePromo').set('html',setPrice(valeurlivraison));
*/

	
	}
	window.addEvent('domready',function () {
		getLivraison();
		//mise en place des evenemts sur les radios de chois de l'adresse de livraison
		var sel = $$('.AdresseRadioLivraison');
		sel.each(function (item,index){
			item.addEvent('click',function (e) {
				getLivraison();
			})
		})
	});
	function setPrice(nStr){
		nStr = parseFloat(nStr);
		nStr =  Math.floor(nStr*Math.pow(10,2))/Math.pow(10,2);
		nStr+='';
		x = nStr.split('.');
		x1 = x[0];
		if (x[1])x[1] = x[1].length==1 ? x[1]+'0':x[1];
		else x[1]="00";
		x2 = x.length > 1 ? ',' + x[1] : '';
		return x1 + x2 + '  [!De::Sigle!]';
	}

</script>