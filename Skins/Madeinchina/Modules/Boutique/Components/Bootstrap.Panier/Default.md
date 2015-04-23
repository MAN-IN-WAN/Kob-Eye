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
	<!-- MODULE Block cart -->
	<div id="cart_block" class="block exclusive block_black">
		<h3 class="title_block">
			[IF [!Panier::Valide!]]
				__MY_ORDER__
			[ELSE]
				__MY_CART__
			[/IF]
		<span id="block_cart_expand" class="hidden">&nbsp;</span><span id="block_cart_collapse" >&nbsp;</span></h3>
		<div class="block_content">
//			<table class="Panier" cellspacing="0">
//				<tr class="panierentete">
//					<th class="NomProduit">Produits</th>
//					<th class="Quantite">Qté</th>
//					[IF [!Panier::Valide!]]
//						<th class="SupprimerItem">Total TTC</th>
//					[ELSE]
//						<th class="TotalTTC">Total TTC</th>
//						<th class="SupprimerItem">Sup</th>
//					[/IF]
//					</tr>
//				[STORPROC [!Panier::LignesCommandes!]|Ligne]
//					<tr class="panierligne [IF [!Ligne::Reference!]=[!Reference!]] justAdded [/IF]">
//						<td class="NomProduit"><a href="/[!Ligne::getUrlProduit()!]">[IF [!Pos!]=[!NbResult!]][!Ligne::Titre!][ELSE][!Ligne::Titre!][/IF]</a></td>
//						<td class="Quantite">[!Ligne::Quantite!]</td>
//						[IF [!Panier::Valide!]]
//							<td class="SupprimerItem">[!Math::PriceV([!Ligne::MontantTTC!])!][!De::Sigle!]</td>
//						[ELSE]
//							<td class="TotalTTC">[!Math::PriceV([!Ligne::MontantTTC!])!][!De::Sigle!]</td>
//							<td class="SupprimerItem"><a class="SupprimerItemPanier" href="/[!Lien!]?Sup[]=[!Ligne::Reference!]">X</a></td>
//						[/IF]
//					</tr>
//					[NORESULT]
//						<tr class="panierligne">
//							<td class="NomProduit">Panier vide...</td>
//							<td class="Quantite"></td>
//							<td class="TotalTTC"></td>
//							<td class="SupprimerItem" style="background:none;"></td>
//						</tr>
//					[/NORESULT]
//					[!NbArticle+=[!Ligne::Quantite!]!]
//				[/STORPROC]
//			</table>
            <ul class="media-list">
            [STORPROC [!Panier::LignesCommandes!]|Ligne]
                <li class="media">
                    [!Ref:=[!Ligne::RefObject!]!]
                    [!Prod:=[!Ref::Prod!]!]
                    <a class="pull-left" href="[!Prod::getUrl()!]">
                        <img class="media-object" src="/[!Prod::Image!].mini.64x64.jpg" />
                        <div class="media-body">
                            <h4 class="media-heading">[!Ligne::Titre!]</h4>
                            <span class="pull-right">[!Math::PriceV([!Ligne::MontantTTC!])!][!De::Sigle!]</span>
                            Quantité: [!Ligne::Quantite!]<br />
                        </div>
                    </a>
                </li>
                [NORESULT]
                    <li>Votre panier est vide</li>
            	[/NORESULT]
            [/STORPROC]
            </ul>
        </div>
	
	//COMMANDES EN COURS
	[STORPROC [!Cli::getPendingCommandes()!]|Com]
		<h3 class="title_block"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" title="__VIEW_SHOPPING_CART__" rel="nofollow">
			__CURRENT_ORDER__
		</a><span id="block_cart_expand" class="hidden">&nbsp;</span><span id="block_cart_collapse" >&nbsp;</span></h3>
		<div class="block_content">
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
	[/STORPROC]
	
	
	//AUTRES PANIERS
	[STORPROC [!Cli::getOtherPanier()!]|Com]
		<h3 class="title_block"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" title="__VIEW_SHOPPING_CART__" rel="nofollow">
			__OTHERS_CARTS__
		</a><span id="block_cart_expand" class="hidden">&nbsp;</span><span id="block_cart_collapse" >&nbsp;</span></h3>
		<div class="block_content">
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
	[/STORPROC]
		<div id="cart_block_list" class="expanded">
			<p id="cart-buttons">
				<a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="button exclusive" title="__VIEW_SHOPPING_CART__" rel="nofollow">__MY_CART__</a>
				<a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]" id="button_order_cart" class="button exclusive" title="Checkout" rel="nofollow"><span></span>__CHECKOUT__</a>
			</p>
		</div>
	</div>
	<!-- /MODULE Block cart -->
</form>
