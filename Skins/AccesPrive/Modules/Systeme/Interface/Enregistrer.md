/////////// Chargement de l'objet ///////////
[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|Last][/STORPROC]
[IF [!Last::Value!]=]
	// Nouvel objet
	[OBJ [!I::Module!]|[!I::TypeChild!]|Objet]
[ELSE]
	// Objet existant qu'on modifie
	[STORPROC [!Query!]|Objet][/STORPROC]
[/IF]

/////////// Modifications ///////////
[STORPROC [!Objet::Proprietes!]|Prop]
	[!Champ:=[!Prop::Nom!]!]
	[!Valeur:=[![!Prop::Nom!]!]!]
	// Cas particuliers
	// -> Booleens
	[IF [!Prop::Type!]=boolean]
		[IF [!Valeur!]=on][!Valeur:=1!][ELSE][!Valeur:=0!][/IF]
	[/IF]
	// -> Fichier
	[IF [!Prop::Type!]=file&&[!Valeur!]=Pas de fichier]
		[!Valeur:=!]
	[/IF]
	[METHOD Objet|Set]
		[PARAM][!Champ!][/PARAM]
		[PARAM][!Valeur!][/PARAM]
	[/METHOD]
[/STORPROC]

/////////// Enregistrement ///////////
[METHOD Objet|Save][/METHOD]

{
	success: true
}