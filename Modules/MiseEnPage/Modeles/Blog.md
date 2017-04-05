// POUR L'INSTANT L'AFFICHAGE DU DETAIL D'UN ARTICLE EST
//    DANS LA SKIN MODULE MISEENPAGE ARTICLE DEFAULT
<div class="Blog row noMargin" >
	<div class="col-md-8">
		[STORPROC [!Query!]/Categorie/*/Article/Publier=1&ALaUne=1|Art|0|6|tmsCreate|DESC]
			[STORPROC MiseEnPage/Categorie/Article/[!Art::Id!]|CatArt|0|1][/STORPROC]
			<div class="quandqui">le <span class="color-red">[DATE d.m.Y][!Art::Date!][/DATE]</span>, par <span class="colorred">
			[!Art::Auteur!] </span></div>
			<div class="titre">
//					<h[IF [!Pos!]=1]1[ELSE]2[/IF]>[!Art::Titre!]</h[IF [!Pos!]=1]1[ELSE]2[/IF]>
				<h3>[!Art::Titre!]</h3>
			</div>
			// Recherche d'une image
			<div class="UnPost">
				[STORPROC MiseEnPage/Article/[!Art::Id!]/Contenu/*/Colonne/*/Image|ACImg|0|1]
					<div class="imageblog">
						<img src="[!Domaine!]/[!ACImg::URL!]" alt="[!ACImg::Alt!]" title="[!ACImg::Title!]" class="img-responsive">
					</div>			
				[/STORPROC]
				<div class="chapo">
					[!Art::Chapo!]
				</div>
				<div class="LienArticle">
					<a href="/[!Systeme::CurrentMenu::Url!]/Categorie/[!CatArt::Url!]/Article/[!Art::Url!]" alt="Lire [!Art::Titre!]" >Lire l'article</a>
				</div>
				<div class="ReseauxSoc">
					fb - tw- g+ 
				</div>
			</div>
		[/STORPROC]
	</div>
	<div class="col-md-4">
		// colonne de droite
		[STORPROC Redaction/Categorie/Accueil|CatCol|0|1]
			[STORPROC Redaction/Categorie/[!CatCol::Id!]/Article/Publier=1|ArtCol|0|1][/STORPROC]				
		[/STORPROC]
		<div class="Avatar">
			[IF [!CatCol::Icone!]!=]
				<img src="[!Domaine!]/[!CatCol::Icone!]" class="img-responsive" alt="Guillaume" title="Guillaume" />
			[/IF]
		</div>
		<div class="ReseauxSoc">
			fb - tw- g+ 
		</div>

		<div class="Apropos">
			<h4>À propos</h4>
			<p>[!ArtCol::Contenu!]</p>
			<div class="LienArticle"><a href="/Contact" alt="Contact" >Me Contacter</a></div>
		</div>
		<div class="Categories">
			<h4>Catégories</h4>
			[STORPROC [!Systeme::CurrentMenu::Alias!]/Categorie/Publier=1|Cat]
				<div class="unecateg"><a href="/[!Systeme::CurrentMenu::Url!]/Categorie/[!Cat::Url!]" [IF [!Lien!]~[!Systeme::CurrentMenu::Url!]/Categorie/[!Cat::Url!]]class="current"[/IF]>[!Cat::Nom!]</a></div>
			[/STORPROC]
		</div>
		<div class="PlusLus">
			<h4>Les plus lus</h4>
		</div>
		
	</div>
</div>
 