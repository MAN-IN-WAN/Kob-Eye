<ul class="BarreModule" style="margin-left:10px;" id="BarreModules">
	[STORPROC [!Systeme::Modules!]|Mod]
		[IF [!Mod::Nom!]=[!Module::Actuel::Nom!]]
			<li class="current"><a href="/[!Mod::Nom!]" title="[!Mod::Nom!]" alt="[!Mod::Nom!]" >[!Mod::Nom!]</a></li>
		[ELSE]
			<li><a href="/[!Mod::Nom!]" title="[!Mod::Nom!]" alt="[!Mod::Nom!]">[!Mod::Nom!]</a></li>
		[/IF]
	[/STORPROC]
</ul>

<!--<div id="SelectModules" style="text-align:left;">
    <img src="/Skins/AdminV2/Img/Modules/[!Module::Actuel::Nom!]/Barre.jpg" style="width:40px;height:30px;margin-right:3px;float:left;" id="ModBar"/>
    <form method="post" style="display:inline;margin:0;padding:0">
	<select name="selModules" style="height:28px;margin:1px 0px 1px 0px;font-size:14pt;background:url('/Skins/AdminV2/Img/Structure/BarreBas.jpg');color:white;" id="ModSel">
	    [STORPROC [!Systeme::Modules!]|Mod]
		[IF [!Mod::Nom!]=[!Module::Actuel::Nom!]]
		    <option value="[!Mod::Nom!]" selected="selected">[!Mod::Nom!]</option>
		[ELSE]
		    <option value="[!Mod::Nom!]">[!Mod::Nom!]</option>
		[/IF]
	    [/STORPROC]
	</select>
    </form>
</div>-->


