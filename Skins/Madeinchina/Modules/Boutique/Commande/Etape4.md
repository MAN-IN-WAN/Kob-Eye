[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]

// Redirection etape 1
[IF [!ModifierCommande!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
[/IF]
// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

// Récupère le panier du client
[!Panier:=[!CLCONN::getPanier()!]!]
//CHECK PANIER
[STORPROC [!Panier::LignesCommandes!]|Pan]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
	[/NORESULT]
[/STORPROC]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]

//VERIFICATION ADRESSE LIVRAISON
[IF [!Mag::EtapeLivraison!]]
	[IF [!Panier::getAdresseLivraison()!]=||[!Panier::getAdresseFacturation()!]=]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape3)!][/REDIRECT]
	[/IF]
[/IF]
[!MontantTotalCommande:=[!Panier::MontantTTC!]!]
[!ReducOk:=0!]

//GESTION OFFRE SPECIALE
[!OFFRESPECIALE:=[!Panier::getOffreSpeciale()!]!]
[IF [!OFFRESPECIALE!]]
	[!MontantTotalCommande-=[!OFFRESPECIALE::getReducMontant([!Panier::MontantTTC!])!])!]
	[!ReducOk:=1!]
[/IF]
[IF [!Mag::EtapePaiement!]=0]
	// PAS DE PAIEMENT ON CONFIRME ET BASTA
	[IF [!Panier::checkAndBuild()!]]
		[METHOD Panier|setValid][/METHOD]
	[/IF]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape5)!][/REDIRECT]
[ELSE]

        [IF [!Mag::EtapeAffiche!]]
               <div class="EtapesCommande">
                       <div class="span3 FondStep1Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="btn btn-inverse btn-large btn-block"><span class="badge badge-protector">1</span> Panier</a></div>
                       <div class="span3 FondStep2"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">2</span> Identification</a></div>
                       [IF [!Mag::EtapeLivraison!]]<div class="span3 FondStep3"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape3)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">3</span> Livraison</a></div>[/IF]
                       [IF [!Mag::EtapePaiement!]]<div class="span3 FondStep4"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">4</span> Paiement</a></div>[/IF]
               </div>
       
       [/IF]
        <div class="CommandeEtape4">
		[IF [!Mag::EtapeAffiche!]=0]<div class="row"><div class="col-md-12"><h2>Mon paiement</h2></div></div>[/IF]
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
				[!Msg:=!]
				[IF [!ErreurAdresseLivraison!]][!Msg+=Vous devez choisir une adresse de livraison.<br />!][/IF]
				[IF [!ErreurAdresseFacturation!]][!Msg+=Vous devez choisir une adresse de facturation.<br />!][/IF]
				[IF [!ErreurCGV!]][!Msg+=Vous devez accepter les conditions de vente en bas de page.<br />!][/IF]
				<script type="text/javascript">
					toastr.warning('[UTIL ADDSLASHES][!Msg!][/UTIL]');
				</script>

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
					[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
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
				[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4b)!][/REDIRECT]
			[/IF]
		
		[/IF]
	
		<form action="/[!Lien!]" method="post">
			<div class="table-responsive">
				<table class="table table-bordered Logistique">
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
							[!AdrFac::Adresse!] <br />
							[!AdrFac::CodePostal!] [!AdrFac::Ville!] [!AdrFac::Pays!]<br />
				
						</td>
					</tr>
				</table>
			</div>	
			<div class="table-responsive">
				<table class="table table-bordered tCommande">
					<tr >
						<th class="Produit gauche">Récapitulatif de votre commande</th>
						<th class="Qualite">Quantité</th>
						<th class="Qualite">Prix initial</th>
						<th class="Qualite">Réduction</th>
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
							<td class="Produit gauche"><a href="/[!Prod::getUrl!]"><strong>[!Pan::Titre!]</strong></a><br />[IF [!Emballage::ConditionnementDefaut!]!=1][/IF]
								<p>[UTIL BBCODE][!Pan::Description!][/UTIL]</p>
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
			
			//<div class="row"><div class="col-md-12">
				//<table class="table table-bordered  TCodePromo">
					//<tr>
						//<th colspan="2" >Code Promo</th>
					//</tr>
					//<tr>
						//<td><input type="text" name="CodePromo" id="CodePromo" value="[!CodePromo!]" /></td>
						//<script type="text/javascript">
							//$(document).ready(function() {
								//[IF [!CodePromo!]!=]
									//RenvoieCodePromo( true );
								//[/IF]
								//$('#CodePromo').keydown(function(e) {
									//if(e.code == 13) {
										//$('#CodePromo').stop();
										//RenvoieCodePromo();
									//}
								//});
							//});
						//</script>
						//<td><input type="button" id="inputCodePromo" value="OK" onclick="RenvoieCodePromo();"/></td>
					//</tr>
				//</table>
			//</div></div>	
	
			<div class="row-fluid">
                            <div class="offset7 span5"><div class="table-responsive">
                                    <table class="table TotalDetaille">
                                        <tr>
                                                <td class="libelle">Total articles (prix initial)</td>
                                                <td class="val">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</td>
                                        </tr>
                                        <tr>
                                                <td class="libelle">Frais de port</td>
                                                <td class="val" id="FraisDePort">[!Math::PriceV([!BLivr::MontantLivraisonTTC!])!] [!De::Sigle!]</td>
                                        </tr>
        
                                        [IF [!Panier::Remise!]>0]
                                                <tr class="Reduction">
                                                        <td class="libelle">Total réduction(s)</td>
                                                        <td class="val">- [!Math::PriceV([!Panier::Remise!])!] [!De::Sigle!]</td>
                                                </tr>
                                        [/IF]
        
                                        [IF [!ReducOk!]=1]
                                                <tr id="ReductionOffreSpeciale" >
                                                        <td class="libelle" id="labelport">[!OFFRESPECIALE::Nom!]</td>
                                                        <td class="val" id="valport">- [!Math::PriceV([!OFFRESPECIALE::getReducMontant([!Panier::MontantTTC!])!])!] [!De::Sigle!]</td>
                                                </tr>
                                        [ELSE]
                                                <tr id="ReductionOffreSpeciale" style="display:none;">
                                                        <td class="libelle" id="labelport"></td>
                                                        <td class="val" id="valport"></td>
                                                </tr>
                                        [/IF]
                                        <tr class="TotalAPayer">
                                            <td class="libelle">Total à payer</td>
                                            <td class="val"  id="TotalAPayer">[!Math::PriceV([!Panier::MontantPaye!])!] [!De::Sigle!]</td>
                                        </tr>
                                    </table>
                            </div>
                            </div></div>
			[COUNT Boutique/TypePaiement/Actif=1|NBmp]	
			[IF [!NBmp!]>0]
				<div class="row-fluid"><div class="offset7 span5">
					<table class="table  table-bordered  ModeDePaiement">
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
								//<td >[IF [!MP::Logo!]!=]<img src="/[!MP::Logo!]" title="[!MP::Nom!]" alt="[!MP::Nom!]" >[/IF]</td>
								<td class="ChoixPaiement" style="text-align:center;border-right:none;"><input type="radio" name="ModePaiement" value="[!MP::Id!]" [IF [!ModePaiement!]=[!MP::Id!]] checked="checked" [/IF] /></td>
							</tr>
						[/STORPROC]
					</table>
				</div></div>
			[/IF]
			<div class="row-fluid"><div class="span5 offset7">
				<div class="AccepterCGV">
					<input type="checkbox" name="AccepterCGV" value="1" [IF [!AccepterCGV!]] checked="checked" [/IF] [IF [!ErreurCGV!]]class="Error"[/IF] />
					J'accepte les <a href="/Conditions-generales-de-vente" onclick="window.open(this.href);return false;">conditions générales</a> de vente
				</div>
			</div></div>
			<div class="row-fluid LigneBoutons"><div class="span12">
				<div class="pull-right">
					<input type="submit" class="button ConfirmerPaiement" name="ConfirmerPaiement" value="Je confirme mon paiement" />
				</div>
				<div class="pull-right">
					<input type="submit" class="button ModifierCommande" name="ModifierCommande" value="Je modifie ma commande" />
				</div>
			</div></div>
	
	
		</form>
	</div>	
