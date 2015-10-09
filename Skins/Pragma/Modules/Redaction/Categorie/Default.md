[STORPROC [!Query!]|Cat|0|1|Id|ASC]
	[IF [!Cat::Modele!]=]
		[MODULE Redaction/Templates/Default?Chemin=[!Query!]]
	[ELSE]
		[MODULE Redaction/Templates/[!Cat::Modele!]?Chemin=[!Query!]]
	[/IF]
[/STORPROC]

