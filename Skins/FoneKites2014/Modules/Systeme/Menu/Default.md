	<div class="navbar navbar-inverse" data-spy="affix" data-offset-top="0" data-offset-bottom="0">
				<!-- Mise en conformité -->
		<div id="cnil-conformite" style="width:100%;">
			<div class="alert alert-disclaimer alert-dismissible hide" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<p>
					__DISCLAIMER_BANDEAU__
				</p>
			</div>
		</div>
		<!-- Mise en conformité -->

		<div class="container nopadding-right noppading-left">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle hidden-md hidden-lg  hidden-sm" data-toggle="collapse" data-target=".navbar-collapse">
				</button>
				<a class="navbar-brand img-responsive" href="/"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/logo_f-one.png" alt="Fone Kitesurfing"/>  </a>
			</div>
			<div class="navbar-collapse collapse">
				<div class="navbar-language">
					<ul>
						<li><a href="[!Domaine!]/[!Lien!]?SwitchLanguage=Francais">FR</a></li>
						<li><a href="[!Domaine!]/[!Lien!]?SwitchLanguage=Anglais">EN</a></li>
					</ul>
				</div>
				<div id="sb-search" class="sb-search hidden-xs">
					<form action="/[!Systeme::getMenu(Systeme/Recherche)!]">
						<input class="sb-search-input" placeholder="$MSGSEARCH$" type="text" value="[!Search!]" name="Search" id="search">
						<input class="sb-search-submit" type="submit" value="">
						<span class="sb-icon-search "></span>
					</form>
				</div>
				<div id="sb-search2" class="sb-search2 hidden-md hidden-lg hidden-sm" style="position:absolute;">
					<form action="/[!Systeme::getMenu(Systeme/Recherche)!]">
						<input class="sb-search-input" placeholder="$MSGSEARCH$" type="text" value="[!Search!]" name="Search" id="search">
						<input class="sb-search-submit" type="submit" value="">
						<span class="sb-icon-search "></span>
					</form>
				</div>
				<ul class="nav navbar-nav topmenu hidden-xs">
					[INFO [!Query!]|I]
					[STORPROC [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|M]
						// je regarde s'il y a un sous menu ou des sous categories
						[!SCat:=0!]
						[COUNT Systeme/Menu/[!M::Id!]/Menu/Affiche=1|SMen]
						[IF [!SMen!]>0]
							[!Req:=Systeme/Menu/[!M::Id!]/Menu/Affiche=1!]
						[ELSE]
							[IF [!M::Alias!]~Redaction]
								[!SCat:=0!]
							[ELSE]
								[!Req:=[!M::Alias!]!]
								[IF [!M::Alias!]~Blog]
									[COUNT [!M::Alias!]|SCat]
								[/IF]
								[IF [!M::Alias!]~Products]
									[COUNT [!M::Alias!]|SCat]
								[/IF]
								[IF [!M::Alias!]~Team]
									[COUNT [!M::Alias!]|SCat]
								[/IF]
								[IF [!M::Alias!]~Distributeur]
									[COUNT [!M::Alias!]|SCat]
								[/IF]
								[IF [!M::Alias!]~Galerie]
									[COUNT [!M::Alias!]|SCat]
									[!Req+=/Publier=1!]
								[/IF]
							[/IF]
						[/IF]
						[IF [!SCat!]>0||[!SMen!]>0]
							[INFO [!M::Alias!]|J]
							<li class="dropdown [IF [!Systeme::CurrentMenu::Id!]=[!M::Id!]] active [/IF]">
								<a href="#" onclick="location.href='/[!M::Url!]'" class="dropdown-toggle" data-toggle="dropdown" class="[IF [!Systeme::CurrentMenu::Id!]=[!M::Id!]] active [/IF] " data-filter=".*">[!M::Titre!] <b class="arrow_menu hidden-xs"></b></a>
								<ul class="dropdown-menu  sub-menu [IF [!I::Module!]=[!J::Module!]]filters[/IF]"> 
									[STORPROC [!Req!]|SCat|0|10|Ordre|ASC]
										<li><a href="[IF [!SCat::LienExterne!]!=][IF [!SCat::LienExterne!]~http][!SCat::LienExterne!][ELSE]/[!M::Url!]/[!SCat::LienExterne!][/IF][ELSE][IF [!SCat::Url!]~http][!SCat::Url!][ELSE]/[!M::Url!]/[!SCat::Url!][/IF][/IF]" [IF [!SCat::LienExterne!]~http||[!SCat::Url!]~http]target="_blank"[/IF] [IF [!SCat::LienExterne!]=]class="filter" data-filter=".[!SCat::Url!]"[/IF]>
											[IF [!Req!]~Products][!SCat::Nom!][ELSE][!SCat::Titre!][!SCat::Nom!][/IF]
										</a></li>
									[/STORPROC]
								</ul>
							</li>
						[ELSE]
							<li class="[IF [!Systeme::CurrentMenu::Id!]=[!M::Id!]] active [/IF]">
								[IF [!M::Url!]~http]
									<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
								[ELSE]
									<a [IF [!SCat!]>0||[!SMen!]>0]class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" href="#"[ELSE]href="/[!M::Url!]"[/IF]>
										[!M::Titre!]
									</a>
								[/IF]
							</li>
						[/IF]
					[/STORPROC]
				</ul>
				<ul class="nav navbar-nav topmenu hidden-md hidden-sm hidden-lg masonry-navbar" style="-webkit-overflow-scrolling: touch;" id="scroller">
					[INFO [!Query!]|I]
					[STORPROC [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|M]
						// je regarde s'il y a un sous menu ou des sous categories
						[!SCat:=0!]
						[COUNT Systeme/Menu/[!M::Id!]/Menu/Affiche=1|SMen]
						[IF [!M::Alias!]~Redaction]
							[!SCat:=0!]
						[ELSE]
							[!Req:=[!M::Alias!]!]
							[IF [!M::Alias!]~Blog]
								[COUNT [!M::Alias!]|SCat]
							[/IF]
							[IF [!M::Alias!]~Products]
								[COUNT [!M::Alias!]|SCat]
							[/IF]
							[IF [!M::Alias!]~Team]
								[COUNT [!M::Alias!]|SCat]
							[/IF]
							[IF [!M::Alias!]~Distributeur]
								[COUNT [!M::Alias!]|SCat]
							[/IF]
							[IF [!M::Alias!]~Galerie]
								[COUNT [!M::Alias!]|SCat]
								[!Req+=/Publier=1!]
							[/IF]
						[/IF]
						[IF [!SCat!]>0||[!SMen!]>0]
							[INFO [!M::Alias!]|J]
							<li class=" [IF [!Systeme::CurrentMenu::Id!]=[!M::Id!]] active [/IF] col-xs-6 masonry-item">
								<a href="#" onclick="location.href='/[!M::Url!]'"  data-toggle="dropdown" class="[IF [!Systeme::CurrentMenu::Id!]=[!M::Id!]] active [/IF] " data-filter=".*">[!M::Titre!] <b class="arrow_menu hidden-xs"></b></a>
								<ul class="  sub-menu [IF [!I::Module!]=[!J::Module!]]filters[/IF]"> 
									[STORPROC [!Req!]|SCat|0|10|Ordre|ASC]
										<li><a href="[IF [!SCat::LienExterne!]!=][IF [!SCat::LienExterne!]~http][!SCat::LienExterne!][ELSE]/[!M::Url!]/[!SCat::LienExterne!][/IF][ELSE][IF [!SCat::Url!]~http][!SCat::Url!][ELSE]/[!M::Url!]/[!SCat::Url!][/IF][/IF]" [IF [!SCat::LienExterne!]~http||[!SCat::Url!]~http]target="_blank"[/IF] [IF [!SCat::LienExterne!]=]class="filter" data-filter=".[!SCat::Url!]"[/IF]>
											[IF [!Req!]~Products][!SCat::Nom!][ELSE][!SCat::Titre!][!SCat::Nom!][/IF]
										</a></li>
									[/STORPROC]
								</ul>
							</li>
						[ELSE]
							<li class="[IF [!Systeme::CurrentMenu::Id!]=[!M::Id!]] active [/IF] col-xs-6 masonry-item">
								[IF [!M::Url!]~http]
									<a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
								[ELSE]
									<a [IF [!SCat!]>0||[!SMen!]>0]class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" href="#"[ELSE]href="/[!M::Url!]"[/IF]>
										[!M::Titre!]
									</a>
								[/IF]
							</li>
						[/IF]
					[/STORPROC]
				</ul>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$('.nav li a').on('click',function(event){
			if ($(this).attr('href')!="#") {
				$('.navbar-collapse.in').collapse('hide');
			}else event.preventDefault();
		})
		/*jquery fix for navbar height*/
		$(function () {
			var startY = 0;
			var startX = 0;
			var b = document.body;

			//resize
			$(window).resize(function(){
				console.log('apply navbar fix');
				$('.masonry-navbar').isotope({
					layoutMode : 'masonry'
				});
				$('.navbar-collapse').css('max-height',$(window).height()-75);
				$('.masonry-navbar').css('height',$(window).height()-150);
			});
			console.log('apply navbar fix');
			$(".navbar-collapse.collapse").on('shown.bs.collapse', function () {
				console.log('collapse event shown');
				$('.masonry-navbar').isotope({
					layoutMode : 'masonry'
				});
				$('.navbar-collapse').css('max-height',$(window).height()-75);
				$('.masonry-navbar').css('height',$(window).height()-150);
				b.addEventListener('touchstart', onTouchStart);
				b.addEventListener('touchmove', onTouchMove);
			});
			$(".navbar-collapse.collapse").on('hidden.bs.collapse', function () {
				console.log('collapse event hidden');
				b.removeEventListener('touchstart', onTouchStart);
				b.removeEventListener('touchmove', onTouchMove);
			});
			function onTouchStart(event) {
				console.log('touchstart');
			    parent.window.scrollTo(0, 1);
			    startY = event.targetTouches[0].pageY;
			    startX = event.targetTouches[0].pageX;
			}
			function onTouchMove (event) {
				console.log('touchmove');
			    event.preventDefault();
			    var posy = event.targetTouches[0].pageY;
			    var h = parent.document.getElementById("scroller");
			    var sty = h.scrollTop;
			
			    var posx = event.targetTouches[0].pageX;
			    var stx = h.scrollLeft;
			    h.scrollTop = sty - (posy - startY);
			    h.scrollLeft = stx - (posx - startX);
			    startY = posy;
			    startX = posx;
			}
		});
	</script>
