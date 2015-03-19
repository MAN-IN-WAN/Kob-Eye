<div class="Panel"  style="position:absolute;top:0;bottom:50px;">
	<h1>H&eacute;ritages existants</h1>
	[MODULE Systeme/Interfaces/Heritage/EnteteHeritage]
	[STORPROC [!Query!]::getHeritages|Heri]
		[ORDER Order|ASC]
			[MODULE Systeme/Interfaces/Heritage/LigneHeritage?Heri=[!Heri!]]
		[/ORDER]
	[/STORPROC]
</div>
<div class="Nav">
		<div class="boutonGauche">
		<form action="/[!Query!]" method="post" style="display:inline;">
		<INPUT type="submit"  value="Fermer" style="float:left"/>
		</form>
		</div>
		<div class="boutonDroite">
		<form action="" method="post" style="display:inline;">
			<INPUT type="hidden" name="Action" value="Ajouter" />
			<INPUT type="submit"  value="Ajouter un H&eacute;ritage" style="float:left;" />
		</form>
		</div>
</div>
