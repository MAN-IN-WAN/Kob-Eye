[STORPROC [!Systeme::Menus!]/MenuPrincipal=1&Affiche=1|M]
	<ul class="Menu0 cssMenu">
		[LIMIT 0|100]
			<li class="Menu0_[!Pos!] [IF [!Pos!]=1] First [/IF] [IF [!Pos!]=[!NbResult!]] Last [/IF]">
				[IF [!M::Url!]~http]
					<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
				[ELSE]
					<a href="/[!M::Url!]" >[!M::Titre!]</a>
					[IF [!M::Alias!]~Redaction]
						[STORPROC [!M::Alias!]/Categorie/Publier=1|SCat|||Ordre|ASC]
							<ul class="Menu1 cssMenu Menu1_[!Pos!]">
								[LIMIT 0|100]
									<li >
										<a href="/[!M::Url!]/[!SCat::Url!]" [IF [!Pos!]=[!NbResult!]]class=" Last" [/IF] >[!SCat::Nom!]</a>
										[STORPROC Redaction/Categorie/[!SCat::Id!]/Categorie/Publier=1|SCat2|||Ordre|ASC]
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
					[IF [!M::Alias!]~Boutique]
						[STORPROC [!M::Alias!]/Categorie/Actif=1|SCat|||Ordre|ASC]
							
							<ul class="Menu1 cssMenu Menu1_[!Pos!] ">
								[LIMIT 0|100]
									[!Encours1:=[!Lien!]!]
									[!Encours2:=[!M::Url!]/[!SCat::Url!]!]
									<li [IF [!Encours1!]=[!Encours2!]]class=" Current" [/IF]>
										<a href="/[!M::Url!]/[!SCat::Url!]" class="[IF [!Pos!]=[!NbResult!]]Last [/IF]" >[!SCat::Nom!]</a>
										//[STORPROC Boutique/Categorie/[!SCat::Id!]/Categorie/Actif=1|SCat2|||Ordre|ASC]
										//	<ul class="Menu2 cssMenu">
										//		[LIMIT 0|100]
										//			<li>
												//		<a href="/[!M::Url!]/[!SCat::Url!]/[!SCat2::Url!]">[!SCat2::Nom!]</a>
										//			</li>
										//		[/LIMIT]
										//	</ul>
										//[/STORPROC]
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