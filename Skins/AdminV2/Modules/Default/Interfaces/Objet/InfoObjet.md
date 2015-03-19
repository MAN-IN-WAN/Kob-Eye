[STORPROC [!Query!]|Objet|0|1]
	[IF [!Objet::QueryType!]="Direct"]
		<div class="ContenuEntete"> 
			[MODULE Systeme/Interfaces/BarreAction?Obj=[!Objet!]]
		</div>
		<div class="ContenuData"> 
			[TITLE]Admin Kob-Eye | Informations Objet[/TITLE]
			[MODULE Systeme/Interfaces/Objet/AffichProprietes]
		</div>
	[ELSE]
		[MODULE Systeme/Interfaces/Objet/InfoModule]
	[/IF]
	[NORESULT]
		[MODULE Systeme/Interfaces/Objet/InfoModule]
	[/NORESULT]
[/STORPROC]

