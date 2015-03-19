[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H][/STORPROC]
<div class="block">
	<h3 class="title_block title_block_green">
		Dernières News
	</h3>
	[IF [!NEWSCATEGORIE!]>0]
		[!Requete:=News/Categorie/[!NEWSCATEGORIE!]/Nouvelle/Publier=1!]
	[ELSE]
		[!Requete:=News/Nouvelle/Publier=1!]
	[/IF]
	[STORPROC [!Requete!]|Ne|0|[!NBNEWS!]|Date|DESC]
	<div class="well [IF [!Ne::Url!]=[!H::Value!]] Current [/IF]">
		<div class="media ">
			[IF [!Ne::Image!]!=]
			<a class="pull-left" href="/[!URLNEWS!]/[!Ne::Url!]" style="padding:5px;">
				<img class="media-object" src="/[!Ne::Image!].mini.150x80.jpg" />
			</a>
			[/IF]
			<div class="media-body">
				<h5 class="media-heading">[DATE d.m.Y][!Ne::Date!][/DATE]<br />[!Ne::Titre!]</h5>
				<p>[SUBSTR 90| [...]][!Ne::Contenu!][/SUBSTR]</p>
				<a class="btn btn-primary btn-large pull-right" href="/[!URLNEWS!]/[!Ne::Url!]">En savoir plus</a>
			</div>
		</div>
	</div>
	[/STORPROC]
	<a class="btn btn-danger btn-block" href="/[!URLNEWS!]">Voir toutes les news</a>
</div>
