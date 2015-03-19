// UPDATE LES PARAMETRES D'UN COMPOSANT

[STORPROC Systeme/ActiveTemplate/[!t!]|TPL][/STORPROC]
[!Cmp:=[!TPL::getComponent([!z!],[!c!])!]!]

[IF [!updateConfig!]=1]
	// On met à jour le composant
	[METHOD TPL|updateComponent]
		[PARAM][!z!][/PARAM]
		[PARAM][!c!][/PARAM]
	[/METHOD]
	[METHOD TPL|Save][/METHOD]
[/IF]

[!Path:=[!Cmp::Module!]/[!Cmp::Title!]!]

<div class="BigTitle">Configuration</div>
[MODULE Systeme/Configuration/Modeles/configComponent?Path=[!Path!]&Cmp=[!Cmp!]&ModeEdition=1]

<input type="hidden" name="updateConfig" value="1" />
<input type="hidden" name="t" value="[!t!]" />
<input type="hidden" name="z" value="[!z!]" />
<input type="hidden" name="c" value="[!c!]" />