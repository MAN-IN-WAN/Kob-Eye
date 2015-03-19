<div id="Redaction" >
	[STORPROC [!Chemin!]|Cat|0|1]
		// Titre Categorie
		<h1>[!Cat::Nom!]</h1>
		[IF [!Cat::Description!]!=]<div class="contenuCatDesc">[!Cat::Description!]</div>[/IF]
		[COUNT [!Chemin!]/Article|NbArt]
		[STORPROC [!Chemin!]/Article/Publier=1|Art|||Ordre|ASC]
			<div class="BlocArticle">
				<div class="LigneTitre">
					<div class="TitreArticle"><h3>[!Art::Titre!]</h3></div>
					[IF [!NbArt!]>1]<div class="Buttons nodisplay " style="display:none;">
						[IF [!Art::Contenu!]!=]<button name="Ouvre_[!Art::Id!]" class="OuvreArticle"></button><button  name="Ferme_[!Art::Id!]" class="FermeArticle"></button>[/IF]
					</div>[/IF]
				</div>
				<div class="ContenuArticle " >
					[IF [!Art::Chapo!]!=]
						<div class="Chapo">[!Art::Chapo!]</div>
					[/IF]
					<div class="ArticleEtImg">
						[COUNT [!Query!]/Article/[!Art::Id!]/Image|P]
						[IF [!P!]]
							[STORPROC [!Query!]/Article/[!Art::Id!]/Image|Img|0|1][/STORPROC]
							<div style="float:right;display:block;position:relative;padding-left:10px;">
								<img src="/[!Img::URL!].limit.250x250.jpg" alt="[!Img::Titre!]" />
							</div>
						[/IF]
						<div class="contenuArt">[!Art::Contenu!]</div>
					</div>	
					<div class="enPlus">
						[STORPROC [!Query!]/Article/[!Art::Id!]/Fichier|Fic]
							<a target="_blank" class="lienArticleFic" href="/[!Fic::URL!]" alt="[!Fic::Titre!]" title="[!Fic::Titre!]" >[!Fic::Titre!]</a>
						[/STORPROC]
						[STORPROC [!Query!]/Article/[!Art::Id!]/Lien|Lie]
							<a href="[!Lie::URL!]" alt="[!Lie::Titre!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=externe]target="_blank"[/IF] >[!Lie::Titre!]</a>
							<div class="contenuArt">[!Lie::Commentaires!]</div>
						[/STORPROC]
						<div class="ArticleAutreImg">
							[STORPROC [!Query!]/Article/[!Art::Id!]/Image/Id!=[!Img::Id!]|ImgD]
								<div class="LigneTitre"><img src="/[!ImgD::URL!]" alt="[!ImgD::Titre!]" /></div>
							[/STORPROC]
						</div>
					</div>
				</div>
			</div>
		[/STORPROC]
	[/STORPROC]
	<div class="TitreArticle"><h3>Choisissez votre résidence en cliquant sur voir la résidence..</h3></div>

</div>	
<div id="Residences">
	[MODULE ParcImmobilier/Residence/Client]
</div>