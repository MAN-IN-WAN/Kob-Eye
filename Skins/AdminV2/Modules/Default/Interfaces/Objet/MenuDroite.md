<ul>
	<li><a href="?Action=Modifier" class="Fonction">Modifier</a></li>
	
	<li><a href="?Action=Deplacer" class="Fonction">Deplacer</a></li>
	
	<li><a href="?Action=Ajout" class="Fonction">Ajouter un enfant</a></li>
	[IF [!Action!]=Ajout]
		[STORPROC [!Query!]::typesEnfant|Type]
			<div class="AjoutEnfant">
				Voulez vous ajouter
				[LIMIT 0|10]
					un <a href="/[!Query!]/[!Type::Titre!]/Ajouter?OldQuery=[!Query!]">
					[IF [!Pos!]=[!NbResult!]]
						[!Type::Titre!]</a> ? 
					[ELSE][!Type::Titre!]</a>, [/IF]
				[/LIMIT]
			</div>
			[NORESULT]<div class="Infos"> Aucun ajout possible pour cet objet</div>[/NORESULT]
		[/STORPROC]
	[/IF]
	
	<li><a href="?Action=Supprimer" class="Fonction">Supprimer</a></li>
	
	[STORPROC Infos::[!Module::Actuel::Nom!]|ObjClass]
		[IF [!ObjClass::titre!]=[!Objet::ObjectType!]]
		[IF [!ObjClass::Heritage!]=VRAI]
		<li><a href="?Action=AddHeritage" class="Fonction">Ajouter un h&eacute;ritage</a></li>
		[/IF]
		[/IF]
	[/STORPROC]
</ul>