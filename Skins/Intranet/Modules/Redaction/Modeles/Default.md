// Modele qui n'affiche pas le contenu des articles en entier
[INFO [!Lien!]|I]
//Recherche du menu racine
[STORPROC [!I::Historique!]|LaRacine|0|1][/STORPROC]

[STORPROC [!Query!]|Cat]
	// AFFICHAGE DE LA CATEGORIE PRINCIPALE
	<div class="Redaction">
		[IF [!Cat::Nom!]!=Accueil]
			<div class="TitreCategorie" [IF [!Cat::LogoTitre!]!=] style="background:url('[!Domaine!]/[!Cat::LogoTitre!]') no-repeat 0 7px;padding-left:20px;"[/IF]>
				<h1>[!Cat::Nom!]</h1>
			</div>
		[/IF]
		[IF [!Cat::Description!]!=]
			<p class="catDesc">[!Cat::Description!]</p>
		[/IF]
		[STORPROC [!Query!]/Article/Publier=1|Art]
			<div class="Article CategBordureP ">
				[IF [!Art::AfficheTitre!]]
					<div class="TitreArticle">
						<h2>[!Art::Titre!]</h2>
					</div>
				[/IF]
				<div class="ArticleAvecImage">
					[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Dessus|ArtImg]
						<div class="ImageArticleDessusDessous">
							[LIMIT 0|10]
								<img src="/[!ArtImg::URL!].limit.400x500.jpg" />
							[/LIMIT]
						</div>
					[/STORPROC]
					[!QuelDiv:=!][!QuelDiv2:=!]
					[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Gauche|ArtImg]
						[!QuelDiv:=Gauche!]
						<div class="ImageArticleGauche">
							[LIMIT 0|10]
								<div><img src="/[!ArtImg::URL!].mini.130x130.jpg" /></div>
							[/LIMIT]
						</div>
					[/STORPROC]
					[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Droite|ArtImg]
						[!QuelDiv2:=Droite!]
						<div class="ImageArticleDroite" >
							[LIMIT 0|10]
								<div><img src="/[!ArtImg::URL!].mini.130x130.jpg" /></div>
							[/LIMIT]
						</div>
					[/STORPROC]
					[IF [!Art::Contenu!]!=]
						<div class="ContenuArticle" [IF [!QuelDiv!]=Gauche]style="margin-left:150px;"[/IF]>
							<p [IF [!QuelDiv!]!=||[!QuelDiv2!]!=]style="margin: 0 10px;"[/IF]>[!Art::Contenu!]</p>
						</div>
					[/IF]
					[STORPROC Redaction/Article/[!Art::Id!]/Image/Position=Dessous|ArtImg]
						<div class="ImageArticleDessusDessous">
							[LIMIT 0|10]
								<img src="/[!ArtImg::URL!].limit.400x500.jpg" />
							[/LIMIT]
						</div>
					[/STORPROC]
				</div>
				
				// Partie lien
				[STORPROC Redaction/Article/[!Art::Id!]/Lien|ArtLie]
					<div class="LienArticle"><a href="[IF [!ArtLie::Type!]=Interne]/[/IF][!ArtLie::URL!]"  class="lelienarticle"  [IF [!ArtLie::Type!]=Externe] target="_blank"[/IF]>[!ArtLie::Titre!]</a></div>
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
		[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato]
			[!DesSousCat:=1!]
			<div class="CategBordure">
				<div class="">
				[IF [!Cato::Id!]=28]
					<a href="/Espace-Pro/Referentiel" ><h2>[!Cato::Nom!]</h2></a>
					[ELSE]
					<a href="/[!Lien!]/[!Cato::Url!]" ><h2>[!Cato::Nom!]</h2></a>
				[/IF]
					[!LeTitre:=[!Cato::Nom!]!]
				</div>
				
				[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/[!Cato::Id!]/Article/Publier=1|Art|0|1]
				[SUBSTR 150][!Art::Contenu!]...[/SUBSTR]
				[/STORPROC]
				
				
				//[IF [!Cato::Description!]!=]
					//<p class="catDesc">[!Cato::Description!]</p>
				//[/IF]
				[IF [!Cato::Id!]=28]
					<a href="/Espace-Pro/Referentiel" class="lirelasuite">Lire la suite</a>
				[ELSE]
				<a href="/[!Lien!]/[!Cato::Url!]" class="lirelasuite">Lire la suite</a>
				[/IF]
			</div>
		[/STORPROC]
	</div>
[/STORPROC]

<div class="HautdePage"><a href="#top">Haut de page</a></div>