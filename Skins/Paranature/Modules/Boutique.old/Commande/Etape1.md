[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
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
	//test du cas formule
	[!Cli::ajouterAuPanier([!Reference!],[!Qte!])!]
[/IF]

// Vider le panier
[IF [!Action!]=Vider mon panier]
	[!Cli::viderPanier()!]
[/IF]

// Récupère le panier du client
[!Panier:=[!Cli::getPanier()!]!]

[IF [!Continue!]]
	[!Men:=[!Systeme::getMenu(Boutique/Magasin/[!Mag::Id!])!]!]
	[IF [!Men!]=Boutique/Magasin/[!Mag::Id!]]
		[REDIRECT][!Domaine!][/REDIRECT]
	[ELSE]
		[REDIRECT][!Domaine!]/[!Men!][/REDIRECT]
	[/IF]
[/IF]

[IF [!Valider!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
//Magasin

[IF [!Replace!]=replace]
	//detection du pack à remplacer
	[!ProdUrl:=[!Panier::replacePackFromBasket([!packreplacement!])!]!]
	//redirection vers la fiche produit préconfigurée
	[REDIRECT][!ProdUrl!][/REDIRECT]
[/IF]

[COMPONENT Boutique/MessagePanier/Default?]
[IF [!Mag::EtapeAffiche!]]
	<div class="EtapesCommande">
		<div class="col-md-3 FondStep1Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]"><span class="FondStep1Active">1 - Panier</span></a></div>
		<div class="col-md-3 FondStep2"><a href="#nogo"><span class="FondStep2">2 - Identification</span></a></div>
		[IF [!Mag::EtapeLivraison!]]<div class="col-md-3 FondStep3"><a href="#nogo"><span class="FondStep3">3 - Livraison</span></a></div>[/IF]
		[IF [!Mag::EtapePaiement!]]<div class="col-md-3 FondStep4"><a href="#nogo"><span class="FondStep4">4 - Paiement</span></a></div>[/IF]
	</div>

[/IF]
<noscript><div id="javascriptehoh">Vous devez activer javascript pour modifier votre panier</div></noscript>
<div class="CommandeEtape1">
	[IF [!Mag::EtapeAffiche!]=0]<div class="row"><div class="col-md-12"><h2>Mon Panier</h2></div></div>[/IF]
	[COUNT [!Panier::LignesCommandes!]|NB]
	[IF [!NB!]>0]
		<form action ="/[!Lien!]" name="Commande" method="post" >
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<tr>
						<th class="gauche">Produit(s)</th>
						<th >Quantité</th>
						<th >Prix initial</th>
						<th >Réduction</th>
						<th >Prix à payer</th>
						<th class="SupprimerItem">Supprimer<br />cet article</th>
					</tr>
					[!MenuBoutique:=[!Systeme::getMenu(Boutique/Categorie)!]!]
					[STORPROC [!Panier::LignesCommandes!]|Pan]
						// Colisage
						[STORPROC Boutique/Produit/Reference/[!Pan::Reference!]|Prod|0|1][/STORPROC]
						[!RefStock:=0!]
						[STORPROC Boutique/Reference/Reference=[!Pan::Reference!]|Re|0|1][/STORPROC]
						[!RefStock:=[!Re::getStockReference!]!]
						[!Emballage:=[!Prod::GetEmballage()!]!]
						[!refObj:=[!Pan::getReference()!]!]
						[!NbUnite:=[!Prod::GetColisage()!]!]
						[IF [!Pan::MontantRemiseTTC!]>0]
							[!montantReduc:=[!Pan::MontantTTC!]!]
							[!montantReduc/=[!Pan::MontantHorsPromoTTC!]!]
							[!montantReduc-=1!]
							[!montantReduc*=100!]
						[/IF]
						<tr class="ReferenceLine" data-ref="[!Pan::Reference!]" data-conf="[!Pan::Config!]">
							<td class="gauche"  >
								<a href="/[!Prod::getUrl!]"><strong>[!Pan::Titre!]</strong></a>[IF [!Emballage::ConditionnementDefaut!]!=1&&[!Emballage::ConditionnementDefaut!]!=](Vendu en [!Emballage::TypeEmballage!])[/IF]				    
								<p>[UTIL BBCODE][!Pan::Description!][/UTIL]</p>
							</td>
							<td >
								[IF [!Pan::TypeProduit!]<4]
								<div class="BoutonMoins"><input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-[!NbUnite!],[!Pan::Quantite!],'[!refObj::Reference!]');" /></div>
								[/IF]
								<div class="LaQuantite">
									[!refObj:=[!Pan::RefObject!]!]
									<input name="Qte[!refObj::Id!]" id="Qte[!refObj::Id!]" class="QteInput" value="[!Pan::Quantite!]"  readonly="readonly" >
								</div>
								[IF [!Pan::TypeProduit!]<4]
								<div class="BoutonPlus"><input type="button" class="InputBtnPlus"  value="+" onclick="CalculQte([!NbUnite!],[!Pan::Quantite!],'[!refObj::Reference!]');"></div>
								[/IF]
							</td>
							<td class="PrixInitial">
								[!Math::PriceV([!Pan::MontantHorsPromoTTC!])!] [!De::Sigle!]
							</td>
							<td >
								[IF [!Pan::MontantRemiseTTC!]>0][!Math::PriceV([!montantReduc!])!] %<br /> soit <br /> - [!Math::PriceV([!Pan::MontantRemiseTTC!])!] [!De::Sigle!][/IF]
							</td>
							<td class="PrixFinal">
								[!Math::PriceV([!Pan::MontantTTC!])!]  [!De::Sigle!]
							</td>
							<td   >
								//<input type="checkbox" name="Sup[]" value="[!Pan::Reference!]" class="Panier_Supr" />
								<a href="#nogo" class="btn btn-gris" style="color:white;padding:0 12px;" onclick="removeLine('[!refObj::Reference!]')">Supp</a>
							</td>
						</tr>
						[!TotGene+=[!Math::PriceV([!Pan::MontantTTC!])!]!]
					[/STORPROC]
				</table>
			</div>
<!--			<div class="row LigneBoutons"><div class="col-md-12">
				<div class="pull-right">
					<input class="btn btn-grisfonce RecalculerPanier" type="submit" name="Recalcul" value="Recalculer le panier" />
				</div>
			</div></div> -->
			<div class="row LigneBoutons"><div class="col-md-12">
				<div class="pull-right Total">
					<span class="MontantTotalCmde">Montant de votre commande</span>
					<span class="MontantTotalTTC">[!Math::PriceV([!Panier::MontantTTC!])!] [!De::Sigle!]</span>
				</div>
			</div></div>
			<div class="row LigneBoutons"><div class="col-md-12">
				[IF [!Mag::TexteLivraison!]!=]
				<div class="TotalNotLines">
					[!Mag::TexteLivraison!]
				</div>
				[/IF]
			</div></div>
			<div class="row LigneBoutons"><div class="col-md-12">
				<div class="pull-right">
					<input class="btn btn-red" type="submit" name="Valider" value="Je valide ma commande" />
				</div>
				<div class="pull-right" style="margin-bottom:30px;">
					<input class="btn btn-gris BoutonContinuer" type="submit" name="Continue" value="Continuer mes achats" />
				</div>
			</div>
		</form>
	[ELSE]
		<div class="row MsgPanierVide"><div class="col-md-12">
			Votre panier est vide.<br />
			<a href="/" >Cliquez ici pour continuer vos achats</a>.
		</div></div>
	[/IF]
	
</div>
	//Détection des packs
	[!Packs:=[!Panier::detectionPacks()!]!]
	[STORPROC [!Packs!]|Pa]
		<h3>Bonnes affaires ! Propositions de packs reprenant les articles de votre commande</h3>
		[LIMIT 0|5]
			<div class="well" style="overflow:hidden;">
				<div class="row">
					<div class="col-md-3">
						<img src="/[!Pa::Image!].mini.200x200.jpg" style="margin:10px;"/>
					</div>
					<div class="col-md-9">
						<div class="pull-right" style="margin-bottom:30px;">
							<h4>[!Pa::getTarif()!] €</h4>
							<form action="" method="post">
								<input type="hidden" name="Replace" value="replace" />
								<input type="hidden" name="packreplacement" value="[!Pa::Id!]" />
								<input class="btn btn-gris BoutonContinuer" type="submit" name="PackReplacement" value="Utiliser cette formule" />
							</form>
						</div>
						<h4>[!Pa::Nom!]</h4>
						<p>[!Pa::Description!]</p>
					//	<b>Produits qui seront remplacées dans votre panier:</b>
					//	<ul>
					//	[!TotalTTC:=0!]
					//	[STORPROC [!Pa::LignesDetection!]|L]
					//		<li><b>[!L::Titre!]</b></li>
					//		[!TotalTTC+=[!L::MontantUnitaireTTC!]!]
					//	[/STORPROC]
					//	</ul>
					//	<b [IF [!TotalTTC!]>[!Pa::getTarif()!]]style="color:red;"[/IF]>Total dans votre panier: [!TotalTTC!] €</b>
					</div>
				</div>
			</div>
		[/LIMIT]
	[/STORPROC]
<script type="text/javascript">
	function CalculQte(PlusMoins,qte,ref) {
		//envoi de la quantité en requete ajax.
		$.ajax({
			type: "GET",
			url: "/Boutique/Commande/getPanier.json",
			data: {
				Qte:PlusMoins,
				Reference:ref
			},
			contentType: "application/json; charset=utf-8",
			dataType: "json"
			
		}).success(function(msg){
			  refreshPanier(msg);
		}).fail(function(msg){
			toastr.error('Une erreur est survenue pendant la modification du panier. veuillez vérifier votre connexion internet ou contactez l\'administrateur.');
		});
	}
	function removeLine(ref) {
		//envoi de la quantité en requete ajax.
		$.ajax({
			type: "GET",
			url: "/Boutique/Commande/getPanier.json",
			data: {
				Sup:[ref]
			},
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			success:function(msg){
			  refreshPanier(msg);
			}
		}).fail(function(msg){
			toastr.error('Une erreur est survenue pendant la modification du panier. veuillez vérifier votre connexion internet ou contactez l\'administrateur.');
		});
	}
	function  refreshPanier(json) {
		if (json.success) toastr.success(json.success);
		if (json.error) toastr.success(json.error);
		switch (json.action){
			case "supprime":
				//on compmare la liste des produits avec la liste retournée
				$('tr.ReferenceLine').each(function (index,item){
					var exists = false;
					for (var i in json.panier) {
						console.log('modification de la ligne ...'+index);
						if (json.panier[i].ref==$(item).attr('data-ref')&&json.panier[i].conf==$(item).attr('data-conf')) {
							exists=true;
						}
					}
					if (!exists) {
						//alors suppression de la ligne
						$(item).remove();
					}
				});
			break;
			case "vider":
				//on vidde le panier
				$('tr.ReferenceLine').each(function (index,item){
					$(item).remove();
				});
			break;
			case "ajout":
				//on modifie toute la ligne
				$('tr.ReferenceLine').each(function (index,item){
					for (var i in json.panier) {
						if (json.panier[i].ref==$(item).attr('data-ref')&&json.panier[i].conf==$(item).attr('data-conf')) {
							console.log('modification de la ligne ...'+index);
							//modification de la quantité
							$(item).find('.QteInput').val(json.panier[i].quantite);
							//modification montant initial
							$(item).find('.PrixInitial').html(json.panier[i].topay);
							//modification du prix à payer
							$(item).find('.PrixFinal').html(json.panier[i].topay);
						}
					}
				});
			break;
		}
		//modification du total
		$('.MontantTotalTTC').html(json.total);
		//modification du panier
		console.log(json);
	}
</script>