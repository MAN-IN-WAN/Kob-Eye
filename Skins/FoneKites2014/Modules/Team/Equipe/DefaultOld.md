[COUNT [!Query!]|NbCat]
[IF [!NbCat!]>1]
	[!Req:=[!Query!]/*!]
[ELSE]
	[!Req:=[!Query!]!]
[/IF]
<div class="container">
  //      <h3>Team</h3>
        <div class="row masonry-container js-masonry" data-masonry-options='{ "columnWidth": ".team-item", "itemSelector": ".team-item" }' id="team-container">
		[STORPROC [!Req!]|Cat]
			[STORPROC Team/Equipe/[!Cat::Id!]/Rider|Rid]
//				[SWITCH [!Legend!]|=]
//					[CASE ][!Legend:=legend-blue!][/CASE]
//					[CASE legend-blue][!Legend:=legend-green!][/CASE]
//					[CASE legend-green][!Legend:=legend-red!][/CASE]
//					[CASE legend-red][!Legend:=legend-bgris!][/CASE]
//					[CASE legend-bgris][!Legend:=legend-orange!][/CASE]
//					[CASE legend-orange][!Legend:=legend-blue!][/CASE]
//				[/SWITCH]
				<div class="item-normal team-item">
					<div class="team">
						[IF [!Rid::Avatar!]!=]
							<a href="/[!Lien!]/Rider/[!Rid::Url!]" class="thumbnail" >
								<img class="img-responsive" src="/[!Rid::Avatar!].mini.290x250.jpg" alt="[!Rid::Nom!]" />
							</a>
						[ELSE]
							[STORPROC Team/Rider/[!Rid::Id!]/Photo|Do|0|1]
								<a href="/[!Lien!]/Rider/[!Rid::Url!]" class="thumbnail">
									<img class="img-responsive" src="/[!Do::Image!].mini.290x250.jpg" alt="[!Rid::Nom!]" />
								</a>
								[NORESULT]
									[IF [!Rid::Fond!]!=]
										<a href="/[!Lien!]/Rider/[!Rid::Url!]" class="thumbnail" ><img class="img-responsive" src="/[!Rid::Fond!].mini.260x290.jpg" alt="[!Rid::Nom!]"/></a>
									[/IF]
								[/NORESULT]
							[/STORPROC]
						[/IF]
						<div class="[!Cat::Couleur!]">
							//<h2><a href="/[!Lien!]/Rider/[!Rid::Url!]" >[!Cat::Titre!]</a></h2>
							<h4><a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Rider/[!Rid::Url!]" >[!Rid::Prenom!]</a></h4>
							<h3><a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Rider/[!Rid::Url!]" >[!Rid::Nom!]</a></h3>
						</div>
					</div>
				</div>
			[/STORPROC]
		[/STORPROC]
    	</div>   
</div>
<script type="text/javascript">
		// layout Masonry again after all images have loaded
		imagesLoaded($("#team-container"), function() {
			$("#team-container").masonry();
		});
</script>