[STORPROC [!Query!]|Cat]
	[TITLE][!Cat::Nom!][IF [!Cat::Chapo!]!=] - [!Cat::Chapo!][/IF][/TITLE]
	[DESCRIPTION][SUBSTR 100][!Cat::Description!][/SUBSTR][/DESCRIPTION]
	[INFO [!Query!]|Inf]
	[STORPROC [!Inf::Historique!]|H|0|1]
		[!Niv0:=[!H::Value!]!]
	[/STORPROC]
	[MODULE Systeme/Structure/PhotoEntete]
	[MODULE Systeme/Structure/Droite?Cata=[!Niv0!]]
	<div id="Milieu">
		<div id="Data">
			[MODULE Systeme/Ariane]
			<div class="TitreCat">
				[MODULE Systeme/Options]
				[IF [!Cat::Icone!]=]
					<div class="TextDefault">
						<h1>[!Cat::Nom!]</h1>
						<h2 style="left:0;">[!Cat::Chapo!]</h2>
					</div>
				[ELSE]
					<div class="TextDefault">
						<h1 style="background:url(/[!Cat::Icone!]) no-repeat;height:40px;background-position:-1% 0%; padding-left:65px;">[!Cat::Nom!]</h1>
						[IF [!Cat::Chapo!]!=]
							<h2>[!Cat::Chapo!]</h2>
						[/IF]
					</div>
				[/IF]
			</div>
			<div>
				[IF [!Cat::Description!]]
					<p class="Description">[!Cat::Description!]</p>
				[/IF]
			</div>
			[!YaArt:=0!]
			[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|20|Ordre|ASC]
				[!YaArt:=1!]
				<div class="Article">
					<div class="Text" style="overflow:hidden;display:block;position:relative;">
						<p class="TitreArt">[!Art::Titre!]</p>
						[IF [!Art::Chapo!]]
							<p class="Chapo">[!Art::Chapo!]</p>
						[/IF]
						<div>[!Art::Contenu!]</div>
						[COUNT Redaction/Article/[!Art::Id!]/Fichier|F]
						[IF [!F!]]
							<ul style="padding:0;margin:0;">
							[STORPROC Redaction/Article/[!Art::Id!]/Fichier|Fic]
								<li style="background-position:left 5px;border-bottom:1px dashed #3F3F3F;"><a href="/[!Fic::URL!]" title="T&eacute;l&eacute;charger [!Fic::Titre!]" class="Lien">[!Fic::Titre!]</a></li>
							[/STORPROC]
							</ul>
						[/IF]
						[COUNT Redaction/Article/[!Art::Id!]/Lien|L]
						[IF [!L!]]
							[STORPROC Redaction/Article/[!Art::Id!]/Lien|Lie]
								<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="">[!Lie::Titre!]</a>
							[/STORPROC]
						[/IF]
						[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|100|Id|ASC]
							[IF [!Pos!]>4]
								<a href="/[!Img::URL!].limit.800x600.jpg" rel="lightbox[acc]" title="[!Img::Titre!]" style="display:none"></a>
							[ELSE]
								<div class="ImgArt">
									<a href="/[!Img::URL!].limit.800x600.jpg" rel="lightbox[acc]" title="[IF [!Img::Chapo!]!=][!Img::Chapo!][ELSE][!Img::Titre!][/IF]">
										<img src="/[!Img::URL!].mini.135x100.jpg" title="[!Img::Titre!]" alt="[!Img::Titre!]" />
									</a>
								</div>
							[/IF]
						[/STORPROC]
						[IF [!L!]]
							[STORPROC Redaction/Article/[!Art::Id!]]/Lien|Lie|0|10|Id|ASC]
								<a href="[!Lie::URL!]" title="[!Lie::Titre!]" [IF [!Lie::Type!]=Externe]onclick="window.open(this.href); return false;"[/IF] class="PrestaLien" style="padding-left:0;margin-left:0;">[!Lie::Titre!]</a>
							[/STORPROC]
						[/IF]
					</div>
				</div>
			[/STORPROC]
			[COUNT Redaction/Categorie/[!Cat::Id!]/Lien|CatLien]
			[IF [!CatLien!]]
				[STORPROC Redaction/Categorie/[!Cat::Id!]/Lien|CatLie|0|10|Id|ASC]
					<a href="[!CatLie::URL!]" title="[!CatLie::Titre!]" class="PrestaLien" style="margin:0;" [IF [!Lie::Type!]=Externe]  onclick="window.open(this.href); return false;" [/IF]>[!CatLie::Titre!]</a>
					
				[/STORPROC]
			[/IF]
			[IF [!YaArt!]]
				<a href="#" title="Revenir en haut de la page" class="HautPage">Haut de page</a>
			[/IF]
			[COUNT Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|C]
			[IF [!C!]]
				<div id="SousCat">
					<ul>
						[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|0|20]
							<li>
								<img src="/Skins/Gabarit1/Img/PuceBeige.png" style="padding-top:0;float:left;margin:0;width:18px;"  alt="puce"/>
								<a  href="/[!Systeme::CurrentMenu::Url!]/[!Cato::Link!]" title="[!Cato::Nom!]" [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]/[!Cato::Link!]] class="ActifDr" [/IF] >[!Cato::Nom!]</a>
							</li>
						[/STORPROC]
					</ul>
				</div>
			[/IF]
		</div>
	</div>
	<div class="Clear"></div>
[/STORPROC]