[!Req:=[!q!]!]
[IF [!init!]=1]
	[!Req+=/[!v!]!]
	[!limit:=1!]
[ELSE]
	[!Req+=/[!tf!]~[!s!]!]
	// Décommenter quand le bug des requetes avec multiple ~ sera corrigé
	// [IF [!tf2!]!=][!Req+=||[!tf2!]~[!s!]!][/IF]
[/IF]
[
	[STORPROC [!Req!]|I|0|[!limit!]|[!tf!]|ASC]
	{
		"TextField": "[!I::[!tf!]!]",
		"TextField2": "[IF [!tf2!]!=][!I::[!tf2!]!][/IF]",
		"ValueField": "[!I::[!vf!]!]"
	}
	[IF [!Pos!]<[!NbResult!]&&[!Pos!]<11],[/IF]
	[/STORPROC]
]