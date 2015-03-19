[TITLE]Admin Kob-Eye | Deplacement d'un objet[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
//SAUVEGARDE DE L URL REFERENTE
[IF [!LAST_URL!]=]
	[!LAST_URL:=[!Systeme::Connection::LastUrl!]!]
[/IF]
<div id="Container">
[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H][/STORPROC]
[STORPROC [!I::LastDirect!]|Objet]
	[!Class:=[!H::DataSource!]!]
	<form method="POST">
	[IF [!Action!]=VALIDER]
		//On supprime les liaisons existantes
		[IF [!Objet::getCard([!Class!])!]=0,n||[!Objet::getCard([!Class!])!]=1,n]
			[!Objet::resetParents([!Class!])!]
			[!Objet::Save!]
		[/IF]
		//On ajoute les liaisons
		[!Stop:=0!]
		[STORPROC [!Dep[!Class!]!]|V][IF [!V!]=ROOT][!Stop:=1!][/IF][/STORPROC]
		[IF [!Stop!]=0]
			[STORPROC [!Dep[!Class!]!]|V]
				[METHOD Objet|AddParent]
					[PARAM][!Module::Actuel::Nom!]/[!Class!]/[!V!][/PARAM]
				[/METHOD]
			[/STORPROC]
			[!Objet::Save!]
			//Recherche de la nouvelle destination
			[COUNT [!Query!]|U]
			[IF [!U!]]
				[REDIRECT][!LAST_URL!][/REDIRECT]
			[ELSE]
				[REDIRECT][!Module::Actuel::Nom!]/[!Objet::ObjectType!]/[!Objet::Id!][/REDIRECT]
			[/IF]
		[ELSE]
			[!Objet::resetParents([!Class!])!]
			[!Objet::Save!]
			[REDIRECT][!LAST_URL!][/REDIRECT]
		[/IF]
	[/IF]
	<div id="Arbo">
		[INFO [!Module::Actuel::Nom!]/[!Class!]|T]
		[BLOC Panneau|bottom:30px;||overflow:hidden;]
			[INFO [!backQuery!]|I]
			[STORPROC [!I::Historique!]|H|0|1][/STORPROC]
			[!Chemin:=[!Module::Actuel::Nom!]/[!H::Value!]/[!Class!]/[!H::Value!]!]
			//AFFICHAGES DES PARENTS 
			[COUNT [!Chemin!]|Nb]
			[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:2px;]
				<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
				<span style="margin-left:5px;">[!T::TypeChild!](s) actuellement lié(s) : [!Nb!]</span>
			[/BLOC]
			//[MODULE Systeme/Interfaces/Liste?Chemin=[!Chemin!]&NbChamp=4&TypeEnf=[!T::TypeChild!]&Affich=Simple&Lang=[!LangC!]&Type=Col&RechPrefixe=Rech2&NoRech=True]
			[OBJ [!Module::Actuel::Nom!]|[!T::TypeChild!]|o]
			[MODULE Systeme/Interfaces/Formulaire/Recherche?Obj=[!o!]]
		[/BLOC]

	</div>
	<div id="Data">
		[BLOC Panneau|bottom:30px;||overflow:auto;]
			[INFO [!Module::Actuel::Nom!]/[!Class!]|T]
			[IF [!T::Reflexive!]]
				//On analyse les cardinalit�s
				[IF [!Class!]=[!Objet::ObjectType!]][!Disable:=[!Objet::Id!]!][/IF]
				[IF [!Objet::getCard([!Class!])!]=0,1||[!Objet::getCard([!Class!])!]=1,1]
					[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Module::Actuel::Nom!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!]&Inter=radio&Disable=[!Disable!]&Check=[!Objet::getParents([!Class!])!]&Prefixe=Dep&Type=Select&Fields=True]
				[ELSE]
					[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Module::Actuel::Nom!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!]&Inter=checkbox&Disable=[!Disable!]&Check=[!Objet::getParents([!Class!])!]&Prefixe=Dep&Type=Select&Fields=True]
				[/IF]
			[ELSE]
				//On analyse les cardinalit�s
				[IF [!Class!]=[!Objet::ObjectType!]][!Disable:=[!Objet::Id!]!][/IF]
				[IF [!Objet::getCard([!Class!])!]=0,1||[!Objet::getCard([!Class!])!]=1,1]
					[MODULE Systeme/Interfaces/Liste?Chemin=[!Module::Actuel::Nom!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!]&Inter=radio&Disable=[!Disable!]&Check=[!Objet::getParents([!Class!])!]&Prefixe=Dep&Type=MultiSelect]
				[ELSE]
					[MODULE Systeme/Interfaces/Liste?Chemin=[!Module::Actuel::Nom!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!]&Inter=checkbox&Disable=[!Disable!]&Check=[!Objet::getParents([!Class!])!]&Prefixe=Dep&Type=MultiSelect]
				[/IF]
			[/IF]
		[/BLOC]
	</div>
	<input type="hidden" name="LAST_URL" value="[!LAST_URL!]" />
	<div class="Enregistrer" style="position:absolute;bottom:10px;"><input type="submit"  class="BoutonBlanc" value="VALIDER" name="Action"></div>
	</form>
[/STORPROC]
</div>
