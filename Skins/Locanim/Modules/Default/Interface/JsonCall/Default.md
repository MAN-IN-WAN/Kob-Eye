//CONFIGURATION
[INFO [!Chemin!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|O]
//Affichage des options de recherche
[STORPROC [!O::SearchOrder!]|So]
	[IF [!label!]!=*&&[!label!]!=&&[!Pos!]=1]
		[IF [!FILTER!]!=][!FILTER+=&!][/IF]
		[IF [!label!]~*]
			[STORPROC [![!label!]:/*!]|l|0|1][/STORPROC]
			[!FILTER+=[!So::Nom!]~[!l!]!]
		[ELSE]
			[!FILTER+=[!So::Nom!]=[!label!]!]
		[/IF]
	[/IF]
	[IF [![!So::Nom!]!]!=||[![!So::Nom!]Du!]!=||[![!So::Nom!]Au!]!=]
		[IF [!FILTER!]!=][!FILTER+=&!][/IF]
		[!Test:=[![![!So::Nom!]!]:/*!]!]
		[IF [![!So::Nom!]!]~*]
			[STORPROC [!Test!]|l|0|1][/STORPROC]
			[!FILTER+=[!So::Nom!]~%[!l!]!]
		[ELSE]
			[SWITCH [!So::Type!]|=]
				[CASE int]
					[!FILTER+=[!So::Nom!]>[![!So::Nom!]!]!]
				[/CASE]http://apaloosa.local/Locanim/Lumiere/Get.json
				[CASE date]
					//On verifie les deux variables d"encadrement.
					[IF [![!So::Nom!]Du!]!=][!FILTER+=[!So::Nom!]>[!Utils::getTms([![!So::Nom!]Du!])!]!][/IF]
					[IF [![!So::Nom!]Au!]!=&&[![!So::Nom!]Du!]!=][!FILTER+=&!][/IF]
					[IF [![!So::Nom!]Au!]!=][!FILTER+=[!So::Nom!]<[!Utils::getTms([![!So::Nom!]Au!])!]!][/IF]
				[/CASE]
				[DEFAULT]
					[!FILTER+=[!So::Nom!]=[![!So::Nom!]!]!]
				[/DEFAULT]
			[/SWITCH]
		[/IF]
	[/IF]
[/STORPROC]
//Constrcution requete
[!REQUETE:=[!Chemin!]!]
[IF [!FILTER!]!=][!REQUETE+=/[!FILTER!]!][/IF]
[COUNT [!REQUETE!]|C]

//Traitement des limites
[IF [!count!]=Infinity]
	[!c:=20!]
[ELSE]
	[!c:=[!count!]!]
[/IF]
//Traitement de l"ordre
[IF [!defaultSort!]=] [!defaultSort:=Id!][/IF]
[IF [!defaultSens!]=] [!defaultSens:=DESC!][/IF]
[IF [!sort!]!=]
	[STORPROC [!O::SearchOrder!]|So]
		[IF [!sort!]=-[!So::Nom!]]
			[!sens:=ASC!]
			[!s:=[!So::Nom!]!]
		[/IF]
		[IF [!sort!]=[!So::Nom!]]
			[!sens:=DESC!]
			[!s:=[!So::Nom!]!]
		[/IF]
	[/STORPROC]
	[IF [!sort!]=-Id]
		[!sens:=ASC!]
		[!s:=Id!]
	[/IF]
	[IF [!sort!]=Id]
		[!sens:=DESC!]
		[!s:=Id!]
	[/IF]
[ELSE][!s:=[!defaultSort!]!][!sens:=[!defaultSens!]!][/IF]
{ 
	"identifier": "Id",
	"label": "label",
	"start": "[!start!]",
	"count": "[!c!]",
	"numRows": "[!C!]",
	"query":"[!REQUETE!]",
	"typeChild":"[!I::TypeChild!]",
	"filter":"[!FILTER!]",
	"items": [
		[IF [!Empty!]]
			{ "Id":"", "label":""}
		[/IF]
		[IF [!Empty!]&&[!C!]>0],[/IF]
		[STORPROC [!REQUETE!]|Z|[!start!]|[!c!]|[!s!]|[!sens!]|]
			[IF [!Pos!]>1],[/IF]
			{ "Id":"[!Z::Id!]", "label":"[JSON][!Z::getFirstSearchOrder!][/JSON]"
			[STORPROC [!Z::Proprietes!]|S],"[!S::Nom!]":"[JSON][!S::Valeur!][/JSON]"[/STORPROC]
//			,"xuid":"[!Z::uid!]","gid":"[!Z::gid!]","tmsCreate":"[DATE d/m/Y H:i][!Z::tmsCreate!][/DATE]","tmsEdit":"[DATE d/m/Y H:i][!Z::tmsEdit!][/DATE]"
			}
		[/STORPROC]
	]
}