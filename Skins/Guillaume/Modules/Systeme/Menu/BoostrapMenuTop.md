<nav role="navigation" class="topNavigation">
	<!-- Tous les autres formats -->
	<ul class="navbar-nav navbar-top">
		<li class="libUrgence" >Numéro d'urgence </li>
		<li class="NUMTEL" style="border-left:none">18</li>
		<li class="NUMTEL" style="border-left:none">112</li>
		<li>
			[IF [!Systeme::User::Public!]=1]
				<a href="/Connexion" title="Connexion" class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] " >Connexion</a>
			[ELSE]
				<a href="/Systeme/Deconnexion" title="déconnexion" class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] "  >Déconnexion</a>
			[/IF]
		</li>

		[STORPROC [!Systeme::Menus!]/Affiche=1&MenuHaut=1|M|0|10|Ordre|DESC]
			<li>
				[IF [!M::Url!]~http]
					<a href="[!M::Url!]" target="_blank" class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] " >[IF [!M::Icone!]!=]<img src="/[!M::Icone!]" alt="[!M::Titre!]" title="[!M::Titre!]" />[/IF][!M::Titre!]</a>
				[ELSE]
					<a href="/[!M::Url!]" class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] ">[IF [!M::Icone!]!=]<img src="/[!M::Icone!]" alt="[!M::Titre!]" title="[!M::Titre!]" />[/IF][!M::Titre!]</a>
				[/IF]
			</li>
		[/STORPROC]
	</ul>
</nav>