[!Req:=[!q!]!]
//[!tf2:=Code!]
[IF [!init!]=1]
	[!Req+=/[!v!]!]
	[!limit:=1!]
[ELSE]
	[!Req+=/[!tf!]>=[!s!]!]
	// Décommenter quand le bug des requetes avec multiple ~ sera corrigé
	// [IF [!tf2!]!=][!Req+=||[!tf2!]~[!s!]!][/IF]
	[!limit:=1!]
[/IF]
[LOG]R : [!Req!][/LOG]
[COUNT [!Req!]|NbR]

[IF [!NbR!]]
	
	[
		[STORPROC [!Req!]|I|0|1|[!tf!]|ASC]
			[LOG]lu1 : [!I::Id!][/LOG]
			[STORPROC Geographie/Departement/Ville/[!I::Id!]|D][/STORPROC]
		{
			"TextField": "[!I::[!tf!]!] ([!D::[!tf2!]!])",
			"ValueField": "[!I::[!vf!]!]"
		}
		[IF [!Pos!]<[!NbResult!]&&[!Pos!]<11],[/IF]
		[/STORPROC]
	]

[ELSE]

	[!Req:=[!q!]/CodePostal!]
	//Specif gazservice
	[!tf2:=Code!]
	[!Req+=/[!tf2!]~[!s!]!]
	[!limit:=10!]
//	[LOG]R : [!Req!][/LOG]
	[
		[STORPROC [!Req!]|I|0|4|[!tf2!]|ASC]
			[LOG]lu2 : [!I::Id!][/LOG]
			[STORPROC Geographie/Departement/Ville/[!I::Id!]|D][/STORPROC]
		{
			"TextField": "[!I::[!tf!]!] ([!D::[!tf2!]!])",
			"ValueField": "[!I::[!vf!]!]"
		}
		[IF [!Pos!]<[!NbResult!]&&[!Pos!]<11],[/IF]
		[/STORPROC]
	]


[/IF]