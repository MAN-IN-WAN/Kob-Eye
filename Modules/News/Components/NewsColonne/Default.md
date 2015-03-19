[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H][/STORPROC]
<div class="EnteteComposant EnteteNews">
	Dernières News
</div>
<div class="ContenuComposant">
	[STORPROC News/Nouvelle/Publier=1|Ne|0|[!NBNEWS!]|tmsCreate|DESC]
		<div class="News [IF [!Ne::Url!]=[!H::Value!]] CurrentNews [/IF]">
			<strong>[DATE d.m.Y][!Ne::tmsCreate!][/DATE]<br />[!Ne::Titre!]</strong>
			<p>[SUBSTR 45| [...]][!Ne::Contenu!][/SUBSTR]</p>
			<a class="LireLaSuiteNews" href="/Actualites/[!Ne::Url!]">En savoir plus</a>
		</div>
	[/STORPROC]
	<a class="ToutesLesNews" href="/Actualites">Voir toutes les news</a>
</div>
