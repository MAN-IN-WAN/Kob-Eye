[!Mag:=[!CurrentMagasin!]!]
[!De:=[!CurrentDevise!]!]
// Acheteur connecté
[!CLCONN:=[!CurrentClient!]!]

// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]

// Redirection etape 1
[IF [!ModifierCommande!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
[/IF]

// Récupère le panier du client
[!Panier:=[!CLCONN::getPanier()!]!]

// Si rien dans le panier redirection etape 1
[STORPROC [!Panier::LignesCommandes!]|Pan]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
	[/NORESULT]
[/STORPROC]

[!MontantTotalCommande:=[!Panier::MontantTTC!]!]
[!ReducOk:=0!]
[STORPROC [!CLCONN::getOffreSpeciale!]|OSP|0|1]
	[!DEBUG::OSP!]
	[IF [!OSP::MiniAchat!]!=]
		[IF [!OSP::MiniAchat!]<[!Panier::MontantTTC!]]
			[!MontantTotalCommande-=[!OSP::getReducMontant([!Panier::MontantTTC!])!])!]
			[!ReducOk:=1!]
		[/IF]
	[ELSE]
		[!MontantTotalCommande-=[!OSP::getReducMontant([!Panier::MontantTTC!])!])!]
		[!ReducOk:=1!]
	[/IF]
[/STORPROC]


[IF [!Mag::EtapeLivraison!]=0||[!Panier::getMontantLivrable()!]=]
	// PAS DE LIVRAISON ON VA VERS PAIEMENT
	// On raccroche le client
	[!Panier::setClient([!CLCONN::Id!])!]
	[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Type=Livraison|AdrLiv|0|1|tmsCreate|DESC][/STORPROC] 
	[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Type=Facturation|AdrFac|0|1|tmsCreate|DESC][/STORPROC] 
	// On raccroche les adresses
	[METHOD Panier|AddParent][PARAM]Boutique/Adresse/[!AdrLiv::Id!][/PARAM][/METHOD]
	[METHOD Panier|AddParent][PARAM]Boutique/Adresse/[!AdrFac::Id!][/PARAM][/METHOD]
	[!CLCONN::savePanier()!]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]

