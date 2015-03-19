[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
	[OBJ [!I::Module!]|[!I::TypeChild!]|O]
	[METHOD O|AddParent][PARAM][!I::LastDirect!][/PARAM][/METHOD]
	[!type:=add!]
[ELSE]
	[STORPROC [!Query!]|O|0|1][/STORPROC]
	[!type:=edit!]
[/IF]
//Alors on enregistre les proprietes
[STORPROC [!O::getElements()!]|M]
	[STORPROC [!M::elements!]|Prop]
		[SWITCH [!Prop::type!]|=]
			[CASE fkey]
				[STORPROC [!Form_[!Prop::name!]!]|K]
					[!O::resetParents([!Prop::objectName!])!]
					[LIMIT 0|100]
						[METHOD O|AddParent]
							[PARAM][!Prop::objectModule!]/[!Prop::objectName!]/[!K!][/PARAM]
						[/METHOD]
					[/LIMIT]
				[/STORPROC]
			[/CASE]
			[CASE rkey]
			[/CASE]
			[DEFAULT]
				[METHOD O|Set]
					[PARAM][!Prop::name!][/PARAM]
					[PARAM][!Form_[!Prop::name!]!][/PARAM]
				[/METHOD]
			[/DEFAULT]
		[/SWITCH]
	[/STORPROC]
[/STORPROC]
//Sauvegarde l objet
[IF [!O::Verify!]]
	[IF [!Clone!]>1]
		[STORPROC [!Clone:-1!]|C]
			[!Ob:=[!O::getClone()!]!]
			[METHOD Ob|Save|ret][/METHOD]
		[/STORPROC]
	[/IF]
	[METHOD O|Save|ret][/METHOD]
	[IF [!ret!]][ELSE][!ret:={}!][/IF]
{"status":{
	"type":"[!type!]",
	"success":"1",
	"Id":"[!O::Id!]",
	"module":"[!O::Module!]",
	"objectClass":"[!O::ObjectType!]",
	"parentClass":"[!I::LastDirectObjectClass!]",
	"parentId":"[!I::LastId!]",
	"errors":[],
	"result":[!ret!]
}}
[ELSE]
{"status":{
	"type":"[!type!]",
	"success":0,
	"Id":"[!O::Id!]",
	"module":"[!O::Module!]",
	"objectClass":"[!O::ObjectType!]",
	"parentClass":"[!I::TypeChild!]",
	"parentId":"[!I::LastId!]",
	"errors":[
		[STORPROC [!O::Error!]|E]
		{"message":"[JSON][!E::Message!][/JSON]","field":"[!E::Prop!]"},
		[/STORPROC]
	],
	"result":{}
}}
[/IF]
