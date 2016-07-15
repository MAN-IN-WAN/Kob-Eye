[INFO [!Query!]|I]
[INFO [!Lien!]|J]
[!SHOW_ALL:=1!]
//Recherche du menu racine
//[COUNT [!I::Historique!]|NbNiv]
[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Html|Do|0|1]
	<div class="baseline"><div class="container">
		[!Do::Html!]
	</div></div>
[/STORPROC]

[!SCat:=0!]
[IF [!J::NbHisto!]>1]
	[STORPROC [!Systeme::CurrentMenu::getParents(Menu)!]|P]
		[COUNT [!P::getSubMenus()!]|SMen]
		[!ReqSys:=[!P::getSubMenus()!]!]
		[!SHOW_ALL:=0!]
		[NORESULT]
			[COUNT [!Systeme::CurrentMenu::getSubMenus()!]|SMen]
			[!Req:=[!Systeme::CurrentMenu::Alias!]!]
		[/NORESULT]
	[/STORPROC]
[ELSE]
	[COUNT [!Systeme::CurrentMenu::getSubMenus()!]|SMen]
	[!Req:=[!Systeme::CurrentMenu::Alias!]!]
[/IF]
[IF [!Systeme::CurrentMenu::Alias!]~Redaction]
	[!SHOW_ALL:=0!]
	[COUNT [!Systeme::CurrentMenu::Alias!]/Categorie/Publier=1|SCat]
	[!Req:=[!Systeme::CurrentMenu::Alias!]/Categorie/Publier=1!]
[/IF]
[IF [!Systeme::CurrentMenu::Alias!]~Blog]
	[COUNT [!Systeme::CurrentMenu::Alias!]|SCat]
[/IF]
[IF [!Systeme::CurrentMenu::Alias!]~Products]
	[COUNT [!Systeme::CurrentMenu::Alias!]|SCat]
[/IF]
[IF [!Systeme::CurrentMenu::Alias!]~Team]
	[COUNT [!Systeme::CurrentMenu::Alias!]|SCat]
[/IF]
[IF [!Systeme::CurrentMenu::Alias!]~Distributeur]
	// on affiche le sous menu dans le module car on insère une carte clicable au dessus
//	[COUNT [!Systeme::CurrentMenu::Alias!]|SCat]
[/IF]
[IF [!Systeme::CurrentMenu::Alias!]~Galerie]
	[COUNT [!Systeme::CurrentMenu::Alias!]/Publier=1|SCat]
	[!Req+=/Publier=1!]
[/IF]


[IF [!SCat!]>0||[!SMen!]>0]
	<div class="second-menu hidden-xs">
		<div class="container nopadding-left nopadding-right">
//			<div class="collapse navbar-collapse navbar-ex1-collapse">
			<div class="wrapper filters">
				[COUNT [!Query!]|NbNiv]
				[!Tous:=0!]
				[IF [!NbNiv!]>1] [!Tous:=1!][/IF]
//				<ul class="nav navbar-second-nav filters">
					[IF [!SHOW_ALL!]]
//					<li >
					<aside class="aside aside-1" >
						<a href="/[!Systeme::CurrentMenu::Url!]#" data-filter=".all" class="[IF [!Tous!]=1]filteractive[/IF] filter">__SHOW_ALL__</a>
					</aside>
//					</li>
					[/IF]
					[STORPROC [!Req!]|CatP]
//						<li >
						<aside class="aside aside-1" >
							<a href="[IF [!CatP::LienExterne!]!=][!CatP::LienExterne!][ELSE]/[!Systeme::CurrentMenu::Url!]/[!CatP::Url!][/IF]" [IF [!CatP::LienExterne!]!=][ELSE]data-filter=".[!CatP::Url!]"[/IF] class="selector [IF [!CatP::LienExterne!]=]filter[/IF] [IF [!Lien!]~[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]] filteractive [/IF]" [IF [!CatP::LienExterne!]~http||[!CatP::Url!]~http]target="_blank"[/IF] data-nomoremedia="[!CatP::NoMoreMedia!]" >
								[IF [!Req!]~Products||[!Req!]~Redaction][!CatP::Nom!][ELSE][!CatP::Titre!][/IF]
							</a>
						</aside>
//						</li>
					[/STORPROC]
					[STORPROC [!ReqSys!]|CatP]
//						<li >
						<aside class="aside aside-1" >
							<a href="/[!P::Url!]/[!CatP::Url!]" class="selector  [IF [!Lien!]~[!CatP::Url!]] filteractive [/IF]"> [!CatP::Titre!]</a>
						</aside>
//						</li>
					[/STORPROC]
//				</ul>
			</div>
		</div>
	</div>
[/IF]
