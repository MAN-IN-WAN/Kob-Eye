//Detection de l existence d une priorite sur un element
[INFO [!Query!]|Test]
[!Default:=Prop!]
[STORPROC [!Test::typesEnfant!]|Enf]
	[IF [!Enf::Behaviour!]="List"][!Default:=[!Enf::Titre!]!][/IF]
[/STORPROC]
[IF [!NavObj!]=][!NavObj:=[!Default!]!][/IF]
[STORPROC [!Query!]|Obj|0|1]
	[IF [!NavObj!]=||[!NavObj!]=Prop]
		[BLOC Rounded|background:#057390;color:#FFFFFF;|margin-bottom:5px;]
			<img src="/Skins/Pilote-Boutique/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">[!Obj::getFirstSearchOrder!]</span>
		[/BLOC]
		[MODULE Systeme/Interfaces/Etat?Obj=[!Obj!]]
	[ELSE]
		[BLOC Rounded|background:#057390;color:#FFFFFF;|margin-bottom:5px;]
			<img src="/Skins/Pilote-Boutique/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;">
			<span style="margin-left:5px;">[!Obj::getFirstSearchOrder!] </span>
		[/BLOC]
		//Si c est une liste ou bien une arborescence
		[OBJ [!Module::Actuel::Nom!]|[!NavObj!]|Test]
		[IF [!Test::isReflexive!]]
			//C est une arborescence
			[MODULE Systeme/Interfaces/Arborescence?Chemin=[!Query!]/[!NavObj!]&TypeEnf=[!NavObj!]&Requete=[!Query!]&Visit[!QueryLastObject!]=[!VisitQuery!]&Type=Full]
		[ELSE]
			//Si il y a un historique C est une liste
			[MODULE Systeme/Interfaces/Liste?Chemin=[!Query!]/[!NavObj!]&TypeEnf=[!NavObj!]&Type=Full&Top=23&Bottom=0]
		[/IF]
	[/IF]
[/STORPROC]
