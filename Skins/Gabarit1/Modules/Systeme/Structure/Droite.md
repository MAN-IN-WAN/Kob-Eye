<div id="Droite">
	[IF [!Cata!]]
		[STORPROC Redaction/Categorie/[!Cata!]|Cate]
			<div id="SousCat">
				<h3>[!Cate::Nom!]</h1>
				<ul>
					[STORPROC Redaction/Categorie/[!Cate::Id!]/Categorie/Publier=1|Cato|0|20]
						<li [IF [!Pos!]=[!NbResult!]]style="border:none;"[/IF]>
							<a href="/[!Systeme::CurrentMenu::Url!]/[!Cato::Url!]" title="[!Cato::Nom!]" [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]/[!Cato::Url!]]class="ActifDr"[/IF]>[!Cato::Nom!]</a>
						</li>
					[/STORPROC]
				</ul>
			</div>
		[/STORPROC]
	[/IF]
	[MODULE Systeme/Skin]
	[MODULE Systeme/Newsletter]
	[MODULE News/Colonne]
</div>