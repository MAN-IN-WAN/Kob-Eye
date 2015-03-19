[STORPROC [!target!]|T|0|1][/STORPROC]
[IF [!reset!]]
	[METHOD T|resetParents][PARAM][!parentObjectClass!][/PARAM][/METHOD]
	[METHOD T|Save][/METHOD]
[/IF]
[IF [!from!]]
	[METHOD T|delParent][PARAM][!from!][/PARAM][/METHOD]
	[METHOD T|Save][/METHOD]
[/IF]
[METHOD T|addParent][PARAM][!to!][/PARAM][/METHOD]
[METHOD T|Save][/METHOD]
{"status":{
	"type":"move",
	"Id":"[!T::Id!]",
	"module":"[!T::Module!]",
	"objectClass":"[!T::ObjectType!]",
	"parent":{
		"from":"[!from!]",
		"to":"[!to!]"
	},
	"success":1
}}
