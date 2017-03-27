// Affichage détail
[!NbComment:=0!]
//ICI RAJOUTER LE COMPTAGE DES COMMENTAIRES
<div class="ArticleBlog row noMargin" >
	<div class="col-md-12">
		[STORPROC [!Query!]|Art]
			[STORPROC MiseEnPage/Categorie/Article/[!Art::Id!]|CatArt|0|1][/STORPROC]
			<div class="quandqui">le <span class="color-red">[DATE d.m.Y][!Art::Date!][/DATE]</span>, par <span class="colorred">[!Art::Auteur!] </span></div>
			<div class="titre"><h2>[!Art::Titre!]</h2></div>
			<div class="ReseauxSoc">
				<div class="pull-left">
					// Commentaires
					<button type="button" class="btn btn-noir btn-xs"><span class="glyphicon glyphicon-edit"></span> Commentaires </button>
				</div>
				<div class="pull-left">
					// Google
					<div class="g-plusone" data-size="small" data-count="true"></div>
					<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
				</div>
				<div class="pull-left">
					<iframe src="http://www.facebook.com/plugins/like.php?href=[!Domaine!]/[!Lien!]&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:20px" allowTransparency="true"></iframe>
				</div>				
				<div class="pull-left">
					// Twitter
					<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="InfoWebMaster">Tweet</a>
					<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				</div>
			</div>
			<div class="chapo">
				[!Art::Chapo!]
			</div>
			// Recherche d'une image
			<div class="UnPost">
				[STORPROC MiseEnPage/Article/[!Art::Id!]/Contenu/*/Colonne/*/Image|ACImg|0|1]
						<div class="imageblog">
							<img src="[!Domaine!]/[!ACImg::URL!]" alt="[!ACImg::Alt!]" title="[!ACImg::Title!]" class="img-responsive">
						</div>			
				[/STORPROC]
			</div>
			[IF [!Art::Contenu!]!=]<div class="contenu">
				[!Art::Contenu!]
			</div>[/IF]
			[COUNT MiseEnPage/Article/[!Art::Id!]/Contenu/*/Colonne/*/Texte/Contenu!=|NbTxte]
			[IF [!NbTxte!]]<div class="contenu">
				[STORPROC MiseEnPage/Article/[!Art::Id!]/Contenu/*/Colonne/*/Texte|ArtTexte]
					<h3>[!ArtTexte::Titre!]</h3>
					<div class="texte">[!ArtTexte::Contenu!]</div>
				[/STORPROC]
			</div>[/IF]

		[/STORPROC]
		<div class="ReseauxSocLiens">
			fb - tw- g+ 
		</div>
		<div class="commentaires">
			// ICI GUILLAUME C AJOUTER LES COMMENTAIRES
			Commentaires
		</div>
	</div>
</div>