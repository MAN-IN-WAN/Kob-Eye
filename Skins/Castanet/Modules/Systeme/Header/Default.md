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
<header id="header" class="header-wrap" style="background-image: url(/[!IMAGE!]);">

	<section class="topbar">
		<div class="container">

			[MODULE Systeme/Header/UserInformations]			
			[MODULE Systeme/Header/MiniPanier]			
			[MODULE Systeme/Header/PermaLinks]			
		</div>
	</section>
	<section class="header">
		<div class="container" >
			<div class="row-fluid">
				<div class="span4 logo-wrapper text-center">
					<a id="header_logo" href="[IF [!Lien!]]/[ELSE]/Systeme/Cat[/IF]" title="[!CurrentMagasin::Nom!]"> <img class="logo img-responsive" src="/[!Systeme::User::Avatar!]" alt="[!CurrentMagasin::Nom!]" /> </a>
				</div>
				<div class="span8">
					<div id="header_right" class="row">
						[MODULE Systeme/Header/TopSearch]
						//[MODULE Systeme/Header/Currency]
						//[MODULE Systeme/Header/Languages]
					</div>
					<div class="row">
						//[MODULE Systeme/Header/Menu]
						[COMPONENT Systeme/Bootstrap.MegaMenu]
					</div>
				</div>
			</div>
		</div>
	</section>

</header>
