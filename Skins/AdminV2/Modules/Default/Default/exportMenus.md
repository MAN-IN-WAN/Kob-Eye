
[TITLE]Admin Kob-Eye | Exportation de menus[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau]
			<a href="/[!Lien!]">REFRESH</a>
		[/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			<pre style="display:block;position:relative;width:100%;height:95%;overflow:auto;background-color: white">
				[STORPROC [!Query!]|G]
					[!G::exportMenus!]
				[/STORPROC] 
			</pre>
		[/BLOC]
	</div>
</div>
