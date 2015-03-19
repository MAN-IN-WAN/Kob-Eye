<div id="Menu">
	<ul>
		[STORPROC [!Systeme::Menus!]|Men]
			[IF [!Men::Affiche!]]
				<li>
					<a href="/[!Men::Url!]" title="[!Men::Titre!]" onfocus="this.blur()" class="[IF [!Lien!]~[!Men::Url!]]Actif[/IF]">[!Men::Titre!]</a>
				</li>
			[/IF]
		[/STORPROC]
	</ul>
	<div class="Clear"></div>
</div>