[INFO [!Query!]|I]
//generation object
[IF [!I::TypeSearch!]=Child]
	//Nouveau
	[OBJ [!I::Module!]|[!I::TypeChild!]|P]
	[!TYPE:=NEW!]
[ELSE]
	[STORPROC [!Query!]|P][/STORPROC]
	[!TYPE:=EDIT!]
[/IF]

//enregistrement
[IF [!TEST!]]
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
	[STORPROC [!P::getParentTypes()!]|Par]
		[IF [![!Par::Nom!]!]>0]
			[METHOD P|AddParent]
				[PARAM][!I::Module!]/[!Par::Titre!]/[![!Par::Nom!]!][/PARAM]
			[/METHOD]
		[/IF]
	[/STORPROC]
	
	//Verification
	[IF [!P::Verify!]]
		//Sauvegarde
		[METHOD P|Save][/METHOD]
		<div class="alert alert-success adjusted">L'élément  a été sauvegardé avec succés</div>
		[CLOSE]1[/CLOSE]
	[ELSE]
		<div class="alert alert-danger adjusted">Des erreurs empêchent la sauvegarde de l'élément:
			<ul>
				[STORPROC [!P::Error!]|E]
				<li>[!E::Message!] [!Error_[!E::Prop!]:=1!]</li>
				[/STORPROC]
			</ul>
		</div>
	[/IF]
[/IF]

		<div class="row-fluid">
			<fieldset>
				<input type="hidden" name="TEST" value="ZOB" />
				<div class="control-group [IF [!Error_Titre!]]error[/IF]">
					<label class="control-label" for="input01">Titre</label>
					<div class="controls">
						<input type="text" class="span12"  name="Titre" value="[!P::Titre!]" />
					</div>
				</div>
				<div class="control-group [IF [!Error_Description!]]error[/IF]">
					<label class="control-label" for="input02">Description</label>
					<div class="controls">
						<textarea class="span12"  name="Description"  >[!P::Description!]</textarea>
					</div>
				</div>
				<div class="control-group [IF [!Error_Ordre!]]error[/IF]">
					<label class="control-label" for="input03">Ordre</label>
					<div class="controls">
						<input type="text" class="span12"  name="Ordre" value="[!P::Ordre!]" />
					</div>
				</div>
				<div class="control-group [IF [!Error_Icone!]]error[/IF]">
					<label class="control-label" for="input03">Icone</label>
					<div class="controls">
						<input type="text" class="span12"  name="Icone" value="[!P::Icone!]" />
					</div>
				</div>
			</fieldset>
		</div>

