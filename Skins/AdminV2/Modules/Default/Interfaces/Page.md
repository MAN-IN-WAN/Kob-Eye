<div class="Principal">
	<div class="Entete">
		ici l'entete
	</div>
	<div class="Modules">
		[STORPROC Explorateur/_Dossier/Modules/_Dossier|Mod]
		[IF [!Lien!]="Redaction/Affich"]
			<span><a href="/[!Mod::Nom!]"> [!Mod::Nom!]</a></span>
		[ELSE]
			[IF [!Mod::Nom!]=[!Module::Actuel::Nom!]]
				<span class="Selection"><a href="/[!Mod::Nom!]"> [!Mod::Nom!]</a></span>
			[ELSE]
				<span><a href="/[!Mod::Nom!]"> [!Mod::Nom!]</a></span>
			[/IF]
		[/IF]
		[/STORPROC]
	</div>
	[IF [!Lien!]="Redaction/Affich"]
	   [TITLE]Admin Kob-Eye | Bienvenue[/TITLE]
	[ELSE]
	<div class="LstObj">
        [STORPROC Infos::[!Module::Actuel::Nom!]|Class]
		[IF [!Lien!]=[!Module::Actuel::Nom!]]
			[IF [!Class::titre!]=[!Module::Actuel::Db::getMaster!]]
			<span class="Selection">
			<a href="/[!Module::Actuel::Nom!]/[!Class::titre!]">[!Class::titre!]</a>
			</span>
			[ELSE]
			<span>
			<a href="/[!Module::Actuel::Nom!]/[!Class::titre!]">[!Class::titre!]</a>
			</span>
			[/IF]
		[ELSE]
			
			[IF [!ObjTest::firstObjType!]=[!Class::titre!]]
				<span class="Selection">
				<a href="/[!Module::Actuel::Nom!]/[!Class::titre!]">[!Class::titre!]</a>
				</span>
			[ELSE]
				<span>
				<a href="/[!Module::Actuel::Nom!]/[!Class::titre!]">[!Class::titre!]</a>
				</span>
			[/IF]
			
		[/IF]
	[/STORPROC]
	</div>
	[/IF]
	<div class="Contenu">
		[IF [!Lien!]="Redaction/Affich"]
			[TITLE]Admin Kob-Eye | Bienvenue[/TITLE]
			<div style="margin:10px;font-size:12pt;text-align:center;">Bienvenue sur la console d'administration Kob-Eye ;)</div>
			<div style="margin:10px;font-size:10pt;text-align:center;">En cours: ajout des champs d'ajout de fichiers, cr&eacute;ation d'un cache pour mySql</div>
		[ELSE]
			[DATA]
		[/IF]
	</div>
</div>
<div class="PiedPage">
	Copyright 2006, Expressiv.net
</div>
