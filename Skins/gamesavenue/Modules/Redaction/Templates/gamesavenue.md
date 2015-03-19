<!-- Redaction/Templates/default-->
<!--- colonne de gauche + le contenu -->
[MODULE Systeme/Structure/Gauche]
<!--- contenu central -->
<div class="colonnecentre" >
	[STORPROC [!Chemin!]|Cat]
		<div class="RedactionnelFond">
			<div class="Categorie"><h1>[!Cat::Nom!]</h1></div>
			[IF [!Cat::Description!]!="]
				<div class="descCateg">[!Cat::Description!]</div>
			[/IF]
			<div class="Article">
				[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|10|Ordre|ASC]
					[IF [!Art::Chapo!]!=]
						<div class="chapoettitre ArtText">
							<div class="chapoleft" >
								<img src="/[!Art::Chapo!]"  />
							</div>
							<h1 style="padding-top:0px;">[!Art::Titre!]</h1>
							<p>[!Art::Contenu!]</p>
						</div>
					[ELSE]
						<h1>[!Art::Titre!]</h1>
						<div class="ArtText"><p>[!Art::Contenu!]</p></div>
					[/IF]
					<div class="ArtText">
						[STORPROC Redaction/Article/[!Art::Id!]/Lien|Li|0|100|Id|ASC]
							//ici mettre les liens avec les flèches
							[LIMIT 0|100]
								<a href="/[!Li::URL!]" title="[!Li::Titre!]" class="ArticleLien">
									[!Li::Titre!]
								</a>
							[/LIMIT]
						[/STORPROC]
					</div>
					<div class="ArtText">
						[STORPROC Redaction/Article/[!Art::Id!]/Fichier|Fic|0|100]
							[LIMIT 0|100]
								<a href="/[!Fic::URL!]" title="T&eacute;l&eacute;charger [!Fic::Titre!]" class="ArticleLien">
									[!Fic::Titre!]
								</a>
							[/LIMIT]
						[/STORPROC]
					</div>
					<div class="ArtText">
						[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|100|Id|ASC]
							<div class="ImgArt" style="overflow:hidden;padding-right:5px;float:left;">
								<img src="/[!Img::URL!].limit.100x100.jpg" title="[!Img::Titre!]" alt="[!Img::Titre!]" />
							</div>
						[/STORPROC]
					</div>
					<div  style="margin-bottom:10px;"></div>
				[/STORPROC]
				[COUNT Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|C]
				[IF [!C!]]
					[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|0|20]
						<h1><a  href="/[!Lien!]/[!Cato::Url!]" title="[!Cato::Nom!]" [IF !Lien!]=[!Systeme::CurrentMenu::Url!]/[!Cato::Url!]] class="ArticleLien" [/IF] >[!Cato::Nom!]</a></h1>
					[/STORPROC]
				[/IF]
			</div>
		</div>
	[/STORPROC]
</div>
[MODULE Systeme/Structure/Droite]
