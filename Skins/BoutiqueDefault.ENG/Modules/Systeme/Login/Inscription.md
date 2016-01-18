<form action="/[!Lien!]" method="post" enctype="multipart/form-data" name="form_inscription" class="form-horizontal" >


[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
[!AutoConnexion:=[!Mag::AutoClient!]!]
[IF [!I_Inscription!]!=]
	////////////////// Si déjà connecté on ne peut plus modifier son mail / idenfiant
	[IF [!Systeme::User::Public!]=0]
		[!I_Pseudonyme:=[!Systeme::User::Mail!]!]
		[!I_Mail:=[!Systeme::User::Mail!]!]
	[/IF]
	////////////////// On verifie les champs du formulaire
	[IF [!Utils::isMail([!I_Mail!])!]!=1][!I_Mail_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Nom!]=][!I_Nom_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Tel!]=][!I_Tel_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Prenom!]=][!I_Prenom_Error:=1!][!I_Error:=1!][/IF]
	//[IF [!I_DateNaissance!]=][!I_DateNaissance_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Adresse!]=][!I_Adresse_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_CodePostal!]=][!I_CodePostal_Error:=1!][!I_Error:=1!][/IF]
	[IF [!I_Ville!]=][!I_Ville_Error:=1!][!I_Error:=1!][/IF]
	[IF [!Systeme::User::Public!]=1]
		// Uniquement à la création
		[IF [!I_Pass!]=][!I_Pass_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Pass2!]!=[!I_Pass!]][!I_Pass2_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Mail2!]!=[!I_Mail!]][!I_Mail2_Error:=1!][!I_Error:=1!][/IF]
	[/IF]
	[IF [!ProParticulier!]=2]
		// Uniquement si PRO
		[IF [!I_Societe!]=][!I_Societe_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Siret!]=][!I_Siret_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_TVAIntraComm!]=][!I_TVAIntraComm_Error:=1!][!I_Error:=1!][/IF]
	[/IF]
	
	[IF [!hash!]]	
		[!VerifMD5:=[!Utils::md5([!Result!])!]!]
		[IF [!VerifMD5!]!=[!hash!]][!C_Code_Error:=1!][!I_Error:=1!][/IF]
	[/IF]

	//////////////// Les champs sont OK on procède à la création ou à l'update
	[IF [!I_Error!]=]
		///////////////// Deja connecté = Modification | Sinon = Création nouveau client
		[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1]
			[!ModeCreation:=0!]
			[NORESULT]
				[!ModeCreation:=1!]
				[OBJ Boutique|Client|Pers]
			[/NORESULT]
		[/STORPROC]
		///////////////// On remplit tous les champs
		[STORPROC [!Pers::Proprietes!]|Prop]
			[IF [!Prop::Nom!]!=UserId&&[!Prop::Nom!]!=ConnexionLe]
				[METHOD Pers|Set]
					[PARAM][!Prop::Nom!][/PARAM]
					[PARAM][!I_[!Prop::Nom!]!][/PARAM]
				[/METHOD]
			[/IF]
		[/STORPROC]
		[IF [!ModeCreation!]=0]
			[METHOD Pers|Set]
				[PARAM]ConnexionLe[/PARAM][PARAM][!TMS::Now!][/PARAM]
			[/METHOD]
		[/IF]
		///////////////// Mot de passe + Identifiant (uniquement en création)
		[IF [!ModeCreation!]=1]
			[METHOD Pers|Set]
				[PARAM]Pseudonyme[/PARAM]
				[PARAM][!I_Mail!][/PARAM]
			[/METHOD]
			[METHOD Pers|Set]
				[PARAM]Pass[/PARAM]
				[PARAM][!I_Pass!][/PARAM]
			[/METHOD]
			[METHOD Pers|Set]
				[PARAM]Actif[/PARAM]
				[PARAM][!AutoConnexion!][/PARAM]
			[/METHOD]

		[/IF]
		[IF [!Pers::Verify(1)!]||[!ModeCreation!]=0]
			////////////// Enregistrement
			[METHOD Pers|Set]
				[PARAM]Pass[/PARAM]
				[PARAM][!I_Pass!][/PARAM]
			[/METHOD]
			[METHOD Pers|Set]
				[PARAM]Pass[/PARAM]
				[PARAM][!I_Pass!][/PARAM]
			[/METHOD]

			[METHOD Pers|Save][PARAM]1[/PARAM][/METHOD]
			[IF [!ModeCreation!]=1]
				[MODULE Systeme/Mail/Inscription?Obj=[!Pers!]&Pass=[!I_Pass!]]
			[ELSE]
				[METHOD Pers|updateUser][/METHOD]
			[/IF]

			////////////// Inscription ou Désinscription Newsletter
                        [IF [!CurrentMagasin::InscriptionNewsletter!]]
                            // Inscription ou Modification
                            [STORPROC Newsletter/Contact/Email=[!I_Mail!]|Con]
                                    [NORESULT]
                                            [OBJ Newsletter|Contact|Con]
                                    [/NORESULT]
                            [/STORPROC]
                            [METHOD Con|Set]
                                    [PARAM]Email[/PARAM][PARAM][!I_Mail!][/PARAM]
                            [/METHOD]
                            [METHOD Con|Set]
                                    [PARAM]Nom[/PARAM][PARAM][!I_Nom!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]Prenom[/PARAM][PARAM][!I_Prenom!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]Adresse[/PARAM][PARAM][!I_Adresse!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]CodePostal[/PARAM][PARAM][!I_CodePostal!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]Ville[/PARAM][PARAM][!I_Ville!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]Telephone[/PARAM][PARAM][!I_Tel!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]Fax[/PARAM][PARAM][!I_Fax!][/PARAM]
                            [/METHOD]							
                            [METHOD Con|Set]
                                    [PARAM]Mobile[/PARAM][PARAM][!I_Portable!][/PARAM]
                            [/METHOD]
                            [IF [!ModeCreation!]=1]
                                    [METHOD Con|Set]
                                            [PARAM]Actif[/PARAM]
                                            [PARAM][!AutoConnexion!][/PARAM]
                                    [/METHOD]
                                    [IF [!I_Newsletter!]]
                                            // groupe de client inscrit newsletter
                                            [METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
                                    [ELSE]
                                            /groupe de clien non inscrit newsletter
                                            [METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/4[/PARAM][/METHOD]
                                    [/IF]
                            [ELSE]
                                    [IF [!I_Newsletter!]]
                                            [METHOD Con|Set]
                                                    [PARAM]Actif[/PARAM]
                                                    [PARAM][!AutoConnexion!][/PARAM]
                                            [/METHOD]
                                            [COUNT Newsletter/GroupeEnvoi/4/Contact/Email=[!I_Mail!]|NbGp]
                                            [IF [!NbGp!]][METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/4[/PARAM][/METHOD][/IF]
                                            [METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
            
                                    [ELSE]
                                            // Désinscription
                                            [STORPROC Newsletter/Contact/Email=[!I_Mail!]|TraceNewsletter]
                                                    [METHOD TraceNewsletter|Delete][/METHOD]
                                            [/STORPROC]
                                    [/IF]
                            [/IF]
                            [METHOD Con|Save][/METHOD]
                        [/IF]

			// Enregistrement première adresse LIVRAISON + FACTURATION (uniquement en création)
			[IF [!ModeCreation!]=1]
				[OBJ Boutique|Adresse|AdrPers]
				[METHOD AdrPers|Set]
					[PARAM]Civilite[/PARAM][PARAM][!I_Civilite!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]Nom[/PARAM][PARAM][!I_Nom!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]Prenom[/PARAM][PARAM][!I_Prenom!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]Adresse[/PARAM][PARAM][!I_Adresse!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]CodePostal[/PARAM][PARAM][!I_CodePostal!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]Ville[/PARAM][PARAM][!I_Ville!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]Pays[/PARAM][PARAM][!I_Pays!][/PARAM]
				[/METHOD]
				[METHOD AdrPers|Set]
					[PARAM]Type[/PARAM][PARAM]Livraison[/PARAM]
				[/METHOD]
				[METHOD AdrPers|AddParent][PARAM]Boutique/Client/[!Pers::Id!][/PARAM][/METHOD]
				[METHOD AdrPers|Save][/METHOD]
				[!AdrFact:=[!AdrPers::getClone()!]!]
				[METHOD AdrFact|Set]
					[PARAM]Type[/PARAM][PARAM]Facturation[/PARAM]
				[/METHOD]
				[METHOD AdrFact|AddParent][PARAM]Boutique/Client/[!Pers::Id!][/PARAM][/METHOD]
				[METHOD AdrFact|Save][/METHOD]
			[/IF]
			
			

			[IF [!ModeCreation!]=1&&[!AutoConnexion!]]
				[CONNEXION [!I_Mail!]|[!I_Pass!]]
				// Redirection
				[IF [!Redirect!]=][!Redirect:=[!Lien!]!][/IF]
				[REDIRECT][!Redirect!][/REDIRECT]
			[ELSE]
				<div class="alert alert-success">Votre compte a été créé avec succès, nos équipes vont vous rappeler très rapidement</div>
			[/IF]
			
		[ELSE]
                    <div class="alert alert-danger">
				<ul>
					[STORPROC [!Pers::Error!]|E]
						<li>[!E::Message!]</li>
                                                [!I_[!E::Prop!]_Error:=1!]
					[/STORPROC]
				</ul>
                    </div>
		[/IF]
	[ELSE]
		[IF [!I_Error!]]
                    <div class="alert alert-danger">
                        <ul>
                                [IF [!I_Nom_Error!]]<li>Le nom est obligatoire</li>[/IF]
                                [IF [!I_Prenom_Error!]]<li>Le prénom est obligatoire</li>[/IF]
                                //[IF [!I_DateNaissance_Error!]]<li>La date de naissance est obligatoire</li>[/IF]
                                [IF [!I_Adresse_Error!]]<li>l'adresse est obligatoire</li>[/IF]
                                [IF [!I_CodePostal_Error!]]<li>Le code postal est obligatoire</li>[/IF]
                                [IF [!I_Ville_Error!]]<li>La ville est obligatoire</li>[/IF]
                                [IF [!I_Tel_Error!]]<li>Le téléphone est obligatoire</li>[/IF]
                                [IF [!I_Mail_Error!]]<li>L'adresse mail est incorrecte</li>[/IF]
                                [IF [!I_Mail2_Error!]]<li>Les adresses mails ne correspondent pas</li>[/IF]
                                [IF [!I_Pass_Error!]]<li>Le mot de passe ne peut pas être vide</li>[/IF]
                                [IF [!I_Pass2_Error!]]<li>Les mots de passe ne correspondent pas</li>[/IF]
                                [IF [!C_Code_Error!]]<li>Opération fausse</li>[/IF]
                                //[IF [!I_Societe_Error!]=]<li>Nom de votre société ne peut pas être vide</li>[/IF]
                                //[IF [!I_Siret_Error!]=]<li>Siret de votre société ne peut pas être vide</li>[/IF]
                                //[IF [!I_TVAIntraComm_Error!]=]<li>TvaIntracomm de votre société ne peut pas être vide</li>[/IF]



                        </ul>
                    </div>
		[ELSE]
                    <div class="alert alert-danger">
                        <ul>
                            [STORPROC [!Pers::Error!]|E]
                                    <li>Erreur d'enregistrement : [!E::Champ!] : [!E::Message!]</li>
                            [/STORPROC]
                        </ul>
                    </div>
		[/IF]
	[/IF]
[ELSE]
	///////////////////// En cas de modification du compte
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|ClientEdit|0|1]

		// Tous les champs client
		[STORPROC [!ClientEdit::Proprietes!]|Prop]
			[IF [!Prop::Nom!]!=UserId&&[!Prop::Nom!]!=ConnexionLe]
				[!I_[!Prop::Nom!]:=[!ClientEdit::[!Prop::Nom!]!]!]
			[/IF]
		[/STORPROC]

		// Date de naissance
		[!I_DateNaissance:=[!Utils::getDate(d/m/Y,[!I_DateNaissance!])!]!]

		// Newsletter
		[STORPROC Newsletter/Contact/Email=[!I_Mail!]|Test]
			[!I_Newsletter:=1!]
		[/STORPROC]

	[/STORPROC]
[/IF]
	<h3 class="ssTitre">Identifiants</h3>
	<div class="control-group [IF [!I_Mail_Error!]]error[/IF]">
		<label class="control-label" for="I_Mail">Votre e-mail <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="text"  name="I_Mail" value="[IF [!Reset!]=][!I_Mail!][/IF]" tabindex="10" [IF [!Systeme::User::Public!]=0] readonly[/IF] />				
		</div>
	</div>
	[IF [!Systeme::User::Public!]=1]
	<div class="control-group [IF [!I_Mail2_Error!]]error[/IF]">
		<label class="control-label" for="I_Mail2">Confirmer e-mail <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="text" name="I_Mail2" value="[IF [!Systeme::User::Public!]=1][IF [!Reset!]=][!I_Mail2!][/IF][ELSE][!I_Mail!][/IF]" tabindex="20"/>
		</div>
	</div>[/IF]
	<div class="control-group [IF [!I_Pass_Error!]]error[/IF]">
		<label class="control-label" for="I_Pass">Votre mot de passe <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="password" name="I_Pass" value="[IF [!Reset!]=][!I_Pass!][/IF]"  tabindex="30" />
		</div>
	</div>
	<div class="control-group [IF [!I_Pass2_Error!]]error[/IF]">
		<label class="control-label" for="I_Pass2">Confirmer mot de passe <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="password"  name="I_Pass2" value="[IF [!Reset!]=][!I_Pass2!][/IF]"  tabindex="40" />
		</div>
	</div>
	<hr class="CreaCompte">
        <h3 class="ssTitre">Coordonnees</h3>
<!--	<div class="control-group [IF [!I_Societe_Error!]]error[/IF]">
		<label class="control-label" for="I_Societe">Nom Societe </label>
		<div class="controls">
			<input type="text"  name="I_Societe" value="[IF [!Reset!]=][!I_Societe!][/IF]" tabindex="50" />
		</div>
	</div>
	<div class="control-group [IF [!I_Siret_Error!]]error[/IF]">
		<label class="control-label" for="I_Siret">N° Siret </label>
		<div class="controls">
			<input type="text"  name="I_Siret" value="[IF [!Reset!]=][!I_Siret!][/IF]" style="text-transform:uppercase;"  tabindex="60" />
		</div>
	</div>
-->
	<!--<div class="control-group" >
		<label class="control-label" for="I_TVAIntraComm">N° TVA IntraComm</label>
		<div class="controls">
			<input type="text"  name="I_TVAIntraComm" value="[IF [!Reset!]=][!I_TVAIntraComm!][/IF]" style="text-transform:uppercase;" tabindex="70"  [IF [!I_TVAIntraComm_Error!]]class="Error"[/IF] /></label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="I_Fax">Fax</label>
		<div class="controls">
			<input type="text"  name="I_Fax" value="[IF [!Reset!]=][!I_Fax!][/IF]" tabindex="80"  [IF [!I_Fax_Error!]]class="Error"[/IF] />
		</div>
	</div>-->


	<div class="control-group [IF [!I_Civilite_Error!]]error[/IF]">
		<label class="control-label" for="I_Civilite">Civilite <span class="obligatoire">*</span></label>
		<div class="controls">
			<select name="I_Civilite" tabindex="90"  class="selectfin">
				<option value="">- Veuillez sélectionner -</option>
				<option value="Mademoiselle" [IF [!I_Civilite!]=Mademoiselle] selected="selected"[/IF]>Mademoiselle</option>
				<option value="Madame" [IF [!I_Civilite!]=Madame] selected="selected"[/IF]>Madame</option>
				<option value="Monsieur" [IF [!I_Civilite!]=Monsieur] selected="selected"[/IF]>Monsieur</option>
			</select>				
		</div>
	</div>
	<div class="control-group [IF [!I_Nom_Error!]]error[/IF]">
		<label class="control-label" for="I_Nom">Nom <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="text"  name="I_Nom" value="[IF [!Reset!]=][!I_Nom!][/IF]" tabindex="100"  style="text-transform:uppercase;"/>			
		</div>
	</div>
	<div class="control-group [IF [!I_Prenom_Error!]]error[/IF]">
		<label class="control-label" for="I_Prenom">Pr&eacute;nom <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="text" name="I_Prenom" value="[IF [!Reset!]=][!I_Prenom!][/IF]" tabindex="110" />
		</div>
	</div>
	<div class="control-group [IF [!I_DateNaissance_Error!]]error[/IF]">
		<label class="control-label" for="I_DateNaissance">Date de naissance <span class="obligatoire">*</span><br /> (jj/mm/aaaa)</label>
		<div class="controls">
			<input type="text" name="I_DateNaissance" value="[IF [!Reset!]=][!I_DateNaissance!][/IF]" tabindex="120"/>
		</div>
	</div>
	<div class="control-group [IF [!I_Tel_Error!]]error[/IF]">
		<label class="control-label" for="I_Tel">Téléphone <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="text"  name="I_Tel" value="[IF [!Reset!]=][!I_Tel!][/IF]" tabindex="130" />
		</div>
	</div>
	<div class="control-group [IF [!I_Portable_Error!]]error[/IF]">
		<label  class="control-label" for="I_Portable">Portable [IF [!AddUser!]=True][/IF]</label>
		<div class="controls">
			<input type="text"  name="I_Portable" value="[IF [!Reset!]=][!I_Portable!][/IF]" tabindex="140" />
		</div>
	</div>
	<div class="control-group [IF [!I_Adresse_Error!]]error[/IF]">
		<label class="control-label" for="I_Adresse">Adresse <span class="obligatoire">*</span></label>
		<div class="controls">
			<textarea name="I_Adresse" cols="40" row-fluids="4"  tabindex="150">[IF [!Reset!]=][!I_Adresse!][/IF]</textarea>
		</div>
	</div>
	<div class="control-group [IF [!I_CodePostal_Error!]]error[/IF]">
		<label class="control-label" for="I_CodePostal">Code postal <span class="obligatoire">*</span> </label>
		<div class="controls">
			<input type="text" name="I_CodePostal" value="[IF [!Reset!]=][!I_CodePostal!][/IF]" tabindex="160"/>
		</div>
	</div>
	<div class="control-group [IF [!I_Ville_Error!]]error[/IF]">
		<label class="control-label" for="I_Ville">Ville <span class="obligatoire">*</span></label>
		<div class="controls">
			<input type="text"  name="I_Ville" value="[IF [!Reset!]=][!I_Ville!][/IF]" tabindex="170"/>
		</div>
	</div>
	<div class="control-group [IF [!I_Pays_Error!]]error[/IF]">
		<label class="control-label" for="I_Pays">Pays <span class="obligatoire">*</span></label>
		<div class="controls">
			<select name="I_Pays" tabindex="180" class="Pays">
				<option value="France"  [IF [!I_Pays!]=] selected="selected" [ELSE][IF [!I_Pays!]=France] selected="selected"[/IF][/IF]>France</option>
                                    [STORPROC Geographie/Pays|Pa|||Nom|ASC]
					//<option value="[!Pa::Nom!]"  [IF [!I_Pays!]=[!Pa::Nom!]] selected="selected"[/IF]>[!Pa::Code!] - [!Pa::Nom!]</option>
					<option value="[!Pa::Nom!]"  [IF [!I_Pays!]=&&[!Pa::Nom!]=France] selected="selected" [ELSE][IF [!I_Pays!]=[!Pa::Nom!]] selected="selected"[/IF][/IF]>[!Pa::Code!] - [!Pa::Nom!]</option>
				[/STORPROC]
			</select>		
		</div>
	</div>
	<div class="control-group [IF [!C_Code_Error!]]error[/IF]">
		<label  class="control-label" >Merci de résoudre cette opération*</label>
		<div class="controls">
			[IF [!Nb1!]=]
				[!Nb1:=[!Utils::random(5)!]!]
				[!Nb1+=4!]
				[!Nb2:=[!Utils::random(4)!]!]
				[IF [!Utils::random(1)!]][!Op:=-!][ELSE][!Op:=+!][/IF]
				[!Tot:=[!Nb1!]!]
				[IF [!Op!]=-][!Tot-=[!Nb2!]!][ELSE][!Tot+=[!Nb2!]!][/IF]
				[!hash:=[!Utils::md5([!Tot!])!]!]
			[/IF]
			<div class="pull-left"><input type="text" readonly="readonly"  name="Nb1"    value="[!Nb1!]" size="5" class="Op" /></div>
			<div class="pull-left"><input type="text"                      name="Op"     value="[!Op!]"  size="5" class="Op"  /></div>
			<div class="pull-left"><input type="text" readonly="readonly"  name="Nb2"    value="[!Nb2!]" size="5" class="Op" /> </div>
			<div class="pull-left"> = </div>
			<div class="pull-left"><input type="text"tabindex="190" name="Result" value="[!Result!]" size="5" class="Op" style="margin-left:5px;"[IF [!C_Calc_Error!]] class="Error" [/IF] /></div>
			<input type="hidden" name="hash" value="[!hash!]" />
		</div>
	</div>
        <p class="LesChamps"><span class="obligatoire">*</span>Champs obligatoires</p>
        <input type="hidden" name="SendContact" value="1" />
            
        <div class="control-group">
		<input type="submit" tabindex="210"  class="button btn-large btn-grisDroite ConnexionConnexion [IF [!Systeme::User::Public!]=1]CreerCompte[ELSE]MettreAJour[/IF]" name="I_Inscription" value="Valider"  />
	</div>

</form>
	
