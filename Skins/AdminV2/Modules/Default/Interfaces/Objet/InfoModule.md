[TITLE]Admin Kob-Eye | Informations Module[/TITLE]
[HEADER]
<script src="/Skins/[!Systeme::User::Skin!]/Js/jquery.history_remote.pack.js" type="text/javascript"></script>
<script src="/Skins/[!Systeme::User::Skin!]/Js/jquery.tabs.pack.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$('#InfoModule').tabs({ fxSlide: false, fxFade: false, fxSpeed: 'fast'});
	});
</script>
<link rel="stylesheet" href="/Skins/[!Systeme::User::Skin!]/Css/jquery.tabs.css" type="text/css" media="print, projection, screen">
[/HEADER]
[MODULE Systeme/Interfaces/BarreModule]
<div id="InfoModule">
	//ON definit les onglets
	<ul>
		<li><a href="#ModInfo"><span>Informations module</span></a></li>		
		<li><a href="#ModAide"><span>Fichiers d'aide et manuels</span></a></li>		
	</ul>
	<div id="ModInfo" style="top:56px;">
		//Pour chaque objectClass on recherche les derniers modifi&eacute;s
		[STORPROC [!Module::Actuel::Db::AccessPoint!]|ObjClass]
		[/STORPROC]
	</div>
	<div id="ModAide" style="top:56px;">
		FICHIERS AIDE
	</div>
</div>
<div class="Nav"></div>



