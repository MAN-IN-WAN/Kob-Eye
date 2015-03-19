[IF [!Menu!]=oui]
	[MODULE Systeme/Structure/Gauche]
[/IF]

//Doit etre défini :
//[!ReloadUrl:=Espace_Abonnes!]
[!I_Error:=!]
//  LA GESTION D'ERREUR
[IF [!I_Valid!]=Inscription||[!P_Valid!]=Modifier]
	//On verifie les champs du formulaire
	[IF [!P_Valid!]!=Modifier&&[!I_Mail2!]!=[!I_Mail!]][!I_Mail2_Error:=1!][!I_Error:=1!][/IF]
	[IF [!P_Valid!]=]
		[OBJ Boutique|Client|Pers]
	[ELSE]
		[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1|tmsCreate|DESC][/STORPROC]
	[/IF]
	[STORPROC [!Pers::Proprietes!]|Prop]
		[METHOD Pers|Set]
			[PARAM][!Prop::Nom!][/PARAM]
			[PARAM][!I_[!Prop::Nom!]!][/PARAM]
		[/METHOD]
	[/STORPROC]
	[IF [!Form_Avatar_Upload!]!=]
		[METHOD Pers|Set]
			[PARAM]Avatar[/PARAM]
			[PARAM][!Form_Avatar_Upload!][/PARAM]
		[/METHOD]
	[/IF]
	[METHOD Pers|Set]
		[PARAM]Actif[/PARAM]
		[PARAM]1[/PARAM]
	[/METHOD]
	[IF [!Modif!]!=]
		[METHOD Pers|Save][PARAM]1[/PARAM][/METHOD]
		[REDIRECT]Espace_Abonnes[/REDIRECT]
	[ELSE]
		[IF [!Pers::Verify(1)!]&&[!I_Error!]=]
			[!InscriptionOK:=True!]
			[METHOD Pers|Save][PARAM]1[/PARAM][/METHOD]
			[IF [!I_Newsletter!]=True]
				<div style="display:none">
					[MODULE Newsletter/AjouterContactFlash?EMAIL=[!Pers::Mail!]]
				</div>
			[/IF]
			[CONNEXION [!Pers::Pseudonyme!]|[!Pers::getPass()!]]
			[REDIRECT]Espace_Abonnes[/REDIRECT]
		[ELSE]    
			[BLOC Erreur|Erreur d'inscription]
				<ul>
					[STORPROC [!Pers::Error!]|E]
						[!I_[!E::Prop!]_Error:=1!]
						<li>[!E::Message!]</li>
					[/STORPROC]
					[IF [!I_Error!]!=]
						[!I_Mail_Error:=1!]
						<li>Les adresses mail ne correspondent pas.</li>
					[/IF]
				</ul>
			[/BLOC]
		[/IF]
	[/IF]
[/IF]
[IF [!Menu!]=]
[ELSE]
	<div class="colonnecentre">
		<div class="RedactionnelFond">
[/IF]
[IF [!C_Titre!]!=]<div class="Categorie" style="padding-top:5px"><h1>[!C_Titre!]</h1></div>[/IF]
<form action="" method="POST" >
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Adresse Mail <span style="color:#ff0000">*</span>
		</label>
		<input type="text" size="36" name="I_Mail" value="[IF [!Reset!]=][!I_Mail!][/IF]"  [IF [!I_Mail_Error!]]class="Error"[ELSE]class="LigneForm" [/IF]/>
	</div>
	[IF [!Modif!]=]
		<div class="LigneForm ">
			<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
				Confirmation Adresse Mail <span style="color:#ff0000">*</span>
			</label>
			<input type="text" size="36" name="I_Mail2" value="[IF [!Reset!]=][!I_Mail2!][/IF]"  [IF [!I_Mail2_Error!]]class="Error"[ELSE]class="LigneForm" [/IF]/>
		</div>
	[/IF]
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Pseudonyme<span style="color:#ff0000">*</span>
		</label>
		<input type="text" size="36" name="I_Pseudonyme" value="[IF [!Reset!]=][!I_Pseudonyme!][/IF]" [IF [!I_Pseudonyme_Error!]]class="Error"[ELSE]Class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Avatar
		</label>
		<label>[IF [!I_Avatar!]!][!I_Avatar!][/IF]</label>
		<input type="file" size="40" name="Form_Avatar_Upload" value=""/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Societe 
		</label>
		<input type="text" size="36" name="I_Societe" value="[IF [!Reset!]=][!I_Societe!][/IF]" style="text-transform:uppercase;" [IF [!I_Societe_Error!]]class="Error"[ELSE]Class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>Civilite</label>
		<select name="I_Civilite" style="text-transform:uppercase;"  [IF [!I_Civilite_Error!]]class="Error"[ELSE]Class="selectfin" [/IF]/>
			<option [IF [!I_Civilite!]=Mademoiselle]selected[/IF]>Mademoiselle</option>
			<option [IF [!I_Civilite!]=Madame]selected[/IF]>Madame</option>
			<option [IF [!I_Civilite!]=Monsieur]selected[/IF]>Monsieur</option>
		</select>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Nom <span style="color:#ff0000">*</span>
		</label>
		<input type="text" size="36" name="I_Nom" value="[IF [!Reset!]=][!I_Nom!][/IF]" style="text-transform:uppercase;" [IF [!I_Nom_Error!]]class="Error"[ELSE]Class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>Pr&eacute;nom <span style="color:#ff0000">*</span></label>
		<input type="text"  size="36" name="I_Prenom" value="[IF [!Reset!]=][!I_Prenom!][/IF]" [IF [!I_Prenom_Error!]]class="Error"[ELSE]Class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Date de naissance [IF [!AddUser!]=True]<span style="color:#ff0000">*</span>[/IF]
		</label>
		<input type="text" size="30" name="I_DateNaissance" value="[IF [!Reset!]=][!I_DateNaissance!][/IF]"[IF [!I_DateNaissance_Error!]]class="Error"[ELSE]Class="LigneForm" [/IF]/>
	</div>
	<p style="font-style:italic;text-align:center;color:#000000;[!TextProperties!]">
		Date de naissance au format jj/mm/aaaa
	</p>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>
			Adresse[IF [!AddUser!]=True]<span style="color:#ff0000">*</span>[/IF]
		</label>
		<textarea name="I_Adresse" cols="40" rows="4" [IF [!I_Adresse_Error!]]class="Error"[ELSE]class="LigneForm"[/IF]>[IF [!Reset!]=][!I_Adresse!][/IF]</textarea>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>Code postal [IF [!AddUser!]=True]<span style="color:#ff0000">*</span>[/IF]</label>
		<input type="text" size="10" name="I_CodPos" value="[IF [!Reset!]=][!I_CodPos!][/IF]"  [IF [!I_CodPos_Error!]]class="Error"[ELSE]Class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>Ville [IF [!AddUser!]=True]<span style="color:#ff0000">*</span>[/IF]</label>
		<input type="text" size="36" name="I_Ville" value="[IF [!Reset!]=][!I_Ville!][/IF]" [IF [!I_Ville_Error!]]class="Error"[ELSE]class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>Pays [IF [!AddUser!]=True]<span style="color:#ff0000">*</span>[/IF]</label>
		<select name="I_Pays" style="text-transform:uppercase;"  Class="selectfin" />
			<option [IF [!I_Pays!]=FRANCE]selected[/IF]>FRANCE</option>
		</select>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;" [/IF]>T&eacute;l&eacute;phone [IF [!AddUser!]=True][/IF]</label>
		<input type="text" size="36" name="I_Tel" value="[IF [!Reset!]=][!I_Tel!][/IF]" [IF [!I_Tel_Error!]]class="Error"[ELSE]class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;" [/IF]>Fax [IF [!AddUser!]=True][/IF]</label>
		<input type="text" size="36" name="I_Fax" value="[IF [!Reset!]=][!I_Fax!][/IF]" [IF [!I_Fax_Error!]]class="Error"[ELSE]class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm ">
		<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;" [/IF]>Portable [IF [!AddUser!]=True][/IF]</label>
		<input type="text" size="36" name="I_Portable" value="[IF [!Reset!]=][!I_Portable!][/IF]"[IF [!I_Portable_Error!]]class="Error"[ELSE]class="LigneForm" [/IF]/>
	</div>
	<div class="LigneForm" style="[!TextProperties!]">
		<label>Les champs marqu&eacute;s</label><span style="color:#ff0000">*</span> sont obligatoires.
	</div>
	[IF [!AddUser!]=True&&[!SI!]!=True]
		<div class="LigneForm ">
			<label>Je souhaite m'inscrire &agrave; la newsletter</label>
			<input type="checkbox" name="I_Newsletter" value="True" checked="checked"/>
		</div>
	[/IF]
	[IF [!AddUser!]==True]
		<div class="LigneForm ">
			<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>&nbsp;</label>
			<input type="hidden" name="I_Valid" value="Inscription"/>
			<div class="btnRouge">
				<div class="btnRougeGauche"></div>
				<div class="btnRougeCentre">
					<input type="submit" name="I_Valid" value="Inscription" class="btnRougeCentre" />
				</div>
				<div class="btnRougeDroite"></div>
			</div>
		</div>
	[ELSE]
		[IF [!Modif!]!=]
			<input type="hidden" name="Modif" value="[!Modif!]"/>
			<div class="LigneForm ">
				<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>&nbsp;</label>
				<input type="submit" name="P_Valid" value="Modifier"/>
			</div>
		<!--	[IF [!TrueUser!]!=True]
				<div class="LigneForm ">
					<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>&nbsp;</label>
					<input type="submit" name="P_Valid" value="Supprimer"/>
				</div>		
			[/IF]-->
		[ELSE]
			<div class="LigneForm ">
				<label [IF [!AddUser!]!=True&&[!SI!]!=True] style="width:100px;"[/IF]>&nbsp;</label>
				<div class="btnRouge">
					<div class="btnRougeGauche"></div>
					<div class="btnRougeCentre">
						<input type="submit" name="I_Valid" value="Ajouter" class="btnRougeCentre" />
					</div>
					<div class="btnRougeDroite"></div>
				</div>
			</div>
		[/IF]
	[/IF]
</form>
[IF [!Menu!]=]
[ELSE]
	</div></div>
	[MODULE Systeme/Structure/Droite]
[/IF]

