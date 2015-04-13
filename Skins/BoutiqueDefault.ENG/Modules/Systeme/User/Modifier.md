[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::getMenu(Systeme/User)!][/REDIRECT][/IF]
<div class="user">
	<h1 class="moncompte">Modifier mes coordonnées</h1>

	<div class="Identification" style="oveflow:hidden;">

		<div class="ColonneCreationCompte">
			[MODULE Systeme/Login/Inscription?Redirect=[!Systeme::getMenu(Systeme/User)!]]
		</div>
	</div>
</div>