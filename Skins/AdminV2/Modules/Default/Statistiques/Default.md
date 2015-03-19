[HEADER]<script language="JavaScript" src="/Skins/[!Systeme::Skin!]/Js/FusionCharts.js"></script>[/HEADER]
[IF [!DateDebut!]=][!DateDebut:=[!Date::getDate(Y-m-d,[!TMS::Now:-2592000!])!]!][/IF]
[IF [!DateFin!]=][!DateFin:=[!Date::getDate(Y-m-d,[!TMS::Now!])!]!][/IF]
[BLOC Panneau|height:auto;]
	[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
		<span style="margin-left:5px;">Statistiques [!Module::Actuel::Nom!]</span>
	[/BLOC]
	[STATS [!Module::Actuel::Nom!]|T]
	//Navigation
	<div style="position: absolute; left: 0px; overflow: hidden; top: 30px; bottom: 0px; width: 260px; padding: 5px;" >
		[MODULE Systeme/Statistiques/Menu]
		<div style="width: 100%; padding: 0px;" >
			[BLOC Rounded||width:100%;float:left;overflow:hidden;height:auto;]
				[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;">Date de début de période [!DateDebut!]</span>
				[/BLOC]
				[MODULE Systeme/Statistiques/Date?Var=DateDebut&Var2=DateFin&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
			[/BLOC]
		</div>
		<div style="width: 100%; padding: 0px;" >
			[BLOC Rounded||width:100%;float:left;overflow:hidden;height:auto;]
				[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
					<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
					<span style="margin-left:5px;">Date de fin de période [!DateFin!]</span>
				[/BLOC]
				[MODULE Systeme/Statistiques/Date?Var=DateFin&Var2=DateDebut&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
			[/BLOC]
		</div>
		[MODULE Systeme/Configuration/Menu]
	</div>
	<div style="position: absolute; left: 265px; overflow: auto; top: 35px; bottom: 0px; right: 5px;padding: 0px 15px 5px 5px;" >
		//AFFICHAGE DES COURBES TOTAUX
		[STATS [!Module::Actuel::Nom!]|S]
		[STORPROC [!S!]|S1][/STORPROC]
		[STORPROC [!S1::Functions!]|F]
			//AFFICHAGE DU RECAPITLATIF DES TOTAUX
			[IF [!F::Type!]=Total]
				[MODULE Systeme/Statistiques/Total?Module=[!Module::Actuel::Nom!]&Trigger=[!S1::Name!]&Fonction=[!F::Name!]&Titre=[!F::Name!]&Id=[!Pos!]&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
			[/IF]
			//AFFICHAGE DES CLASSEMENTS
			[IF [!F::Type!]=Classement]
				[MODULE Systeme/Statistiques/Classement?Module=[!Module::Actuel::Nom!]&Trigger=[!S1::Name!]&Fonction=[!F::Name!]&Titre=[!F::Name!]&Id=[!Pos!]&DateDebut=[!DateDebut!]&DateFin=[!DateFin!]]
			[/IF]
		[/STORPROC]
	</div>
[/BLOC]
