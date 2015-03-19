// FORMULAIRE DE CONFIGURATION DU COMPOSANT QUI EST AJOUTE / MODIFIE

[!Config:=[!Component::getInstance([!Path!])!]!]

[STORPROC [!Config::Proprietes!]|Prop]
	[IF [!ModeEdition!]=1]
		[!Prop:=[!Cmp::getPropAvecValeur([!Prop!])!]!]
	[/IF]
	<div class="ProprieteModif" style="overflow:hidden">
		<div class="ProprieteTitreModif">[!Prop::description!]</div>
		<div class="ProprieteValeurModif">
			[MODULE Systeme/Interfaces/Formulaire/GetInput?&Prop=[!Prop!]&Prefixe=CC_&Valeur=[!Prop::Valeur!]]
		</div>
	</div>
[/STORPROC]