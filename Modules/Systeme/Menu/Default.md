[STORPROC [!Systeme::Menus!]/MenuPrincipal=1&Affiche=1|M]
	<ul class="Menu0 cssMenu">
			[LIMIT 0|100]
				<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] Current [/IF] [IF [!Pos!]=1] First [/IF] [IF [!Pos!]=[!NbResult!]] Last [/IF]">
					[IF [!M::Url!]~http]
						<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
					[ELSE]
						<a href="/[!M::Url!]" >[!M::Titre!]</a>
						[IF [!M::Alias!]~Redaction]
							[STORPROC [!M::Alias!]/Categorie/Publier=1|SCat]
								<ul class="Menu1 cssMenu">
									[LIMIT 0|100]
										<li>
											<a href="/[!M::Url!]/[!SCat::Url!]">[!SCat::Nom!]</a>
											[STORPROC Redaction/Categorie/[!SCat::Id!]/Categorie/Publier=1|SCat2]
												<ul class="Menu2 cssMenu">
													[LIMIT 0|100]
														<li>
															<a href="/[!M::Url!]/[!SCat::Url!]/[!SCat2::Url!]">[!SCat2::Nom!]</a>
														</li>
													[/LIMIT]
												</ul>
											[/STORPROC]
										</li>
									[/LIMIT]
								</ul>
							[/STORPROC]
						[/IF]
					[/IF]
				</li>
			[/LIMIT]
	</ul>
[/STORPROC]