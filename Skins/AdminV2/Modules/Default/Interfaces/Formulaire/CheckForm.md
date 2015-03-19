[INFO [!Qu!]|Test]
[IF [!Action!]=Modifier]
    [STORPROC [!Qu!]|Objet|0|1]
    [/STORPROC]
[ELSE]
    
    [OBJ [!Test::Module!]|[!Test::TypeChild!]|Objet]
[/IF]

{ "query":"[!Qu!]",
//Alors on enregistre les proprietes
[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
    [STORPROC [!Objet::Proprietes([!Key!])!]|Prop]
	[METHOD Objet|Set]
	    [PARAM][!Prop::Nom!][/PARAM]
	    [PARAM][!Form_[!Prop::Nom!]!][/PARAM]
	[/METHOD]
    [/STORPROC]
[/STORPROC]
//Sauvegarde l objet

[IF [!Objet::Verify()!]]
    "form_result": true
[ELSE]
    "form_result": false,
    "errors": [
    [STORPROC [!Objet::Error!]|E]
	"[!E::Message!]"
	[IF [!Pos!]!=[!NbResult!]]
	    ,
	[/IF]
    [/STORPROC]
    ],
    "errors_field": [
    [STORPROC [!Objet::Error!]|E]
	"[!E::Prop!]"
	[IF [!Pos!]!=[!NbResult!]]
	    ,
	[/IF]
    [/STORPROC]
    ]
[/IF]
}
