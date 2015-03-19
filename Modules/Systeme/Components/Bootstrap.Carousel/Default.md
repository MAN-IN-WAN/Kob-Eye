[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image|I]
<div id="myCarousel" class="carousel slide">
	<div class="carousel-inner">
		[LIMIT 0|10]
		<div class="item [IF [!Pos!]=1]active[/IF]">
			<img src="/[!I::Lien!].mini.1200x350.jpg" alt="">
			<div class="carousel-caption">
				<h5>[!I::Titre!]</h5>
			</div>
		</div>
		[/LIMIT]
	</div>
	<a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
	<a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
</div>
[/STORPROC]