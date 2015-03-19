//[IF [!SERVER::REMOTE_ADDR!]=192.168.1.74||[!SERVER::REMOTE_ADDR!]=178.22.145.106]
	//178.22.145.106]
	// MODIFICATIONS DEMANDEES JUIN 2014
	// ON SIMPLIFIE LA SAISIE
	// ON GARDE JUSTE NOM PRENOM ET TEL OU MAIL (il faut au moins un des champs de renseigné)
	// on ajoute INFORMATIONS COMPLEMENTAIRES UN CHAMP LIBRE
	
	<h1>Dénonciation</h1>
	[!Reset:=1!]
	[IF [!Envoi!]!=EnvoiForm||[!C_Pr_Error!]]
		[IF [!C_Pr_Error!]][!Reset:=0!][/IF]
		<div class="DivFormDenonce">
			<form class="FormDenonce" enctype="multipart/form-data"  method="post"  action="/[!Lien!]">

<div class="BlocEnvoiDenonciation"><h3>Afin que votre dénonciation soit effective, nous vous prions de bien vérifier<br />l’orthographe du Nom et du Prénom du contact.<br />Dans le cas où la dénonciation concerne deux personnes,<br />veuillez s’il vous plait réaliser deux dénonciations différentes</h3>
</div>
				<div class="LigneForm">
					<label>Nom <span class="Obligatoire">*</span></label>
					<input type="text" name="C_Pr_Nom"  value="[IF [!Reset!]=0][!C_Pr_Nom!][/IF]"  class="[IF [!C_Pr_Nom_Error!]]inputError[/IF]" onkeyup='this.value=this.value.toUpperCase()'  onblur='this.value=this.value.toUpperCase()' />
				</div>
				<div class="LigneForm">
					<label>Pr&eacute;nom <span class="Obligatoire">*</span></label>
						<input type="text" name="C_Pr_Prenom" value="[IF [!Reset!]=0][!C_Pr_Prenom!][/IF]" class="[IF [!C_Pr_Prenom_Error!]]inputError[/IF]" onkeyup='this.value=this.value.toUpperCase()' onblur='this.value=this.value.toUpperCase()' />
				</div>
				<fieldset >
 					<LEGEND >Renseigner une de ces deux informations au choix <span class="Obligatoire">*</span></LEGEND> 
					<div class="LigneForm">
						<label>N&deg; de t&eacute;l&eacute;phone </label>
						<input type="text" name="C_Pr_Tel" value="[IF [!Reset!]=0][!C_Pr_Tel!][/IF]"  class="[IF [!C_Pr_Tel_Error!]]inputError[/IF] Reduit"/>
					</div>
					<div class="LigneForm">
						<label>Adresse e-mail</label>
						<input type="text" name="C_Pr_MailContact" value="[IF [!Reset!]=0][!C_Pr_MailContact!][/IF]"  class="[IF [!C_Pr_Mail_Error!]]inputError[/IF]" />
					</div>
				</fieldset>
				<div class="LigneForm">
					<label >Informations complémentaires </label>
					<textarea cols="80" rows="8" name="C_Pr_AutreRenseignement" style="[IF [!C_Pr_AutreRenseignement_Error!]]background-color:#FFDE01;[/IF]" >[IF [!Reset!]=0][!C_Pr_AutreRenseignement!][/IF]</textarea>
				</div>
				<div class="LigneForm" style="padding-left:10px;">
					Les champs marqu&eacute;s (<span class="obligatoire">*</span>) sont obligatoires.
				</div>
				<div class="LigneForm ">
					<input type="hidden" name="Envoi" value="EnvoiForm" />
					<input type="hidden" name="Affichage" value="Liste" />
					<div class="lienBtnCnt">
						<input type="submit" value="Envoyer">
					</div>
				</div>
			</form>
		</div>
	[/IF]
	
//[ELSE]
//	[MODULE ParcImmobilier/Denonciation/CreationOld]
//[/IF]