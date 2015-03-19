[IF [!Prefixe!]=][!Prefixe:=Form_!][/IF]
[!Valeur:=[![!Prefixe!][!Prop::Nom!]!]!]
[!BgColor:=#8BB2C2!]
[IF [!Prop::obligatoire!]]
    [!BgColor:=#6990A0!]
[/IF]
[SWITCH [!Prop::Type!]|=]
	[CASE password]
	<div class="ProprieteModif">
		<div class="ProprieteTitreModif  PropModif " style="width:25%;float:left;">[!Prop::description!]</div>
		<div class="ProprieteValeurModif">
		<input type="text" class="Champ" name="Form_[!Prop::Nom!]" value="*******" >
		</div>
	</div>
	[/CASE]
	[DEFAULT]
	<div class="ProprieteModif" style="overflow:hidden">
	    [STORPROC [!Objet::Conditions!]|Cond]
		[IF [!Cond::Name!]=[!Prop::Nom!]]
		    [!DisplayReload:=True!]
		[/IF]
	    [/STORPROC]
		<div class="ProprieteTitreModif   [IF [!Prop::obligatoire!]||[!DisplayReload!]=True] ChampObligatoire [/IF] Champ[!Prop::Titre!]">[!Prop::description!] </div>
		<div class="[IF
		[!Prop::Type!]!=text]ProprieteValeurModif[ELSE]PropValeurMce[/IF]">
			[IF [!Type!]!=Rech]
				[IF [!Valeur!]=&&[**Prop::Valeur**]!=]
					[!Valeur==[**Prop::Valeur**]!]
				[/IF]
			[/IF]
			[MODULE Systeme/Interfaces/Formulaire/GetInput?ObjectTT=[!ObjectTT!]&DisplayReload=[!DisplayReload!]&Prop=[!Prop!]&Prefixe=[!Prefixe!]&Valeur=[!Valeur!]&Type=[!Type!]&&O=[!O!]]
		</div>
	</div>
	[/DEFAULT]
[/SWITCH]
