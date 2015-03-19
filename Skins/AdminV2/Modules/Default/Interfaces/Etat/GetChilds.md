[IF [!Module!]][ELSE]
    [!Module:=[!Module::Actuel::Nom!]!]
[/IF]

[STORPROC [!Query!]|Obj|0|1][/STORPROC]
[IF [!Enf!]=]
	[!E:=[!Obj::typesEnfant!]!]
	[!Enf:=[!E::[!K!]!]!]
[/IF]

[!DispName:=[!Enf::Description!]!]
[IF [!DispName!]=]
	[!DispName:=[!Enf::Titre!]!]
[/IF]


[SWITCH [!Type!]|=]
	[CASE Etat]
		[!Affich:=Etat!]
	[/CASE]
	[DEFAULT]
		[!Affich:=Simple!]
	[/DEFAULT]
[/SWITCH]

[COUNT [!Query!]/[!Enf::Titre!]|T]
<div class="ChildDisplay Child[!Enf::Titre!]">
	<div class="BigTitle" style="height:20px;background:white;margin-bottom:10px;color:#057390;line-height:20px;">
		<a href="[!Query!]/[!Enf::Titre!]" style="float:right;width:75px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Recherche</a>
		<a href="[!Query!]/[!Enf::Titre!]/Selection" style="float:right;width:75px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" class="KEBouton">Selection</a>
		<a href="[IF [!Enf::Behaviour!]=Integrated]#[ELSE]/[!Query!]/[!Enf::Titre!]/Ajouter[/IF]" class="KEBouton [IF [!Enf::Behaviour!]=Integrated]   makePopup   [/IF]" style="float:right;width:75px;height:12px;line-height:11px;margin-top:2px;margin-right:5px;" rel="/Systeme/Interfaces/Formulaire/Popup.htm?Action=Ajouter&QueryObj=[!Query!]/[!Enf::Titre!]::[!Query!]/[!Enf::Titre!]/Ajouter::true">Ajouter</a>
		[!DispName!] ([!T!] &eacute;l&eacute;ments)
	</div>
	[COUNT [!Query!]/[!Enf::Titre!]|T]
	<form action="/[!QueryObj!]/GetChilds?K=[!K!]&NoContent=True" method="post" class="refreshMyself">
		<div style="position:absolute;left:2px;right:2px;top:50px;bottom:5px;">
			[IF [!T!]>0]
				[IF [!Enf::isReflexive()!]]
					[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Obj::getUrl!]/[!Enf::Titre!]&NbChamp=4&TypeEnf=[!Enf::Titre!]&Affich=[!Affich!]&Lang=Francais&Behaviour=[!Enf::Behaviour!]]
				[ELSE]
					[MODULE Systeme/Interfaces/Liste?Chemin=[!Obj::getUrl!]/[!Enf::Titre!]&NbChamp=4&TypeEnf=[!Enf::Titre!]&Affich=[!Affich!]&Lang=Francais&Type=Full&Behaviour=[!Enf::Behaviour!]&MaxLine=20]
				[/IF]
			[/IF]
		</div>
	</form>
</div>
