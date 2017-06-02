<div class="row">
	<div class="col-md-6">
		<ul class="MenuFooter row">
			[!Cpt:=0!]
			[STORPROC Systeme/Menu/MenuBas=1&MenuPrincipal=1&Affiche=1|M]
				[IF [!Cpt!]=2][!Cpt:=0!]</ul><ul class="MenuFooter row" >[/IF]
				<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] col-md-5">
					[IF [!M::Url!]~http]
						<a href="[!M::Url!]" target="_blank" >[!M::Titre!] [IF [!M::SousTitre!]!=]....[/IF]</a>
					[ELSE]
						<a href="/[!M::Url!]" >[!M::Titre!] [IF [!M::SousTitre!]!=]...[/IF]</a>
					[/IF]
				</li>
				[!Cpt+=1!]
			[/STORPROC]
			<li class="[IF [!Systeme::CurrentMenu::Url!]=[!Systeme::getMenu(News/Nouvelle)!]] active [/IF] col-md-5">
//				<a href="/[!Systeme::getMenu(Reservation/Spectacle/ListeFinis)!]" >Derniers spectacles</a>
				<a href="/[!Systeme::getMenu(News/Nouvelle)!]" >Aux dernière nouvelles</a>
			</li>
		</ul>
	</div>
	<div class="col-md-6 FooterDroit">
		<div class="row">
			<div class="col-md-12">
				<ul class="MenuFooter row">
					<a href="/AccesPro" class="btn btn-bleu" style="color:#fff;float:right;margin-right:30px;">Accès partenaires sociaux</a>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<ul class="MenuFooter2 row">
					[!Cpt:=0!]
					[STORPROC Systeme/Menu/MenuBas=1&MenuPrincipal=0&Affiche=1|M]
						//[IF [!Cpt!]=3][!Cpt:=0!]</ul><ul class="MenuFooter row" >[/IF]
			//			<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] col-md-[IF [!Cpt!]=0]5[ELSE]3[/IF]">
						<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] col-md-4">
							[IF [!M::Url!]~http]
								<a href="[!M::Url!]" target="_blank" >[!M::Titre!] [IF [!M::SousTitre!]!=]...[/IF]</a>
							[ELSE]
								<a href="/[!M::Url!]" >[!M::Titre!] [IF [!M::SousTitre!]!=]...[/IF]</a>
							[/IF]
						</li>[!Cpt+=1!]
					[/STORPROC]
				</ul>
			</div>		
		</div>


	</div>

</div>