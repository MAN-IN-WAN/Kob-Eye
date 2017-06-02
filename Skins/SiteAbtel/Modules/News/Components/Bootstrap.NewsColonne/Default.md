[STORPROC News/Categorie|Cat]
	[!Tab[!Cat::Id!]:=0!]
[/STORPROC]
[IF [!NEWSCATEGORIE!]>0]
	[!Requete:=News/Categorie/[!NEWSCATEGORIE!]/Nouvelle/Publier=1!]
[ELSE]
	[!Requete:=News/Nouvelle/Publier=1!]
[/IF]
<div class="[!NOMDIV!]">
	[STORPROC [!Requete!]|Ne|0|[!NBNEWS!]|Date|DESC]
		[STORPROC News/Categorie/Nouvelle/[!NE::Id!]|Cat|0|1]
			[IF [!Tab[!Cat::Id!]!]=0]
				<h4>[!Cat::Nom:]</h4>
				[!Tab[!Cat::Id!]:=1!]
			[/IF]
		[/STORPROC]
		<div class="UneNews">
			[IF [!Ne::Image!]!=]
				<a class="pull-left" href="/[!URLNEWS!]/[!Ne::Url!]" style="padding:5px;">
					<img class="media-object" src="/[!Ne::Image!].mini.150x80.jpg" />
				</a>
			[/IF]
			<div class="contenuNews">
				<h5>[!Ne::Titre!]</h5>
				<p>[SUBSTR 90| [...]][!Ne::Contenu!][/SUBSTR]</p>
				<a class="add-on pull-right" href="/[!URLNEWS!]/[!Ne::Url!]">></a>
			</div>
		</div>
	[/STORPROC]
	[IF [!URLNEWS!]]<a class="btn btn-danger btn-block" href="/[!URLNEWS!]">Voir toutes les news</a>[/IF]
</div>
