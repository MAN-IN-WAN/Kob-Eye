<div class="BarreObject">
	<ul>
	[STORPROC [!Module::Actuel::Db::AccessPoint!]|ObjClass]
		[IF [!ObjClass::titre!]=[!QueryFirstObject!]]
			<li id="Selected">
				<a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]">
				<img src="[!ObjClass::Icon!]"  style="margin-bottom:-5px;"/>&nbsp;[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</a>
			</li>
		[ELSE]
			<li>
				<a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]"><img src="[!ObjClass::Icon!]" style="margin-bottom:-5px;"/>&nbsp;[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</a>
			</li>
		[/IF]
	[/STORPROC]
	</ul>
	<div style="position:absolute;height:25px;top:10px;right:5px;width:200px;">
		[STORPROC Systeme/User/[!Systeme::User::Id!]|Prop]
			Vous &ecirc;tes connect&eacute; en tant que [!Prop::Login!]
		[/STORPROC]
		<a href="Systeme/Deconnexion"><img src="/Skins/AdminV2/Img/logout.png" style="margin-bottom:-3px;"></a>
	</div>
</div>