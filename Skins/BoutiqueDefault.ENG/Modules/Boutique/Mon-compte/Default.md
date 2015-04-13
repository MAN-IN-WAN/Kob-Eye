<div class="block search">
[IF [!Systeme::User::Public!]=0]
	// Déjà connecté
    <h3 class="title_block">Bienvenu</h3>
    <div class="search">
    	[MODULE Boutique/Mon-compte/Home]
        </div>
[ELSE]
    <h3 class="title_block">Connectez-vous</h3>
	<div class="CommandeEtape2 search">
		[MODULE Systeme/Login/CreaCompte?Redirect=[!Systeme::getMenu(Boutique/Commande/Etape3)!]]
	</div>
[/IF]
</div>
