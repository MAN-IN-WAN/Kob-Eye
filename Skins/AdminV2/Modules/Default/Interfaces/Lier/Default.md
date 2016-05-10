[TITLE]Admin Kob-Eye | Liaison d'un objet[/TITLE]
//SAUVEGARDE DE L URL REFERENTE
[IF [!LAST_URL!]=]
	[!LAST_URL:=[!Systeme::Connection::LastUrl!]!]
[/IF]
[MODULE Systeme/Interfaces/FilAriane]
[INFO [!Query!]|T]
[STORPROC [!T::Historique!]|H][/STORPROC]
<div id="Container">
[STORPROC [!T::LastDirect!]|Objet|0|1]
	//[IF [!H::Key!]!=][!KEY:=.[!H::Key!]!][/IF]
	[!Class:=[!H::DataSource!]!]
	[!Module:=[!H::Module!]!]
	[IF [!Action!]]
		//On supprime les liaisons existantes
		[STORPROC [!Objet::getUrl!]/[!Class!][!KEY!]|T]
			[METHOD T|DelParent]
				[PARAM][!Objet::Module!]/[!Objet::ObjectType!]/[!Objet::Id!][/PARAM]
				[PARAM][!H::Key!][/PARAM]
			[/METHOD]
			[METHOD T|Save][/METHOD]
		[/STORPROC]
		//On ajoute les liaisons
		[STORPROC [!Dep[!Class!]!]|V]
			//On instancie l objet a lier
			[STORPROC [!Module!]/[!Class!]/[!V!]|L|0|1][/STORPROC]
			[METHOD L|AddParent]
				[PARAM][!Objet::Module!]/[!Objet::ObjectType!]/[!Objet::Id!][/PARAM]
				[PARAM][!H::Key!][/PARAM]
			[/METHOD]
			[METHOD L|Save][/METHOD]
		[/STORPROC]
		[IF [!Action!]=VALIDER]
			[REDIRECT][!Objet::Module!]/[!Objet::ObjectType!]/[!Objet::Id!][/REDIRECT]
		[/IF]
	[/IF]
	//<form [IF [!Test::Reflexive!]=]target="Liste[!Test::TypeChild!]"[/IF] name="liste" method="post">
		<input type="hidden" name="Action" action="Refresh" />
		[IF [!T::Reflexive!]]
			[BLOC Panneau|bottom:30px;||]
				//On analyse les cardinalités
				[IF [!Class!]=[!Objet::ObjectType!]][!Disable:=[!Objet::Id!]!][/IF]
				[IF [!Objet::getCard([!Class!])!]=0,1||[!Objet::getCard([!Class!])!]=1,1]
					[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Module!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!]&Inter=radio&Disable=[!Disable!]&Check=[!Objet::getChilds([!Class!])!]&Prefixe=Dep&Type=Select&Fields=True]
				[ELSE]
					[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Module!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!]&Inter=checkbox&Disable=[!Disable!]&Check=[!Objet::getChilds([!Class!])!]&Prefixe=Dep&Type=Select&Fields=True]
				[/IF]
			[/BLOC]
		[ELSE]
			<div id="Arbo">
				[BLOC Panneau|bottom:50%;top:0;||overflow:auto;]
					[OBJ [!Module!]|[!T::TypeChild!]|o]
					//Formulaire de recherche exacte
					[MODULE Systeme/Interfaces/Formulaire/Recherche?Obj=[!o!]]
				[/BLOC]
				[BLOC Panneau|bottom:30px;top:50%;||overflow:auto;]
					//AFFICHAGES DES ENFANTS 
					[COUNT [!backQuery!]/[!T::TypeChild!][!KEY!]|Nb]
					[BLOC Rounded|background:#9A9EA0;color:#FFFFFF;|margin-bottom:2px;]
						<img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
						<span style="margin-left:5px;">[!T::TypeChild!](s) actuellement lié(s) : [!Nb!]</span>
					[/BLOC]
					//[MODULE Systeme/Interfaces/Liste?Chemin=[!Query!]/[!T::TypeChild!]&NbChamp=4&TypeEnf=[!T::TypeChild!]&Affich=Simple&Lang=[!LangC!]&Type=Mini&RechPrefixe=Rech2]
				[/BLOC]
			</div>
			<div id="Data">
				[BLOC Panneau|bottom:30px;||]
					//On analyse les cardinalités
					[IF [!Class!]=[!Objet::ObjectType!]][!Disable:=[!Objet::Id!]!][/IF]
					[IF [!Objet::getCard([!Class!])!]=0,1||[!Objet::getCard([!Class!])!]=1,1]
						[MODULE Systeme/Interfaces/Liste?Chemin=[!Module!]/[!Class!]&TypeEnf=[!Class!][!KEY!]&Inter=radio&Disable=[!Disable!]&Check=[!Objet::getChildren([!Class!][!KEY!])!]&Prefixe=Dep&Type=MultiSelect]
					[ELSE]
						[MODULE Systeme/Interfaces/Liste?Chemin=[!Module!]/[!Class!]&NbChamp=4&TypeEnf=[!Class!][!KEY!]&Inter=checkbox&Disable=[!Disable!]&Check=[!Objet::getChildren([!Class!][!KEY!])!]&Prefixe=Dep&Type=MultiSelect]
					[/IF]
				[/BLOC]
			</div>
		[/IF]
		<input type="hidden" name="LAST_URL" value="[!LAST_URL!]" />
		<div class="Enregistrer" style="position:absolute;bottom:10px;"><input type="submit"  class="KEBouton"   style="width:80%;" value="VALIDER" name="Action"></div>
	//</form>
[/STORPROC]
</div>
