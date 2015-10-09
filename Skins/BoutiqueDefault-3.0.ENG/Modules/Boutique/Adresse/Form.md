[!Target:=[!Lien!]?Save=1!]
[IF [!Id!]!=][!Target+=&Id=[!Id!]!][/IF]

<form method="post" action="/[!Target!]&Type=[!Type!]" class="form-horizontal">
<!--	<div class="LigneForm">
		<label>Nom Societe </label>
		<input type="text"  name="I_Societe" value="[IF [!Reset!]=][!I_Societe!][/IF]" tabindex="50"  [IF [!I_Societe_Error!]]class="Error"[/IF]/>
	</div>
	<div class="LigneForm">
		<label>N° Siret </label>
		<input type="text"  name="I_Siret" value="[IF [!Reset!]=][!I_Siret!][/IF]" style="text-transform:uppercase;"  tabindex="60" [IF [!I_Siret_Error!]]class="Error"[/IF] />
	</div>-->
	<div class="LigneForm">
		<label>Civilité <span class="obligatoire">*</span></label>
		<select name="I_Civilite">
			<option value="">- Veuillez sélectionner -</option>
			<option value="Mademoiselle" [IF [!I_Civilite!]=Mademoiselle||[!Adr::Civilite!]=Mademoiselle] selected="selected" [/IF]>Mademoiselle</option>
			<option value="Madame" [IF [!I_Civilite!]=Madame||[!Adr::Civilite!]=Madame] selected="selected" [/IF]>Madame</option>
			<option value="Monsieur" [IF [!I_Civilite!]=Monsieur||[!Adr::Civilite!]=Monsieur] selected="selected" [/IF]>Monsieur</option>
		</select>
	</div>
	<div class="LigneForm">
		<label>Nom <span class="obligatoire">*</span></label>
		<input type="text" name="I_Nom" value="[IF [!I_Nom!]][!I_Nom!][ELSE][!Adr::Nom!][/IF]" style="text-transform:uppercase" />
	</div>
	<div class="LigneForm">
		<label>Prénom <span class="obligatoire">*</span></label>
		<input type="text" name="I_Prenom" value="[IF [!I_Prenom!]][!I_Prenom!][ELSE][!Adr::Prenom!][/IF]" />
	</div>
	<div class="LigneForm">
		<label>Adresse <span class="obligatoire">*</span></label>
		<textarea name="I_Adresse" cols="50" rows="4">[IF [!I_Adresse!]][!I_Adresse!][ELSE][!Adr::Adresse!][/IF]</textarea>
	</div>
	<div class="LigneForm">
		<label>Code postal <span class="obligatoire">*</span></label>
		<input type="text" name="I_CodePostal" value="[IF [!I_CodePostal!]][!I_CodePostal!][ELSE][!Adr::CodePostal!][/IF]" />
	</div>
	<div class="LigneForm">
		<label>Ville <span class="obligatoire">*</span></label>
		<input type="text" name="I_Ville" value="[IF [!I_Ville!]][!I_Ville!][ELSE][!Adr::Ville!][/IF]" />
	</div>
	<div class="LigneForm">
		<label>Pays </label>
		<select name="I_Pays" tabindex="16">
			[STORPROC Geographie/Pays|Pa|||Nom|ASC]
				<option value="[!Pa::Nom!]"  [IF [!I_Pays!]=&&[!Pa::Nom!]=France] selected="selected" [ELSE][IF [!I_Pays!]=[!Pa::Nom!]] selected="selected"[/IF][/IF]>[!Pa::Nom!] - [!Pa::Code!]</option>
			[/STORPROC]
		</select>
	</div>
	<div class="">

		<input type="hidden" name="I_Type" value="[!Type!]" />
		<div class="buttons">
			<input type="submit" name="I_Valid" class="btn btn-primary ValiderAd" value="Valider" />
			<input type="button" onclick="history.go(-1)" class="btn btn-warning RetourAd" value="Retour" />
		</div>
	</div>
	
</form>