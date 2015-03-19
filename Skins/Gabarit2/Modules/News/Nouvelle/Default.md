[STORPROC [!Query!]|N]
	[MODULE Systeme/Structure/Gauche]
	<div id="Milieu">
		[MODULE Systeme/Menu/ImageMenu]
		<div id="Data">
			[MODULE Systeme/Ariane]
			<div id="Options">
				<a href="/Redaction/Affich/SendToFriend?Rubrique=Actualit&eacute; : [!N::Titre!]" title="Envoyer la page &agrave; un ami">Envoyer &agrave; un ami</a>
				<a href="/Actualites/Nouvelle/[!N::Id!]/Imprimer.print" title="Imprimer l'actualit&eacute;">Imprimer</a>
			</div>
			<div class="Article">
				<h1>[!N::Titre!]</h1>
				[IF [!N::Image!]!=]
					<div class="ImgArt">
						<a href="/[!N::Image!].limit.800x800.jpg" class="mb" rel="type:jpg" title="[!N::Titre!]"><img src="/[!N::Image!].limit.120x200.jpg" alt="[!N::Titre!]" title="[!N::Titre!]" /></a>
					</div>
				[/IF]
				<div class=[IF [!N::Image!]!=]"TextImg"[ELSE]"Text"[/IF]>
					[!N::Contenu!]
					[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic|0|10|Id|ASC]
						<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a>
					[/STORPROC]
					[STORPROC News/Nouvelle/[!N::Id!]/Lien|Liens|0|10|Id|ASC]
						<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="Lien">[!Lie::Titre!]</a>
					[/STORPROC]
				</div>
			</div>
			<div class="HistoNews">
				<h4>Historique des actualit&eacute;s :</h4>
				<ul>
					[STORPROC News/Nouvelle|Neu|0|6|Id|DESC]
						<li>
							<a href="/Actualites/Nouvelle/[!Neu::Id!]" title="[!Neu::Titre!]"><span class="Bold">[SUBSTR 75][!Neu::Titre!] [...][/SUBSTR]</span> [DATE d.m.Y][!Neu::tmsCreate!][/DATE]</a>
							
						</li>
					[/STORPROC]
				</ul>
			</div>
		</div>
	</div>
	<div class="Clear"></div>
[/STORPROC]