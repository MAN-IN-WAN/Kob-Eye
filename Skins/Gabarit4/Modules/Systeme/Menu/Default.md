<div id="Menu">
	<ul>
		[STORPROC [!Systeme::Menus!]|Test|0|100|Ordre|ASC]
			[IF [!Test::Affiche!]]
				<li class="[IF [!Lien!]~[!Test::Url!]]Actif[/IF][IF [!Lien!]=Redaction/Templates/Gabarit&&[!Test::Titre!]=Accueil]Actif[/IF]"[IF [!Pos!]=[!NbResult!]]style="border:none;"[/IF]>
					[IF [!Test::Alias!]~Redaction]
						[STORPROC [!Test::Alias!]/Categorie/Publier=1|Cato|0|15|Id|ASC]
							<a title="[!Test::Titre!]" onfocus="this.blur()"  href="#nogo">[SUBSTR 30][!Test::Titre!][/SUBSTR]<!--[if IE 7]><!--></a><!--<![endif]-->
							<!--[if lte IE 6]><table><tr><td><![endif]--><ul>
								[LIMIT 0|100]
									<li>
										<a href="/[!Test::Url!]/[!Cato::Url!]" title="[!Cato::Nom!]" onfocus="this.blur()">[!Cato::Nom!]</a>
									</li>
								[/LIMIT]
							</ul><!--[if lte IE 6]></td></tr></table></a><![endif]-->
							[NORESULT]
								<a href="/[!Test::Url!]" title="[!Test::Titre!]" onfocus="this.blur()">[!Test::Titre!]</a>
							[/NORESULT]
						[/STORPROC]
					[ELSE]
						<a href="/[!Test::Url!]" title="[!Test::Titre!]" onfocus="this.blur()">[!Test::Titre!]</a>
					[/IF]
				</li>
			[/IF]
		[/STORPROC]
	</ul>
	<div class="Clear"></div>
</div>