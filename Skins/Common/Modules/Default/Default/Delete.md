[STORPROC [!Query!]|O|0|1]
	[!Id:=[!O::Id!]!]
	[!module:=[!O::Module!]!]
	[!objectclass:=[!O::ObjectType!]!]
	[IF [!O::Delete()!]]
{"status":{
	"type":"delete",
	"success":"1",
	"module":"[!module!]",
	"objectClass":"[!objectclass!]",
	"Id":"[!Id!]",
	"errors":[]
}}

	[ELSE]
{"status":{
	"type":"delete",
	"success":"0",
	"module":"[!module!]",
	"objectClass":"[!objectclass!]",
	"Id":"[!Id!]",
	"errors":[{"message":"[JSON]Curieuse erreur[/JSON]","field":""}],
}}
	[/IF]
[/STORPROC]