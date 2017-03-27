<div id="articleBack">
		//<a href="[!Systeme::CurrentMenu::Url!]">Retour à la liste des Actualités</a>
		<a href="/Actualite">< Retour à la liste des Actualités</a>
</div>
<div id="detailArticle">
	[STORPROC [!Query!]|Art][/STORPROC]
	[!Art::generateDefaultLayout()!]
</div>


<script type="text/javascript">
	$(document).ready(function () {
		$('a.zoombox').zoombox({
			theme : 'darkprettyphoto',
			opacity     : 0.8,
			duration    : 800,              // Animation duration
			animation   : true,             // Do we have to animate the box ?
			width       : 600,              // Default width
			height      : 400,              // Default height
			gallery     : true,             // Allow gallery thumb view
			autoplay : false                // Autoplay for video			
		});
	});
</script>



//theme 	'zoombox' 	
// zoombox, lightbox, prettyphoto, darkprettyphoto, simple
//opacity 	0.8 	Page overlay opacity
//duration 	800 	Zoombox opening animation duration (ms)
//animation 	true 	Set it to false if you don't want width/height animation, zoombox will be directly appended to body and displayed
//width 	600 	Default width for videos and iframes (image size is automatically detected)
////height 	400 	Default height for videos and iframes (image size is automatically detected)
//gallery 	true 	If set to true zoombox will display a gallery of images thumbs
//autoplay 	false 	Autoplay video opened with zoombox
//overflow 	false 	Allow
