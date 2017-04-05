[STORPROC [!Query!]|Cat|0|1|Id|ASC]
	[IF [!Cat::Modele!]=]
		[MODULE Redaction/Modeles/Default?Chemin=[!Query!]]
	[ELSE]
		[MODULE Redaction/Modeles/[!Cat::Modele!]?Chemin=[!Query!]]
	[/IF]
[/STORPROC]

