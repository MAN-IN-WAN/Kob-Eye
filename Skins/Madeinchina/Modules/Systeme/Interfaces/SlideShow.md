<section id="slideshow" class="hidden-phone">
	<div id="kbboutik"  class="carousel slid leobttslider hidden-phone">
		<div class="carousel-inner">
            [STORPROC [!Systeme::CurrentMenu::getChildren(Donnee)!]/Type=Video|D]
            <div class="item [IF [!Pos!]=1]active[/IF] xs-hidden">
                <a href="#">
                    <video style="width: 100%;object-fit: cover;height: 500px;" id="video_background" preload="auto" autoplay="true" loop="loop" muted="muted" volume="0">
                        <!-- Source vidéo par défaut -->
                        <source type="video/mp4" src="/[!D::Lien!]" media="(orientation: landscape)">
                        <source type="video/ogg" src="/[!D::Alternatif!]" media="(orientation: landscape)">
                        [STORPROC [!Systeme::CurrentMenu::getChildren(Donnee)!]/Type=Image|D]
                         <img src="/[!D::Lien!].mini.1600x400.jpg" alt="[!D::Titre!]" />
                        [/STORPROC]
                    </video>
                </a>
                <div class="slide-info hidden-tablet">
                    <div class="slide-title">
                        [!D::Titre!]
                    </div>
                    <div class="slide-description">
                        [!D::Html!]
                    </div>
                </div>
            </div>
                [NORESULT]
                    [STORPROC [!Systeme::CurrentMenu::getChildren(Donnee)!]/Type=Image|D]
                    <div class="item [IF [!Pos!]=1]active[/IF]">
                        <a href="#"><img src="/[!D::Lien!].mini.1600x400.jpg" alt="[!D::Titre!]" /></a>
                        <div class="slide-info hidden-tablet">
                            <div class="slide-title">
                                [!D::Titre!]
                            </div>
                            <div class="slide-description">
                                [!D::Html!]
                            </div>
                        </div>
                    </div>
                    [/STORPROC]
                [/NORESULT]
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