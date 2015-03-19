[!Page[!TypeEnf!]:=1!]
[!NbChamp:=2!]
			<h1 style="height:20px;background:url(/Skins/AdminV2/Img/Tabs/hover_m.png);"> Recherche : <input type="text" name="Rech[!TypeEnf!]" value="[!Rech!]" style="width:120px;"> <input type="submit" ></h1>
			<div class="Page" style="height:20px;background:url(/Skins/AdminV2/Img/Tabs/hover_m.png);">
				[IF [!Page[!TypeEnf!]!]>1]
				<input id="First" type="image" src="/Skins/AdminV2/Img/page-first.gif" value="1" name="Page[!TypeEnf!]">
				<input id="Prev" type="image" src="/Skins/AdminV2/Img/page-prev.gif" value="[!Page[!TypeEnf!]:-1!]" name="Page[!TypeEnf!]">
				[/IF]
				<div>Page : [!Page[!TypeEnf!]!]</div>
				<input id="Last" type="image" src="/Skins/AdminV2/Img/page-last.gif" value="[!Page[!TypeEnf!]:+1!]" name="Page[!TypeEnf!]"> 
				<input id="Next" type="image" src="/Skins/AdminV2/Img/page-next.gif" value="[!Page[!TypeEnf!]:+1!]" name="Page[!TypeEnf!]"> 
			</div>
	[STORPROC [!Chemin!]|Ob|0|1]
	<table cellspacing="0" border="1" cellpadding="0" width="100%" class="TabListe" id="TabListe[!TypeEnf!]" style="width:100%;">
		[LIMIT 0|1]
		//On construit l entete de la liste
		<tr class="header" height=20>
			[IF [!Inter!]!=]
			<td width="20">Test</td>
			[/IF]
			<td width="20">Id</td>
			[STORPROC [!Ob::SearchOrder([!Lang!])!]|Prop]
				[LIMIT 0|[!NbChamp!]]
					<td>[!Prop::Nom!]</td>
				[/LIMIT]
			[/STORPROC]	
		</tr>
		[/LIMIT]
	[/STORPROC]
		//ON affiche les lignes

	[STORPROC [!Chemin!]|Ob|[![!Page[!TypeEnf!]:-1!]:*25!]|25]
		<tr>
			<td width="20"><a href="/[!Ob::getUrl!]">[!Ob::Id!]</a></td>
			[STORPROC [!Ob::SearchOrder([!Lang!])!]|Prop]
				[LIMIT 0|[!NbChamp!]]
					<td><input type="submit" value="[!Requete!]/[!Ob::Id!]" title="[SUBSTR 30][!Ob::[!Prop::Nom!]!][/SUBSTR]" name="Requete" />[SUBSTR 30][!Ob::[!Prop::Nom!]!][/SUBSTR]</td>
				[/LIMIT]
			[/STORPROC]	
		
		</tr>
	[/STORPROC]
	</table>
[IF [!Affich!]!=Simple]
[/IF]
//<script>$(document).ready(function(){ $("#TabListe[!TypeEnf!]").tableFilter({imagePath:"/Skins/AdminV2/Img"});});</script>

