[!Cpt:=0!]
[STORPROC [!Query!]|Prod|0|1][/STORPROC]
[COUNT Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|NbImg]
[IF [!NbImg!]]
	<div class="row">
		[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|Img|||Ordre|ASC]
			[!Cpt+=1!]
			[IF [!Cpt!]>4]
				</div><div class="row">
				[!Cpt:=1!]
			[/IF]
			<div class="col-md-3">
				<a href="/[!Img::Fichier!].limit.800x600.jpg" class="zoombox  zgallery1">
					<img src="/[!Img::Fichier!]" class="img-responsive" alt="[!P::Nom!] - [!Img::Titre!]" title="[!P::Nom!] - [!Img::Titre!]" /></a>
				</a>
			</div>
		[/STORPROC]
	</div>
[/IF]



<script type="text/javascript">
	$(document).ready(function () {
		$('a.zoombox').zoombox(
			{
 				theme       : 'prettyphoto',        //available themes : zoombox,lightbox, prettyphoto, darkprettyphoto, simple
        			opacity     : 0.8,              // Black overlay opacity
        			duration    : 800,              // Animation duration
        			animation   : true,             // Do we have to animate the box ?
        			width       : 800,              // Default width
        			height      : 950,              // Default height
        			gallery     : false
 			}
		);


	});
</script>