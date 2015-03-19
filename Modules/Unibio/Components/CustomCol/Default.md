<div class="BlocNavigation">
	<div class="EntoureNavigation">
		<div class="TitreNavigation">Espace Pro</div>
	</div>
	<div class="ContenuComposantNavigation">
		[STORPROC Systeme/Menu/[!MENU!]|Men]
			[STORPROC Systeme/Menu/[!MENU!]/Menu/Affiche=1|Men2]
				<a href="/[!Men::Url!]/[!Men2::Url!]" [IF [!Systeme::CurrentMenu::Id!]=[!Men2::Id!]] class="CurrentArbo" [/IF]>[!Men2::Titre!]</a>
			[/STORPROC]
		[/STORPROC]
	</div>
</div>