		<nav>
			<div class="navleft">
				<div class="navleft-wrapper">
					<ul class="menu">
						[STORPROC [!Systeme::Menus!]/MenuPrincipal=1&Affiche=1|M1|0|2|Ordre|DESC]
						<li class="bgnav">
							<a href="/[!M1::Url!]">	
								<h3>[!M1::Titre!]</h3>
								<span>[!M1::SousTitre!]</span>
							</a>							
							[STORPROC [!M1::Menus!]|M2|0|100|Ordre|ASC]
							<ul class="submenu">
								[LIMIT 0|100]
								<li class="bgnav">
									<a href="/[!M1::Url!]/[!M2::Url!]">	
										<h3>[!M2::Titre!]</h3>
										<span>[!M2::SousTitre!]</span>
									</a>							
									[STORPROC [!M2::Menus!]|M3|0|100|Ordre|ASC]
									<ul class="submenu">
										[LIMIT 0|100]
										<li class="bgnav">
											<a href="/[!M1::Url!]/[!M2::Url!]/[!M3::Url!]">	
												<h3>[!M3::Titre!]</h3>
												<span>[!M3::SousTitre!]</span>
											</a>							
										</li>
										[/LIMIT]
									</ul>
									[/STORPROC]
								</li>
								[/LIMIT]
							</ul>	
							[/STORPROC]
						</li>
						[/STORPROC]
					</ul>
				</div>			
			</div>
			<div class="navright">
				<div class="navright-wrapper">
					<ul class="menu">
						[STORPROC [!Systeme::Menus!]/MenuPrincipal=1|M1|2|2|Ordre|ASC]
						<li class="bgnav">
							<a href="/[!M1::Url!]">	
								<h3>[!M1::Titre!]</h3>
								<span>[!M1::SousTitre!]</span>
							</a>							
							[STORPROC [!M1::Menus!]|M2|0|100|Ordre|ASC]
							<ul class="submenu">
								[LIMIT 0|100]
								<li class="bgnav">
									<a href="/[!M1::Url!]/[!M2::Url!]">	
										<h3>[!M2::Titre!]</h3>
										<span>[!M2::SousTitre!]</span>
									</a>							
									[STORPROC [!M2::Menus!]|M3|0|100|Ordre|ASC]
									<ul class="submenu">
										[LIMIT 0|100]
										<li class="bgnav">
											<a href="/[!M1::Url!]/[!M2::Url!]/[!M3::Url!]">	
												<h3>[!M3::Titre!]</h3>
												<span>[!M3::SousTitre!]</span>
											</a>							
										</li>
										[/LIMIT]
									</ul>
									[/STORPROC]
								</li>
								[/LIMIT]
							</ul>	
							[/STORPROC]
						</li>
						[/STORPROC]
					</ul>
				</div>			
			</div>
		</nav>
		<div class="logo">
			<a href="/">
				<!-- Logo masuk sini -->
				<img alt="" src="/Skins/JPhotolio/images/logo.png" />
			</a>
		</div>
		<div class="navselect">
			<select>
				<option value="#!/">Navigate ...</option>
			[STORPROC [!Systeme::Menus!]/MenuPrincipal=1|M1|2|2|Ordre|ASC]
				<option  value="/[!M1::Url!]">[!M1::Titre!]</option>							
				[STORPROC [!M1::Menus!]|M2|0|100|Ordre|ASC]
					<option  value="/[!M1::Url!]/[!M2::Url!]">&nbsp;&nbsp; [!M2::Titre!]</option>							
					[STORPROC [!M2::Menus!]|M3|0|100|Ordre|ASC]
						<option  value="/[!M1::Url!]/[!M2::Url!]/[!M3::Url!]">&nbsp;&nbsp;&nbsp;&nbsp; [!M3::Titre!]</option>							
					[/STORPROC]
				[/STORPROC]
			[/STORPROC]
			</select>
		</div>		
		