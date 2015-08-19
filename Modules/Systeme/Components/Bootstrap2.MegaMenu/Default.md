<script type="text/javascript" src="/Tools/Js/Masonry/masonry.min.js"></script>

<nav id="topnavigation">
	<div class="navbar">
		<div class="navbar-inner">
			<a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
			<div class="nav-collapse collapse">
				<ul class="nav megamenu">
					[OBJ Systeme|Menu|M0]
					[STORPROC [!M0::getMainMenus()!]|M|0|100|Ordre|ASC]
						[STORPROC [!M::getSubMenus()!]|M2|0|100|Ordre|ASC]
						<li class="parent dropdown ">
							<a class="dropdown-toggle" data-toggle="dropdown" href="/[!M::Url!]" onmouseover='$("#container[!M::Id!]").masonry({ "columnWidth": 200, "itemSelector": ".item-menu" });'><span class="menu-title">[!M::Titre!]</span><b class="caret"></b></a>
							<div class="dropdown-menu menu-content mega-cols cols3" [IF [!M::BackgroundImage!]]style="background-image:url(/[!M::BackgroundImage!])"[/IF]>
								<div class="row-fluid">
									//test pour voir si il y a une publicite
									[COUNT Systeme/Menu/[!M::Id!]/Donnee/Type=Pub|PUB]
									<div id="container[!M::Id!]" class="span[IF [!PUB!]]9[ELSE]12[/IF]"  style="position:relative;">
										<ul class="level0 ">
										[LIMIT 0|20]
												<li class="item-menu" style="">
													<a class="" href="/[!M::Url!]/[!M2::Url!]"><span class="menu-title">[!M2::Titre!]</span></a>
													<ul class="level1">
														[STORPROC [!M2::getSubMenus()!]|M3|0|10|Ordre|ASC]
															//[STORPROC [!M3::getSubMenus()!]|M4|0|10|Ordre|ASC]
															//<li class="">
															//	<a class="" href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]"><span class="menu-title">[!M3::Titre!]</span></a>
															//	<ul class="level2">
															//		[LIMIT 0|10]
															//		<li class=" ">
															//			<a href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]/[!M4::Url!]"><span class="menu-title">[!M4::Titre!]</span></a>
															//		</li>
															//		[/LIMIT]
															//	</ul>
															//</li>
															//[NORESULT]
																<li class=" ">
																	<a href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]"><span class="menu-title">[!M3::Titre!]</span></a>
																</li>
															//[/NORESULT]
															//[/STORPROC]
														[/STORPROC]
													</ul>
												</li>
										[/LIMIT]
										</ul>
									</div>
									[IF [!PUB!]]
										<div class="span3">
											[STORPROC Systeme/Menu/[!M::Id!]/Donnee/Type=Pub|P|0|1]
												<a href="[!P::Alternatif!]"><img src="/[!P::Lien!]" width="300" height="300"/></a>
											[/STORPROC]
										</div>
									[/IF]
								</div>
							</div>
						</li>
						[NORESULT]
						<li class="">
							<a href="/[!M::Url!]"><span class="menu-title">[!M::getFirstSearchOrder!]</span></a>
						</li>
						[/NORESULT]
						[/STORPROC]
					[/STORPROC]
				</ul>
			</div>
		</div>
	</div>
</nav>
