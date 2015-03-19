<div id="Archives">
	<h1>Archives</h1>
	[STORPROC Blog/Post|P|0|1|tmsCreate|ASC][/STORPROC]//[!DEBUG::P!]
	[!Old:=[!Utils::getDate(Y,[!P::tmsCreate!])!]!]
	[!New:=[!Utils::getDate(Y,[!TMS::Now!])!]!]
	
	[!OldMonth:=[!Utils::getDate(n,[!P::tmsCreate!])!]!]
	[!NewMonth:=[!Utils::getDate(n,[!TMS::Now!])!]!]
	[BLOC Rounded|background-color:#0C0C0C;||padding:5px;]
		[!New-=[!Old!]!]
		[STORPROC [!New:+1!]|Ann|0|[!Old!]]
			[!NbMois:=12!]
			[!MoisDep:=0!]
			//si ann = 0, on affiche l annee du + vieux post.
			[IF [!New:-[!Ann!]!]=0]
				[!MoisDep:=[!OldMonth:-1!]!]
			[/IF]
			[IF [!Ann!]=0]
				[!NbMois:=[!NewMonth:-1!]!]
			[/IF]
			
			<ul class="ListArchives">
				<li style="border-bottom:none;"><h2 style="padding:0;">[!Old:+[!New:-[!Ann!]!]!]</h2></li>
				[STORPROC 12|Mois|[!MoisDep!]|[!NbMois!]]
					[!Depart:=[!Utils::getTms(1,[!Mois:+1!],[!Old:+[!New:-[!Ann!]!]!])!]!]
						[!Fin:=[!Utils::getTms(1,[!Mois:+2!],[!Old:+[!New:-[!Ann!]!]!])!]!]
						[!Requete:=Blog/Post/tmsCreate>[!Depart!]&tmsCreate<[!Fin!]&Actif=1&Brouillon=0!]
						[COUNT [!Requete!]|NbPost]
						[IF [!NbPost!]]
							<li>
								
								<a href="/Blog/Archives/Post?Mois=[!Mois:+1!]&amp;Annee=[!Old:+[!New:-[!Ann!]!]!]" title="Posts du mois de [UTIL MONTH][!Mois:+1!][/UTIL] [!Old:+[!New:-[!Ann!]!]!]">[UTIL MONTH][!Mois:+1!][/UTIL]([!NbPost!])</a>
								
									<ul style="padding-top:2px;">
										[STORPROC [!Requete!]|Po|0|5|tmsCreate|DESC]//[!DEBUG::Po!]
											<li class="ListPost" style="padding:0;"><a href="/Blog/Post/[!Po::Link!]" title="[!Po::Titre!], post du [DATE d.m.Y][!Po::tmsCreate!][/DATE]" style="background:none;color:#FFBB00;">
											<span style="font-family:georgia,times,Arial,Verdana,serif;color:#ffbb00;font-weight:bold;">[DATE d][!Po::tmsCreate!][/DATE] . </span>[SUBSTR 25|[...]][!Po::Titre!][/SUBSTR]</a>
											</li>
										[/STORPROC]
											<li style="padding:5px 0 0;"><a href="/Blog/Archives/Post?Mois=[!Mois:+1!]&amp;Annee=[!Old:+[!New:-[!Ann!]!]!]" title="Posts du mois de [UTIL MONTH][!Mois:+1!][/UTIL] [!Old:+[!New:-[!Ann!]!]!]" style="background:none;color:#FFBB00;">Tous les posts ...</a>
											</li>
									</ul>
							</li>
						[/IF]
				[/STORPROC]
			</ul>
			
			//Si la liste est paire, je mets un clear
			[IF [!Math::Floor([!Pos:/2!])!]==[!Pos:/2!]]<div style="clear:left;"></div>[/IF]		
		[/STORPROC]
		
	[/BLOC]
</div>
