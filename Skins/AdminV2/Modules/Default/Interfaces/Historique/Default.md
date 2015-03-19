//Historique
[STORPROC [!Chemin!]::pastHistory|Histo]
<div class="Historique">
	<div class="Panel">
		<h1>Navigation par historique</h1>
		<div>Retrouvez les pages pr&eacute;c&eacute;dentes de votre navigation ci-dessous</div>
	[LIMIT 0|100]
		<div class="HistoItem"><a href="/[!Histo::getUrl!]">[!Histo::ObjectType!] [!Histo::Id!]</a></div>
	[/LIMIT]
	</div>
</div>
<br />
[/STORPROC]
