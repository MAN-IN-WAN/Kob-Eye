[IF [!Chemin!]=]
	[!Chemin:=[!Query!]!]
[/IF]
[STORPROC [!Chemin!]|N|0|1]
	<h2>[!N::Titre!]</h2>
	<div class="UneNews">
		[IF [!N::Image!]!=]
			<div class="UneImage">
				<a href="/[!N::Image!].limit.600x600.jpg" rel="shadowbox;" title="[!N::Titre!]" >
					<img src="/[!N::Image!].limit.220x250.jpg" alt="[!N::Titre!]" title="[!N::Titre!]"/>
				</a>
			</div>
		[/IF]
		<div class="Contenu">
			<p class="Chapo">[!N::Chapo!]</p>
			<p>[!N::Contenu!]</p>
			[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic]
				[IF [!Fic::Type!]=Fichier]
					<a href="[!Domaine!]/[!Fic::URL!]" target="blank" class="lienNewsFichier">[!Fic::Titre!]</a>
				[ELSE]
					<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" target="_blank" class="lienNewsFichier" rel="shadowbox;height=480;width=640" >[!Fic::Titre!]</a>
				[/IF]
			[/STORPROC]
			[STORPROC News/Nouvelle/[!N::Id!]/Lien|L]
				<a href="[IF [!L::URL!]~http][ELSE]/[/IF][!L::URL!]" [IF [!L::URL!]~http] target="_blank"[/IF]  class="lienNewsLien">[!L::Titre!]</a>
			[/STORPROC]
		</div>
	</div>

	[COUNT News/Nouvelle/[!N::Id!]/Image|NbImg]
	[IF [!NbImg!]>1]
		<div id="Diapo" style="overflow:hidden;">
			[STORPROC News/Nouvelle/[!N::Id!]/Image|ImgD|||tmsEdit|DESC]
				<a href="/[!ImgD::URL!].limit.900x900.jpg" rel="shadowbox;" title="[!ImgD::Titre!]" >
					<img src="/[!ImgD::URL!].mini.150x150.jpg" width="150" height="150" alt="[!ImgD::Titre!]" />
				</a>
			[/STORPROC]
		</div>
	[/IF]
[/STORPROC]
