//Recherche du tiers
[STORPROC Systeme/User/[!Systeme::User::Id!]/Third:NOVIEW|Th][/STORPROC]
<div class="siteWidth" style="padding-top:60px;">
	<div class="navbar navbar-inverse navbar-fixed-top siteWidth">
		<div class="navbar-inner">
			<button data-target=".ke-modules-menu" data-toggle="collapse" class="btn btn-navbar" type="button">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<div id="img_menu" class="pull-left img_menu"></div>
			<div class="ke-modules-menu nav-collapse collapse">
			<ul class="nav">
				[STORPROC [!Systeme::Menus!]/Affiche=1&&MenuHaut=1|M1]
				<li>
					[STORPROC Systeme/Menu/[!M1::Id!]/Menu/Affiche=1|M2]                  
					<a class="dropdown [IF [!M1::Url!]=[!Systeme::CurrentMenu::Url!]]active[/IF]" data-toggle="dropdown" href="#">[!M1::Titre!]</a>
					<ul class="dropdown-menu">
					[LIMIT 0|100]
						<li><a href="/[!M1::Url!]/[!M2::Url!]">[!M2::Titre!]</a></li>
					[/LIMIT]
					</ul>
					[NORESULT]
						[IF [!M1::Alias!]=Murphy/Enquiry&&[!Th::Buyer!]=]
						[ELSE]
							[IF [!M1::Alias!]=Murphy/Proposal&&[!Th::Supplier!]=]
							[ELSE]
								<a href="/[!M1::Url!]" class="[IF [!M1::Url!]=[!Systeme::CurrentMenu::Url!]]active[/IF]">[!M1::Titre!]</a>
							[/IF]
						[/IF]
						
					[/NORESULT]
					[/STORPROC]
				</li>
				[/STORPROC]
			</ul>
			<div id="InfosConnexion" class="pull-right currentuser">
				<span class="info_log">[!Systeme::User::Prenom!] [!Systeme::User::Nom!]</span>
				<a href="/Systeme/Deconnexion"><img src="/Skins/[!Systeme::Skin!]/img/deconnexion.png" /></a>
			</div>
			</div>
		</div>
	</div>
	[IF [!Systeme::CurrentMenu::Id!]>0]
	[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image|Img]    
	<div id="myCarousel" class="carousel slide">
		<div class="carousel-inner">
			[LIMIT 0|10]
			<div class="[IF [!Pos!]=1]active [/IF]item">
			<img src="/[!Img::Lien!]" alt="[!Img::Titre!]" />
			</div>
			[/LIMIT]
		</div>
		<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
	</div>
	[/STORPROC]
	[/IF]
</div>
