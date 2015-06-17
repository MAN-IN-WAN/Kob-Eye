//------------------------------------------------
//--		INFOS				--
//------------------------------------------------
//********INPUT********
//Chemin	//Chemin a utiliser pour afficher les objets
//NbChamp	//Nombre de champ a afficher
//TypeEnf   	//Type d objet a afficher
//Type   	//Type d interface
//	Full 		//AFFICHAGE STANDARD
//	Explorer	//AFFICHAGE VALIDATION 
//	Mini		//AFFICHAGE RESTREINT
//	Select		//AFFICHAGE POUR LA SELECTION
//Prefixe	//Prefixe de la variable
//RechPrefixe	//Prefixe des variables de recherche
//Inter		//Type des input de ligne
//Disable	//Tableau contenant les IDS a desactiver
//Check		//Tableau contenant les IDS a checker 
//********OUTPUT********
//Select 	//Tableau contenant les nouvelles selections
//UnSelect	//Tableau contenant les selections supprimées
//------------------------------------------------
//--		PARAMETRES			--
//------------------------------------------------
//CONFIG REQUETE
[INFO [!Chemin!]|Test]
[!TypeEnf:=[!Test::TypeChild!]!]
//PAGINATION
[!Page[!TypeEnf!]:=1!]
[!Module:=[!Test::Module!]!]
[!MaxLine:=30!]
[!PagNbNum:=3!]
[!Order:=Id!]
[!OrderType:=DESC!]
[IF [!NbChamp!]=][!NbChamp:=1!][/IF]
[IF [!RechPrefixe!]=][!RechPrefixe:=Rech!][/IF]
//VARIABLES
//[IF [!Var!]!=][ELSE][!Var:=[!Prefixe!][!TypeEnf!]!][/IF]
//SELECTION



[IF [!Type!]=Select]
	[IF [![!Var!]!]!=]
		//On doit comparer les champs precedement selectionner et les champs deselectionner
		[STORPROC [![!Var!]SelectTest!]|S]
			[!Te:=1!]
			[STORPROC [![!Var!]Select!]|T]
				[IF [!S!]=[!T!]][!Te:=0!][/IF]
			[/STORPROC]
			[IF [!Te!]]
				//Alors Id a supprimer
				[COUNT [!TabSup!]|C]
				[!TabSup::[!C!]:=[!S!]!]
			[/IF]
		[/STORPROC]
		[!TempTab:=!]
		[STORPROC [![!Var!]!]|C]
			[!Te:=1!]
			[STORPROC [!TabSup!]|T][IF [!C!]=[!T!]][!Te:=0!][/IF][/STORPROC]
			[IF [!Te!]&&[!Te!]!=ROOT]
				[COUNT [!TempTab!]|Y]
				[!TempTab::[!Y!]:=[!C!]!]
			[/IF]
		[/STORPROC]
		[STORPROC [!TempTab!]|X]
			[![!Var!]Tab::[!Key!]:=[!X!]!]
		[/STORPROC]
	[ELSE]
		//On doit sauvegarder les selections donc on ajoute autant de type hidden que necessaire
		[STORPROC [!Check!]|C]
			[![!Var!]Tab::[!Key!]:=[!C::Id!]!]
		[/STORPROC]
		[!FirstTime:=1!]
	[/IF]
[/IF]
<div style="[IF [!Type!]!=Mini&&[!FromAjax!]!=True]position:absolute;top:2px;bottom:2px;[/IF]overflow:auto;right:2px;left:2px;">
	[IF [!Form!]]<form action="/[!Lien!]#[!TypeEnf!]" method="GET" name="rech[!TypeEnf!]">[/IF]
	[IF [!Type!]!=Mini&&[!FromAjax!]!=True]
		//RECHERCHE
		[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
			<span style="margin-left:5px;"> Recherche : <input type="text" name="Rech[!TypeEnf!]" value="[!Rech[!TypeEnf!]!]"  style="background-color:white;margin:0;padding:0;" /> <input type="submit" style="background-color:white;margin:0;padding:0;color:black;" value="Envoyer"/></span>
		[/BLOC]
	[/IF]
	[IF [!Visit[!TypeEnf!]!]!=][!Chemin:=[!Visit[!TypeEnf!]!]/[!TypeEnf!]!][/IF]
	[INFO [!Chemin!]|Che]
	[!Requete:=[!Che::Module!]!]
	//Calcul du niveau
	[!Niveau:=0!]
	[STORPROC [!Che::Historique!]|H|0]
	[IF [!Type!]=Mini&&[!Pos!]=[!NbResult:-1!]]
		[!Requete:=[!Requete!]/[!H::DataSource!]/[!H::Value!]!]
	[/IF]
	[IF [!H::DataSource!]!=[!TypeEnf!]&&[!H::Value!]!=]
		[!Requete:=[!Requete!]/[!H::DataSource!]/[!H::Value!]!]
		[!Niveau+=1!]
	[/IF]
	[/STORPROC]
	[IF [!Type!]=Mini]
		[BLOC Rounded||]
			<div>[!Obj::GetFirstSearchOrder!]</div>
		[/BLOC]
	[ELSE]
		[BLOC Rounded||]
			[!ROOT:=1!]
			[STORPROC [!Check!]|V][!ROOT:=0!][/STORPROC]
			[IF [!Type!]!=Full&&[!Type!]!=Mini&&[!FromAjax!]!=True]
				[IF [!Inter!]=checkbox]
				[ELSE]
					<input type="radio" value="ROOT" name="[IF [!Var!]=][!Prefixe!][!TypeEnf!][ELSE][!Var!][/IF][]" style="float:left;" [IF [!ROOT!]]checked="checked"[/IF][/STORPROC]/>
				[/IF]
			[/IF]
			<div>Racine</div>
		[/BLOC]
	[/IF]
    [MODULE Systeme/Interfaces/Arborescence/RecursivArbo?Niveau=[!Niveau!]&Chemin=[!Chemin!]&TypeEnf=[!Che::TypeChild!]&Requete=[!Requete!]&Visit[!TypeEnf!]=[!Visit[!TypeEnf!]!]&Prefixe=[!Prefixe!]&PrefixeVar=[!PrefixeVar!]&Inter=[!Inter!]&Check=[!Check!]&Type=[!Type!]&Objet=[!!]|GLOBAL]
	[STORPROC [![!Var!]Tab!]|C]
		[!T:=1!]
		[STORPROC [!TabFirst!]|D][IF [!C!]=[!D!]][!T:=0!][/IF][/STORPROC]
		//[STORPROC [!TabSup!]|E][IF [!E!]=[!C!]][!T:=0!][/IF][/STORPROC]
		[IF [!T!]]
			<input type="hidden" name="[!Var!][]" value="[!C!]" />
		[ELSE]
			//[!DEBUG::AFFICHPAS-[!C!]!]
		[/IF]
	[/STORPROC]
	[IF [!Form!]]</form>[/IF]
</div>
