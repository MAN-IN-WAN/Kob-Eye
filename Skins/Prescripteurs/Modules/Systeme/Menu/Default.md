[!ActiveMenu:=0!]
[STORPROC [!Systeme::Menus!]/Affiche=1&MenuAlternatif=1|M]
	[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]]
		[!ActiveMenu:=1!]
	[/IF]
[/STORPROC]

<table id="Menu" class="siteWidth">
	<tr>
		[STORPROC [!Systeme::Menus!]/Affiche=1&MenuAlternatif=1|M]
			<td class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] current [/IF] [IF [!ActiveMenu!]=0&&[!Pos!]=1] current [/IF] [IF [!Pos!]=[!NbResult!]] last [/IF]">
				[IF [!M::Url!]~http]
					<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
				[ELSE]
					<a href="/[!M::Url!]">[!M::Titre!]</a>
					[IF [!M::Alias!]~Redaction]
						[STORPROC [!M::Alias!]/Categorie/Publier=1|SCat]
							<ul>
								[LIMIT 0|100]
									<li>
										<a href="/[!M::Url!]/[!SCat::Url!]">[!SCat::Nom!]</a>
									</li>
								[/LIMIT]
							</ul>
						[/STORPROC]
					[/IF]
				[/IF]
			</td>
		[/STORPROC]
	</tr>
</table>