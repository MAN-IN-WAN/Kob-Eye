[IF [!start!]!=]
// Production CSV
[OBJ Newsletter|Contact|Ct][!Ct::sendHeader()!]
[STORPROC Newsletter/GroupeEnvoi/1/Contact/tmsCreate>=[!start!]&tmsCreate<=[!stop!]|C] [!Ct::addContact([!C!])!][/STORPROC]
[ELSE]
	// Formulaire
	<form action="" method="post">
		De <select name="start">
			[!Annee:=2010!]
			[STORPROC 5|An]
				[!Annee+=1!]
				[STORPROC 12|Mois]
					<option value="[!Utils::getTms(1/[!Mois:+1!]/[!Annee!])!]">[UTIL MONTH][!Mois:+1!][/UTIL] [!Annee!]</option>
				[/STORPROC]
			[/STORPROC]
		</select>
		à <select name="stop">
			[!Annee:=2010!]
			[STORPROC 5|An]
				[!Annee+=1!]
				[STORPROC 12|Mois]
					<option value="[!Utils::getTms(1/[!Mois:+2!]/[!Annee!])!]">[UTIL MONTH][!Mois:+1!][/UTIL] [!Annee!]</option>
				[/STORPROC]
			[/STORPROC]
		</select> (inclus)
		<button type="submit">OK</button>
	</form>
[/IF]