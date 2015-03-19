[HEADER]
	<style type="text/css">
		#Ariane {display:none;}
		body{
			background:url(/Skins/Expressiv/Img/fond-gris.png);
			background-position:bottom;
			background-repeat:repeat-x;
		}
	</style>
	[DESCRIPTION]Agence de communication de creation de sites internet, hébergement, webdesign de site web Montpellier Hérault 34 - Nîmes Gard 30[/DESCRIPTION]
[/HEADER]
[STORPROC [!Query!]|Cat]
	[INFO [!Query!]|Inf]
	[STORPROC [!Inf::Historique!]|H|0|1]
		[!Niv0:=[!H::Value!]!]
	[/STORPROC]
	<div id="Milieu">
		<div id="Data">
			<div class="BlocHorizon" style="margin-top:5px;">
				[STORPROC Portfolio/Categorie/5|Cat][/STORPROC]
				[STORPROC Portfolio/Categorie/5/Reference/Publier=1|Ref|0|12|DateSortie|DESC]
					<div class="[IF [!Math::Round([!Pos:/4!])!]=[!Pos:/4!]]LeftLast[ELSE]Left[/IF]">
						<div class="inner">
							<a href="/Les-References/Categorie/[!Cat::Url!]/Reference/[!Ref::Url!]" title="Voir le d&eacute;tail de [!Ref::Titre!]">
								[IF [!Ref::Icone!]]
									<img src="/[!Ref::Icone!]" alt="[!Ref::Titre!]" />
								[ELSE]
									<img src="/Skins/Expressiv/Img/RefDefault.jpg" alt="[!Ref::Titre!]"/>
								[/IF]
							</a>
							<div class="InfoRef">
								<span class="DateSortie" style="float:right;">
									[DATE m.Y][!Ref::DateSortie!][/DATE]
								</span>
								<h2>[!Ref::Titre!]</h2>
							</div>
							<p style="width:230px;">[!Ref::Chapo!]</p>
						</div>
					</div>
				[/STORPROC]
			</div>
			<div class="BlocHorizon1">
				[STORPROC Redaction/Categorie/41/Article/Publier=1|Art|0|2|Ordre|ASC]
					<div class="Left">
						<div class="inner">
							<h3>[!Art::Titre!]</h3>
							<p style="text-align:justify;">[SUBSTR 280][!Art::Contenu!][/SUBSTR]...</p>
							<a href="/L-agence" title="Pr&eacute;sentation de la soci&eacute;t&eacute;" class="LaSuite">Lire la suite</a>
						</div>
					</div>
				[/STORPROC]
				<div class="Left">
					<div class="inner">
						<div style="width:230px;overflow:hidden;">
							<a href="[!Domaine!]/News/Nouvelle/Rss.xml" title="S'abonner au flux RSS des actualit&eacute;s" style="float:right;outline:0;border:0;padding-top:3px;" onclick="window.open(this.href);return false;">
								<img src="/Skins/Expressiv/Img/Icones/FilRSS.png" alt="Flux Rss" style="width:14px;height:15px;"/>
							</a>
							<h3 style="border-bottom:1px solid #939292;">News</h3>
						</div>
						[MODULE News/Colonne]
					</div>
				</div>
				<div class="LeftLast" style="width:230px;">
					[MODULE Portfolio/Clients/Logo]
				</div>
			</div>
		</div>
	</div>
[/STORPROC]