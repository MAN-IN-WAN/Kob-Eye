//Affichage des points d acces du schema
<div class="BarreIcone">
[STORPROC [!Module::Actuel::Db::AccessPoint!]|ObjClass]
	[IF [!ObjClass::titre!]=[!QueryFirstObject!]]
		<div class="ItemSelected">
			<a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]"><img src="/Skins/AdminV2/Img/[!ObjClass::titre!].png" />[!ObjClass::titre!]</a>
		</div>
	[ELSE]
		<div>
			<a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]"><img src="/Skins/AdminV2/Img/[!ObjClass::titre!].png" />[!ObjClass::titre!]</a>
		</div>
	[/IF]
[/STORPROC]
</div>