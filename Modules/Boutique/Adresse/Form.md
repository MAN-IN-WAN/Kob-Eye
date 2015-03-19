[!Target:=[!Lien!]?Save=1!]
[IF [!Id!]!=][!Target+=&Id=[!Id!]!][/IF]

<form method="post" action="/[!Target!]&Type=[!Type!]">
	<fieldset>
		<legend>Coordonnées de cette adresse</legend>
		<div class="LigneForm">
			<label>Civilité
			<select name="I_Civilite">
				<option value="">- Veuillez sélectionner -</option>
				<option value="Mademoiselle" [IF [!I_Civilite!]=Mademoiselle||[!Adr::Civilite!]=Mademoiselle] selected="selected" [/IF]>Mademoiselle</option>
				<option value="Madame" [IF [!I_Civilite!]=Madame||[!Adr::Civilite!]=Madame] selected="selected" [/IF]>Madame</option>
				<option value="Monsieur" [IF [!I_Civilite!]=Monsieur||[!Adr::Civilite!]=Monsieur] selected="selected" [/IF]>Monsieur</option>
			</select></label>
		</div>
		<div class="LigneForm">
			<label>Nom
			<input type="text" name="I_Nom" value="[IF [!I_Nom!]][!I_Nom!][ELSE][!Adr::Nom!][/IF]" style="text-transform:uppercase" /></label>
		</div>
		<div class="LigneForm">
			<label>Prénom
			<input type="text" name="I_Prenom" value="[IF [!I_Prenom!]][!I_Prenom!][ELSE][!Adr::Prenom!][/IF]" /></label>
		</div>
		<div class="LigneForm">
			<label>Adresse
			<textarea name="I_Adresse" cols="50" rows="4">[IF [!I_Adresse!]][!I_Adresse!][ELSE][!Adr::Adresse!][/IF]</textarea></label>
		</div>
		<div class="LigneForm">
			<label>Code postal
			<input type="text" name="I_CodePostal" value="[IF [!I_CodePostal!]][!I_CodePostal!][ELSE][!Adr::CodePostal!][/IF]" /></label>
		</div>
		<div class="LigneForm">
			<label>Ville
			<input type="text" name="I_Ville" value="[IF [!I_Ville!]][!I_Ville!][ELSE][!Adr::Ville!][/IF]" /></label>
		</div>
		<div class="LigneForm">
			<label>Pays 
			<select name="I_Pays" tabindex="16">
				[STORPROC Geographie/Pays/Nom=France|Pa|||Nom|ASC]
					<option value="[!Pa::Nom!]"  [IF [!I_Pays!]=[!Pa::Nom!]] selected="selected"[/IF]>[!Pa::Code!] - [!Pa::Nom!]</option>
				[/STORPROC]
			</select></label>
		</div>
		<div class="LigneForm">
	
			<input type="hidden" name="I_Type" value="[!Type!]" />
			<div class="buttons">
				<input type="submit" name="I_Valid" class="Valider" value="Valider" />
				<input type="button" onclick="history.go(-1)" class="Retour" value="Retour" />
			</div>
		</div>
	</fieldset>
</form>