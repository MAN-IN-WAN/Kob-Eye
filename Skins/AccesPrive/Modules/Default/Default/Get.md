
[!REQUETE:=[!Systeme::CurrentMenu::Alias!]!]

[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::TypeChild!]|Obj]

// Filtre sur le menu ?
[IF [!Systeme::CurrentMenu::Filtre!]!=]
	[!FILTER:=[!Systeme::CurrentMenu::Filtre!]!]
[/IF]

//Recherche sur tous les champs
[IF [!rapidSearch!]!=]
[!FILTER+=~[!rapidSearch!]!]
[/IF]

//Affichage des options de recherche
[STORPROC [!Obj::SearchOrder!]|So]
	//[IF [!label!]!=*&&[!label!]!=&&[!Pos!]=1]
	//	[IF [!FILTER!]!=][!FILTER+=&!][/IF]
	//	[IF [!label!]~*]
	//		[STORPROC [![!label!]:/*!]|l|0|1][/STORPROC]
	//		[!FILTER+=[!So::Nom!]~[!l!]!]
	//	[ELSE]
	//		[!FILTER+=[!So::Nom!]=[!label!]!]
	//	[/IF]
	//[/IF]
	[IF [![!So::Nom!]!]!=||[![!So::Nom!]Du!]!=||[![!So::Nom!]Au!]!=]
		[IF [!FILTER!]!=][!FILTER+=&!][/IF]
		[!Test:=[![![!So::Nom!]!]:/*!]!]
		//[IF [![!So::Nom!]!]~*]
		//	[STORPROC [!Test!]|l|0|1][/STORPROC]
		//	[!FILTER+=[!So::Nom!]~%[!l!]!]
		//[ELSE]
			[SWITCH [!So::Type!]|=]
				[CASE int]
					[!FILTER+=[!So::Nom!]>[![!So::Nom!]!]!]
				[/CASE]
				[CASE date]
					//On verifie les deux variables d'encadrement.
					[IF [![!So::Nom!]Du!]!=][!FILTER+=[!So::Nom!]>=[!Utils::getTmsSencha([![!So::Nom!]Du!])!]!][/IF]
					[IF [![!So::Nom!]Au!]!=&&[![!So::Nom!]Du!]!=][!FILTER+=&!][/IF]
					[IF [![!So::Nom!]Au!]!=][!FILTER+=[!So::Nom!]<[!Utils::getTmsSencha([![!So::Nom!]Au!], true)!]!][/IF]
				[/CASE]
				[DEFAULT]
					[!FILTER+=[!So::Nom!]=[![!So::Nom!]!]!]
				[/DEFAULT]
			[/SWITCH]
		//[/IF]
	[/IF]
[/STORPROC]

//tmsCreate
[IF [!FILTER!]!=][!FILTER+=&!][/IF]
[IF [!tmsCreateDu!]!=][!FILTER+=tmsCreate>=[!Utils::getTmsSencha([!tmsCreateDu!])!]!][/IF]
[IF [!tmsCreateAu!]!=&&[!tmsCreateDu!]!=][!FILTER+=&!][/IF]
[IF [!tmsCreateAu!]!=][!FILTER+=tmsCreate<[!Utils::getTmsSencha([!tmsCreateAu!], true)!]!][/IF]
																				
//tmsEdit
[IF [!FILTER!]!=][!FILTER+=&!][/IF]
[IF [!tmsEditDu!]!=][!FILTER+=tmsEdit>=[!Utils::getTmsSencha([!tmsEditDu!])!]!][/IF]
[IF [!tmsEditAu!]!=&&[!tmsEditDu!]!=][!FILTER+=&!][/IF]
[IF [!tmsEditAu!]!=][!FILTER+=tmsEdit<[!Utils::getTmsSencha([!tmsEditAu!], true)!]!][/IF]

//Construction requete
[IF [!FILTER!]!=]
	[!REQUETE+=/[!FILTER!]!]
[/IF]
[COUNT [!REQUETE!]|C]

//Traitement des limites
[IF [!limit!]=Infinity]
	[!c:=20!]
[ELSE]
	[!c:=[!limit!]!]
[/IF]

//Traitement de l'ordre
[IF [!defaultSort!]=] [!defaultSort:=Id!][/IF]
[IF [!defaultSens!]=] [!defaultSens:=DESC!][/IF]
[IF [!sort!]!=]
	[STORPROC [!Obj::SearchOrder!]|So]
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
[ELSE]
	[!s:=[!defaultSort!]!]
	[!sens:=[!defaultSens!]!]
[/IF]

// Données
[SWITCH [!Load!]|=]
	[CASE Columns]
		// Chargement des en-têtes colonnes
		[
			{"header": "Id", "width": 30, "dataIndex": "Id"},
			[STORPROC [!Obj::SearchOrder!]|So]
				{"header": "[!So::Nom!]", "dataIndex": "[!So::Nom!]"},
			[/STORPROC]
			{"header": "Créateur", "dataIndex": "uid"},
			{"header": "Groupe Créateur", "dataIndex": "gid"},
			{"header": "Date de création", "dataIndex": "tmsCreate"},
			{"header": "Date de dernière modification", "dataIndex": "tmsEdit"},
		]
	[/CASE]
	[CASE Fields]
		// Chargement du nom des champs
		[
			"Id",
			[STORPROC [!Obj::SearchOrder!]|So]
				"[!So::Nom!]" ,
			[/STORPROC]
			"uid",
			"gid",
			"tmsCreate",
			"tmsEdit"
		]
	[/CASE]
	[CASE SearchOrder]
		// Chargement des champs de recherche
		[
			{
				fieldLabel: "ID",
				labelSeparator: "",
				anchor: "100%",
				listeners: {
					"valid": function( field ) {
						var store = field.ownerCt.ownerCt.nextSibling().nextSibling().getStore();
						store.reload();
					}
				}
			}
		]
	[/CASE]
	[CASE Data]
		[COUNT [!REQUETE!]|Count]
		// Chargement des données
		{
			"count": "[!Count!]",
			"requete": "[!REQUETE!]",
			"filtre": "[!FILTER!]",
			"data": [
				[STORPROC [!REQUETE!]|Z|[!start!]|[!c!]|[!sort!]|[!dir!]]
					{
						"Id": "[!Z::Id!]",
						"label": "[JS][!Z::getFirstSearchOrder!][/JS]",
						[STORPROC [!Z::SearchOrder!]|S]
							[SWITCH [!S::Type!]|=]
								[CASE date]
									"[!S::Nom!]": "[IF [!S::Valeur!]!=][!Utils::getDate(d/m/Y, [!S::Valeur!])!][/IF]",
								[/CASE]
								[DEFAULT]
									"[!S::Nom!]": "[JS][!S::Valeur!][/JS]",
								[/DEFAULT]
							[/SWITCH]
						[/STORPROC]
						"uid": "[STORPROC Systeme/User/[!Z::uid!]|U|0|1][JS][!U::Nom!] [!U::Prenom!][/JS][/STORPROC]",
						"gid": "[STORPROC Systeme/Group/[!Z::gid!]|G|0|1][JS][!G::Nom!][/JS][/STORPROC]",
						"tmsCreate": "[DATE d/m/Y H:i][!Z::tmsCreate!][/DATE]",
						"tmsEdit": "[DATE d/m/Y H:i][!Z::tmsEdit!][/DATE]"
					},
				[/STORPROC]
			]
		}
	[/CASE]
[/SWITCH]