[INFO [!Query!]|Inf]
[IF [!Inf::Nom!]!=]
	[TITLE]Sites internet réalisés par _expressiv™, agence web à Montpellier[/TITLE]
	[DESCRIPTION]Web agency basée à Montpellier 34, _expressiv™ réalise et crée des sites web dynamiques[/DESCRIPTION]
[/IF]
[IF [!Inf::TypeSearch!]=Child]
	<div style="overflow:hidden;">[!Inf::TypeSearch!]
		[MODULE Portfolio/Structure/Gauche]
		<div id="Milieu" style="margin-left:260px;">
			<div id="Data" style="border-top:1px solid #827152;">
				[STORPROC 5|L]
					[STORPROC Portfolio/Categorie/Publier=1|Cata|[!L:*3!]|3]
						
						<div style="overflow:hidden;[IF [!L!]>0]border-top:1px solid #827152;[/IF]">
							[LIMIT 0|3]
								<div style="float:left;width:229px;margin:0px 5px 15px 0;">
									<h1 class="Reference">[!Cata::Nom!]</h1>
									[IF [!Cata::Icone!]=]
										<a href="/[!Systeme::CurrentMenu::Url!]/Categorie/[!Cata::Url!]/Reference" title="[!Cata::Titre!]"><img src="/Skins/Expressiv/Img/RefDefault.jpg" width="229" height="133" alt="[!Cata::Titre!]"/></a>
									[ELSE]
										<a href="/[!Systeme::CurrentMenu::Url!]/Categorie/[!Cata::Url!]" title="[!Cata::Titre!]"><img src="/[!Cata::Icone!]" width="229" height="133" alt="[!Cata::Titre!]"/></a>
									[/IF]
									<p>[!Cata::Description!]</p>
								</div>
							[/LIMIT]
						</div>
					[/STORPROC]
				[/STORPROC]
			</div>
		</div>
	</div>
[ELSE]
	[MODULE Portfolio/Reference/Liste?Chemin=[!Query!]/Reference]
[/IF]
