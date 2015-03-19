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


[!MontantTotalCommande:=[!Panier::MontantTTC!]!]
[!ReducOk:=0!]
[STORPROC [!CLCONN::getOffreSpeciale!]|OSP|0|1]
	[!MontantTotalCommande-=[!OSP::getReducMontant([!Panier::MontantTTC!])!])!]
	[!ReducOk:=1!]
[/STORPROC]
<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="Step1">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="Step2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="Step3 Step3Active">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="Step4">4 - Paiment</a>
</div>
<div class="CommandeEtape3">
	<h1>Ma livraison</h1>
	// Erreurs
	[IF [!ValiderCommande!]]
		[IF [!Livraison!]!]
			[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Id=[!Livraison!]&&Type=Livraison|AdrLiv|0|1]
				[NORESULT]
					// Cette adresse n'est pas à lui
					[!Erreur:=1!]
					[!ErreurAdresseLivraison:=1!]
				[/NORESULT]
				[METHOD AdrLiv|Save][/METHOD]
			[/STORPROC] 
		[ELSE]
			// N'a pas choisi d'adresse de livraison
			[!Erreur:=1!]
			[!ErreurAdresseLivraison:=1!]
		[/IF]
	
		[IF [!Facturation!]!]
			[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Id=[!Facturation!]&&Type=Facturation|AdrFac|0|1]
				[NORESULT]
					// Cette adresse n'est pas à lui
					[!Erreur:=1!]
					[!ErreurAdresseFacturation:=1!]
				[/NORESULT]
				// On la passe par défaut
				[METHOD AdrFac|Save][/METHOD]
			[/STORPROC] 
		[ELSE]
			// N'a pas choisi d'adresse de facturation
			[!ErreurAdresseFacturation:=1!]
		[/IF]
	
		// Vérification type livraison
		[IF [!TypeLivraison!]!=]
			[STORPROC LivraisonStock/TypeLivraison/[!TypeLivraison!]|TypLiv|0|1]
			[!TarifLivraison:=[!TypLiv::recupereTarif([!Panier!],[!AdrLiv!])!]!]
			[IF [!TarifLivraison::ObjectType!]=TarifLivraison]
				[!Zone:=[!TypLiv::GetZone([!AdrLiv::Pays!],[!AdrLiv::CodePostal!])!]!]
				[IF [!TypLiv::verifierChoix([!Panier!],[!AdrLiv!],[!ChoixLivraison!])!]]
					[!Panier::setBonLivraison([!AdrLiv!],[!TypLiv!],[!TarifLivraison!],[!Zone!],[!ChoixLivraison!],[!TypLiv::TvaLivr!])!]
				[ELSE]
					// Choix du mode de livraison semble incorrect
					[!Erreur:=1!]
					[!ErreurTypeLivraison:=1!]
				[/IF]
			[ELSE]
				// Type de livraison semble incorrect
				[!Erreur:=1!]
				[!ErreurTypeLivraison:=1!]
			[/IF]
			[NORESULT]
				[!Erreur:=1!]
				[!ErreurTypeLivraison:=1!]
			[/NORESULT]
		[/STORPROC]
		[ELSE]
			[!Erreur:=1!]
			[!ErreurTypeLivraison:=1!]
		[/IF]
		
		[IF [!Erreur!]]
			[BLOC Erreur|Vérifiez les points suivants]
				<ul>
					[IF [!ErreurAdresseLivraison!]]<li>Vous devez choisir une adresse de livraison.</li>[/IF]
					[IF [!ErreurAdresseFacturation!]]<li>Vous devez choisir une adresse de facturation.</li>[/IF]
					[IF [!ErreurTypeLivraison!]]<li>Vous devez choisir un mode de livraison.</li>[/IF]
				</ul>
			[/BLOC]
		[ELSE]
			// Tout est OK 
			// On raccroche les adresses
			[!Panier::setAdresseLivraison([!Livraison!])!]
			[!Panier::setAdresseFacturation([!Facturation!])!]

			// On raccroche le client
			[!Panier::setClient([!CLCONN::Id!])!]
	
			// on raccroche le bon de promo
			[IF [!ReducOk!]=1]
				[!Panier::setOffreSpeciale([!OSP!])!]
			[/IF]

	
			[!CLCONN::savePanier()!]

			[REDIRECT]Boutique/Commande/Etape4[/REDIRECT]
		[/IF]
	
	[/IF]

	<form action="/[!Lien!]" method="post" id="CommandeEtape3">

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
						<td class="Produit"><a href="/[!Prod::getUrl!]">[!Pan::Titre!]</a><br />
							[IF [!Emballage::ConditionnementDefaut!]!=1](Vendu en [!Emballage::TypeEmballage!])[/IF]
						</td>
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
		<div class="Etape3">
			[IF [!ReducOk!]=1]
				<h2 class="CDE_OFFRESPEC">[!OSP::Nom!]</h2>
			[/IF]
			<div class="LaLivraison">
				<div class="TitreModeLivraison">Mode de livraison</div>
