[MODULE Systeme/Configuration/Top]
<div class="PetiteBoiteDeDialogue">
	[IF [!Module::Actuel::Check!]=1]
		La mise &agrave; jour s'est bien pass&eacute;e
	[ELSE]
		Une erreur a eu lieu lors de la mise &agrave; jour.
	[/IF]
	<div style="text-align:center;margin-top:10px;"><a href="/[!Module::Actuel::Nom!]">Retour &agrave; l'admin</a></div>
</div>
[MODULE Systeme/Configuration/Bottom]
