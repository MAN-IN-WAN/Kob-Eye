[SWITCH [!P::type!]|=]
	[CASE date]
		"[DATE d/m/Y H:i:s][!O::[!P::name!]!][/DATE]"
	[/CASE]	
	[DEFAULT]
		"[JSON][!O::[!P::name!]!][/JSON]"
	[/DEFAULT]
[/SWITCH]	
