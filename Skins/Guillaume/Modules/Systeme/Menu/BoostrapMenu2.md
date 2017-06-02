<nav class="navbar navbar-default menuPrincipal2" role="navigation">
	<!-- formats téléphone -->
	<div class="navbar-header visible-xs visible-sm menutelephone">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-menuprincipal">
			<span class="sr-only"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand hidden-md hidden-lg hidden-sm" href="#" >Menu</a>
	</div>
	<!-- Menu autre format-->
	<div >
		<div class="navbar-menuprincipal2 ">
			<ul class="row navbar-nav menuPrincipal2 ">
				[STORPROC Systeme/Menu/Affiche=1&MenuPrincipal=1|M]
					// je regarde s'il y a un sous menu ou des sous categories
					[!SCat:=0!]
					[COUNT Systeme/Menu/[!M::Id!]/Menu/Affiche=1|SMen]
					[!Req:=[!M::Alias!]/Categorie!]
					[IF [!M::Alias!]~Redaction]
						[COUNT [!M::Alias!]/Categorie/Publier=1|SCat]
						[!Req:=[!M::Alias!]/Categorie/Publier=1!]
					[/IF]
					<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] [IF [!Pos!]=[!NbResult!]]Last[/IF]  " style="[IF [!M::BackgroundColor!]!=]background-color:[!M::BackgroundColor!];[/IF][IF [!M::SousTitre!]=]padding:15px 7px;[/IF] ">
						[IF [!M::Url!]~http]
							<a href="[!M::Url!]" target="_blank">[!M::Titre!][IF [!M::SousTitre!]!=]<br />[!M::SousTitre!][/IF]</a>
							
						[ELSE]
							<a [IF [!SCat!]>0||[!SMen!]>0]class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" href="#"[ELSE]href="/[!M::Url!]"[/IF]>
								[!M::Titre!][IF [!M::SousTitre!]!=]<br />[!M::SousTitre!][/IF]
							</a>
						[/IF]
						[IF [!SCat!]>0]
							<ul class="dropdown-menu ">
								[STORPROC [!Req!]|SCat|0|10|Ordre|ASC]
									<li>
										<a href="[IF [!SCat::Url!]~http][ELSE]/[/IF][!M::Url!]/[!SCat::Url!]" [IF [!SCat::Url!]~http]target="_blank"[/IF] >[!SCat::Nom!]</a>
										
									</li>
								[/STORPROC]
							</ul>
						[/IF]
					</li>
				[/STORPROC]

			</ul>
		</div><!-- /.navbar-collapse -->
	</div>
</nav>