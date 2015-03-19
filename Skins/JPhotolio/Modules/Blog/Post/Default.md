		<div class="container">		
			<div class="span8 blog-side-width">
			
			
			
			[STORPROC [!Query!]|Post]
				[STORPROC [!Post::getParents(Categorie)!]|Cat][/STORPROC]
				<div class="blogentry containerborder">
					<div class="inner-container">
						<div class="blog-img">					
							<a href="/[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Post/[!Post::Url!]">
								[STORPROC Blog/Post/[!Post::Id!]/Donnees/Type=Image|Im]
								<img width="602" height="300" src="/[!Im::Fichier!].mini.602x300.jpg" alt="[!Im::Titre!]" title="[!Im::Titre!]" />
								[/STORPROC]			
							</a>
						</div>
		
						<div class="blogtitle headingwithimage"> 
							<h2> [!Post::Titre!] </h2> 
						</div>
		
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
		
						<div class="comment-container" id="comments">
						[COUNT Blog/Post/[!Post::Id!]/Commentaire|NbC]
							<h2>[!NbC!] Commentaires </h2>
							<hr/>
							<div id="comment-content-container">
								<ul class="commentlist"> <!-- First level Comment list -->
								[STORPROC Blog/Post/[!Post::Id!]/Commentaire|Com]
									<li class="comment"> 
										<div>
											<div class="coment-box">
												[STORPROC Systeme/User/[!Com::userCreate!]|U2][/STORPROC]
												<div class="coment-box-inner">
													<div class="comment-autor">
														<img alt='' src='/[!U2::Avatar!]' class='avatar avatar-80 photo' height='80' width='80' />			
													</div>
													<div class="comment-meta portfolio-meta blog-meta">
														<ul>
															<li class="addby"><i class="icon-user"></i><a href='[!U2::Url!]' rel='external nofollow' class='url'>[!U2::Login!]</a></li>
															<li class="addtime"><i class="icon-time"></i>[DATE m/d/Y][!Com::tmsCreate!][/DATE]</li>
															<li class="replycomment" data-comment-id="2">Répondre</li>
															<li class="closecommentform">Annuler la réponse</li>
														</ul>
													</div>
													<div class="comment-text">
														<p>[!Com::Comment!]</p>				
													</div>
													<div style="clear: both;"></div>
												</div>
											</div>
										</div>
									</li>
									[/STORPROC]
								</ul>				
							</div>
							
							<div id="respond">	<!-- Respond reply -->						
								<h3 id="reply-title">
									Laisser un commentaire
									<small>
										<a rel="nofollow" id="cancel-comment-reply-link" href="#" style="display:none;">
											Annuler le commentaire
										</a>
									</small>
								</h3>					
								<form action="#" method="post" id="commentform">
									<p class="comment-notes">
										Votre adresse email ne sera pas publiée. Les champs obligatoires sont marqués d'un <span class="required">*</span>
									</p>
									<p class="comment-form-author">
										<span class="comment-author-wrapper">
											<label for="author">Nom</label> <span class="required">*</span>
											<input id="author" name="author" type="text" value="" size="30" aria-required='true' />
										</span>
									</p>
									<p class="comment-form-email">
										<label for="email">Email</label> <span class="required">*</span>
										<input id="email" name="email" type="text" value="" size="30" aria-required='true' />
									</p>
									<p class="comment-form-url">
										<label for="url">Site internet</label>
										<input id="url" name="url" type="text" value="" size="30" />
									</p>
									<p class="comment-form-comment">
										<label for="comment">Commentaire</label>
										<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
									</p>
									<p class="form-allowed-tags">
										vous pouvez utiliser les tags et attributs <abbr title="HyperText Markup Language">HTML</abbr> suivants:  
										<code>&lt;a href=&quot;&quot; title=&quot;&quot;&gt; &lt;abbr title=&quot;&quot;&gt; &lt;acronym title=&quot;&quot;&gt; &lt;b&gt; &lt;blockquote cite=&quot;&quot;&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=&quot;&quot;&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=&quot;&quot;&gt; &lt;strike&gt; &lt;strong&gt; </code>
									</p>
									<p class="form-submit">
										<input name="submit" type="submit" id="submit" value="Valider le commentaire" />
										<input type='hidden' name='comment_post_ID' value='100' id='comment_post_ID' />
										<input type='hidden' name='comment_parent' id='comment_parent' value='0' />
									</p>
								</form>
							</div><!-- #respond -->	
						</div>		
					</div>
				</div>	
				[/STORPROC]
				
			</div>
			
			<!-- Sidebar -->
			[MODULE Blog/Interface/SideBar]
			
		</div>
		<div class="page-bottom-spacer"></div>

		<script type="text/javascript" src="/Skins/JPhotolio/js/jeggallery.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				/** bind jeg default **/
				$(window).jegdefault({
					curtain : 1,
					rightclick 	: 0,
					clickmsg	: "Right mouse click disabled, you can enabled it from admin menu"
				});

				/** jeg blog **/
				$("body").jegblog({ minItem : 1 });
			});
		</script>
