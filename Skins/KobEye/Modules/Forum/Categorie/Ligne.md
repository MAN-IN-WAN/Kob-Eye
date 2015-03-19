[STORPROC [!Query!]/Categorie|Cat|0|100|Id|ASC]
	<div>
		[IF [!Systeme::User::Admin!]]
			<div class="FRight" style="width:130px;">
				[BLOC Bouton|width:120px;margin-right:10px;||width:85px;]
					<a href="/[!Query!]?act=newCat&referer=[!Lien!]">Ajouter</a>
				[/BLOC]
			</div>
		[/IF]
		<h2 class="CatForum">[!Cat::Nom!]</h2>
		<div class="Clear"></div>
	</div>
	[LIMIT 0|100]
		<div class="SsCatForum">
			[MODULE Forum/Categorie/[!Cat::Id!]/Ligne]
		</div>
	[/LIMIT]
	[NORESULT]
		<table class="LigneForum">
			<tr>
				<td>
					<h2 class="CatForum">
						<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]" title="[!Cat::Nom!]">[!Cat::Nom!]</a>
					</h2>
				</td>
				[COUNT Forum/Categorie/[!Cat::Id!]/Sujet|NbSuj]
				// On regarde s il y a déjà des sujets dans la catégorie
				<td style="width:80px;">
					[IF [!NbSuj!]>0]
						[!NbSuj!] Sujet(s)
					[ELSE]
						Pas de sujet
					[/IF]
				</td>
				<td style="width:80px;">
					[STORPROC Forum/Categorie/[!Cat::Id!]/Sujet/*/Post|Pst]
						[!NbPost:=[!NbResult!]!]
						[LIMIT 0|1]
							[IF [!NbPost!]!=1]
								[!NbPost!] message(s)
							[ELSE]
								Pas de message
							[/IF]
						[/LIMIT]
						[NORESULT]
							Pas de message
						[/NORESULT]
					[/STORPROC]
				</td>
				[IF [!Systeme::User::Admin!]]
					<td style="width:50px;">
						[IF [!NbSuj!]=0]
							// S il n y a pas de sujets, alors on peut ajouter une catégorie
							<a href="/[!Query!]?act=newCat&referer=[!Lien!]" class="LienAjou"></a>
						[/IF]
						<a href="/[!Systeme::CurrentMenu::Url!]?act=suppCat&chemin=[!Query!]" class="LienSupp" [IF [!NbSuj!]=0]style="float:left;"[/IF]></a>
					</td>
				[/IF]
			</tr>
		</table>	
	[/NORESULT]
[/STORPROC]