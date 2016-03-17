[SWITCH [!P::type!]|=]
	[CASE fkey]
            [STORPROC [!O::getParents([!P::objectName!])!]|O2|0|1]
		[!O2::Id!]
                [NORESULT]
                ""
                [/NORESULT]
            [/STORPROC]
	[/CASE]	
	[CASE image]
		"[!O::[!P::name!]!]"
	[/CASE]	
	[CASE date]
		"[DATE d/m/Y H:i:s][!O::[!P::name!]!][/DATE]"
	[/CASE]	
	[DEFAULT]
		"[JSON][!O::[!P::name!]!][/JSON]"
	[/DEFAULT]
[/SWITCH]	
