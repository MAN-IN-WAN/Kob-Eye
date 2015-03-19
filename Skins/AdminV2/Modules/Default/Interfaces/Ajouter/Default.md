[MODULE Systeme/Interfaces/FilAriane]
[INFO [!Query!]|Parent]
<div id="Container">
	[OBJ [!Parent::Module!]|[!Parent::TypeChild!]|O2]
	//On ajoute une liaison sur l emplacement si il s agit d un nouvel objet
	[METHOD O2|AddParent]
		[PARAM][!Query!][/PARAM]
	[/METHOD]
	
	[!Integrated:=False!]
	[STORPROC [!O2::typesParent!]|Par]
		[IF [!Par::Titre!]==[!Parent::ObjectType!]&&[!Par::Behaviour!]==Integrated]
			[!Integrated:=True!]
		[/IF]
	[/STORPROC]
	[IF [!Integrated!]==True]
		[INFO [!Query!]|HistoObj]
		[STORPROC [!HistoObj::Historique!]|Histo|0|1]
			[LIMIT 0|1]
				[MODULE [!HistoObj::Module!]/[!Histo::DataSource!]/[!Histo::Value!]]
				[!BackUrl:=[!HistoObj::Module!]/[!Histo::DataSource!]/[!Histo::Value!]!]
			[/LIMIT]
		[/STORPROC]
		[BLOC PopUpForm|AJOUTER|Larger|[!BackUrl!]]
			[MODULE Systeme/Interfaces/Formulaire?O=[!Objet!]&Action=Ajouter&Type=PopUp]
		[/BLOC]
	[ELSE]
		<div id="Arbo">	
			[BLOC Panneau]
				[INFO [!Query!]|I]
				//Bouton ajouter
				<a href="/[!I::LastChild!]/Ajouter" class="KEBouton">Ajouter [!I::TypeChild!]</a>
				[MODULE Systeme/Interfaces/AdminNav]
			[/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau|overflow:auto;]
				[MODULE Systeme/Interfaces/Formulaire?O=[!O2!]&Action=Ajouter]
			[/BLOC]
		</div>
	[/IF]
</div>
//TEST
