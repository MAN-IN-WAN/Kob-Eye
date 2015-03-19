[HEADER]<script language="JavaScript" src="/Skins/[!Systeme::Skin!]/Js/swfobject.js"></script>[/HEADER]
//Affichage des apercus
[IF [!Type!]=Full]
	[!Options:=width:100%;height:auto;!]
	[!Limit:=100!]
	[!Hauteur:=300!]
[ELSE][!Options:=height:auto;!][!Limit:=5!][!Hauteur:=200!][/IF]
[STATS [!Module!]/[!Trigger!]|S]

[STORPROC [!S!]|F]
	[IF [!F::Name!]=[!Fonction!]][!Func:=[!F!]!][/IF]
[/STORPROC]
[!DateD:=[![!DateDebut!]:/-!]!]
[!DateF:=[![!DateFin!]:/-!]!]
[!Dd:=[!Utils::getTms([!DateD::2!]/[!DateD::1!]/[!DateD::0!])!]!]
[!Df:=[!Utils::getTms([!DateF::2!]/[!DateF::1!]/[!DateF::0!])!]!]
<div style="overflow:hidden;width:50%;float:left;">
[BLOC Rounded|height:auto;|[!Options!];left:2px;right:2px;]
	[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
		<span style="margin-left:5px;font-weight:bold;">[!Titre!] du [!Utils::getDate(d/m/Y,[!Dd!])!] au [!Utils::getDate(d/m/Y,[!Df!])!]</span>
	[/BLOC]
	<div id="chartdiv[!Id!]" align="center"> </div>
	<script type="text/javascript">
	swfobject.embedSWF("/Skins/[!Systeme::Skin!]/Img/open-flash-chart.swf", "chartdiv[!Id!]", "100%", "[!Hauteur!]", "9.0.0", "expressInstall.swf", {"data-file":'/Systeme/Statistiques/Total/Get.xml?Requete=[!Module!]-[!Trigger!]-[!Fonction!]-[!Dd!]-[!Df!]'} );
	</script>
	[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
		<span style="margin-left:5px;">[!Titre!] - Synthèse</span>
	[/BLOC]
	[STATS [!Module!]/[!Trigger!]/[!Fonction!]|S|[!Dd!]|[!Df!]]
	[STORPROC [!S!]|St]
		[IF [!St::Value!]>[!Max!]][!Max:=[!St::Value!]!][/IF]
		[IF [!St::Value!]<[!Min!]||[!Min!]=][!Min:=[!St::Value!]!][/IF]
		[!Total+=[!St::Value!]!]
		[!Nb+=1!]
	[/STORPROC]
	<div style="width:50%;float:left;">
		[BLOC Rounded|background:#DDDDDD;color:#000000;|margin-bottom:1px;]
			<span style="margin-left:5px;">Total sur la période : </span><span style="font-weight:bold;font-size:16px;margin-top:5px;">
			[IF [!Func::Unit!]!=Secondes]
				[!Total!]
			[ELSE]
				[!Hour:=[!Math::Floor([!Total:/3600!])!]!][!Minu:=[!Math::Floor([![!Total:-[!Hour:*3600!]!]:/60!])!]!][!Sec:=[![!Total:-[!Minu:*60!]!]:-[!Hour:*3600!]!]!]
				[!Hour!]h [!Minu!]m [!Sec!]s
			[/IF]
			</span>
		[/BLOC]
	</div>
	<div style="width:50%;float:left;">
		[BLOC Rounded|background:#DDDDDD;color:#000000;|margin-bottom:1px;]
			<span style="margin-left:5px;">Moyenne sur la période : </span><span style="font-weight:bold;font-size:16px;margin-top:5px;">
			[IF [!Func::Unit!]!=Secondes]
				[!Utils::getPrice([!Total:/[!Nb!]!])!]
			[ELSE]
				[!Hour:=[!Math::Floor([![!Total:/[!Nb!]!]:/3600!])!]!][!Minu:=[!Math::Floor([![![!Total:/[!Nb!]!]:-[!Hour:*3600!]!]:/60!])!]!][!Sec:=[!Math::Floor([![![!Total:/[!Nb!]!]:-[!Minu:*60!]!]:-[!Hour:*3600!]!])!]!]
				[!Hour!]h [!Minu!]m [!Sec!]s
			[/IF]
			</span>
		[/BLOC]
	</div>
	<div style="width:50%;float:left;">
		[BLOC Rounded|background:#DDDDDD;color:#000000;|margin-bottom:1px;]
			<span style="margin-left:5px;">Maximum sur la période : </span><span style="font-weight:bold;font-size:16px;margin-top:5px;">
			[IF [!Func::Unit!]!=Secondes]
				[!Max!]
			[ELSE]
				[!Hour:=[!Math::Floor([!Max:/3600!])!]!][!Minu:=[!Math::Floor([![!Max:-[!Hour:*3600!]!]:/60!])!]!][!Sec:=[![!Max:-[!Minu:*60!]!]:-[!Hour:*3600!]!]!]
				[!Hour!]h [!Minu!]m [!Sec!]s
			[/IF]
			</span>
		[/BLOC]
	</div>
	<div style="width:50%;float:left;">
		[BLOC Rounded|background:#DDDDDD;color:#000000;|margin-bottom:1px;]
			<span style="margin-left:5px;">Minimum sur la période : </span><span style="font-weight:bold;font-size:16px;margin-top:5px;">
			[IF [!Func::Unit!]!=Secondes]
				[!Min!]
			[ELSE]
				[!Hour:=[!Math::Floor([!Min:/3600!])!]!][!Minu:=[!Math::Floor([![!Min:-[!Hour:*3600!]!]:/60!])!]!][!Sec:=[![!Min:-[!Minu:*60!]!]:-[!Hour:*3600!]!]!]
				[!Hour!]h [!Minu!]m [!Sec!]s
			[/IF]
			</span>
		[/BLOC]
	</div>
	<div class="Bouton" style="float:right;margin:0;padding:0;margin-top:-10px;margin-bottom:-10px;">
		<b class="b1"></b>
		<b class="b2" style="text-align:center;display:inline;">
			<a href="/[!Module!]/Statistiques/[!Trigger!]/[!Fonction!]" style="float:left;">Voir le détail</a> 
		</b>
		<b class="b3"></b>
	</div>
[/BLOC]
</div>