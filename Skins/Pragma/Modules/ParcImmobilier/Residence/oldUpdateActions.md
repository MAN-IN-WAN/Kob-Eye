toto
/////////////////// Pseudo CRON : Supprime les options qui ne sont plus valables et les dénonciations dépassées
[!MinTms:=[!TMS::Now!]!]
[!MinTms-=172800!]
[STORPROC ParcImmobilier/Action/Type=Optionner&&tmsCreate<[!MinTms!]|Act]

	// on recupère le lot et ensuite on va chercher pour ce lot la dernière action active 
 	[STORPROC ParcImmobilier/Lot/Action/[!Act::Id!]|StLot|0|1]
		[METHOD StLot|Set][PARAM]Statut[/PARAM][PARAM]1[/PARAM][/METHOD]
		[METHOD StLot|Save][/METHOD]
	[/STORPROC]
	
	[!LAction:=[!Act::Id!]!]
 	// envoi du mail de option échue
	[STORPROC Systeme/User/Action/[!LAction!]|Prs|0|1][/STORPROC]
	[MODULE ParcImmobilier/Mail?Type=OptionEchu&LeLot=[!LotId!]&Prescripteur=[!Prs::Id!]]
 
	[!Act::Delete()!]

[/STORPROC]

[!MinTms:=[!TMS::Now!]!]
[!MinTms-=7948800!]

[STORPROC ParcImmobilier/Denonciation/tmsCreate<[!MinTms!]|Den]

 	// envoi du mail de option échue
	[STORPROC Systeme/User/Denonciation/[!Den::Id!]|Prs|0|1][/STORPROC]
	[MODULE ParcImmobilier/Mail?Type=DenonciationEchu&Prescripteur=[!Prs::Id!]&Qui=[!Den::Id!]]
 
	[!Den::Delete()!]

[/STORPROC]

///////////////////////// Fin Pseudo Cron ---------------------------------------------------------
tutu