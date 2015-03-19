[STORPROC [!Systeme::Menus!]/MenuHaut=1&Affiche=1|M]
	<ul class="MenuHaut cssMenuHaut">
		[LIMIT 0|100]
			<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] Current [/IF] [IF [!Pos!]=1] First [/IF] [IF [!Pos!]=[!NbResult!]] Last [/IF]">
				[IF [!M::Url!]~http]
					<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
				[ELSE]
					<a href="/[!M::Url!]" >[!M::Titre!]</a>
				[/IF]
			</li>
		[/LIMIT]
	</ul>
[/STORPROC]