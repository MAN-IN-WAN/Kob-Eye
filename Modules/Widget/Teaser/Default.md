[STORPROC Widget/Teaser/Publier=1&DateExpiration>=[!TMS::Now!]|Wg|0|1]
	[IF [!Teas::Modele!]=]
		[MODULE Widget/Modeles/Default]
	[ELSE]
		[MODULE Widget/Modeles/[!Teas::Modele!]]
	[/IF]
[/STORPROC]

