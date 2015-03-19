[STORPROC [!Query!]|Cat]
	[INFO [!Query!]|Inf]
	[STORPROC [!Inf::Historique!]|H|0|1]
		[!Niv0:=[!H::Value!]!]
	[/STORPROC]
	[MODULE Systeme/Structure/Droite?Cata=[!Niv0!]]
	<div id="Milieu">
		[MODULE Systeme/Ariane]
		<div id="Options">
			<a href="/Redaction/Affich/SendToFriend?Rubrique=[!Cat::Nom!]&amp;TitreMenu=[!Systeme::CurrentMenu::Titre!]" title="Envoyer la page &agrave; un ami">Envoyer &agrave; un ami</a>
			<a href="/Redaction/Categorie/[!Cat::Id!]/Imprimer.print" title="Imprimer la page">Imprimer</a>
		</div>
		<h1><img src="/[!Cat::Icone!]" style="float:left;">[!Cat::Nom!]</h1>
		[IF [!Cat::Description!]]
			<p class="Description">[!Cat::Description!]</p>
		[/IF]
		[!YaArt:=0!]
		[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|20|Ordre|ASC]
			[!YaArt:=1!]
			<div class="Article">
				<h2>[!Art::Titre!]</h2>
				[!YaImg:=0!]
				[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|1|Id|ASC]
					[!YaImg:=1!]
					<div class="ImgArt">
						<a href="/[!Img::URL!].limit.800x600.jpg" rel="lightbox[acc]" title="[!Img::Titre!]" style="float:left;margin:5px;">
							<img src="/[!Img::URL!].limit.120x200.jpg" title="[!Img::Titre!]" alt="[!Img::Titre!]" />
						</a>
					</div>
				[/STORPROC]
				<div [IF [!YaImg!]]class="TextImg"[ELSE]class="Text"[/IF]>
					[IF [!Art::Chapo!]]
						<p class="Chapo">[!Art::Chapo!]</p>
					[/IF]
					[!Art::Contenu!]
					[STORPROC Redaction/Article/[!Art::Id!]/Fichier|Fic]
						<a href="/[!Fic::URL!]" title="T&eacute;l&eacute;charger [!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a>
					[/STORPROC]
					[STORPROC Redaction/Article/[!Art::Id!]/Lien|Lie]
						<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="Lien">[!Lie::Titre!]</a>
					[/STORPROC]
				</div>
			</div>
		[/STORPROC]
		[IF [!YaArt!]]
			<a href="#" title="Revenir en haut de la page" class="HautPage">Haut de page</a>
		[/IF]
		[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|0|20|Id|ASC]
			<h3>Sous-cat&eacute;gories de : [!Cat::Nom!]</h3>
			<ul>
				[LIMIT 0|20]
					<li>
						<a href="/[!Lien!]/[!Cato::Link!]" title="[!Cato::Nom!]">[!Cato::Nom!]</a>
					</li>
				[/LIMIT]
			</ul>
		[/STORPROC]
	</div>
	<div class="Clear"></div>
[/STORPROC]