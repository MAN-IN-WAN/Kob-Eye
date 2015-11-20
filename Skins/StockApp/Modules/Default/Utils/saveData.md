{
        "success": true,
        "results":[
//enregistrement
[!All:=[!JsonP::getInput()!]!]
[STORPROC [!All!]|Data]
	[IF [!Data!]]
	    [LOG]-----------JSONP INPUT---------[/LOG]
	    //[STORPROC [!Data!]|O]
		[!O:=[!Data!]!]
		[INFO [!Query!]|I]
		//generation object
		[IF [!O::id!]~ext-record||[!O::id!]=]
			//Nouveau
			[!O::id:=!]
			[OBJ [!I::Module!]|[!I::TypeChild!]|P]
			[!HistoBase:=[!I::Historique!]!]
			[!HistoBase:=[!HistoBase::0!]!]
			[METHOD P|AddParent]
				[PARAM][!HistoBase::Module!]/[!HistoBase::DataSource!]/[!HistoBase::Value!][/PARAM]
			[/METHOD]
			[!TYPE:=NEW!]
			
		[ELSE]
			[STORPROC [!Query!]/[!O::id!]|P][/STORPROC]
			[!TYPE:=EDIT!]
		[/IF]
		[LOG][!O::id!] - [!O::Nom!] ([!TYPE!])[/LOG]
	
		//Proprietes
		[STORPROC [!P::Proprietes()!]|Prop]
			[SWITCH [!Prop::type!]|=]
				[CASE date]
					[METHOD P|Set]
						[PARAM][!Prop::Nom!][/PARAM]
						[PARAM][!O::[![!Prop::Nom!]Date!] [!O::[!Prop::Nom!]Time!][/PARAM]
					[/METHOD]
				[/CASE]
				[DEFAULT]
					[METHOD P|Set]
						[PARAM][!Prop::Nom!][/PARAM]
						[PARAM][!O::[!Prop::Nom!]!][/PARAM]
					[/METHOD]
				[/DEFAULT]
			[/SWITCH]
		[/STORPROC]
		
		//Parents
		[STORPROC [!P::getParentElements()!]|Par]
			[IF [!O::[!Par::name!]!]>0]
				[METHOD P|AddParent]
					[PARAM][!I::Module!]/[!Par::objectName!]/[!O::[!Par::name!]!][/PARAM]
				[/METHOD]
			[/IF]
		[/STORPROC]
		
		//Verification
		[IF [!P::Verify!]]
			//Sauvegarde
			[METHOD P|Save][/METHOD]
		[ELSE]
		    [STORPROC [!P::Error!]|E]
			[LOG]-- EE > [!E::Message!] [!Error_[!E::Prop!]:=1!][/LOG]
		    [/STORPROC]
		[/IF]
		
		//Affichage du résultat
		//[IF [!Pos!]>1],[/IF]
		{
			"id":"[!P::Id!]",
			"label":"[!P::getFirstSearchOrder()!]"
			[STORPROC [!P::getElementsByAttribute(list,,1)!]|Prop]
				[NORESULT]
					[STORPROC [!P::getElementsByAttribute(type,,1)!]|Prop]
						,
						"[!Prop::name!]":[MODULE Systeme/Utils/getDataType?P=[!Prop!]&O=[!P!]]
					[/STORPROC]
				[/NORESULT]
				,
				"[!Prop::name!]":[MODULE Systeme/Utils/getDataType?P=[!Prop!]&O=[!O!]]
			[/STORPROC]
		},
	    //[/STORPROC]
	[/IF]
[/STORPROC]
 ]
}