[IF [!Envoi!]=EnvoiRappel&&[!C_Tel!]!=]
	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!C_Tel!]!=][ELSE][!C_Tel_Error:=1!][!C_Error:=1!][/IF]
	//Si il y a des erreurs, on les affiche
	[IF [!C_Error!]]
		<div></div>
	[ELSE]
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Demande de rappel par le site : [/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACTRAPPEL!][/PARAM][/METHOD]
		[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					<div >
						Merci de rappeler <u style="font-weight:bold;">Numéro de téléphone</u> : [!C_Tel!]<br/>
					</div>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]

		// création du contact si inexistant
		[!MoisEncours:=[!Utils::getDate(M,[!TMS::Now!])!]!]

		[STORPROC Newsletter/GroupeEnvoi/17/Contact/Email=[!MoisEncours!]@pragma-immobilier.com|Ctc|0|1]
			[NORESULT]
				[OBJ Newsletter|Contact|Con]
				[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!MoisEncours!]@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Campagne[/PARAM][PARAM][!C_Add!][/PARAM][/METHOD]
				[IF [!C_Add!]]
					[METHOD Con|Set][PARAM]AddWord[/PARAM][PARAM]1[/PARAM][/METHOD]
				[/IF]
				[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]0[/PARAM][/METHOD]
				[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/17[/PARAM][/METHOD]
				[METHOD Con|Save][/METHOD]
			[/NORESULT]
		[/STORPROC]

		// Enregistrement du message
		[STORPROC Newsletter/GroupeEnvoi/17/Contact/Email=[!MoisEncours!]@pragma-immobilier.com|Con|0|1]
			[OBJ Newsletter|Reception|Rec]
			[METHOD Rec|Set]
				[PARAM]Contenu[/PARAM]
				[PARAM][!C_Tel!][/PARAM]
			[/METHOD]
			[METHOD Rec|Set]
				[PARAM]Sujet[/PARAM]
				[PARAM]Demande de rappel champ en haut du site[/PARAM]
			[/METHOD]
			[METHOD Rec|AddParent]
				[PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
			[/METHOD]
			[METHOD Rec|Save][/METHOD]
		[/STORPROC]

	[/IF]
	<div class="FormRappelReponse" >Merci, votre demande de rappel a été prise en compte.</div>
[ELSE]
	<form class="FormRappel" method="post" action="/[!Lien!]" id="rappel">
		<div class="LigneForm" style="overflow:hidden;">
			<input type="text" name="C_Tel" id="TelRap" value="" maxlength="12" class="rapnum" />
			<input type="hidden" name="Envoi" value="EnvoiRappel" />
			<input type="hidden" name="C_Sujet" value="Demande de rappel" />
			<input type="submit" name="envoyer" class="rapbtn">
		</div>
		
	</form>
[/IF]
