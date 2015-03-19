<div class="Contenu">
	<div class="MenuHaut">
		[IF [!Systeme::User::Public!]=0]
			<div id="userEspace">
				<strong>[!Systeme::User::Nom!] [!Systeme::User::Prenom!]</strong>
				(<a href="/Mon_compte/Deconnexion" title="Déconnexion">Déconnexion</a>)
			</div>
		[/IF]
		<ul class="MenuHautR">
			[STORPROC [!Systeme::Menus!]/Affiche=1&MenuHaut=1|M|0|100|Ordre|ASC]
				<li [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] class="current" [/IF]>
					<a href="/[!M::Url!]" class="[!M::Url!]">[!M::Titre!]</a>
				</li>
			[/STORPROC]
		</ul>
	</div>
</div>
<div class="Bandeau">
	<div class="Contenu">
		<a href="/">
			<img src="/Skins/[!Systeme::Skin!]/Img/img-bando.jpg" alt="Retour à l'accueil" title="Retour à l'accueil" style="border:0"/>
		</a>
		[MODULE Systeme/Menu]
	</div>
</div>