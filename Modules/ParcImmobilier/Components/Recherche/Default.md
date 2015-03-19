[IF [!Affichage!]=Client]
	[COMPONENT ParcImmobilier/Navigation/Default?NOMDIV=RESIDENCECLIENT]
[ELSE]
	<div id="RechercheAccueil">
		[MODULE ParcImmobilier/Residence/Recherche]
	</div>
[/IF]