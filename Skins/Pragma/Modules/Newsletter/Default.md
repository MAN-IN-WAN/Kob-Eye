[STORPROC [!Query!]/Lettre/[!Id!]|Let|0|1]
	[IF [!Let::Modele!]=]
		[MODULE Newsletter/Modeles/Default?Id=[!Let::Id!]]
	[ELSE]
		[MODULE Newsletter/Modeles/[!Let::Modele!]?Id=[!Let::Id!]]
	[/IF]
[/STORPROC]