[ELSE]
        [IF [!Mag::EtapeAffiche!]]
               <div class="EtapesCommande">
                       <div class="span3 FondStep1Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="btn btn-inverse btn-large btn-block"><span class="badge badge-protector">1</span> Panier</a></div>
                       <div class="span3 FondStep2"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">2</span> Identification</a></div>
                       [IF [!Mag::EtapeLivraison!]]<div class="span3 FondStep3"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape3)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">3</span> Livraison</a></div>[/IF]
                       [IF [!Mag::EtapePaiement!]]<div class="span3 FondStep4"><a href="#nogo" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-success">4</span> Paiement</a></div>[/IF]
               </div>
       
       [/IF]
	<div class="CommandeEtape3">
		[IF [!Mag::EtapeAffiche!]=0]<div class="row-fluid"><div class="span12"><h2>Ma livraison</h2></div></div>[/IF]
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
				[IF [!Panier::getMontantLivrable()!]>0]
					[!Erreur:=1!]
					[!ErreurTypeLivraison:=1!]
				[/IF]
			[/IF]
			
			[IF [!Erreur!]]
				[!Msg:=!]
				[IF [!ErreurAdresseLivraison!]][!Msg+=Vous devez choisir une adresse de livraison.<br />!][/IF]
				[IF [!ErreurAdresseFacturation!]][!Msg+=Vous devez choisir une adresse de facturation.<br />!][/IF]
				[IF [!ErreurTypeLivraison!]][!Msg+=Vous devez choisir un mode de livraison.<br />!][/IF]
				<script type="text/javascript">
					toastr.warning('[UTIL ADDSLASHES][!Msg!][/UTIL]');
				</script>

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
	
				[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
			[/IF]
		
		[/IF]
	
		<form action="/[!Lien!]" method="post" id="CommandeEtape3">
			<div class="table-responsive">
				<table class="table table-bordered table-hover Logistique">
					<tr>
						<th class="AdresseLivraison" >Adresse de livraison</th>
						<th class="AdresseFacturation" style="border-right:none;">Adresse de facturation</th>
					</tr>
					<tr>
						<td class="AdresseLivraison gauche">
							[MODULE Boutique/Commande/Adresses?Type=Livraison&CLCONN=[!CLCONN!]]
						</td>
						<td class="AdresseFacturation gauche" style="border-right:none;">
							[MODULE Boutique/Commande/Adresses?Type=Facturation&CLCONN=[!CLCONN!]]
						</td>
					</tr>
				</table>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered table-hover tCommande">
					<tr >
						<th class="gauche">Récapitulatif de votre commande</th>
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
							<td class="gauche"><a href="/[!Prod::getUrl!]"><strong>[!Pan::Titre!]</strong></a><br />
								[IF [!Emballage::ConditionnementDefaut!]!=1&&[!Emballage::ConditionnementDefaut!]!=](Vendu en [!Emballage::TypeEmballage!])[/IF]
								<p>[UTIL BBCODE][!Pan::Description!][/UTIL]</p>
							</td>
							<td class="Quantite" >[!Pan::Quantite!]</td>
							<td class="PrixInitial">[!Math::PriceV([!Pan::MontantHorsPromoTTC!])!] [!De::Sigle!]</td>
							<td class="Remise">[IF [!Pan::MontantRemiseTTC!]>0][!Math::PriceV([!montantReduc!])!] %<br /> soit <br /> - [!Math::PriceV([!Pan::MontantRemiseTTC!])!] [!De::Sigle!][/IF]</td>
							<td class="TotalTTC">[!Math::PriceV([!Pan::MontantTTC!])!]  [!De::Sigle!]</td>
						</tr>
						
						[!TotGene+=[!Math::PriceV([!Pan::MontantTTC!])!]!]
					[/STORPROC]
				</table>
			</div>
			[IF [!ReducOk!]=1]
				<div class="row-fluid"><div class="span12">
					<h2 class="CDE_OFFRESPEC">[!OSP::Nom!]</h2>
				</div></div>
			[/IF]
			<div class="row-fluid">
				<div class="span7"></div>
				<div class="span5">
					<div class="LaLivraison">
						<div class="TitreModeLivraison">Mode de livraison</div>
		//				<div class="LivraisonDetails">Poids de la commande : [!Panier::Poids!] kg</div>
						<div id="ModesLivraison">
							<div style="text-align:center"><img src="/Skins/[!Systeme::Skin!]/Img/ajax-loader.gif" alt="Chargement..." /></div>
							<noscript>Vous devez activer Javascript pour choisir un mode de livraison</noscript>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span7"></div>
				<div class="span5"><div class="table-responsive">
					<table class="table TotalDetaille">
						<tr class="TotalCommande">
							<td class="libelle">Total commande</td>
							<td class="val" id="TotalCommande" rel="[!Panier::MontantTTC!]">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
						</tr>
						<tr>
							<td class="libelle">Frais de port</td>
							<td class="val" id="FraisDePort">-</td>
						</tr>
						[IF [!ReducOk!]=1]
							[IF [!OSP::TypeVariation!]=3]
								<tr id="ReductionOffreSpeciale" style="display:none;">
									<td class="libelle" id="labelport">{ReducDesc}</td>
									<td class="val" id="valport">- {ReducMontant}</td>
								</tr>
							[ELSE]
								<tr id="ReductionOffreSpeciale" >
									<td class="libelle" id="labeloff">[!OSP::Nom!]</td>
									<td class="val" id="valoff">- [!Math::PriceV([!OSP::getReducMontant([!Panier::MontantTTC!])!])!] [!De::Sigle!]</td>
								</tr>
							[/IF]				
							
						[/IF]
                                                <tr class="TotalAPayer">
                                                        <td class="libelle">Total à payer</td>
                                                        //<td class="val"  id="TotalAPayer">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
                                                        <td class="val"  id="TotalAPayer">[!Math::PriceV([!MontantTotalCommande!])!] [!De::Sigle!]</td>
                                                </tr>
					    </table>
				</div></div>
			</div>
			<div class="row-fluid LigneBoutons"><div class="span12">
				<div class="pull-right">
					<input type="submit" class="btn btn-success " name="ValiderCommande" value="Je valide ma commande" />
				</div>
				<div class="pull-right">
					<input type="submit" class="btn btn-protector" name="ModifierCommande" value="Je modifie ma commande" />
				</div>
			</div></div>
	
	
		</form>
	</div>	

[/IF]
// Surcouche JS
<script type="text/javascript">

	var loadingText = $('#ModesLivraison').html();

	/**
	* Traitements après le chargement de page
	* -> Masquer adresses livraison / facturation
	* -> Chargement des modes de livraison initiaux
	* -> A chaque changement d'adresse on recharge avec la nouvelle valeur
	*/
	$(document).ready(function () {

		// Masquer autres adresses
		$('a.ChooseMoreAdresses').css('display','block');

		// Chargement des modes de livraison initiaux
		var livraisonId = -1;
		radioInputs = $('.AdresseRadioLivraison:checked');
		livraisonId =$(radioInputs).val();
		
		[IF [!Panier::getMontantLivrable()!]>0]
			getLivraison( livraisonId );
	
			// A chaque changement on recharge avec la nouvelle valeur
			var sel = $('.AdresseRadioLivraison');
			$.each(sel,function (index,item){
				$(item).click(function (e) {
					getLivraison($(item).val());
				})
			})
	
			// Verification à la soumission du formulaire
			$('#CommandeEtape3').submit(function(e) {
				if(!verifForm()) e.preventDefault();
			});
		[/IF]
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
		var radios = $('input.InputTypeLivraison:checked');
		level1 = true;
		// Si choix possibles on doit en avoir coché un
		if($(radios).prop('ChoixObligatoire')) {
			var div = $(radios).parents('div.NomTypeLivraison');
			var table = $(div).next('table.ListeChoixLivraison');
			var choix = $(table).find('input.ChoixLivraison');
			choix.each( function(item2) {
			$('input.InputTypeLivraison:checked').find
				if($(item2).attr('checked')) level2 = true;
			});
		}
		else {
			level2 = true;
		}

		if(!level1) {
//			alert("Vous devez choisir un mode de livraison...");
			toastr.warning("Vous devez choisir un mode de livraison...");

			return false;
		}
		if(!level2) {
//			alert("Vous devez choisir un complément pour ce mode de livraison...");
			toastr.warning("Vous devez choisir un complément pour ce mode de livraison...");
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
		var place = $('#ModesLivraison');
		place.html(loadingText);

		// On execute la requete      
		var r =$.getJSON("/LivraisonStock/TypeLivraison/GetList.json",{"Livraison":AdrLivraisonId})
		.fail(function (){
			alert('probleme de connexion');
		})
		.done(function(json){
			// On vide l'espace "Mode de livraison"
			place.empty();
			// Affichage de tous les types de livraison
			$.each(json, function( index,TypLiv ) {
				// Type livraison
				place.append('<div class="BlocTypeLivraison"><div class="NomTypeLivraison"><input type="radio" rel="'+TypLiv.Prix+'" value="'+TypLiv.Id+'" name="TypeLivraison" class="InputTypeLivraison" /><div class="TitreTypeLivraison">'+TypLiv.Titre+'</div><div class="PrixTypeLivraison">'+TypLiv.Prix +' [!De::Sigle!]</div><p class="DescTypeLivraison">'+[UTIL ADDSLASHES]TypLiv.Desc[/UTIL]+'</p></div><div class="TypeLivraisonChoices"></div></div>');
				$('.InputTypeLivraison').click(function(){
					// Met à jour les tarifs
					setFdp($(this).attr('rel'));
					// Décoche tous les choix de second niveau qui auraient pu être faits
					$.each($('input.ChoixLivraison'),  function( index, choix ) {
						$(choix).attr('checked',null);
					});
					// Récupère les choix possibles
					if($(this).prop('loaded') == 0) {
						getLivraisonChoices(AdrLivraisonId, $(this).val(), this);
						$(this).prop('loaded', 1);
					}
				});
				$('.InputTypeLivraison').prop('ChoixObligatoire', TypLiv.ChoixObligatoire);
				$('.InputTypeLivraison').prop('MsgNonChoisi', TypLiv.MsgNonChoisi);
				$('.InputTypeLivraison').prop('loaded',0);
			});
			if(json.length == 0) {
				//place.html('[UTIL ADDSLASHES]Impossible de livrer votre commande à cette adresse.<br /><br />Elle peut être trop lourde, trop encombrante, comporter trop d\'articles ou votre adresse ne fait pas partie des zones couvertes.<br /><br />Veuillez nous <a href="/Contact">contacter</a> pour trouver une solution.[/UTIL]');  
				toastr.warning("[UTIL ADDSLASHES]Impossible de livrer votre commande à cette adresse.<br /><br />Elle peut être trop lourde, trop encombrante, comporter trop d\'articles ou votre adresse ne fait pas partie des zones couvertes.<br /><br />Veuillez nous <a href="/Contact">contacter</a> pour trouver une solution.[/UTIL]");
			}
		});
	};  

	/**
	* Met à jour les choix possibles pour un type de livraison
	* @param	int			ID de l'adresse de livraison sélectionnée
	* @param	int			ID du type de livraison sélectionné
	* @param	HTMLObject	"input" choisi
	* @return	void
	*/
	function getLivraisonChoices( AdrLivraisonId, TypeLivraisonId, input ) {

		// Affichage chargement
		var place = $(input).parent().parent().find('div.TypeLivraisonChoices');
		place.html(loadingText);
		var r = $.getJSON("/LivraisonStock/TypeLivraison/GetList2.json",{Livraison:AdrLivraisonId,TypeLivraison:TypeLivraisonId})
		.fail(function (){
			alert('probleme de connexion');
		})
		.done(function(json){

			// On masque le chargement
			$(place).empty();

			// On affiche les résultats
			if(json.length > 0) {
				$.each(json, function( index,choixLiv ) {
					if(choixLiv != undefined && choixLiv != null) {
						$(place).append('<div><input type="radio" name="ChoixLivraison" class="ChoixLivraison" value="'+choixLiv.Uid+'" style="display:block;position:relative;float:left;margin-left:20px;" /><div class="LibelleChoixLivraison" style="margin-left:50px;">'+choixLiv.Libelle+'</div></div>');
						$('.ChoixLivraison').click(function(){
							// Coche le parent correspondant
							var choix = $(this).parents('div.TypeLivraisonChoices');
							var div = $(choix).prev('div.NomTypeLivraison');
							var input = $(div).find('input.InputTypeLivraison');
							$(input).prop('checked','checked');
							// Met à jour les tarifs
							setFdp($(input).attr('rel'));
						});
						
					}
				});
			} else {
			if($(input).attr('ChoixObligatoire')) {
		place.html("<div style='color:red;padding:5px'>"+escape(input.get('MsgNonChoisi'))+"</div>");
		$(input).prop('checked',null);
		$(input).prop('loaded', 0);
			}
			}
		})
	}

	/**
	* Met a jour les totaux de la commande avec les frais de port
	* @return	void
	*/
	function setFdp( fdp ) {
//		$('#FraisDePort').html(setPrice(fdp)); // pour éviter pb d'arrondi
		var oldval =$("#labelport").text();
		var oldval2 =$("#valport").text();
		var oldval3 =$("#labelOff").text();
		var oldval4 =$("#valOff").text();
		$('#FraisDePort').html(" " + fdp +" €");


		$('#TotalAPayer').html(setPrice(parseFloat('[!Panier::MontantTTC!]')+parseFloat(fdp)));
		[IF [!ReducOk!]=1]
			//*** mise à jour des frais de livraison
			var model=$('#ReductionOffreSpeciale');
			var t=model.html();
			[IF [!OSP::TypeVariation!]=3] 
				var json = { 
					ReducDesc:"Frais de Port offerts",
					ReducMontantFloat:fdp,
					ReducMontant:setPrice(fdp)
				};
				$("#labelport").text( $("#labelport").text().replace(oldval, "Frais de Port offerts") );
				$("#valport").text( $("#valport").text().replace(oldval2, "- " + fdp +" €") );

			[ELSE]
				var json = { 
					ReducDesc:'[UTIL ADDSLASHES][!OSP::Nom!][/UTIL]',
					ReducMontantFloat:[!OSP::getReducMontant([!Panier::MontantTTC!])!],
					ReducMontant:setPrice([!OSP::getReducMontant([!Panier::MontantTTC!])!])
				};
				$("#labelport").text( $("#labelport").text().replace(oldval3, [UTIL ADDSLASHES][!OSP::Nom!][/UTIL]) );
				$("#valport").text( $("#valport").text().replace(oldval4, [!OSP::getReducMontant([!Panier::MontantTTC!])!]) +" €") );


			[/IF]

			//model.html(t.substitute(json));

			model.css('display','table-row-fluid');
			
			//Mise a jour du total
			total = parseFloat('[!Panier::MontantTTC!]')+parseFloat(fdp) - parseFloat(json.ReducMontantFloat);
			$('#TotalAPayer').html(setPrice(total));
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
		$.each($('div.AdresseType' + type), function(index,div) {
			$(div).css('display', 'block');
		});
		$(lien).css('display','none');
	}

</script>
