<!--Boutique/Menu/Default-->
[!UniversEnCours:=0!]
//recherche de l'univers en cours pour le menu demand�
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|1|1]
	[!MenuDemande:=[!H::Value!]!]
[/STORPROC]
[!MenuLu:=0!]
<ul class="menuprincipal">
	[STORPROC Boutique/Univers/Menu=1|Univ|0|100|Ordre|ASC]
		// affichage des items de menu
		[IF [!Pos!]=[!NbResult!]] [!MenuFin:=1!][/IF]
		[STORPROC Boutique/Univers/[!Univ::Id!]/Categorie/Menu=1|Men]
			[IF [!MenuDemande!]=[!Men::Url!] ]
				// je suis dans le menu actif
				[IF [!MenuLu!]=0] 
					// pour ne pas faire apparaitre le border si on est au d�but
					[!MenuLu:=1!]
					<li style="border-left:none;float:left;background:url(/[!Univ::ImgFondMenuSelect!]) repeat-x;">
						<a style="color:[!Univ::TexteMenuColorSelect!];"  href="/GamesAvenue/[!Men::Url!]" >[!Men::Nom!]</a>
					</li>
				[ELSE]
					[IF [!MenuFin!]] 
						<li style="float:left;background:url(/[!Univ::ImgFondMenuSelect!]) repeat-x;padding-right:0px">
							<a style="color:[!Univ::TexteMenuColorSelect!];"  href="/GamesAvenue/[!Men::Url!]" >[!Men::Nom!]</a>
						</li>
					[ELSE]
						<li style="float:left;background:url(/[!Univ::ImgFondMenuSelect!]) repeat-x;">
							<a style="color:[!Univ::TexteMenuColorSelect!];"  href="/GamesAvenue/[!Men::Url!]" >[!Men::Nom!]</a>
						</li>
					[/IF]
				[/IF]
			[ELSE]
				[IF [!MenuLu!]=0]
					// pour ne pas faire apparaitre le border si on est au d�but
					[!MenuLu:=1!]
					<li style="border-left:none;float:left;background:url(/[!Univ::ImgFondMenu!]) repeat-x;"><a style="color:[!Univ::LienColor!];"  onmouseover="this.style.color='[!Univ::LienRollOverColor!]';" onmouseout="this.style.color='[!Univ::LienColor!]';" href="/GamesAvenue/[!Men::Url!]" >[!Men::Nom!]</a></li>
				[ELSE]
					[IF [!MenuFin!]] 
						<li style="float:left;background:url(/[!Univ::ImgFondMenu!]) repeat-x;;padding-right:0px">
							<a style="color:[!Univ::LienColor!];"  onmouseover="this.style.color='[!Univ::LienRollOverColor!]';" onmouseout="this.style.color='[!Univ::LienColor!]';" href="/GamesAvenue/[!Men::Url!]" >
								[!Men::Nom!]
							</a>
						</li>
					[ELSE]
						<li style="float:left;background:url(/[!Univ::ImgFondMenu!]) repeat-x;">
							<a style="color:[!Univ::LienColor!];"  onmouseover="this.style.color='[!Univ::LienRollOverColor!]';" onmouseout="this.style.color='[!Univ::LienColor!]';" href="/GamesAvenue/[!Men::Url!]" >[!Men::Nom!]</a>
						</li>
					[/IF]
				[/IF]
			[/IF]
		[/STORPROC]
	[/STORPROC]
</ul>