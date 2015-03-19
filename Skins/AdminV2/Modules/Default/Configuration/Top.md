[MODULE Systeme/Interfaces/FilAriane]
<div class="Panneau" style="height:auto;top:20px;">
	[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
		<span style="margin-left:5px;">Configuration [!Module::Actuel::Nom!]</span>
	[/BLOC]
	//Navigation
	<div style="position: absolute; left: 0; overflow: auto; top: 35px; bottom: 0px; width:250px;padding: 0px 10px 5px 5px;" >
		[MODULE Systeme/Configuration/Menu]
		[IF [!CONF::MODULE::[!Module::Actuel::Nom!]::TRIGGER!]]
			[MODULE Systeme/Statistiques/Menu]
		[/IF]
	</div>
	<div style="position: absolute; left: 270px; overflow: auto; top: 35px; bottom: 0px; right: 5px;padding: 0px 10px 5px 5px;" >