//				<div class="LivraisonDetails">Poids de la commande : [!Panier::Poids!] kg</div>
				<div id="ModesLivraison">
					<div style="text-align:center"><img src="/Skins/[!Systeme::Skin!]/Img/ajax-loader.gif" alt="Chargement..." /></div>
					<noscript>Vous devez activer Javascript pour choisir un mode de livraison</noscript>
				</div>
			</div>
		</div>	
		
		<div class="Etape3">
			<table class="TotalDetaille">
				<tr class="TotalCommande">
					<td class="label">Total commande</td>
					<td class="val" id="TotalCommande" rel="[!Panier::MontantTTC!]">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
				</tr>
				<tr>
					<td class="label">Frais de port</td>
					<td class="val" id="FraisDePort">-</td>
				</tr>
				[IF [!ReducOk!]=1]
					[IF [!OSP::TypeVariation!]=3]
						<tr id="ReductionOffreSpeciale" style="display:none;">
							<td class="label">{ReducDesc}</td>
							<td class="val">- {ReducMontant}</td>
						</tr>
					[ELSE]
						<tr id="ReductionOffreSpeciale" >
							<td class="label">[!OSP::Nom!]</td>
							<td class="val">- [!Math::PriceV([!OSP::getReducMontant([!Panier::MontantTTC!])!])!] [!De::Sigle!]</td>
						</tr>
					[/IF]				
					
				[/IF]
				<tr class="vide">
					<td  colspan="2">&nbsp;</td>
				</tr>
				<tr class="TotalAPayer">
					<td class="label">Total à payer</td>
					//<td class="val"  id="TotalAPayer">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
					<td class="val"  id="TotalAPayer">[!Math::PriceV([!MontantTotalCommande!])!] [!De::Sigle!]</td>
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

	var loadingText = $('ModesLivraison').innerHTML;

	/**
	 * Traitements après le chargement de page
	 * -> Masquer adresses livraison / facturation
	 * -> Chargement des modes de livraison initiaux
	 * -> A chaque changement d'adresse on recharge avec la nouvelle valeur
	 */
	window.addEvent('domready',function () {

		// Masquer autres adresses
		$$('a.ChooseMoreAdresses').setStyle('display','block');

		// Chargement des modes de livraison initiaux
		var livraisonId = -1;
		radioInputs = $$('.AdresseRadioLivraison');
		radioInputs.each(function (item,index) {
			if (item.get('checked')) livraisonId = item.get('value');
		});
		getLivraison( livraisonId );

		// A chaque changement on recharge avec la nouvelle valeur
		var sel = $$('.AdresseRadioLivraison');
		sel.each(function (item,index){
			item.addEvent('click',function (e) {
				getLivraison( item.get('value') );
			})
		})

		// Verification à la soumission du formulaire
		$('CommandeEtape3').addEvent('submit', function(e) {
			if(!verifForm()) new Event(e).stop();
		});

	});

	/**
	 * Vérifie le formulaire avant soumission
	 * @return	void
	 */
	function verifForm() {
		// Tests
		var level1 = false;
		var level2 = false;

		// On doit avoir choisi un mode de livraison
		var radios = $$('input.InputTypeLivraison');
		radios.each( function(item) {
			if(item.checked) {
				level1 = true;
				// Si choix possibles on doit en avoir coché un
				if(item.get('ChoixObligatoire')) {
					var div = item.getParent('div.NomTypeLivraison');
					var table = div.getNext('table.ListeChoixLivraison');
					var choix = table.getElements('input.ChoixLivraison');
					choix.each( function(item2) {
						if(item2.checked) level2 = true;
					});
				}
				else {
					level2 = true;
				}
			}
		});

		if(!level1) {
			alert("Vous devez choisir un mode de livraison...");
			return false;
		}
		if(!level2) {
			alert("Vous devez choisir un complément pour ce mode de livraison...");
			return false;
		}
		return true;
	}

	/**
	 * Met à jour les types de livraison
	 * @param	int		ID de l'adresse de livraison sélectionnée
	 * @return	void
	 */
	function getLivraison ( AdrLivraisonId ) {

		// Affichage chargement
		var place = $('ModesLivraison');
		place.innerHTML = loadingText;

		// On execute la requete      
		var r = new Request.JSON({
			url: "/LivraisonStock/TypeLivraison/GetList.json?Livraison=" + AdrLivraisonId,
			onSuccess: function (json) {

				// On vide l'espace "Mode de livraison"
				place.empty();

				// Affichage de tous les types de livraison
				json.each( function( TypLiv ) {

					// Type livraison
					var bloc = new Element('div', {
						'class': 'BlocTypeLivraison'
					});
					var divTitre = new Element('div', {
						'class': 'NomTypeLivraison'
					}).inject(bloc);
					var inputTL = new Element('input', {
						'type': 'radio',
						'name': 'TypeLivraison',
						'class': 'InputTypeLivraison',
						'value': TypLiv.Id,
						'rel': TypLiv.Prix,
						'loaded' : 0
					}).inject(divTitre).addEvent('click', function() {
						// Met à jour les tarifs
						setFdp(this.get('rel'));
						// Décoche tous les choix de second niveau qui auraient pu être faits
						$$('input.ChoixLivraison').each( function( choix ) {
							choix.checked = false;
						});
						// Récupère les choix possibles
						if(this.get('loaded') == 0) {
							getLivraisonChoices(AdrLivraisonId, this.get('value'), this);
							this.set('loaded', 1);
						}
					});
                    inputTL.set('ChoixObligatoire', TypLiv.ChoixObligatoire);
                    inputTL.set('MsgNonChoisi', TypLiv.MsgNonChoisi);
					var titreTL = new Element('div', {
						'html': TypLiv.Titre,
						'class': 'TitreTypeLivraison'
					}).inject(divTitre);
					var prixTL = new Element('div', {
						'html': TypLiv.Prix + ' [!De::Sigle!]',
						'class': 'PrixTypeLivraison'
					}).inject(divTitre);
					var pDesc = new Element('p', {
						'class': 'DescTypeLivraison',
						'html': TypLiv.Desc
					}).inject(divTitre);
					var divChoices = new Element('div',  {
						'class': 'TypeLivraisonChoices'
					}).inject(bloc);

					// Ajout du type de livraison
					bloc.inject(place);

				});
				if(json.length == 0) {
					place.innerHTML = "Impossible de livrer votre commande à cette adresse.<br /><br />Elle peut être trop lourde, trop encombrante, comporter trop d'articles ou votre adresse ne fait pas partie des zones couvertes.<br /><br />Veuillez nous <a href='/Contact'>contacter</a> pour trouver une solution.";
				}
			}
		}).send();  
	}

	/**
	 * Met à jour les choix possibles pour un type de livraison
	 * @param	int			ID de l'adresse de livraison sélectionnée
	 * @param	int			ID du type de livraison sélectionné
	 * @param	HTMLObject	"input" choisi
	 * @return	void
	 */
	function getLivraisonChoices( AdrLivraisonId, TypeLivraisonId, input ) {

		// Affichage chargement
		var place = input.getParent('div.BlocTypeLivraison').getElement('div.TypeLivraisonChoices');
		place.innerHTML = loadingText;

		var r = new Request.JSON({
			url: "/LivraisonStock/TypeLivraison/GetList2.json?Livraison=" + AdrLivraisonId + "&TypeLivraison=" + TypeLivraisonId,
			onSuccess: function (json) {

				// On masque le chargement
				place.empty();

				// On affiche les résultats
				if(json.length > 0) {
					json.each( function( choixLiv ) {
						if(choixLiv != undefined && choixLiv != null) {
							var ligneChoix = new Element('div').inject(place);
							var inputChoix = new Element('input', {
								'type': 'radio',
								'name': 'ChoixLivraison',
								'class': 'ChoixLivraison',
								'value': choixLiv.Uid,
								'styles': {
									'display':'block',
									'position':'relative',
									'float':'left',
									'margin-left':'20px'
								}
							}).addEvent('click', function() {
								// Coche le parent correspondant
								var choix = this.getParent('div.TypeLivraisonChoices');
								var div = choix.getPrevious('div.NomTypeLivraison');
								var input = div.getElement('input.InputTypeLivraison');
								input.checked = true;
								// Met à jour les tarifs
								setFdp(input.get('rel'));
							}).inject(ligneChoix);
							var tdChoixLibelle = new Element('div', {
								'class': 'LibelleChoixLivraison',
								'html': choixLiv.Libelle,
								'styles': {
									'margin-left':'50px'
								}
							}).inject(ligneChoix);
						}
					});
				} else {
				    if(input.get('ChoixObligatoire')) {
                        place.innerHTML = "<div style='color:red;padding:5px'>"+input.get('MsgNonChoisi')+"</div>";
                        input.checked = false;
                        input.set('loaded', 0);
				    }
				}
			}
		}).send();

	}

	/**
	 * Met a jour les totaux de la commande avec les frais de port
	 * @return	void
	 */
	function setFdp( fdp ) {
		$('FraisDePort').set('html',setPrice(fdp));
		$('TotalAPayer').set('html',setPrice(parseFloat('[!Panier::MontantTTC!]')+parseFloat(fdp)));
		[IF [!ReducOk!]=1]
			//*** mise à jour des frais de livraison
			var model=$('ReductionOffreSpeciale');
			var t=model.get('html');
			[IF [!OSP::TypeVariation!]=3] 
				var json = { 
					ReducDesc:"Frais de Port offerts",
					ReducMontantFloat:fdp,
					ReducMontant:setPrice(fdp)
				};
			[ELSE]
				var json = { 
					ReducDesc:'[JSON][!OSP::Nom!][/JSON]',
					ReducMontantFloat:[!OSP::getReducMontant([!Panier::MontantTTC!])!],
					ReducMontant:setPrice([!OSP::getReducMontant([!Panier::MontantTTC!])!])
				};
			[/IF]
			model.set('html',t.substitute(json));
			model.setStyle('display','table-row');
			
			//Mise a jour du total
			total = parseFloat('[!Panier::MontantTTC!]')+parseFloat(fdp) - parseFloat(json.ReducMontantFloat);
			$('TotalAPayer').set('html',setPrice(total) );
		[ELSE]
			total = parseFloat("[!MontantTotalCde!]");
		[/IF]			
		
		
	}


	/**
	 * Met en forme un tarif (2 chiffres après la virgule + devise)
	 * @param	float	Tarif
	 * @return	Chaine complète
	 */
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

	/**
	 * Afficher autres adresses
	 * @param	object	objet HTML
	 * @param	string	type d'adresse
	 * @return	void
	 */
	function showMoreAdresses( lien, type ) {
		$$('div.AdresseType' + type).each( function(div) {
			div.setStyle('display', 'block');
		});
		lien.setStyle('display','none');
	}

</script>
