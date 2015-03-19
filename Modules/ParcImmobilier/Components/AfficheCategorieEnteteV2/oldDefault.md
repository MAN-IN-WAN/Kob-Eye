//[HEADER JS]Tools/Jquery/1.9.2/js/jquery-1.8.3.min.js[/HEADER]
[!Key:=0!]
[STORPROC ParcImmobilier/CategorieHeader/1/Header/Publier=1|Img|0|3]
	<div id="myCarousel" class="carousel slide">
		// Elements
		<div class="carousel-inner">
			[LIMIT 0|10]
				<div class="[IF [!Key!]=0]active[/IF] item">
					<img src="[!Domaine!]/[!Img::Bandeau!]" alt="[!Img::Titre!]" />
				</div>
			[/LIMIT]
		</div>
		// Gauche - Droite
		//    <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		//    <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
	</div>
[/STORPROC]

	
<script type="text/javascript">
	jQuery(document).load(function() {
		$('.carousel').carousel({
			interval: 7000
		});
	});
</script>