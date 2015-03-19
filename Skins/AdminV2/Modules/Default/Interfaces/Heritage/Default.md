[TITLE]Admin Kob-Eye | Gestion des h&eacute;ritages[/TITLE]
//[MODULE Systeme/Interfaces/Objet/InfoObjet]
<form action="" method="post" >
	<div class="ContenuEntete"> 
		<h1>[MODULE Systeme/Interfaces/AdminNav/Ariane]</h1>
		[MODULE Systeme/Interfaces/BarreAction]
	</div>

	<div class="ContenuData"> 
		<div class="Panel"  style="position:absolute;top:0;bottom:0px;">
			<h1>Gestion des h&eacute;ritages</h1>

[STORPROC [!Query!]|Objet|0|1]
	[IF [!Action!]!=""]
		[SWITCH [!Action!]|=]
			[CASE "Ajouter"]
				[SWITCH [!Ajouter!]|=]
					[CASE "Valider"]
						//Ajout de l heritage
						[METHOD Objet|addHeritage]
							[PARAM][!EnfantProp!][/PARAM]
							[PARAM][!NomProp!][/PARAM]
							[PARAM][!TypeProp!][/PARAM]
							[PARAM][!GroupProp!][/PARAM]
							[PARAM][!LevelProp!][/PARAM]
							[PARAM][!OrderProp!][/PARAM]
						[/METHOD]
						//Ajout de la valeur par defaut pour chaque langue
						[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
							[METHOD Objet|addHeritageValue]
								[PARAM][!NomProp!][/PARAM]
								[PARAM][!Key!][/PARAM]
								[PARAM][![!Key!]-TitreProp!][/PARAM]
								[PARAM][![!Key!]-ValueProp!][/PARAM]
							[/METHOD]
						[/STORPROC]
						[REDIRECT][!Lien!][/REDIRECT]
					[/CASE]
					[CASE "Suivant"]
						[MODULE Systeme/Interfaces/Heritage/FormulaireHeritageValue]
					[/CASE]
					[DEFAULT]
						[MODULE Systeme/Interfaces/Heritage/FormulaireHeritage]
					[/DEFAULT]
				[/SWITCH]
			[/CASE]
			[CASE "Monter"]
				[STORPROC [!Objet::getHeritageById([!ObjId!])!]|Herit]
					[!OrderProp:=[!Herit::Order!]:-1!]
					[METHOD Objet|changeHeritage]
						[PARAM][!ObjId!][/PARAM]
						[PARAM][!Herit::Field!][/PARAM]
						[PARAM][!Herit::Type!][/PARAM]
						[PARAM][!Herit::Target!][/PARAM]
						[PARAM][!Herit::Group!][/PARAM]
						[PARAM][!Herit::Level!][/PARAM]
						[PARAM][!OrderProp!][/PARAM]
					[/METHOD]
					[REDIRECT][!Lien!][/REDIRECT]
				[/STORPROC]
			[/CASE]
			[CASE "Descendre"]
				[STORPROC [!Objet::getHeritageById([!ObjId!])!]|Herit]
					[!OrderProp:=[!Herit::Order!]:+1!]
					[METHOD Objet|changeHeritage]
						[PARAM][!ObjId!][/PARAM]
						[PARAM][!Herit::Field!][/PARAM]
						[PARAM][!Herit::Type!][/PARAM]
						[PARAM][!Herit::Target!][/PARAM]
						[PARAM][!Herit::Group!][/PARAM]
						[PARAM][!Herit::Level!][/PARAM]
						[PARAM][!OrderProp!][/PARAM]
					[/METHOD]
					[REDIRECT][!Lien!][/REDIRECT]
				[/STORPROC]
			[/CASE]
			[CASE "Modifier"]
				[SWITCH [!Modifier!]|=]
					[CASE "Valider"]
						[METHOD Objet|changeHeritage]
							[PARAM][!ObjId!][/PARAM]
							[PARAM][!NomProp!][/PARAM]
							[PARAM][!TypeProp!][/PARAM]
							[PARAM][!EnfantProp!][/PARAM]
							[PARAM][!GroupProp!][/PARAM]
							[PARAM][!LevelProp!][/PARAM]
							[PARAM][!OrderProp!][/PARAM]
						[/METHOD]
						[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
							[METHOD Objet|changeHeritageValue]
								[PARAM][!ObjId!][/PARAM]
								[PARAM][![!Key!]-TitreProp!][/PARAM]
								[PARAM][![!Key!]-ValueProp!][/PARAM]
								[PARAM][!Key!][/PARAM]
							[/METHOD]
						[/STORPROC]
						[REDIRECT][!Lien!][/REDIRECT]					
					[/CASE]
					[CASE "Suivant"]
						[STORPROC [!Objet::getHeritageById([!ObjId!])!]|Herit]
							[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
								[![!Key!]-ValueProp:=[!Herit::[!Key!]-Value!]!]
								[![!Key!]-TitreProp:=[!Herit::[!Key!]-Titre!]!]
							[/STORPROC]
						[/STORPROC]
						[MODULE Systeme/Interfaces/Heritage/FormulaireHeritageValue]
					[/CASE]
					[DEFAULT]
						[STORPROC [!Objet::getHeritageById([!ObjId!])!]|Herit]
							[!EnfantProp:=[!Herit::Target!]!]
							[!NomProp:=[!Herit::Field!]!]
							[!TypeProp:=[!Herit::Type!]!]
							[!GroupProp:=[!Herit::Group!]!]
							[!LevelProp:=[!Herit::Level!]!]
							[!OrderProp:=[!Herit::Order!]!]

							[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
								[![!Key!]-ValueProp:=[!Herit::[!Key!]-Value!]!]
								[![!Key!]-ValueProp:=[!Herit::[!Key!]-Titre!]!]
							[/STORPROC]
						[/STORPROC]
						[MODULE Systeme/Interfaces/Heritage/FormulaireHeritage]
					[/DEFAULT]
				[/SWITCH]
			[/CASE]
			[CASE "Supprimer"]
				//Faire une demande de confirmation
				[STORPROC [!Objet::getHeritageById([!ObjId!])!]|Herit]
					[IF [!Supprimer!]="Valider"]
						[METHOD Objet|removeHeritage]
							[PARAM][!ObjId!][/PARAM]
						[/METHOD]
						[REDIRECT][!Lien!][/REDIRECT]
					[ELSE]
						//Demande de confirmation
						<div class="PetiteBoiteDeDialogue" style="z-index:26000;">
							<h1><img />Suppresion d'un h&eacute;ritage</h1>
							<img />
							<div class="Message">
								Etes vous sur de vouloir supprimer l'h&eacute;ritage [!Herit::Field!] ?
							</div>
							<div class="Nav">
								<div class="boutonGauche">
									<form action="/[!Lien!]" method="post" style="display:inline;">
									<INPUT type="hidden" name="Action" VALUE="Supprimer" />
									<INPUT type="hidden" name="ObjId" VALUE="[!ObjId!]" />
									<INPUT type="submit" name="Supprimer" VALUE="Valider" />
									</form>
								</div>
								<div class="boutonDroite">
									<form action="/[!Lien!]" method="post" style="display:inline;">
									<INPUT type="submit"  value="Annuler" />
									</form>
								</div>
							</div>
						</div>
					[/IF]
				[/STORPROC]
			[/CASE]
			[DEFAULT]
				[MODULE Systeme/Interfaces/Heritage/ListeHeritage]
			[/DEFAULT]
		[/SWITCH]
	[ELSE]
		[MODULE Systeme/Interfaces/Heritage/ListeHeritage]
	[/IF]
[/STORPROC]
		</div>
	</div>

