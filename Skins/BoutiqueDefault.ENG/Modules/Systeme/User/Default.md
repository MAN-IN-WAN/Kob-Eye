<h1 class="moncompte">Mon Compte</h1>
<div class="user">
	[IF [!Systeme::User::Public!]=0]
		// Déjà connecté
		[MODULE Systeme/User/Home]
	[ELSE]
		// Propose connexion
		<fieldset>
			<legend>Déjà client ?</legend>
			[MODULE Systeme/Login]
		</fieldset>
		<fieldset>
			<legend>Créer mon compte</legend>
			// Propose inscription
			<div class="CommandeEtape2">
				<div class="Identification" style="oveflow:hidden;">
					<div class="ColonneCreationCompte">
						[MODULE Systeme/Login/Inscription]
					</div>
				</div>
			</div>
		</fieldset>
	[/IF]
</div>