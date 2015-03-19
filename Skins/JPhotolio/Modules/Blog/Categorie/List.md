		<div class="container">		
			<div class="span8 blog-side-width"> <!-- Blog Container -->
			
			[STORPROC [!Chemin!]/Post|Post]
				[STORPROC [!Post::getParents(Categorie)!]|Cat][/STORPROC]
				<div class="blogentry containerborder"> <!-- Blog Entry -->
					<div class="inner-container">
						<div class="blog-img"> <!-- Blog Header Image -->
							<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Post/[!Post::Url!]">
								[STORPROC Blog/Post/[!Post::Id!]/Donnees/Type=Image|Im]
								<img width="602" height="300" src="/[!Im::Fichier!].mini.602x300.jpg" alt="[!Im::Titre!]" title="[!Im::Titre!]" />
								[/STORPROC]			
							</a>
						</div> <!-- End blog header image -->
						
						<div class="blogtitle  headingwithimage"> <!-- Blog Title -->
							<h2> 
								<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Post/[!Post::Url!]">[!Post::Titre!]</a> 
							</h2> 
						</div>	<!-- end blog Title -->
						
						<div class="portfolio-meta blog-meta"> <!-- blog meta -->
							<ul>
								[STORPROC Systeme/User/[!Post::userCreate!]|U][/STORPROC]
								<li class="addby"><i class="icon-user"></i><a href="#" title="Posts par [!U::Login!]" rel="author">[!U::Login!]</a></li>
								<li class="addtime"><i class="icon-time"></i>[DATE m/d/Y][!Post::tmsCreate!][/DATE] </li>
								<li class="commentcount"><i class="icon-comment"></i>
								[COUNT Blog/Post/[!Post::Id!]/Commentaire|NbC] 
								<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Post/[!Post::Url!]#comments"> [!NbC!] Comment </a>
								</li>
								<div class="clearboth"></div>
							</ul>
						</div> <!-- end blog meta -->
								
						<div class="blog-container"> <!-- Blog Summary -->
							<p>[!Post::Contenu!]<a class="moretag" href="http://jegtheme.com/themes/jphotolio/2012/08/07/finibus-bonorum-et-malorum-2/">  Lire plus .. </a></p>
						</div> <!-- end blog summary -->
						
						<div class="bottom-bar"> <!-- Blog bottom bar -->			 
							<div class="blog-more" style="float: left;"> <!-- Blog More -->
								<ul>
									<li>
										<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Post/[!Post::Url!]" data-rel="tooltip" title="Plus de Detail">
											<i class="misc-preview"></i> 
										</a>					
									</li>
								</ul>
							</div> <!-- End blog like -->
							
							<div class="clearbottombar"></div>
						</div>
					</div>
				</div> <!-- End Blog Entry -->
				[/STORPROC]
				
				
				
				<div class="btn-toolbar blogpagging"> <!-- Start Paging --> 
					<div class="btn-group">
						<button class="btn active">Page 1 of 6 </button> 
						<button class="btn active">1</button>
						<a href="#" class="btn">2</a>
						<a href="#" class="btn">3</a>
						<a href="#" class="btn"><span>&rsaquo;</span></a>
						<a href="#" class="btn">&raquo;</a> 
					</div>
				</div>	<!-- End Paging -->
			</div> <!-- End Blog Container -->
				
			
			<!-- Sidebar -->
			[MODULE Blog/Interface/SideBar]
		</div>
		<div class="page-bottom-spacer"></div>

		<script type="text/javascript" src="/Skins/JPhotolio/js/jeggallery.js"></script>
