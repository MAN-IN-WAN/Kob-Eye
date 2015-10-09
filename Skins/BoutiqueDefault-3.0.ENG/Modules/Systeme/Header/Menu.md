<nav id="topnavigation">
	<div class="navbar">
		<div class="navbar-inner">
			<a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
			<div class="nav-collapse collapse">
				<ul class="nav megamenu">
					[OBJ Systeme|Menu|M0]
					[STORPROC [!M0::getMainMenus()!]|M]
						[STORPROC [!M::getSubMenus()!]|M2]
						<li class="parent dropdown ">
							<a class="dropdown-toggle" data-toggle="dropdown" href="/[!M::Url!]"><span class="menu-title">[!M::Titre!]</span><b class="caret"></b></a>
							<div class="dropdown-menu menu-content mega-cols cols3">
								<div class="row">
									<div class="mega-col col-md-12">
										<ul>
        									[LIMIT 0|40]
											<li class="parent dropdown-submenu mega-group">
												<a class="dropdown-toggle" data-toggle="dropdown" href="/[!M::Url!]/[!M2::Url!]"><span class="menu-title">[!M2::Titre!]</span><b class="caret"></b></a>
												<ul class="dropdown-mega level1">
													[STORPROC [!M2::getSubMenus()!]|M3|0|50]
														[STORPROC [!M3::getSubMenus()!]|M4|0|50]
														<li class="parent dropdown-submenu ">
															<a class="dropdown-toggle" data-toggle="dropdown" href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]"><span class="menu-title">[!M3::Titre!]</span><b class="caret"></b></a>
															<ul class="dropdown-menu level1">
																[LIMIT 0|100]
																<li class=" ">
																	<a href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]/[!M4::Url!]"><span class="menu-title">[!M4::Titre!]</span></a>
																</li>
																[/LIMIT]
															</ul>
														</li>
														[NORESULT]
															<li class=" ">
																<a href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]"><span class="menu-title">[!M3::Titre!]</span></a>
															</li>
														[/NORESULT]
														[/STORPROC]
													[/STORPROC]
												</ul>
											</li>
                									[/LIMIT]
										</ul>
									</div>
									<div class="mega-col col-md-3 col-3">
										<ul>
											<li class=" ">
												<div class="menu-content">
													<p>
														<iframe src="http://player.vimeo.com/video/40117938" frameborder="0" width="250" height="150"></iframe>
													</p>
													<p>
														Dorem ipsum dolor sit amet consectetur adipiscing elit congue sit amet erat roin tincidunt vehicula lorem in adipiscing urna iaculis vel.
													</p>
												</div>
											</li>
										</ul>
									</div>
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
