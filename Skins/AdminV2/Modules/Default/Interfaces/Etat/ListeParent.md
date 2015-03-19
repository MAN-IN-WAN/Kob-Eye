[STORPROC [!Obj::getParents([!Type!])!]|Par|0|10]
	[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;" />
		<span style="margin-left:5px;">[!Key!]</span>
	[/BLOC]
	[LIMIT 0|100]
		<div style="overflow:auto;margin-bottom:3px;">
		[MODULE Systeme/Interfaces/Liste/Ligne?Ob=[!Par!]&Type=Mini]
		</div>
	[/LIMIT]
[/STORPROC]
