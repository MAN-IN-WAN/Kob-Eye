[BLOC Rounded]
	[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
		<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
		<span style="margin-left:5px;">Parent de type [!Obj::ObjectType!]</span>
	[/BLOC]
	[STORPROC [!Obj::Proprietes()!]|Prop|0|5]
		[MODULE Systeme/Interfaces/LignePropriete?Prop=[!Prop!]&Class=Petit]
	[/STORPROC]
[/BLOC]
