[IF [!Lien!]!=]
	//recherche de l'image de fond
	[!IMAGE:=!]
	[STORPROC Systeme/Menu/[!Systeme::CurrentMenu::Id!]/Donnee/Type=Image|IM|0|1]
		[!IMAGE:=[!IM::Lien!]!]
                [NORESULT]
                        [STORPROC Systeme/Menu/1/Donnee/Type=Image|IM]
            		[!IMAGE:=[!IM::Lien!]!]
                        [/STORPROC]
                [/NORESULT]
	[/STORPROC]
[/IF]
<header id="header" class="header-wrap" style="background-image: url(/[!IMAGE!].mini.2000x300.jpg);">

	<section class="topbar">
		<div class="container">

			[MODULE Systeme/Header/UserInformations]
			[MODULE Systeme/Header/PermaLinks]
			<div id="header_right" class="row" style="float:right">
				[MODULE Systeme/Header/TopSearch]
			</div>
		</div>
	</section>
	<section class="header">
		<div class="container" >
			<div class="row-fluid">
				<div class="span4 logo-wrapper text-center">
					<a id="header_logo" href="/" title="[!CurrentMagasin::Nom!]"> <img class="logo img-responsive" src="/[!CurrentMagasin::Logo!]" alt="[!CurrentMagasin::Nom!]" /> </a>
				</div>
				<div class="span8">
					<div class="row">
						//[MODULE Systeme/Header/Menu]
						[COMPONENT Systeme/Bootstrap2.MegaMenu]
					</div>
				</div>
			</div>
		</div>
	</section>

</header>