[/IF]
// Surcouche JS
<script type="text/javascript">

	var source;
	var t;
	var model;
	$(document).ready(function () {
		//On recupere le bloc promo dans total
		model = $('#ReductionCodePromo');
		source=model.html();

	});

	/**
	* gestion du code promo on vérifie si le code taper existe et on renvoie le montant de la promotion
	*/
	function RenvoieCodePromo ( noError ) {
		//initialisation
		var total = 0;
		var req = {};
		var CodePromo= $('#CodePromo').val();
		req['CodePromo'] = CodePromo;
			//On execute la requete
			var r = $.getJSON('/Boutique/Commande/GetReducCodePromo.json',req)
			.fail(function (){
				toastr.error('probleme de connexion');
			})
			.done(function(json,text){
				//Montant reduction 
				json.ReducMontant = setPrice(json.ReducMontant);
				if (json.PortOffert) json.ReducMontant =Math.round([!BLivr::MontantLivraisonTTC!]*100)/100 ;

				if(json.ReducOk) {
					t = source;
					toastr.success(json.Message+': '+json.ReducDesc);
					$("#labelport").text( json.ReducDesc );
					$("#valport").text( "- " + json.ReducMontant +" €");
					$("#ReductionOffreSpeciale").css('display','table-row');
				}
				else {
					$('#CodePromo').val("");
					toastr.warning(json.Message);
				}

				//Mise a jour du total
				total = parseFloat("[!MontantTotalCde!]");
				// ne sert plus car on met à jour reducmontant au dessus
				if(json.PortOffert) {
					total = [!Panier::MontantTTC:-[!Panier::Remise!]!];
				}else {
					total = total - parseFloat(json.ReducMontant);
				}
				$('#TotalAPayer').html(setPrice(total) + " [!De::Sigle!]");
			})
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