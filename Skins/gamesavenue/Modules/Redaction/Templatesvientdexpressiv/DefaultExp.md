[STORPROC [!Query!]|Cat]
	[HEADER]
		[IF [!Cat::Title!]][TITLE][!Cat::Title!][/TITLE][/IF]
		[DESCRIPTION][!Cat::MetaDesc!][/DESCRIPTION]
		<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
	[/HEADER]
	[INFO [!Query!]|Inf]
	[STORPROC [!Inf::Historique!]|H|0|1]
		[!Niv0:=[!H::Value!]!]
	[/STORPROC]
<div style="overflow:hidden;">
	[MODULE Redaction/Structure/Gauche]
	<div id="Milieu" style="margin-left:260px;">
		<div id="Data" style="border-top:0px solid #827152;">
			<div class="FicheClient">
				<h1 style="color:#f29400;border-bottom:1px solid #f29400;">[!Cat::Nom!]</h1>
				<div class="Description">[!Cat::Chapo!]</div>
				[IF [!Cat::Description!]]
					<div class="Description2">[!Cat::Description!]</div>
				[/IF]
				[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|10|Ordre|ASC]
					<h2>[!Art::Titre!]</h2>
					<div class="ArtText">
						<p class="Chapo">[!Art::Chapo!]</p>
						<div style="padding-left:0;">[!Art::Contenu!]</div>
					</div>
					[STORPROC Redaction/Article/[!Art::Id!]/Fichier|Fic|0|100]
						<ul style="padding:0;margin:0;">
						[LIMIT 0|100]
							<li style="background-position:left 5px;border-bottom:1px dashed #3F3F3F;"><a href="/[!Fic::URL!]" title="T&eacute;l&eacute;charger [!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a></li>
						[/LIMIT]
						</ul>
					[/STORPROC]
					[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|100|Id|ASC]
						[IF [!Pos!]>4]
							<a href="/[!Img::URL!].limit.800x600.jpg" rel="lightbox[acc]" title="[!Img::Titre!]" style="display:none"></a>
						[ELSE]
						<div class="ImgArt">
							<img src="/[!Img::URL!]" title="[!Img::Titre!]" alt="[!Img::Titre!]" />
						</div>
						[/IF]
					[/STORPROC]
					[STORPROC News/Nouvelle/[!Art::Id!]/Lien|Liens|0|100|Id|ASC]
						<div class="LiensArt">
							[LIMIT 0|100]
							<a href="[!Liens::URL!]" title="[!Liens::Titre!]" onclick="window.open(this.href); return false;" class="Lien">[!Liens::Titre!]</a>
							[/LIMIT]
						</div>
					[/STORPROC]
					<div  style="margin-bottom:10px;"></div>
				[/STORPROC]
				[COUNT Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|C]
				[IF [!C!]]
					<div id="SousCat">
						<ul>
							[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|0|20]
								<li>
									<img src="/Skins/Expressiv/Img/Boutons/puce_23.jpg" style="padding-top:10px;float:left;margin:0;width:6px;"  alt="puce"/>
									<a  href="/[!Lien!]/[!Cato::Url!]" title="[!Cato::Nom!]" [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]/[!Cato::Url!]] class="ActifDr" [/IF] >[!Cato::Nom!]</a>
								</li>
							[/STORPROC]
						</ul>
					</div>
				[/IF]
			</div>
		</div>
	</div>
</div>
[/STORPROC]