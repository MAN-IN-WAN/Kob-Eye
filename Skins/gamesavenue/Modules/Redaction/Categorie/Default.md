<!--- contenu central -->
[STORPROC [!Query!]|Cat|0|1|Id|ASC]
	[IF [!Cat::Title!]!=][TITLE][!Cat::Title!][/TITLE][/IF]
	[MODULE Redaction/Templates/[!Cat::Template!]?Chemin=[!Query!]]
[/STORPROC]	
