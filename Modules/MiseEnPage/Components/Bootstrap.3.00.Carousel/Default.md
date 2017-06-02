[IF [!REQUETE!]=]
	[!REQUETE:=MiseEnAvant/InfoEnAvant!]
[/IF]
[COUNT [!REQUETE!]/Publier=1|Cpt]

<div class="[!NOMDIV!]">
	<div id="myCarousel" class="carousel slide ">

		<!-- Indicators -->
		[IF [!INDICATORS!]=1]
			<ol class="carousel-indicators">
				[STORPROC [!Cpt!]|C]
					<li data-target="#myCarousel" data-slide-to="[!Pos!]" class="active"></li>
				[/STORPROC]
			</ol>
		[/IF]
		<!-- Wrapper for slides -->
		<div class="carousel-inner">
			[STORPROC [!REQUETE!]/Publier=1|R|||Ordre|ASC]
				[!Lelien:=!][!LelienAffiche:=!][!LelienTitre:=!]
				[STORPROC MiseEnAvant/InfoEnAvant/[!R::Id!]/Donnee/Type~Lien|Lie|0|1]
					[!Lelien:=[!Lie::UrlLien!]!]
					[!LelienAffiche:=[!Lie::LienAffiche!]!]
					[!LelienTitre:=[!Lie::Titre!]!]
				[/STORPROC]
				<div class="item [IF [!Pos!]=1]active[/IF]">
					[IF [!Lelien!]!=]<a href="[IF [!Lie::UrlLien!]~http][ELSE]/[/IF][!Lie::UrlLien!]" >[/IF]
						<img src="[!Domaine!]/[!R::Image!]" alt="[!R::Titre!]" >
						<div class="carousel-caption ">
							[IF [!R::AfficherTitre!]]
								<div class="Titre" [IF [!R::CodeCouleurTitre!]!=]style="color:[!R::CodeCouleurTitre!];"[/IF]>
									[IF [!Pos!]=1]<h1>[ELSE]<h2>[/IF][!R::Titre!][IF [!Pos!]=1]</h1>[ELSE]</h2>[/IF]</div>
							[/IF]
							[IF [!R::Chapo!]!=]
								<div class="Chapo" >
									[!R::Chapo!]
								</div>
							[/IF]
							<div class="Description">[!R::Description!]</div>
							[IF [!LelienAffiche!]]
								<div class="Lien">
									[!LelienTitre!]
								</div>
							[/IF]
						</div>
					[IF [!Lelien!]!=]</a>[/IF]
				</div>
			[/STORPROC]
		</div>
		[IF [!NAVIGATION!]=1]
			<!-- Controls -->
			[IF [!PRECEDENT!]=1]
				<a class="left carousel-control" href="#myCarousel" data-slide="prev"  ><span class="icon-prev"></span></a>
			[/IF]
			[IF [!SUIVANT!]=1]
				<a class="right carousel-control" href="#myCarousel" data-slide="next" ></a>
			[/IF]
		[/IF]
	</div>
</div>


<script type="text/javascript">
	$(document).ready( function(){
		$('.carousel').carousel({
			  interval: 7000
		})
		
		
	});
</script>