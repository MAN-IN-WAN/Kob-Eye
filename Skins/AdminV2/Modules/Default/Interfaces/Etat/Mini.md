[BLOC Panneau|background:#DEE0DF;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
	[STORPROC [!Obj::SearchOrder!]|P]
		<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;background-color:#C465C7;">[LIMIT 0|1][!P::Valeur!][/LIMIT]</div>
		<div style="margin:5px">
			[LIMIT 1|100]
				[MODULE Systeme/Interfaces/LignePropriete?Prop=[!P!]]
			[/LIMIT]
		</div>
	[/STORPROC]
[/BLOC]
