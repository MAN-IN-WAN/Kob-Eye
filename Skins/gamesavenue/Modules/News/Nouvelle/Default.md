[STORPROC [!Query!]|N]
	[MODULE Systeme/Structure/Gauche]
	<div id="Milieu">
		<div id="Data" style="overflow:hidden;width:inherit;position:relative;display:block;">
			<div class="BlocMiseEnAvant">	
				<div class="BlocTitreMiseEnAvant">
					[MODULE Systeme/Options]
					<div class="TitreRoseCadreRose">[!Systeme::CurrentMenu::Titre!]</div>
				</div>
				[IF [!Cat::Description!]]
					<div class="TitreNoirCadreRose">[!Cat::Description!]</div>
				[/IF]
			</div
			<div class="BlocCadreRose" >
				[!NEnAvant:=[!N::Id!]!]
				<div class="TitreRoseCadreRose"><h1>[!N::Titre!]</h1></div>
				<span class="TitreRoseGaucheGris">Le [DATE d.m.Y][!N::tmsCreate!][/DATE]</span>
				<div class="Clear"></div>									
				[IF [!N::Image!]!=]
					<div class="ImageCadreRose">
						<a href="/[!N::Image!].limit.800x800.jpg" class="mb" rel="type:jpg" title="[!N::Titre!]"><img src="/[!N::Image!].limit.120x200.jpg" alt="[!N::Titre!]" title="[!N::Titre!]" /></a>
					</div>
				[/IF]
				<div class="TexteCadreRose">[!N::Contenu!]</div>				
				[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic|1|10|Id|ASC]
					<div class="PointilleGris">	
						<div class="lienArticleRose">
							<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" >[!Fic::Titre!]</a>
						</div>
						<div class="lienArticlePlusRose">
							<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" >&nbsp;</a>
						</div>
					</div>
				[/STORPROC]
				[STORPROC News/Nouvelle/[!N::Id!]/Lien|Liens|0|10|Id|ASC]
					<div class="PointilleGris">	
						<div class="lienArticleRose">
							<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF]>[!Lie::Titre!]</a>
						</div>
						<div class="lienArticlePlusRose">
							<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF]>&nbsp;</a>
						</div>
					</div>
				[/STORPROC]
			</div>
		</div>
		<div class="HistoNews">
			<div class="TitreRoseCadreRose">Historique des actualit&eacute;s :</div>
			[STORPROC News/Nouvelle|Neu|0|6|Id|DESC]
				[IF [!NEnAvant!] != [!Neu::Id!] ]
					<div class="PointilleGris">	
						<div class="lienArticleRose">
							<a href="/[!Systeme::CurrentMenu::Url!]/[!Neu::Id!]" title="[!Neu::Titre!]" >[SUBSTR 75][!Neu::Titre!] [...][/SUBSTR] [DATE d.m.Y][!Neu::tmsCreate!][/DATE]</a>
						</div>
						<div class="lienArticlePlusRose">
							<a href="/[!Systeme::CurrentMenu::Url!]/[!Neu::Url!]" title="[!Neu::Titre!]" >&nbsp;</a>
						</div>
					</div>
				[/IF]
			[/STORPROC]
		</div>
	</div>
	<div class="Clear"></div>
[/STORPROC]