<!-- Redaction/Templates/default-->
<!--- colonne de gauche + le contenu -->
[MODULE Systeme/Structure/Gauche]
<!--- contenu central -->
<div class="colonnecentre" >
	[STORPROC [!Chemin!]|Cat]
		[HEADER]
			[IF [!Cat::Title!]][TITLE][!Cat::Title!][/TITLE][/IF]
			[DESCRIPTION][!Cat::Description!][/DESCRIPTION]
			<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
		[/HEADER]
		[INFO [!Query!]|Inf]
		[STORPROC [!Inf::Historique!]|H|0|1]
			[!Niv0:=[!H::Value!]!]
		[/STORPROC]
		<div class="Categorie">
			<h1 >[!Cat::Nom!]test</h1>
			<div>[!Cat::Chapo!]</div>
			[IF [!Cat::Description!]]
				[!Cat::Description!]
			[/IF]
			<div class="Article">
				[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|10|Ordre|ASC]
					<h1>[!Art::Titre!]</h1>
					<div class="ArtText">
						<p>[!Art::Chapo!]</p>
						<p>[!Art::Contenu!]</p>
					</div>
					[STORPROC Redaction/Article/[!Art::Id!]/Fichier|Fic|0|100]
						<ul>
						[LIMIT 0|100]
							<li><a href="/[!Fic::URL!]" title="T&eacute;l&eacute;charger [!Fic::Titre!]" class="lienRougeMaj12">[!Fic::Titre!]</a></li>
						[/LIMIT]
						</ul>
					[/STORPROC]
					[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|100|Id|ASC]
						<div class="ImgArt">
							<img src="/[!Img::URL!]" title="[!Img::Titre!]" alt="[!Img::Titre!]" />
						</div>
					[/STORPROC]
					<div  style="margin-bottom:10px;"></div>
				[/STORPROC]
			</div>
			[COUNT Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|C]
			[IF [!C!]]
				<div class="Categorie">
					<ul>
						[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|0|20]
							<li>
								<a  href="/[!Lien!]/[!Cato::Url!]" title="[!Cato::Nom!]" [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]/[!Cato::Url!]] class="lienRougeMaj12" [/IF] >[!Cato::Nom!]</a>
							</li>
						[/STORPROC]
					</ul>
				</div>
			[/IF]
		</div>
	[/STORPROC]
</div>
[MODULE Systeme/Structure/Droite]
