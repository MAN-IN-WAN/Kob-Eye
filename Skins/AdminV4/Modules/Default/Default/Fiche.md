<!-- page header -->
[INFO [!Query!]|I]
//generation object
[IF [!I::TypeSearch!]=Child]
	//Nouveau
	[OBJ [!I::Module!]|[!I::ObjectType!]|P]
	[!TYPE:=NEW!]
	[ELSE]
	[STORPROC [!Query!]|P][/STORPROC]
	[!TYPE:=EDIT!]
[/IF]

//enregistrement
[IF [!submitted!]]
	//Proprietes
	[STORPROC [!P::Proprietes()!]|Prop]
		[SWITCH [!Prop::type!]|=]
			[CASE date]
				[METHOD P|Set]
					[PARAM][!Prop::Nom!][/PARAM]
					[PARAM][![!Prop::Nom!]Date!] [![!Prop::Nom!]Time!][/PARAM]
				[/METHOD]
			[/CASE]
			[DEFAULT]
				[METHOD P|Set]
					[PARAM][!Prop::Nom!][/PARAM]
					[PARAM][![!Prop::Nom!]!][/PARAM]
				[/METHOD]
			[/DEFAULT]
		[/SWITCH]
	[/STORPROC]
	
	//Parents
	[STORPROC [!P::getElements()!]|Els]
		[STORPROC [!Els::elements!]|Par]
			[IF [!Par::type!]=fkey&&[![!Par::name!]!]]
				[METHOD P|resetParents][PARAM][!Par::objectName!][/PARAM][PARAM][!Par::name!][/PARAM][/METHOD]
				[STORPROC [![!Par::name!]!]|Pa]
					[METHOD P|AddParent]
						[PARAM][!Par::objectModule!]/[!Par::objectName!]/[!Pa!][/PARAM]
					[/METHOD]
				[/STORPROC]
			[/IF]
		[/STORPROC]
	[/STORPROC]
	
	//Verification
	[IF [!P::Verify!]]
		//Sauvegarde#Cc445c
		[METHOD P|Save][/METHOD]
		[IF [!TYPE!]=NEW]
			[REDIRECT][!Systeme::CurrentMenu::Url!]/[!P::Id!]?message=success[/REDIRECT]
		[/IF]
		<div class="alert alert-success adjusted">
			Le [!P::getDescription!] a été sauvegardé avec succés
		</div>
	[ELSE]
		<div class="alert alert-danger adjusted">
			Des erreurs empêchent la sauvegarde du [!P::getDescription!]:
			<ul>
				[STORPROC [!P::Error!]|E]
				<li>
					[!E::Message!] [!Error_[!E::Prop!]:=1!]
				</li>
				[/STORPROC]
			</ul>
		</div>
	[/IF]
[/IF]

//Message success apres redirect
[IF [!message!]=success]
<div class="alert alert-success adjusted">
	Le [!P::getDescription!] a été sauvegardé avec succés
</div>
[/IF]

//<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-large btn-warning pull-right">Retour à la liste des [!P::getDescription!]</a>
//[IF [!TYPE!]=NEW]
//<h1 id="page-header">Nouveau [!P::getDescription!]</h1>
//[ELSE]
//<h1 id="page-header">Edition du [!P::getDescription!] [!P::Titre!]</h1>
//[/IF]

[MODULE [!I::Module!]/[!I::ObjectType!]/FormOnly?NBCOL=3&VERTICAL=1]