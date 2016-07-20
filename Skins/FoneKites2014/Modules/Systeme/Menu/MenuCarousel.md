[INFO [!Lien!]|I]
[IF [!Lien!]~Locator||[!Lien!]~Localiser][ELSE]
	[IF [!Lien!]=[!Systeme::CurrentMenu::Url!]]
		//Slider du menu en cours
		[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image+Type=ImageVideo|M]
			[!M::Image:=[!M::Lien!]!]
			[!Sliders:::=[!M!]!]
			[NORESULT]
				[IF [!I::NbHisto!]>1]
					//on récupère le menu parent
					[STORPROC Systeme/Menu/Menu/[!Systeme::CurrentMenu::Id!]|MP]
						[STORPROC Systeme/Menu/[!MP::Id!]/Donnee/Type=Image+Type=ImageVideo|M]
							[!M::Image:=[!M::Lien!]!]
							[!Sliders:::=[!M!]!]
						[/STORPROC]
					[/STORPROC]
				[ELSE]
					//on récupère le menu principal
					[STORPROC Systeme/Menu/Url=/Donnee/Type=Image+Type=ImageVideo|M]
						[!M::Image:=[!M::Lien!]!]
						[!Sliders:::=[!M!]!]
					[/STORPROC]
				[/IF]
			[/NORESULT]
		[/STORPROC]
	[ELSE]
		[INFO [!Query!]|I]
		[IF [!I::TypeSearch!]=Direct||[!I::TypeSearch!]=Interface]
			[STORPROC [!Query!]/Donnee/Type=Image+Type=ImageVideo|P]
				[!Sliders:::=[!P!]!]
				[NORESULT]
					[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image+Type=ImageVideo|M]
						[!M::Image:=[!M::Lien!]!]
						[!Sliders:::=[!M!]!]
						[NORESULT]
							[IF [!I::NbHisto!]>1]
								//on récupère le menu parent
								[STORPROC Systeme/Menu/Menu/[!Systeme::CurrentMenu::Id!]|MP]
									[STORPROC Systeme/Menu/[!MP::Id!]/Donnee/Type=Image+Type=ImageVideo|M]
										[!M::Image:=[!M::Lien!]!]
										[!Sliders:::=[!M!]!]
									[/STORPROC]
								[/STORPROC]
							[ELSE]
								//on récupère le menu principal
								[STORPROC Systeme/Menu/Url=/Donnee/Type=Image+Type=ImageVideo|M]
									[!M::Image:=[!M::Lien!]!]
									[!Sliders:::=[!M!]!]
								[/STORPROC]
							[/IF]
						[/NORESULT]
					[/STORPROC]
				[/NORESULT]
			[/STORPROC]
		[/IF]
	[/IF]
[/IF]


[STORPROC [!Sliders!]|Do]
	<div id="headerCarousel" class="carousel slide hidden-xs">
		<div class="carousel-inner">
			[LIMIT 0|100]
				<div class="item [IF [!Pos!]=1]active[/IF]"   style="background:url(/[!Do::Lien!].mini.2560x590.jpg) no-repeat scroll center center;">
					<div class="inner-item" >
						//<img src="/[!Do::Lien!].mini.2560x590.jpg" data-src="/[!Do::Lien!].mini.2560x590.jpg"  alt="[IF [!Pos!]=1]First[/IF] slide"/>
						<div class="container">
							[IF [!Do::Type!]=ImageVideo]
							<div class="carousel-video" style="position: absolute;top: 0;left: 50%;">
								[!Do::VideoHtml!]
							</div>
							[/IF]
							<div class="carousel-caption">
								[IF [!Do::AfficheTitre!]]
									<h1>[IF [!Do::LienTitre!]!=]<a href="[IF [!Do::LienTitre!]~http][ELSE]/[/IF][!Do::LienTitre!]" alt="[!Do::Titre!]" [IF [!Do::LienTitre!]~http] target="_blank"[/IF]>[/IF][!Do::Titre!][IF [!Do::LienTitre!]!=]</a>[/IF]</h1>
									<p>[IF [!Do::LienTitre!]!=]<a href="[IF [!Do::LienTitre!]~http][ELSE]/[/IF][!Do::LienTitre!]" alt="[!Do::Titre!]" [IF [!Do::LienTitre!]~http] target="_blank"[/IF]>[/IF][!Do::Html!][IF [!Do::LienTitre!]!=]</a>[/IF]</p>
								[/IF]
							</div>
						</div>
					</div>
				</div>
			[/LIMIT]
		</div>
		<a class="left carousel-control" href="#headerCarousel" data-slide="prev"><span class="carouleft"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-left.png" alt="left"/></span></a>
		<a class="right carousel-control" href="#headerCarousel" data-slide="next"><span class="carouright"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-right.png" alt="left"/></span></a>
	</div>
	<script type="text/javascript">
		$(function () {
			//cycle carousel
			$('.carousel').carousel('cycle');
		});
	</script>
[/STORPROC]

