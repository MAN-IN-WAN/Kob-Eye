<div id="MenuBarre">
	<div id="MenuGauche"></div>
	<div id="MenuMilieu">
		<div class="menu">
			<ul>
				[STORPROC [!Systeme::Menus!]|Test|0|100|Ordre|ASC]
					[IF [!Test::Affiche!]]
						<li [IF [!Pos!]=[!NbResult!]] style="border:none;"[/IF]>
							<div style="width:100%;" class="[IF [!Lien!]~[!Test::Url!]]BlocActif[/IF][IF [!Lien!]=Redaction/Templates/Accueil&&[!Test::Titre!]=Accueil]BlocActif[/IF]">
								<a href="/[!Test::Url!]" title="[!Test::Titre!]" onfocus="this.blur()" class="[IF [!Lien!]~[!Test::Url!]]Actif[/IF][IF [!Lien!]=Redaction/Templates/Accueil&&[!Test::Titre!]=Accueil]Actif[/IF]">[!Test::Titre!]</a>
							</div>
							[STORPROC [!Test::Alias!]/Categorie/Publier=1|Cato|0|15|Id|ASC]
								<ul>
									[LIMIT 0|100]
										<li [IF [!Pos!]=[!NbResult!]] style="border:none;border:0;"[/IF]>	
											<a href="/[!Test::Url!]/[!Cato::Link!]" title="[!Cato::Nom!]" onfocus="this.blur()" >[!Cato::Nom!]</a>
										</li>
									[/LIMIT]
								</ul>
							[/STORPROC]
						</li>
					[/IF]
				[/STORPROC]
			</ul>
		</div>
	</div>
	<div id="MenuDroite"></div>
</div>
<div class="Clear"></div>
