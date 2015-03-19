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
						<li class="parent dropdown "  >
							<a class="dropdown-toggle [!M::ClassCss!] " id="dropdown[!Pos!]" data-toggle="dropdown" href="/[!M::Url!]" ><span class="menu-title" >[!M::Titre!]</span><b class="caret"></b></a>
							<div class="dropdown-menu menu-content mega-cols cols3" style="[IF [!M::BackgroundImage!]]background-image:url(/[!M::BackgroundImage!]);[/IF][IF [!M::BackgroundColor!]]border:3px solid [!M::BackgroundColor!];[/IF]" >
<script type="text/javascript">
	$('#dropdown[!Pos!]').on('show.bs.dropdown', function () {
		$("#container[!M::Id!]").masonry({ "columnWidth": 200, "itemSelector": ".item-menu" });
	})
</script>
								<div class="row">
									//test pour voir si il y a une publicite
									[COUNT Systeme/Menu/[!M::Id!]/Donnee/Type=Pub|PUB]
									<div id="container[!M::Id!]" class="col-md-[IF [!PUB!]]8[ELSE]12[/IF]"  style="position:relative;">
										<ul class="level0" >
											[LIMIT 0|10]
												<li class="item-menu" >
													<a class="" href="/[!M::Url!]/[!M2::Url!]" style="[IF [!M::BackgroundColor!]]color:[!M::BackgroundColor!];[/IF]">[IF [!M::Icone!]!=]<img src="/[!M::Icone!]" style="display: block; float:left;margin-right:8px;">[/IF]<span class="menu-title">[!M2::Titre!]</span></a>
													<ul class="level1">
														[STORPROC [!M2::getSubMenus()!]|M3|0|5|Ordre|ASC]
															[STORPROC [!M3::getSubMenus()!]|M4|0|5|Ordre|ASC]
															<li class="">
																<a class="" href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]"><span class="menu-title">[!M3::Titre!]</span></a>
																[IF [!NIVEAU!]=3]
																	<ul class="level2">
																		[LIMIT 0|10]
																		<li class=" ">
																			<a href="/[!M::Url!]/[!M2::Url!]/[!M3::Url!]/[!M4::Url!]"><span class="menu-title">[!M4::Titre!]</span></a>
																		</li>
																		[/LIMIT]
																	</ul>
																[/IF]
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
									[IF [!PUB!]]
										//<div class="col-md-4" style="[IF [!M::BackgroundColor!]]background-color:[!M::BackgroundColor!];[/IF]">
										<div class="col-md-4" style="">
											[STORPROC Systeme/Menu/[!M::Id!]/Donnee/Type=Pub|P|0|1]
												[IF [!P::Alternatif!]<a  class="imgMenu" href="[IF [!P::Alternatif!]~http||[!P::Alternatif!]~www][ELSE]/[/IF][!P::Alternatif!]" [IF [!P::Alternatif!]~http||[!P::Alternatif!]~www]target="_blank"[/IF] >[/IF]<img class="imgMenu" src="/[!P::Lien!]" />[IF [!P::Alternatif!]</a>[/IF]
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
