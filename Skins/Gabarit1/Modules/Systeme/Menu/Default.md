<div id="Menu">
	<ul>
		[STORPROC [!Systeme::Menus!]|Test|0|100|Ordre|ASC]
			[IF [!Test::Affiche!]]
				<li [IF [!Pos!]=[!NbResult!]]style="border:none;"[/IF]>
					<a href="/[!Test::Url!]" title="[!Test::Titre!]" onfocus="this.blur()" class="[IF [!Lien!]~[!Test::Url!]]Actif[/IF][IF [!Lien!]=Redaction/Templates/Accueil&&[!Test::Titre!]=Accueil]Actif[/IF]">[!Test::Titre!]</a>
				</li>
			[/IF]
		[/STORPROC]
	</ul>
	<div class="Clear"></div>
</div>