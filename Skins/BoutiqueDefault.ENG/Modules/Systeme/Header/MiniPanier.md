
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

<div id="topminibasket">
	<div id="header_nav">
		<div id="shopping_cart">
			<span id="cart_block"></span>
			<a class="kenyan_coffee_rg" href="/[!Sys::getMenu(Boutique/Commande/Etape1)!]" title="Votre panier	">Panier: </a>
                        [IF [!Panier::MontantTTC!]>0]
                            <span class="ajax_cart_no_product">[!Utils::getPrice([!Panier::MontantTTC!])!] [!De::Sigle!] </span>
                        [ELSE]
                            <span class="ajax_cart_no_product">(Vide) </span>
                        [/IF]
		</div>
	</div>
</div>
