
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "http://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<style>
	.fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget span iframe[style] {
width: 100% !important;
}
</style>

<div class="[!NOMDIV!] disclaimer-hidden resociaux">
   	<div class="container nopadding-left nopadding-right">
		<div class="reseau">
			<h1>[!TITRE!]</h1>
			<div class="col-lg-6 col-sm-6 col-xs-12 nopadding-left">
				<div class="SocialHome" style="margin-top:47px;">
					//<iframe id="facebookIframe" src-disclaimer="http://www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Ffonefoil&height=590&colorscheme=light&show_faces=false&header=true&stream=true&show_border=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100%; height:590px;" allowTransparency="true"></iframe>
					<div  style="padding: 0 15px;" class="fb-page" data-href="https://www.facebook.com/fonefoil" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="true" data-width="590" data-height="550"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/fonefoil"><a href="https://www.facebook.com/fonefoil">F-one  International</a></blockquote></div></div>
					<div class="reseau-1">
						<h2>FACEBOOK</h2>
						<h3>F-ONEFOIL</h3>
					</div>
				</div>
				<div class="nav-prod">
				//	<a id="scrollDownFacebook" href="#SocialHome" class="next"></a>
				</div>
			</div>
			<div class="col-lg-6 col-sm-6 col-xs-12 nopadding-left">
				<div id="myCarouselBlog1" class="carousel slide vertical">
					<div class="vertical carousel-inner ">
						[!Req:=Blog/Post/Display=1!]
						[IF [!BLOGCATEGORIE!]!=]
							[!Req:=Blog/Post/Display=1!]
							[!Req2:=Blog/Post!]
						[/IF]

						[STORPROC [!Req!]|Po|0|[!NBBLOGCOL:*2!]|Date|DESC]
						[IF [!Utils::isPair([!Pos!])!]=]
						[STORPROC Blog/Categorie/Post/[!Po::Id!]|Cat|0|1][/STORPROC]
						<div class="[IF [!Pos!]=1]active [/IF]item">
							<div class="blog">
								<div class="category">
									<div class="cat-bloc"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]">NEWS | [!Cat::Titre!]</a></div>
								</div>
								<div class="produits-inner">
									//[STORPROC [!Req2!]/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
									//	<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Do::Titre!]"/></a>
									//[/STORPROC]
									<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Po::Fichier!].mini.570x350.jpg" alt="[!Do::Titre!]"/></a>
									<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
										<h2>[!Po::Titre!]</h2>
										<h3>[!Po::Chapo!]</h3>
									</div>
									<div class="teaser">
										<div class="texteaser">
											[SUBSTR 200|...][!Po::Contenu!][/SUBSTR]
										</div>
										<div class="teaser-info">
											<div class="date">[DATE d/m/Y][!Po::Date!][/DATE]</div>
											<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">__MORE_DETAILS__</a></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						[/IF]
						[/STORPROC]
					</div>
				</div>
				<div class="nav-prod">
					<a class="next" href="#myCarouselBlog1" data-slide="next"></a>
				</div>
			</div>
		</div>
	</div> 
</div>
