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
		<span id="block_cart_expand" class="hidden">&nbsp;</span></h3>
        <div class="marge_block">
            <div class="block_content">
                <ul class="media-list">
                [STORPROC [!Panier::LignesCommandes!]|Ligne]
                    <li class="media">
                        [!Ref:=[!Ligne::RefObject!]!]
                        [!Prod:=[!Ref::Prod!]!]
                        <a class="pull-left" href="[!Prod::getUrl()!]">
                            <img class="media-object" src="/[!Prod::Image!].mini.64x64.jpg" />
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">[!Ligne::Titre!]</h4>
                            <span class="pull-right">[!Math::PriceV([!Ligne::MontantTTC!])!][!De::Sigle!]</span>
                            Quantité: [!Ligne::Quantite!]<br />
                        </div>
                    </li>
                    [NORESULT]
                        <li>Votre panier est vide</li>
                    [/NORESULT]
                [/STORPROC]
                </ul>
            </div>
            <div id="cart_block_list" class="expanded">
                <p id="cart-buttons">
                    <a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]" class="btn btn-success btn-block" title="Checkout" rel="nofollow"><span></span>__CHECKOUT__</a>
                    <a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="btn btn-warning btn-block" title="__VIEW_SHOPPING_CART__" rel="nofollow">__MY_CART__</a>
                    <a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]?Action=Vider" class="btn btn-danger btn-block" title="Checkout" rel="nofollow"><span></span>__EMPTY__</a>
                </p>
            </div>
        </div>
    </div>


    //COMMANDES EN COURS
    [STORPROC [!Cli::getPendingCommandes()!]|Com]
    <!-- MODULE Block cart -->
    <div class="block exclusive block_black">
		<h3 class="title_block">
			__CURRENT_ORDER__
		</h3>
		<div class="block_content">
            <ul class="media-list">
                [LIMIT 0|10]
                <li class="media">
                    <a style="overflow:hidden;margin-bottom: 15px;" href="#">
                        <!-- <img class="media-object" src="/[!Prod::Image!].mini.64x64.jpg" />-->
                        <div class="media-body">
                            <h4 class="media-heading">[!Com::RefCommande!]</h4>
                            <span class="pull-right">[!Math::PriceV([!Com::MontantTTC!])!][!CurrentDevise::Sigle!]</span>
                            Date: [DATE d/m/Y][!Com::DateCommande!][/DATE]<br />
                            [IF [!Com::Prepare!]=0]
                            <span class="badge badge-warning">En cours de préparation</span>
                            [/IF]
                            [IF [!Com::Expedie!]=&&[!Com::Prepare!]=1]
                            <span class="badge badge-success">Commande préparée. En attente de retrait.</span>
                            [/IF]
                            [IF [!Com::Expedie!]=1&&[!Com::Prepare!]=1]
                            <span class="badge badge-info">Commande retirée le [DATE d/m/Y à h:i:s][!Com::ExpedieLe!][/DATE].</span>
                            [/IF]
                        </div>
                    </a>
                    [SWITCH [!Com::getStatus()!]|=]
                        [CASE 1]
                        //<a class="btn btn-success btn-block" href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a>
                        <a class="btn btn-danger btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Annule">Annuler ma commande</a>
                        //<a class="btn btn-protector btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Utiliser">Modifier ma commande</a>
                        [/CASE]
                        [CASE 2]
                        //<a class="btn btn-success btn-block" href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a>
                        <a class="btn btn-danger btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Annule">Annuler ma commande</a>
                        //<a class="btn btn-protector btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Utiliser">Modifier ma commande</a>
                        [/CASE]
                        [CASE 3]
                        //<a class="btn btn-success btn-block" href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]?Com=[!Com::RefCommande!]&action=paiement">Payer ma commande</a>
                        //<a class="btn btn-danger btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Annule">Annuler ma commande</a>
                        //<a class="btn btn-protector btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Utiliser">Modifier ma commande</a>
                        [/CASE]
                    [/SWITCH]
                    </li>
                [/LIMIT]
            </ul>
		</div>
    </div>
    [/STORPROC]


    //AUTRES PANIERS
    [STORPROC [!Cli::getOtherPanier()!]|Com]
    <!-- MODULE Block cart -->
    <div class="block exclusive block_black">
		<h3 class="title_block"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" title="__VIEW_SHOPPING_CART__" rel="nofollow">
			__OTHERS_CARTS__
		</a></h3>
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
							<a  class="btn btn-protector btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]">Utiliser ce panier</a>
							<a  class="btn btn-danger btn-block" href="/[!Lien!]?Com=[!Com::RefCommande!]&Action=Annule">Supprimer ce panier</a>
						</td>
					</tr>
					[/LIMIT]
			</table>
		</div>
	</div>
	<!-- /MODULE Block cart -->
    [/STORPROC]
</form>
