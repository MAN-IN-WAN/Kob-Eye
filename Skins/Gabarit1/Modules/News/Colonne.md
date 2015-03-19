<div id="NewsColonne">
	<h3>Affichage derni&egrave;res actualit&eacute;s</h3>
	[STORPROC News/Categorie/1|Cat]
		[STORPROC News/Categorie/[!Cat::Id!]/Nouvelle/Publier=1|N|0|3|tmsEdit|DESC]
			<div class="BlocActu">
				[IF [!N::Image!]!=]
					<div class="ImgActu">
						<a href="/News/Nouvelle/[!N::Id!]" title="Acc&egrave;s actualit&eacute;"><img src="/[!N::Image!].limit.100x80.jpg" alt="[!N::Titre!]" title="[!N::Titre!]" /></a>
					</div>
					<div class="TextActu">
				[/IF]
				<h4>[!N::Titre!]</h4>
				<p>[SUBSTR 200][!N::Contenu!][/SUBSTR] (...)</p>
				<a href="/Actualites/Nouvelle/[!N::Id!]" title="Acc&egrave;s actualit&eacute;">Lire la suite...</a>
				[IF [!N::Image!]!=]
					</div>
					<div class="Clear"></div>
				[/IF]
			</div>
			<hr />
		[/STORPROC]
	[/STORPROC]
	<a href="/Actualites" title="Lien vers toutes les actus">Lien vers toutes les actualit&eacute;s</a>
</div>