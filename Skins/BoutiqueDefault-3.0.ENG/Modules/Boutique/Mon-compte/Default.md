<div class="block search">
[IF [!Systeme::User::Public!]=0]
	// Déjà connecté
    <h3 class="title_block">Mon compte</h3>
    <div class="block_content">
    	[MODULE Boutique/Mon-compte/Home]
        </div>
[ELSE]
    <h3 class="title_block">Connectez-vous</h3>
	<div class="block_content">
		[MODULE Systeme/Login/CreaCompte]
	</div>
[/IF]
</div>
