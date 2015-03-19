[!Chemin:=!]
2 ----->[!Query!]
<div class="ListeNews">
	<h1>[!Systeme::CurrentMenu::Titre!]</h1>
	<div id="ContentCategorie"> 
		[STORPROC [!Query!]|N|0|1]
			[STORPROC News/Categorie/Nouvelle/[!N::Url!]|CatN|0|1][/STORPROC]
			[!Chemin:=News/Categorie/[!CatN::Id!]/Nouvelle/[!N::Url!]!]
			1-+[!Chemin!]<br />
			
		[/STORPROC]
		[MODULE News/Nouvelle/Fiche?Chemin=[!Chemin!]]
	</div>
	[COUNT News/Categorie/[!CatN::Id!]/Nouvelle/Id!=[!N::Id!]&Publier=1|NbNe]
	[IF [!NbNe!]]
		<ul class="ListeNews">
			<h2>Liste des News</h2>
			[STORPROC News/Categorie/[!CatN::Id!]/Nouvelle/Id!=[!N::Id!]&Publier=1|N2|0|100|tmsCreate|DESC]
				[!LienLu:=[!Systeme::CurrentMenu::Url!]/Nouvelle/[!N2::Url!]!]
				<li [IF [!Lien!]=[!LienLu!]||[!current!]=1]class="currentPage"[/IF]>
					<a href="/[!Systeme::CurrentMenu::Url!]/Nouvelle/[!N2::Url!]" class="lienNews">[!N2::Titre!]</a>
				</li>
			[/STORPROC]
		</ul>
	[/IF]		
</div>