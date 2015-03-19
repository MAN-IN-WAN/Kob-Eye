
			<style type="text/css">
				#jeglio .item {
					width : 220px;
				}
			</style>

			<div id="liofilter">
				<span>
					<div class="misc-list"></div>
					<ul>
						<li class="apply-filter filter-select" data-filter="*">All</li>
						[STORPROC Galerie/Categorie|C]	
						<li class='apply-filter' data-filter='.[!C::Url!]'> [!C::Nom!] </li>
						[/STORPROC]
					</ul>
				</span>
			</div>
			
			<div class="lioseparator"></div>

			<div id="jeglio">
			
			
				[STORPROC Galerie/Portfolio/Public=1|P]
				<div class="item[STORPROC Galerie/Categorie/Portfolio/[!P::Id!]|Cat] [!Cat::Url!][/STORPROC]" data-id="[!P::Url!]" data-url="[!P::Url!]" data-src="[!P::Url!]">
					<a href="/[!Systeme::CurrentMenu::Url!]#!/[!P::Url!]" data-tourl="false">
						<div class="shadow"></div>
					</a>			
					<div class="small-loader"></div>					
						<div class="love-this " data-id="[!P::Url!]" data-voted="Merci pour votre vote!" data-vote="J'aime" data-counter="[!P::LikeCompteur!]">					
						<span class="love-counter">[!P::LikeCompteur!]</span>
						<b class="icon-heart"></b>
					</div>
						
					<a href="[!Systeme::CurrentMenu::Url!]#" data-tourl="false">
						<div class="closeme">
							<div class="icon-remove"></div>
						</div>
					</a>
					<a href="/[!Systeme::CurrentMenu::Url!]#!/[!P::Url!]" data-tourl="false">
						<div class="item-wrapper" >			
							<figure>
								[STORPROC Galerie/Portfolio/[!P::Id!]/Image|Im|0|1]
								<img src="/[!Im::Fichier!].mini.220x[!200:+[!P::LikeCompteur!]!].jpg"/>
								[/STORPROC]
							</figure>
							<div class="bottom-holder">
								<div class="desc-holder">						
									<h3>[!P::Nom!]</h3>
									<h4>[STORPROC Galerie/Categorie/Portfolio/[!P::Id!]|Cat][IF [!Pos!]>1], [/IF][!Cat::Nom!][/STORPROC]</h4>
									<i class=" icon-picture "></i>
								</div>
							</div>	
						</div>
					</a>
				</div>
				[/STORPROC]
				




			</div>

			<div class="lio-loader"></div>		

			<div id="item-theater-overlay">		
				<div id="item-theater">		
					<div id="item-theater-detail">
						<a href="#!/">
							<div class="closeme">
								<div class="icon-remove"></div>
							</div>
						</a>
						<div class="love-this">					
							<span class="love-counter"></span>
							<b class="icon-heart"></b>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript" src="/Skins/JPhotolio/js/jeglio.js"></script>
			<script type="text/javascript">
				jQuery(document).ready(function($)
				{
					/** bind jeg default **/
					$(window).jegdefault({
						curtain 	: 1,
						rightclick 	: 1,
						clickmsg	: "Click droit d&eacute;sactiv&eacute;" 
					});
					
					$("#jeglio").jeglio({
						itemWidth 		: "220",
						galleryDim		: "3",
						descDim			: "1",
						loadAnimation	: "sequpfade",	
						theatherMode	:  0,
						lang			: {
							portfoliotitle						: "Entrez le mot de passe",
							passwordplaceholder  				: "Mot de passe",
							submit 								: "Valider"
						}
					});
					
				});
			</script>
