[MODULE Systeme/Interfaces/FilAriane]
[STORPROC [!Query!]|O|0|1]
	<div id="Container">
		<div id="Arbo" class="ACRelative">	
			[BLOC Panneau]
				[INFO [!Query!]|I]
				//Bouton ajouter
				<a href="/[!I::LastChild!]/Ajouter" class="KEBouton">Ajouter [!I::TypeChild!]</a>
				[MODULE Systeme/Interfaces/AdminNav]
			[/BLOC]
		</div>
		<div id="Data" class="ACRelative">
			[MODULE Systeme/Interfaces/Formulaire?O=[!O!]&Action=Modifier]
		</div>
		[NORESULT]
			Il n y a aucun correspondant a la requete [!Query!]
		[/NORESULT]
	</div>
[/STORPROC]

