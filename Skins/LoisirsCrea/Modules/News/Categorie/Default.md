[!Chemin:=!]
[STORPROC [!Query!]/Nouvelle/Publier=1&&Date>=[!TMS::Now!]|N|0|1|date|ASC][/STORPROC]
<div class="ListeNews">
	<h1>[!Systeme::CurrentMenu::Titre!]</h1>
	<div id="ContentCategorie">
		[!Chemin:=News/Nouvelle/[!N::Url!]!]
		[MODULE News/Nouvelle/Fiche?Chemin=[!Chemin!]]
	</div>
	[COUNT News/Nouvelle/Publier=1|NbNe]
	[IF [!NbNe!]>1]
		<ul class="ListeNews">
			<h2>Liste des News </h2>
			[STORPROC News/Nouvelle/Publier=1|N2|0|100|Date|ASC]
				[!LienLu:=[!Systeme::CurrentMenu::Url!]/Nouvelle/[!N2::Url!]!]
				<li [IF [!Lien!]=[!LienLu!]||[!current!]=1]class="currentPage"[/IF]>
					<a href="/[!Systeme::CurrentMenu::Url!]/Nouvelle/[!N2::Url!]" class="lienNews">[!N2::Titre!]</a>
				</li>
			[/STORPROC]
		</ul>
	[/IF]		
</div>