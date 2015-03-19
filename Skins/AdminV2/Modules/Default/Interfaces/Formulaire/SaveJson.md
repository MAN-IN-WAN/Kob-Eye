[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
	[OBJ [!I::Module!]|[!I::TypeChild!]|O]
	[METHOD O|AddParent][PARAM][!Query!][/PARAM][/METHOD]
[ELSE]
	[STORPROC [!Query!]|O|0|1][/STORPROC]
[/IF]
//Alors on enregistre les proprietes
[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
	[STORPROC [!O::Proprietes([!Key!])!]|Prop]
		[METHOD O|Set]
			[PARAM][!Prop::Nom!][/PARAM]
			[PARAM][!Form_[!Prop::Nom!]!][/PARAM]
		[/METHOD]
	[/STORPROC]
[/STORPROC]
//Sauvegarde l objet
[IF [!O::Verify!]]
	[IF [!Clone!]>1]
		[STORPROC [!Clone:-1!]|C]
			[!Ob:=[!O::getClone()!]!]
			[METHOD Ob|Save][/METHOD]
		[/STORPROC]
	[/IF]
	[METHOD O|Save][/METHOD]
	{
		"success": 1,
		"errors": []
	}
[ELSE]
	{
		"success": 0,
		"errors": [
		[STORPROC [!O::Error!]|E]
			{"message":"[JSON][!E::Message!][/JSON]","field":"[!E::Prop!]"},
		[/STORPROC]
		]
	}
[/IF]
