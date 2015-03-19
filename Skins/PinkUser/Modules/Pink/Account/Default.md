[IF [!Systeme::User::Public!]=0]
	// Déjà connecté => gestion du compte
	[MODULE Pink/Account/Home]
[ELSE]
	<div class="CommandeEtape2">
		[MODULE Systeme/Login/CreaCompte?Redirect=[!Systeme::getMenu(Pink/Homepage)!]]
	</div>
[/IF]
