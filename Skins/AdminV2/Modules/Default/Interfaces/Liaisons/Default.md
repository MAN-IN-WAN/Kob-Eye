[STORPROC [!Query!]|Obj|0|100]
	//Enfants
	<div class="Panel">
		<h1>Cet &eacute;l&eacute;ment contient</h1>
		[STORPROC [!Query!]::typesEnfant|Type]
			[STORPROC [!Obj::Module!]/[!Obj::ObjectType!]/[!Obj::Id!]/[!Type::Titre!]|Objet]
			<div class="SousPanel">
				<h1>[!Type::Titre!]</h1>
				<div>
					[LIMIT 0|100]
						[MODULE Systeme/Interfaces/LigneObjet?Objet=[!Objet!]]
					[/LIMIT]
				</div>
			</div>
			[/STORPROC]
		[/STORPROC]
	</div>
	//Parents
	<div class="Panel">
		<h1>Cet &eacute;l&eacute;ment appartient &agrave;</h1>
		[STORPROC [!Query!]::typesParent|Type]
			[STORPROC [!Obj::Module!]/[!Type::Titre!]/[!Obj::ObjectType!]/[!Obj::Id!]|Objet]
			<div class="SousPanel">
				<h1>[!Type::Titre!]</h1>
				<div>
					[LIMIT 0|100]
						[MODULE Systeme/Interfaces/LigneObjet?Objet=[!Objet!]]
					[/LIMIT]
				</div>
			</div>
			[/STORPROC]
		[/STORPROC]
	</div>
	//Heritages
	[IF [!Obj::isHeritage!]]
		<div class="Panel">
			<h1>Cet &eacute;l&eacute;ment distribue les propri&eacute;t&eacute;s suivantes</h1>
			[STORPROC [!Obj::getHeritages!]|Heri]
				[MODULE Systeme/Interfaces/Heritage/PetiteLigneHeritage?Heri=[!Heri!]]
			[/STORPROC]
		</div>
	[/IF]

[/STORPROC]
