<section id="slideshow" class="hidden-phone">
	<div id="kbboutik"  class="carousel slid leobttslider hidden-phone">
		<div class="carousel-inner">
			[STORPROC [!Systeme::CurrentMenu::getChildren(Donnee)!]/Type=Image|D]
			<div class="item [IF [!Pos!]=1]active[/IF]">
				<a href="#"><img src="/[!D::Lien!].mini.1600x400.jpg" alt="[!D::Titre!]" /></a>
				<div class="slide-info hidden-tablet">
					<!--<div class="slide-title">
						[!D::Titre!]
					</div>
					<div class="slide-description">
						[!D::Html!]
					</div>-->
				</div>
			</div>
			[/STORPROC]
		</div>
		<div class="carousel-thumb">
			<div class="carousel-button">
				<a class="carousel-control left" href="#kbboutik" data-slide="next">&lsaquo;</a>
				<a class="carousel-control right" href="#kbboutik" data-slide="prev">&rsaquo;</a>
			</div>
			<ol class="carousel-indicators thumb-indicators hidden-phone">
				[STORPROC [!Systeme::CurrentMenu::getChildren(Donnee)!]/Type=Image|D]
				<li data-target="#kbboutik" data-slide-to="[!Key!]" class=" [IF [!Pos!]=1]active[/IF]">
					<img src="/[!D::Lien!].mini.100x50.jpg"/>
				</li>
				[/STORPROC]
			</ol>
		</div>

	</div>
	<script type="text/javascript">
		$("#kbboutik").carousel({
			interval : 6000
		});
	</script>
</section>