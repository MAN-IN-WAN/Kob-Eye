[IF [!Systeme::User::Public!]=0]
	// Déjà connecté
	[MODULE Boutique/Mon-compte/Home]
[ELSE]
	<div class="CommandeEtape2">
		[MODULE Systeme/Login/CreaCompte?Redirect=[!Systeme::getMenu(Boutique/Commande/Etape3)!]]
	</div>
[/IF]
