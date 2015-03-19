<div class="Propriete">
	<h1>[!Lia::Titre!]</h1>
	//Selon les cardinalit&eacute;s de la liaison , on propose soit de creer une nouvelle entree , soit d en selectionner une, soit les deux
	[IF [!Lia::Card!]=0,1||[!Lia::Card!]=1,1]
		//Faible cardinalite
		//Donc on propose la creation d une ou plusieurs entrees
		<a href="/[!Lien!]?Liaison=[!Lia::Titre!]&Action=Ajouter">Ajouter [!Lia::Titre!]</a>
		[IF [!Liaison!]==[!Lia::Titre!]&&[!Action!]==Ajouter]
			//Affichage du formulaire
			<form action="" method="post" >
			<div class="GrosseBoiteDeDialogue">
				<h1>Ajout liaison [!Lia::Titre!]</h1>
				[OBJ [!Module::Actuel::Nom!]|[!Lia::Titre!]|Objet]
				[MODULE Systeme/Interfaces/Formulaire?Objet=[!Objet!]]
			</div>
			</form>
		[/IF]
	[/IF]
	[IF [!Lia::Card!]=0,n||[!Lia::Card!]=1,n]
		//Forte cardinalite
		//Donc on propose La creation d une ou plusierus entree ainsi que la selection avec des entrees existantes
	[/IF]
</div>