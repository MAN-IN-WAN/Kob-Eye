
<div id="detailNews">
	[STORPROC [!Query!]|N|0|1]
		[STORPROC News/Categorie/Nouvelle/[!N::Id!]|Cat][/STORPROC]
		[!Ent:=[!Cat::getOneChild(Entite)!]!]
		<div id="newsTitle" style="background-color:[!Ent::CodeCouleur!]">
			<h1 id="titreNews">[!N::Titre!]</h1>
			//<a href="[!Systeme::CurrentMenu::Url!]">B</a>
			<a href="/Actualite">B</a>
		</div>
		<div id="newsContent">
			<h2>[!N::Chapo!]</h2>
			<div class="row ">
				<div class="col-md-12">
					[!NbImages:=0!]
					[IF [!N::Image!]!=]
						[!NbImages+=1!]
						<a href="/[!N::Image!]" class="zoombox zgallery1" ><img src="/[!N::Image!]" alt="[!N::Titre!]" title="[!N::Titre!]" class="img-responsive pull-left" width="225px;" /></a>
					[/IF]
					<p>[!N::Contenu!]</p>
					[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic]
						[IF [!Fic::Type!]=Fichier]
							<div><a href="[!Domaine!]/[!Fic::URL!]" target="blank" class="lienNews">[!Fic::Titre!]</a></div>
							[ELSE]
								<div class="pull-left"><a href="/[!Fic::URL!].limit.800x600.jpg" title="[!Fic::Titre!]" target="_blank" class="zoombox zgallery1"  ><img src="[!Domaine!]/[!Fic::URL!].limit.150x120.jpg" title="[!Fic::Titre!]" alt="[!Fic::Titre!]"  ></a></div>
							[/IF]
						
					[/STORPROC]
					[STORPROC News/Nouvelle/[!N::Id!]/Lien|L]
						<div>
							<a href="[!L::URL!]" [IF [!L::URL!]~http] target="blank"[/IF]  class="lienNews">[!L::Titre!]</a>
						</div>
					[/STORPROC]
				</div>
	
			</div>	
		</div>
		
	[/STORPROC]
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
