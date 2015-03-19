[COUNT [!Systeme::Menus!]/MenuBas=1&&Affiche=1|NbMen]
// on compte le nombre de colonne 
[!NbCol:=12!]
[!Col:=12!]
[STORPROC [!Systeme::Menus!]/MenuBas=1&Affiche=1|M]
	[COUNT Systeme/Menu/Menu/[!M::Id!]|Nb]
	// on ne tient compte que du niveau supérieur 
	[IF [!Nb!]][ELSE][!NbCol-=1!][/IF]
[/STORPROC]
[!Col-=[!NbCol!]!]
<div class="MenuBas">
	[STORPROC [!Systeme::Menus!]/MenuBas=1&Affiche=1|M]
		<div class="span[!Col!]">
			<ul>
				<li>
					[IF [!M::Url!]~http]
						<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
					[ELSE]
						<a href="/[!M::Url!]" >[!M::Titre!]</a>
						[IF [!M::Alias!]~Redaction]
							[STORPROC [!M::Alias!]/Categorie/Publier=1|SCat|||Ordre|ASC]
								<ul>
									[LIMIT 0|100]
										[!Encours1:=[!Lien!]!]
										[!Encours2:=[!M::Url!]/[!SCat::Url!]!]
										<li [IF [!Encours1!]=[!Encours2!]]class=" Current" [/IF]>
											<a href="/[!M::Url!]/[!SCat::Url!]"  >- [!SCat::Nom!]</a>
											[STORPROC Redaction/Categorie/[!SCat::Id!]/Categorie/Publier=1|SCat2|||Ordre|ASC]
												<ul>
													[LIMIT 0|100]
														<li>
															<a href="/[!M::Url!]/[!SCat::Url!]/[!SCat2::Url!]">- [!SCat2::Nom!]</a>
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
								<ul>
									[LIMIT 0|100]
										[!Encours1:=[!Lien!]!]
										[!Encours2:=[!M::Url!]/[!SCat::Url!]!]
										<li [IF [!Encours1!]=[!Encours2!]]class=" Current" [/IF]>
											<a href="/[!M::Url!]/[!SCat::Url!]" class="[IF [!Pos!]=[!NbResult!]]Last [/IF]" >- [!SCat::Nom!]</a>
										</li>
									[/LIMIT]
								</ul>
							[/STORPROC]
						[/IF]
					[/IF]
				</li>
			</ul>
		</div>
	[/STORPROC]
	// ajout du menu divers et qui sommes nous
	<div class="span[!Col!]">
		[STORPROC Systeme/Menu/MenuBas=1&Affiche=0|M|||Ordre|ASC]
			[IF [!M::Alias!]~Boutique||[!M::Alias!]~Categorie]
			[ELSE]
				[COUNT Systeme/Menu/[!M::Id!]/Menu/MenuBas=1&Affiche=1|Nb]
				[IF [!Nb!]]
					<ul><li>
						[!M::Titre!]
						[STORPROC Systeme/Menu/[!M::Id!]/Menu/MenuBas=1&Affiche=1|SCat|||Ordre|ASC]
							<ul>
								[!Encours1:=[!Lien!]!]
								[!Encours2:=[!M::Url!]/[!SCat::Url!]!]
								<li [IF [!Encours1!]=[!Encours2!]]class=" Current" [/IF]>
									<a href="/[!M::Url!]/[!SCat::Url!]" class="[IF [!Pos!]=[!NbResult!]]Last [/IF]" >- [!SCat::Titre!]</a>
								</li>
							</ul>
						[/STORPROC]
					</li></ul>
				[/IF]
			[/IF]
		[/STORPROC]

	</div>


	
</div>
