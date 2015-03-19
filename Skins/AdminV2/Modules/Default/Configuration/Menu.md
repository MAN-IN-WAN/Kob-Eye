//Navigation
<div style="width: 100%; padding: 0px;" >
	[BLOC Rounded||width:100%;float:left;overflow:hidden;height:auto;]
		[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
			<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">Navigation</span>
		[/BLOC]
		<ul>
			<li><a href="/[!Module::Actuel::Nom!]">Accueil</a></li>
			<li>Accès
				<ul>
				[STORPROC [!Module::[!Module::Actuel::Nom!]::Db::AccessPoint!]|ObjClass]
					<li><a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]">
						[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</b><b class="p3"></b></a>
					</li>
				[/STORPROC]
				</ul>
			</li>
			[STORPROC [!Module::Actuel::Db::Dico!]|ObjClass]
			<li>Dictionnaires
				<ul>
					[LIMIT 0|100]
					<li><a href="/[!Module::Actuel::Nom!]/[!ObjClass::titre!]">
						[IF [!ObjClass::Description!]!=][!ObjClass::Description!][ELSE][!ObjClass::titre!][/IF]</b><b class="p3"></b></a>
					</li>
					[/LIMIT]
				</ul>
			</li>
			[/STORPROC]
		</ul>
	[/BLOC]
</div>
<div style="width: 100%; padding: 0px;" >
	[BLOC Rounded||width:100%;float:left;overflow:hidden;height:auto;]
		[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
			<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">Configuration</span>
		[/BLOC]
		<ul>
			<li><a href="/[!Module::Actuel::Nom!]/Configuration/Modeles">Templates</a></li>
			<li><a href="/[!Module::Actuel::Nom!]/Configuration/Plugins">Plugins</a></li>
			<li><a href="/[!Module::Actuel::Nom!]/Configuration/Infos">Informations</a></li>
		</ul>
	[/BLOC]
</div>
<div style="width: 100%; padding: 0px;" >
	[BLOC Rounded||width:100%;float:left;overflow:hidden;height:auto;]
		[BLOC Rounded|background:#3D3D3D;color:#FFFFFF;|margin-bottom:5px;]
			<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">Fonctions</span>
		[/BLOC]
		<ul>
			<li><a href="/[!Module::Actuel::Nom!]/Miseajour">&nbsp;Mise  &agrave; jour</a></li>
			<li><a href="/[!Module::Actuel::Nom!]/Sauvegarde">&nbsp;Sauvegarde</a></li>
			[STORPROC [!Module::Systeme::Functions!]|F]
				<li><a href="/[!F::URL!]">&nbsp;[!F::TITRE!]</a></li>
			[/STORPROC]
		</ul>
	[/BLOC]
</div>

