	[!DateD:=[![!DateDebut!]:/-!]!]
	[!DateF:=[![!DateFin!]:/-!]!]
	[!Dd:=[!Utils::getTms([!DateD::2!]/[!DateD::1!]/[!DateD::0!])!]!]
	[!Df:=[!Utils::getTms([!DateF::2!]/[!DateF::1!]/[!DateF::0!])!]!]
	//Affichage des apercus
	[IF [!Type!]=Full]
		[!Options:=width:100%;height:auto;!]
		[!Limit:=100!]
	[ELSE][!Options:=height:250px;!][!Limit:=5!][/IF]
<div style="overflow:hidden;width:50%;float:left;">
	[BLOC Rounded||[!Options!]height:300px;left:2px;right:2px;margin:2px 0;]
		[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;font-weight:bold;|margin-bottom:5px;]
			<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">[!Titre!] du [!Utils::getDate(d/m/Y,[!Dd!])!] au [!Utils::getDate(d/m/Y,[!Df!])!]</span>
		[/BLOC]
		<h4>[!Ur::Name!]</h4>
		[STATS [!Module!]/[!Trigger!]/Info/[!Fonction!]|F|[!Dd!]|[!Df!]]
		[STORPROC [!F!]|F|0|1][/STORPROC]
		[STATS [!Module!]/[!Trigger!]/[!Fonction!]|V|[!Dd!]|[!Df!]]
		<table width="100%" border="0" cellspacing="0" cellpadding="0" border-color="#cdcdcd" style="margin-bottom:5px;">
			<tr >
				<td>
				[BLOC Rounded|background:#676767;color:#FFFFFF;|]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;font-weight:bold;">Identifiants</span>
				[/BLOC]
				</td>
				<td>
				[BLOC Rounded|background:#676767;color:#FFFFFF;|]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;font-weight:bold;">Valeurs ([!F::Unit!])</span>
				[/BLOC]
				</td>
				[IF [!F::Increment!]]
				<td>
				[BLOC Rounded|background:#676767;color:#FFFFFF;|]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;font-weight:bold;">Nombre d'occurences</span>
				[/BLOC]
				</td>
				[/IF]
			</tr>
		[STORPROC [!V!]|Fu|0|[!Limit!]]
			<tr style="">
				<td>
					[BLOC Rounded|background:#dddddd;color:#000;|]
						//[IF [!F::Link!]]<a href="/[!Module::Actuel::Nom!]/Statistiques/[!Trigger!]/[!Fu::Link!]">[/IF]
						[SWITCH [!F::IdUnit!]|=]
							[CASE User]
								[STORPROC Systeme/User/[!Fu::Id!]|U|0|1][/STORPROC]
								[!U::Id!] - [!U::Nom!] [!U::Prenom!] ([!U::Login!])
							[/CASE]
							[DEFAULT]
								[!Fu::Id!]
							[/DEFAULT]
						[/SWITCH]
						//[IF [!Fu::Link!]]</a>[/IF]
					[/BLOC]
				</td>
				<td>
					[BLOC Rounded|background:#dddddd;color:#000;|]
						[SWITCH [!F::Unit!]|=]
							[CASE Secondes]
								[!Hour:=[!Math::Floor([!Fu::Value:/3600!])!]!][!Minu:=[!Math::Floor([![!Fu::Value:-[!Hour:*3600!]!]:/60!])!]!][!Sec:=[![!Fu::Value:-[!Minu:*60!]!]:-[!Hour:*3600!]!]!]
								[!Hour!]h [!Minu!]m [!Sec!]s
							[/CASE]
							[DEFAULT]
								[IF [!Type!]!=Full]
								[SUBSTR 60][!Fu::Value!][/SUBSTR]
								[ELSE][!Fu::Value!]
								[/IF]
							[/DEFAULT]
						[/SWITCH]
					[/BLOC]
				</td>
				[IF [!F::Increment!]]
				<td>
					[BLOC Rounded|background:#dddddd;color:#000;|]
						[!Fu::Total!]
					[/BLOC]
				</td>
				[/IF]
			</tr>
		[/STORPROC]
		</table>
		[IF [!Type!]!=Full]
		<div class="Bouton" style="float:right;margin:0;padding:0;margin-top:-10px;margin-bottom:-10px;">
			<b class="b1"></b>
			<b class="b2" style="text-align:center;display:inline;">
				<a href="/[!Module!]/Statistiques/[!Trigger!]/[!Fonction!]" style="float:left;">Voir le détail</a> 
			</b>
			<b class="b3"></b>
		</div>
		[/IF]
	[/BLOC]
</div>