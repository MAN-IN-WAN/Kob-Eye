//On repete maintenant l interface pour chaque langue
<div class="BigTitle" style="margin-top:20px">Recherche sur les propri&eacute;t&eacute;s</div>
[!Prefixe:=RechProp!]
[STORPROC [!Obj::SearchOrder()!]|Prop]
	//Dabord les proprietes de type varchar ,private,titre,password
	[IF [!Prop::Filter!]=]
	    	[!Prop::Default:=!]
		[SWITCH [!Prop::Type!]|=]
			[CASE file][/CASE]
			[CASE image][/CASE]
			[CASE son][/CASE]
			[CASE video][/CASE]
			[CASE text][/CASE]
			[CASE bbcode][/CASE]
			[CASE html][/CASE]
			[CASE boolean]
			<div class="ProprieteModif" style="overflow:hidden">
				<div class="ProprieteTitreModif">[!Prop::description!] </div>
				<div class="ProprieteValeurModif">
						<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="1" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" [IF [!RechProp[!Prop::Nom!]!]=1]CHECKED[/IF]>Oui
						<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="-1" class="[IF [!DisplayReload!]=True] ChangeOnReload[/IF]" [IF [!RechProp[!Prop::Nom!]!]=-1]CHECKED[/IF]>Non
						<input type="radio" name="[!Prefixe!][!Prop::Nom!]" value="" [IF [!RechProp[!Prop::Nom!]!]=]CHECKED[/IF]>Indifférent
				</div>
			</div>
			[/CASE]
			[DEFAULT]
				[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?Prop=[!Prop!]&Prefixe=[!Prefixe!]&Valeur=&Type=Rech]
			[/DEFAULT]
		[/SWITCH]
	[/IF]
[/STORPROC]
<input type="submit" class="KEBouton" value="Rechercher" style="width:100%"/>

[OBJ [!Module::Actuel::Nom!]|[!Obj::ObjectType!]|T]
[STORPROC [!T::GetFilter()!]|P|0|100]
    <div class="BigTitle" style="margin-top:3px;">Filtrer sur les propri&eacute;t&eacute;s</div>
	[LIMIT 0|100]
		[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?Prefixe=RechFilter&Prop=[!P!]&Valeur=[!RechFilter[!P::Nom!]!]&Type=Recherche] 
	[/LIMIT]
[/STORPROC]
[STORPROC [!Obj::typesEnfant!]|Enf]
	[IF [!Enf::search!]]
	    <div class="BigTitle" style="margin-top:5px;">Recherche [!Enf::Titre!]</div>
		[!Pr::Nom:=[!Enf::Titre!]!]
		[!Pr::description:=[!Enf::Titre!]!]
		[!Pr::Valeur:=!]
		//[MODULE Systeme/Interfaces/Formulaire/ModifProprietes?Prefixe=RechEnf&Prop=[!Pr!]&Valeur=[!RechEnf[!Pr::Nom!]!]]
	[/IF]
[/STORPROC]
//Recherche sur le propriétaire ou la date de creation/modification
    <div class="BigTitle" style="margin-top:5px;">Filtrer sur les propri&eacute;t&eacute;s syst&egrave;me</div>

	[!Prefixe:=RechFilter!]
	[!P::Nom:=uid!]
	[!P::description:=Utilisateur!]
	[!P::Valeur:=[!RechFilteruid!]!]
	[!P::Type:=ObjectClass!]
	[!P::query:=Systeme/User!]
	<div class="ProprieteModif">
		<div class="ProprieteTitreModif">[!P::description!] </div>
		<div class="ProprieteValeurModif">
			[IF [!Valeur!]!=][!P::Valeur:=[!Valeur!]!][/IF]
			[!T:=[![!Prefixe!]Explore[!P::Nom!]!]!]
			[IF [!Utils::isArray([!T!])!]]
				[STORPROC [!T!]|E]
					[!VAL:=[!E!]!]
				[/STORPROC]
			[/IF]
			[IF [!VAL!]=][!VAL:=[!P::Valeur!]!][/IF]
			<a href="" class="makePopup" style="display:block;float:right;margin-right:10px;padding-top:5px;" rel="/Systeme/Interfaces/Explorer/Popup.htm?Prop=[!P::Nom!]&Obj=[!Obj::ObjectType!]&Module=[!Module::Actuel::Nom!]&Prefixe=&InputId=FiltreUid::/[!Query!]::false"><img src="/Skins/AdminV2/Img/folder_explore.png"/></a>
			<input type="text" class="Champ" name="[!Prefixe!][!P::Nom!]" value="[!VAL!]" id="FiltreUid" style="width:85%"/>
//			<input type="submit" name="[!Prefixe!]Explore[!P::Nom!]_explore" value="OK" class="ExplorerBouton"/>
			[IF [![!Prefixe!]Explore[!P::Nom!]_explore!]=OK]
				[INFO [!P::query!]|Test]
				[MODULE Systeme/Interfaces/Explorer?Prop=[!P!]&Prefixe=[!Prefixe!]Explore]
			[/IF]
		</div>
	</div>
	[!VAL:=!]
	[!P::Nom:=gid!]
	[!P::description:=Groupe!]
	[!P::Valeur:=[!RechFiltergid!]!]
	[!P::Type:=ObjectClass!]
	[!P::query:=Systeme/Group!]
	<div class="ProprieteModif">
		<div class="ProprieteTitreModif">[!P::description!]</div>
		<div class="ProprieteValeurModif">
			[IF [!Valeur!]!=][!P::Valeur:=[!Valeur!]!][/IF]
			[!T:=[![!Prefixe!]Explore[!P::Nom!]!]!]
			[IF [!Utils::isArray([!T!])!]]
				[STORPROC [!T!]|E]
					[IF [!E!]!=ROOT][!VAL:=[!E!]!][/IF]
				[/STORPROC]
			[/IF]
			[IF [!VAL!]=][!VAL:=[!P::Valeur!]!][/IF]
			<a href="" style="display:block;float:right;margin-right:10px;padding-top:5px;" class="makePopup" rel="/Systeme/Interfaces/Explorer/Popup.htm?Prop=[!P::Nom!]&Obj=[!Obj::ObjectType!]&Module=[!Module::Actuel::Nom!]&Prefixe=&InputId=FiltreGid::/[!Query!]::false"><img src="/Skins/AdminV2/Img/folder_explore.png"/></a>
			<input type="text" class="Champ" name="[!Prefixe!][!P::Nom!]" value="[!VAL!]" id="FiltreGid" style="width:85%"/>
			[IF [![!Prefixe!]Explore[!P::Nom!]_explore!]=OK]
				[INFO [!P::query!]|Test]
				[MODULE Systeme/Interfaces/Explorer?Prop=[!P!]&Prefixe=[!Prefixe!]Explore]
			[/IF]
		</div>
	</div>
//VALIDER
<input type="submit" class="KEBouton" value="Rechercher" style="width:100%"/>

<script type="text/javascript">
    Fl.makePopup();
</script>


