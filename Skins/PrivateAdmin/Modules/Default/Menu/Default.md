					<!-- aside item: Menu -->
					<div class="sidebar-nav-fixed">
						
						<ul style="" class="menu ctAccordion" id="accordion-menu-js">
							[STORPROC [!Systeme::Menus!]|M]
							<li class="[IF [!Systeme::CurrentMenu::Url!]~[!M::Url!]]current open[/IF][IF [!Systeme::CurrentMenu::Url!]=&&[!M::Url!]=]current open[/IF]">	
								[COUNT [!M::Menus!]|nbM]
								<a class="head" href="/[!M::Url!]"><i class="icon-off"></i>[!M::Titre!][IF [!nbM!]>1]<span class="badge">[!nbM!]</span>[/IF]</a>
								[STORPROC [!M::Menus!]|M2]
								<ul style="display: block;">
									[LIMIT 0|10]
									<li class="">
										<a href="/[!M::Url!]/[!M2::Url!]" class=" [IF [!Systeme::CurrentMenu::Url!]~[!M2::Url!]]expanded[/IF]">[!M2::Titre!]</a>
									</li>
									[/LIMIT]
								</ul>
								[/STORPROC]
							</li>
							[/STORPROC]
							<li class="">
								<a href="/Systeme/Deconnexion"><i class="icon-off"></i>D&eacute;connexion</a>
							</li>
						</ul>
						
					</div>
