[HEADER]<script language="JavaScript" src="/Skins/[!Systeme::Skin!]/Js/FusionCharts.js"></script>[/HEADER]
[IF [!DateDebut!]=][!DateDebut:=[!Date::getDate(Y-m-d,[!TMS::Now:-2592000!])!]!][/IF]
[IF [!DateFin!]=][!DateFin:=[!Date::getDate(Y-m-d,[!TMS::Now!])!]!][/IF]
[BLOC Panneau|]
	[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
		<span style="margin-left:5px;">Statistiques [!Module::Actuel::Nom!]</span>
	[/BLOC]
	[STATS [!Module::Actuel::Nom!]|T]
	//Navigation
	<div style="position:absolute;left:0px;overflow:auto;top:25px;bottom:0px;width:250px;" >
		[BLOC Rounded||width:250px;float:left;]
			[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
				<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
				<span style="margin-left:5px;">Menu</span>
			[/BLOC]
			[MODULE Systeme/Statistiques/Menu]
		[/BLOC]
		[BLOC Rounded||width:250px;float:left;]
			[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
				<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
				<span style="margin-left:5px;">Date de début de période [!DateDebut!]</span>
			[/BLOC]
			[MODULE Systeme/Statistiques/Date?Var=DateDebut&Var2=DateFin&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
		[/BLOC]
		[BLOC Rounded||width:250px;float:left;]
			[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
				<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
				<span style="margin-left:5px;">Date de fin de période [!DateFin!]</span>
			[/BLOC]
			[MODULE Systeme/Statistiques/Date?Var=DateFin&Var2=DateDebut&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
		[/BLOC]
	</div>
	<div style="position:absolute;left:250px;overflow:auto;top:25px;bottom:0px;right:0;" >
		//AFFICHAGE DES COURBES TOTAUX
		[INFO [!Lien!]|I]
		[!Req:=[!Module::Actuel::Nom!]!]
		[STORPROC [!I::Historique!]|Trigger|2|1]
			[!Req+=/[!Trigger::Value!]!]
			[STORPROC [!I::Historique!]|Fonction|3|1]
				[!Req+=/Info/[!Fonction::Value!]!]
			[/STORPROC]
		[/STORPROC]
		[STATS [!Req!]|S]
		[STORPROC [!S!]|F]
			//AFFICHAGE DU RECAPITLATIF DES TOTAUX
			[IF [!F::Type!]=Total]
				[MODULE Systeme/Statistiques/Total?Module=[!Module::Actuel::Nom!]&Trigger=[!Trigger::Value!]&Fonction=[!F::Name!]&Titre=[!F::Description!]&Id=[!Pos!]&Type=Full]
			[/IF]
			//AFFICHAGE DES CLASSEMENTS
			[IF [!F::Type!]=Classement]
				[MODULE Systeme/Statistiques/Classement?Module=[!Module::Actuel::Nom!]&Trigger=[!Trigger::Value!]&Fonction=[!F::Name!]&Titre=[!F::Description!]&Id=[!Pos!]&Type=Full]
			[/IF]
			//AFFICHAGE DES JOURNAUX
			[IF [!F::Type!]=Journal]
				[MODULE Systeme/Statistiques/Journal?Module=[!Module::Actuel::Nom!]&Trigger=[!Trigger::Value!]&Fonction=[!F::Name!]&Titre=[!F::Description!]&Id=[!Pos!]&Type=Full]
			[/IF]
		[/STORPROC]
	</div>
[/BLOC]
