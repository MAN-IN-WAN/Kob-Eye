[MODULE Systeme/Configuration/Top]
<div style="float:right;width:49%;-moz-border-radius:3px 3px;margin-left:10px;">
	<div style="background:white;-moz-border-radius:3px 3px;padding:3px;;">
		<div class="BigTitle">Fichier de configuration</div>
		<ul>
			[MODULE Systeme/Configuration/Infos/Liste?MyArray=[!CONF::MODULE::[!Module::Actuel::Nom!]!]]
		</ul>
	</div>
	<div style="background:white;margin-top:10px;-moz-border-radius:3px 3px;padding:3px;;">
		<div class="BigTitle">Configuration g&eacute;n&eacute;rale</div>
		<ul>
			[MODULE Systeme/Configuration/Infos/Liste?MyArray=[!CONF::GENERAL!]]
		</ul>
	</div>
</div>
<div style="width:49%">
	<ul>
		[STORPROC [!CONF::MODULE::[!Module::Actuel::Nom!]!]|S]
		[IF [!Key!]=SCHEMA]
			[MODULE Systeme/Configuration/Infos/Schema?A=[!S!]]
		[/IF]
		[/STORPROC]
	</ul>
</div>
[MODULE Systeme/Configuration/Bottom]
