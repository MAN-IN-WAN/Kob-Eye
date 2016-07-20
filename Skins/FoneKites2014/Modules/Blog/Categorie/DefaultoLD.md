[COUNT [!Query!]|NbCat]
[!NbLimitA:=4!]
[IF [!More!]][!NbLimitA+=[!More!]!][ELSE][!More:=0!][/IF]

[IF [!NbCat!]>1]
	[!NbLimitA:=2!]
	[!Req:=[!Query!]/*!]
//	ALL CATEGORIE
[ELSE]
//	UNE CATEGORIE
	[!Req:=[!Query!]!]

[/IF]

<div class="last-news">
	<div class="container">
		<h1>Last News</h1>
		<div class="reseau masonry-container js-masonry" data-masonry-options='{ "columnWidth": ".post-item", "itemSelector": ".post-item" }' id="post-container">
			[!Cpt:=0!]
			[STORPROC [!Req!]|Cat]
//				[COUNT Blog/Categorie/[!Cat::Id!]/Post/Actif=1&Valide=1|NbPo]

				[COUNT Blog/Categorie/[!Cat::Id!]/Post|NbPo]
				[IF [!NbPo!]]
//					[STORPROC Blog/Categorie/[!Cat::Id!]/Post/Actif=1&Valide=1|Po|0|1|tmsCreate|DESC]

					[STORPROC Blog/Categorie/[!Cat::Id!]/Post|Po|0|[!NbLimitA!]|tmsCreate|DESC]
						<div class="col-lg-6 col-sm-6 post-item">	
							[!Cpt+=1!]
							<div class="category">
								<div class="cat-bloc">
									<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]">
										NEWS | [!Cat::Titre!]
									</a>
								</div>
							</div>
							<div class="produits ">
								[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Video|Do|0|1]
									<div class="Post-Aff">
										//<a href="#myModal" role="button" class="btn" data-toggle="modal">Launch demo modal</a>
										<iframe width="auto" height="auto" src="[!Domaine!]/[!Do::Fichier!]" frameborder="0" ></iframe>
										[NORESULT]
											[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
												<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.570x350.jpg" alt="[!Do::Titre!]"/></a>
											[/STORPROC]
										[/NORESULT]
									</div>
								[/STORPROC]
								<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><div class="lastnews-[!Pos!]">
									<h2>[!Po::Titre!]</h2>
									<h3>[!Po::Chapo!]</h3>
								</div></a>
								<div class="teaser-blog">
									<div class="teaser">
										<div class="texteaser" [IF [!HAUTEURBLOCTEXTE!]!=] style="height:290px;"[/IF]> 
											[!Po::Contenu!]
										</div>
										<div class="teaser-info">
											<div class="date"style="font-size:22px; font-weight:100;>[DATE d/m/Y][!Po::Date!][/DATE]</div>
											<div class="more_lastnews-[!Pos!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">MORE DETAILS</a></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					[/STORPROC]
				[/IF]
			[/STORPROC]
		</div> 
	</div>
</div>  
<div class="container">
	<div class="load-more">
		[!More+=4!]
		<a href="/[!Lien!]?More=[!More!]" class="btn-more-Media btn-primary">$MSGMOREPOST$</a>
	</div> 
</div>

<div class="modal hide fade">
	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    		<h3>Modal header</h3>
    	</div>
    	<div class="modal-body">
    		<p><iframe width="auto" height="auto" src="[!Domaine!]/[!Do::Fichier!]" frameborder="0" ></iframe></p>
    	</div>
    	<div class="modal-footer">
    		<a href="#" class="btn">Close</a>
    	</div>
</div>



<script type="text/javascript">
	// layout Masonry again after all images have loaded
	imagesLoaded($("#blog-container"), function() {
		$("#blog-container").masonry();
	});
</script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
    
      
    });
</script>