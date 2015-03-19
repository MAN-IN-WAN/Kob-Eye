[MODULE Systeme/Structure/PhotoEntete]
[MODULE Systeme/Structure/Droite?Cata=[!Niv0!]]
[STORPROC [!Query!]|N][/STORPROC]
<div id="Milieu">
	<div id="Data">
		[MODULE Systeme/Ariane]
			<div class="TitreCat">
				<div id="Options">
					<a href="/Redaction/Affich/SendToFriend?Rubrique=[!Cat::Nom!]&amp;TitreMenu=[!Systeme::CurrentMenu::Titre!]" title="Envoyer la page &agrave; un ami"><img src="/Skins/Gabarit1/Img/IconeEnvoyer.jpg" alt="Envoyer &agrave; un ami" style="padding-top:15px;" /></a>
					<a href="/News/Categorie/2/Nouvelle/[!N::Id!]/Imprimer.print" title="Imprimer la page"><img src="/Skins/Gabarit1/Img/IconeImprimer.jpg" alt="Imprimer la page"  style="padding-top:15px;"/></a>
				</div>
				[IF [!Cat::Icone!]=]
					<div class="TextDefault">
						<h1>[!N::Titre!]</h1>
						<h2 style="left:0;">[!N::Chapo!]</h2>
					</div>
				[ELSE]
					<div class="TextDefault">
						<h1 style="background:url(/[!Cat::Icone!]) no-repeat;height:40px;background-position:-1% 0%; padding-left:65px;">[!N::Nom!]</h1>
						[IF [!N::Chapo!]!=]
							<h2>[!N::Chapo!]</h2>
						[/IF]
					</div>
				[/IF]
			</div>
		<div class="Article">
			[IF [!N::Image!]!=]
				<div class="ImgArt">
					<a href="/[!N::Image!].limit.800x800.jpg" rel="lightbox[acc]" title="[!N::Titre!]"><img src="/[!N::Image!].limit.120x200.jpg" alt="[!N::Titre!]" title="[!N::Titre!]" /></a>
				</div>
			[/IF]
			<div class=[IF [!N::Image!]!=]"TextImg"[ELSE]"Text"[/IF]>
				[!N::Contenu!]
				[COUNT News/Nouvelle/[!N::Id!]/Fichier|F]
				[IF [!F!]]
					[STORPROC News/Nouvelle/[!N::Id!]/Fichier|Fic|0|10|Id|ASC]
						<a href="/[!Fic::URL!]" title="[!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a>
					[/STORPROC]
				[/IF]
				[COUNT News/Nouvelle/[!N::Id!]/Lien|L]
				[IF [!L!]]
<!-- 					<div id="SousCat"> -->
<!-- 					<ul> -->
					[STORPROC News/Nouvelle/[!N::Id!]/Lien|Lie|0|10|Id|ASC]
						<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="PrestaLien" style="margin-left:10px;">[!Lie::Titre!]</a>
					[/STORPROC]
<!-- 					</ul> -->
<!-- 					</div> -->
				[/IF]
			</div>
		</div>
		[COUNT News/Categorie/2/Nouvelle|Nn]
		[IF [!Nn!]>1]
			<div class="HistoNews" >					
				<h4>Nos autres offres sp&eacute;ciales :</h4>
				<ul>
					[STORPROC News/Categorie/2/Nouvelle/Publier=1|Neu|0|6|Id|DESC]
						[IF [!Neu::Id!]=[!N::Id!]]
						[ELSE]
							<li>
								<a href="/Offres-speciales/Nouvelle/[!Neu::Url!]" title="[!Neu::Titre!]" style=""><span class="Bold">[!Neu::Titre!]</span> [DATE d.m.Y][!Neu::tmsCreate!][/DATE]</a>
								
							</li>
						[/IF]
					[/STORPROC]
				</ul>
			</div>
		[/IF]
	</div>
</div>
<div class="Clear"></div>