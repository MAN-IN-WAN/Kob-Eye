// Modele qui n'affiche pas le contenu des articles en entier
[INFO [!Lien!]|I]
//Recherche du menu racine
[STORPROC [!I::Historique!]|LaRacine|0|1][/STORPROC]

<div class="row-fluid">
	[STORPROC [!Query!]|Cat]
		// AFFICHAGE DE LA CATEGORIE PRINCIPALE
		<div class="Redaction">
			<div class="well">
				[IF [!Cat::Icone!]]
					<img src="/[!Cat::Icone!].mini.300x150.jpg" class="pull-left thumbnail" style="margin-right:10px;"/>
				[/IF]
				<h1>[!Cat::Nom!]</h1>
				<blockquote>[!Cat::Description!]</blockquote>
			</div>
			[STORPROC [!Query!]/Article/Publier=1|Art|0|100|Ordre|ASC]
				<div class="Article CategBordureP well">
					[IF [!Art::AfficheTitre!]]
						<div class="TitreArticle">
							<h2>[!Art::Titre!]</h2>
						</div>
					[/IF]
					<div class="ArticleAvecImage">
						[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Dessus|ArtImg]
							<div class="ImageArticleDessusDessous">
								[LIMIT 0|10]
									<a href="/[!ArtImg::URL!]" title="[!Art::Titre!]" class="mb" rel="[[!Art::Id!]]">
										<img src="/[!ArtImg::URL!].limit.400x500.jpg" />
									</a>
								[/LIMIT]
							</div>
						[/STORPROC]
						[!QuelDiv:=!][!QuelDiv2:=!]
						[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Gauche|ArtImg]
							[!QuelDiv:=Gauche!]
							<div class="ImageArticleGauche">
								[LIMIT 0|10]
									<div>
										<a href="/[!ArtImg::URL!]" title="[!Art::Titre!]" class="mb" rel="[[!Art::Id!]]">
											<img src="/[!ArtImg::URL!].limit.150x150.jpg" />
										</a>
									</div>
								[/LIMIT]
							</div>
						[/STORPROC]
						[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Droite|ArtImg]
							[!QuelDiv2:=Droite!]
							<div class="ImageArticleDroite" >
								[LIMIT 0|10]
									<div>
										<a href="/[!ArtImg::URL!]" title="[!Art::Titre!]" class="mb" rel="[[!Art::Id!]]">
											<img src="/[!ArtImg::URL!].limit.150x150.jpg" />
										</a>
									</div>
								[/LIMIT]
							</div>
						[/STORPROC]
						[IF [!Art::Contenu!]!=]
							<p>
								[!Art::Contenu!]
							</p>
						[/IF]
						[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Dessous|ArtImg]
							<div class="ImageArticleDessusDessous">
								[LIMIT 0|10]
									<a href="/[!ArtImg::URL!]" title="[!Art::Titre!]" class="mb" rel="[[!Art::Id!]]">
										<img src="/[!ArtImg::URL!].limit.400x500.jpg" />
									</a>
								[/LIMIT]
							</div>
						[/STORPROC]
					</div>
					
					// Partie lien
					[STORPROC Redaction/Article/[!Art::Id!]/Lien|ArtLie]
						<div class="LienArticle"><a href="/[!ArtLie::URL!]"  class="lelienarticle">[!ArtLie::Titre!]</a></div>
					[/STORPROC]
	
					// Partie fichier et vidéo
					[STORPROC Redaction/Article/[!Art::Id!]/Fichier/Type=Fichier|ArtFic]
						<div class="LienArticle"><a href="/[!ArtFic::URL!]" class="lelienarticle">[!ArtFic::Titre!]</a></div>
					[/STORPROC]
					[STORPROC Redaction/Article/[!Art::Id!]/Fichier/Type=VideoSwf|ArtVidSwf|0|1]
						<div class="TitreArticle">
							<h2>[!ArtVidSwf::Titre!]</h2>
						</div>
						<div class="Video">
							<div id="VideoVpoSwf[!ArtVidSwf::Id!]">
								<script type="text/javascript">
									new Swiff('[!Domaine!]/[!ArtVidSwf::URL!]', {
										container: $('VideoVpoSwf[!VideoVpoSwf::Id!]'),
										width: 640,
										height: 480
									});
								</script>
							</div>
						</div>
					[/STORPROC]
					[STORPROC Redaction/Article/[!Art::Id!]/Fichier/Type=VideoFlv|ArtVidFlv|0|1]
						<div class="TitreArticle">
							<h2>[!ArtVidFlv::Titre!]</h2>
						</div>
						<div class="Video">
							<div id="VideoVpoFlv[!ArtVidFlv::Id!]">
								<script type="text/javascript">
									new Swiff('/Skins/[!Systeme::Skin!]/Images/mb_Components/Files/flvplayer.swf', {
										container: $('VideoVpoFlv[!ArtVidFlv::Id!]'),
										width: 640,
										height: 548,
										vars: { path:"[!Domaine!]/[!ArtVidFlv::URL!]" }
									});
								</script>	
							</div>
						</div>
					[/STORPROC]
				</div>
			[/STORPROC]
			[!DesSousCat:=0!]
			// AFFICHAGE DES SOUS CATEGORIES 
			[COUNT Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|NbCat]
			[!NbLigne:=[!Math::Floor([!NbCat:/2!])!]!]
			[IF [!NbLigne!]!=[!NbCat:/2!]][!NbLigne++!][/IF]
			[STORPROC[!NbLigne!]|N]
				<div class="row-fluid">
					[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|[!N:*2!]|2|Ordre|ASC]
					<div class="span6 well">
						[!DesSousCat:=1!]
						<div class="CategBordure">
							<div class="TitreCategorie">
								<a href="/[!Lien!]/[!Cato::Url!]" ><h2>[!Cato::Nom!]</h2></a>
								[!LeTitre:=[!Cato::Nom!]!]
							</div>
								[IF [!Cato::Icone!]]
									<img src="/[!Cato::Icone!].mini.150x150.jpg" class="pull-left thumbnail" style="margin-right:10px;"/>
								[/IF]
								[IF [!Cato::Description!]!=]
									<p class="catDesc">[SUBSTR 200][!Cato::Description!][/SUBSTR]</p>
								[/IF]
							<a href="/[!Lien!]/[!Cato::Url!]" class="btn btn-primary pull-right">Lire la suite</a>
						</div>
					</div>
					[/STORPROC]
				</div>
			[/STORPROC]
		</div>
	[/STORPROC]
</div>