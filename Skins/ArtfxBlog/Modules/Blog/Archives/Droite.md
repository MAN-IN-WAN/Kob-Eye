<h2>Archives</h2>
[STORPROC Blog/Post|P|0|1|tmsCreate|ASC][/STORPROC]//[!DEBUG::P!]
[!Old:=[!Utils::getDate(Y,[!P::tmsCreate!])!]!]
[!New:=[!Utils::getDate(Y,[!TMS::Now!])!]!]

[!OldMonth:=[!Utils::getDate(n,[!P::tmsCreate!])!]!]
[!NewMonth:=[!Utils::getDate(n,[!TMS::Now!])!]!]
<!--<ul>-->
	[!New-=[!Old!]!]
	[STORPROC [!New:+1!]|Ann|0|1]
		[!NbMois:=12!]
		[!MoisDep:=0!]
		//si ann = 0, on affiche l annee du + vieux post.
		[IF [!New:-[!Ann!]!]=0]
			[!MoisDep:=[!OldMonth:-1!]!]
		[/IF]
		[IF [!Ann!]=0]
			[!NbMois:=[!NewMonth:-1!]!]
		[/IF]
		<span style="font-family:georgia,times,Arial,Verdana,serif;color:#ffbb00;font-weight:bold;">[!Old:+[!New:-[!Ann!]!]!]</span>
		<ul class="ListPostDate">
		[STORPROC 12|Mois|[!MoisDep!]|[!NbMois!]]
			[!Depart:=[!Utils::getTms(1,[!Mois:+1!],[!Old:+[!New:-[!Ann!]!]!])!]!]
			[!Fin:=[!Utils::getTms(1,[!Mois:+2!],[!Old:+[!New:-[!Ann!]!]!])!]!]
			[!Requete:=Blog/Post/tmsCreate>[!Depart!]&tmsCreate<[!Fin!]&Actif=1&Brouillon=0!]
			[COUNT [!Requete!]|NbPost]
			[IF [!NbPost!]]
				<li>					
					<a href="/Blog/Archives/Post?Mois=[!Mois:+1!]&amp;Annee=[!Old:+[!New:-[!Ann!]!]!]" title="Posts du mois de [UTIL MONTH][!Mois:+1!][/UTIL] [!Old:+[!New:-[!Ann!]!]!]">[UTIL MONTH][!Mois:+1!][/UTIL]([!NbPost!])</a>
				</li>
			[/IF]
		
		[/STORPROC]
		</ul>
		
	[/STORPROC]
<!--</ul>-->