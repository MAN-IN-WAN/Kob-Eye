[STORPROC [!Lien!]::Historique|His]
	<h1>Historique de navigation</h1>
	[LIMIT 0|100]
		[!Decal:=[!Pos:*5!]!]
		[STORPROC [!His::getUrl!]|Nav]
			<div style="padding-left:[!Decal!]px;height:16px;"><a href="/[!His::getUrl!]"><img src="[!Nav::getIcon!]"> [!Nav::ObjectType!] - [!Nav::getFirstSearchOrder!]</a></div>
		[/STORPROC]
	[/LIMIT]
	<hr style="border:0;border-top:1px dashed #CDCDCD"/>
	Ci-dessous les éléments de type [!QueryLastObject!] appartenant a l'élément [!Nav::getFirstSearchOrder!].
	<hr style="border:0;border-top:1px dashed #CDCDCD"/>
	[NORESULT]
	<a href="/[!Lien!]/Ajouter" style="margin:4px;width:98%;display:block;height:20px;background-color:white;border:1px solid #CDCDCD;text-align:center;padding-top:5px;">Ajouter [!QueryLastObject!] à la racine</a>
	[/NORESULT]
[/STORPROC]