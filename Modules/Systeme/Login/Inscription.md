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
	[IF [!I_DateNaissance!]=][!I_DateNaissance_Error:=1!][!I_Error:=1!][/IF]
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
				[PARAM]1[/PARAM]
			[/METHOD]

		[/IF]
		[IF [!Pers::Verify(1)!]||[!ModeCreation!]=0]
			////////////// Enregistrement
			[METHOD Pers|Save][PARAM]1[/PARAM][/METHOD]
			[IF [!ModeCreation!]=1]
				[MODULE Systeme/Mail/Inscription?Obj=[!Pers!]&Pass=[!I_Pass!]]
			[ELSE]
				[METHOD Pers|updateUser][/METHOD]
			[/IF]

			////////////// Inscription ou Désinscription Newsletter
			[IF [!I_Newsletter!]]
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
				[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
				[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/1[/PARAM][/METHOD]
				[METHOD Con|Save][/METHOD]
			[ELSE]
				// Désinscription
				[STORPROC Newsletter/Contact/Email=[!I_Mail!]|TraceNewsletter]
					[METHOD TraceNewsletter|Delete][/METHOD]
				[/STORPROC]
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

			[IF [!ModeCreation!]=1]
				[CONNEXION [!I_Mail!]|[!I_Pass!]]
			[/IF]
			
			// Redirection
			[IF [!Redirect!]=][!Redirect:=[!Lien!]!][/IF]
			[REDIRECT][!Redirect!][/REDIRECT]
		[ELSE]
			[BLOC Erreur|Liste des erreurs]
				<ul>
					<li>Cette adresse e-mail est déjà utilisée !</li>
					[STORPROC [!Pers::Error!]|E]
						<li>[!E::Champ!] : [!E::Message!]</li>
					[/STORPROC]
				</ul>
			[/BLOC]
		[/IF]
	[ELSE]
		[IF [!I_Error!]]
			[BLOC Erreur|Liste des erreurs]
				<ul>
					[IF [!I_Nom_Error!]]<li>Le nom est obligatoire</li>[/IF]
					[IF [!I_Prenom_Error!]]<li>Le prénom est obligatoire</li>[/IF]
					[IF [!I_DateNaissance_Error!]]<li>La date de naissance est obligatoire</li>[/IF]
					[IF [!I_Adresse_Error!]]<li>l'adresse est obligatoire</li>[/IF]
					[IF [!I_CodePostal_Error!]]<li>Le code postal est obligatoire</li>[/IF]
					[IF [!I_Ville_Error!]]<li>La ville est obligatoire</li>[/IF]
					[IF [!I_Tel_Error!]]<li>Le téléphone est obligatoire</li>[/IF]
					[IF [!I_Mail_Error!]]<li>L'adresse mail est incorrecte</li>[/IF]
					[IF [!I_Mail2_Error!]]<li>Les adresses mails ne correspondent pas</li>[/IF]
					[IF [!I_Pass_Error!]]<li>Le mot de passe ne peut pas être vide</li>[/IF]
					[IF [!I_Pass2_Error!]]<li>Les mots de passe ne correspondent pas</li>[/IF]
				</ul>
			[/BLOC]
		[ELSE]
			[BLOC Erreur|Liste des erreurs]
				<ul>
					[STORPROC [!Pers::Error!]|E]
						<li>[!E::Champ!] : [!E::Message!]</li>
					[/STORPROC]
				</ul>
			[/BLOC]
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
<form action="/[!Lien!]" method="post" enctype="multipart/form-data" name="form_inscription">
	<div class="ColonneCreationCompteBord">
		<h2>Je cr&eacute;e mon compte</h2>
		<div class="Panel">
			<div class="Radio">
				<input type="radio" [IF [!I_Siret!]=||[!ProParticulier!]=1] checked="checked"[/IF] style="" name="ProParticulier" value="1"  onclick="$('Professionnel').tween('display','none');" />
				<label>Je suis un particulier</label>
			</div>
			&nbsp;&nbsp;&nbsp;
			<div class="Radio">
				<input type="radio" [IF [!I_Siret!]!=||[!ProParticulier!]=2] checked="checked"  [/IF] name="ProParticulier" value="2" onclick="$('Professionnel').tween('display','block');" />
				<label>Je suis un professionnel</label>
			</div>
		</div>
		<h3>Identifiants</h3>
		<div class="Panel Border" >
			<div class="LigneForm">
				<label>Votre e-mail <span class="obligatoire">*</span></label>
				<input type="text"  name="I_Mail" value="[IF [!Reset!]=][!I_Mail!][/IF]" tabindex="1" [IF [!I_Mail_Error!]]class="Error"[/IF]/>				
			</div>
			<div class="LigneForm">
				<label>Confirmer e-mail <span class="obligatoire">*</span></label>
				<input type="text" name="I_Mail2" value="[IF [!Systeme::User::Public!]=1][IF [!Reset!]=][!I_Mail2!][/IF][ELSE][!I_Mail!][/IF]" tabindex="2"  [IF [!I_Mail2_Error!]]class="Error"[/IF]/>
			</div>
			<div class="LigneForm">
				<label>Votre mot de passe <span class="obligatoire">*</span></label>
				<input type="password" name="I_Pass" value="[IF [!Reset!]=][!I_Pass!][/IF]"  tabindex="3"  [IF [!I_Pass_Error!]]class="Error"[/IF]/>
			</div>
			<div class="LigneForm">
				<label>Confirmer MDP <span class="obligatoire">*</span></label>
				<input type="password"  name="I_Pass2" value="[IF [!Reset!]=][!I_Pass2!][/IF]"  tabindex="4"  [IF [!I_Pass2_Error!]]class="Error"[/IF]/>
			</div>
		</div>
		<div id="Professionnel">
			<h3>Société</h3>
			<div class="Panel Border">
				<div class="LigneForm">
					<label>Societe</label>
					<input type="text"  name="I_Societe" value="[IF [!Reset!]=][!I_Societe!][/IF]" tabindex="5"  [IF [!I_Societe_Error!]]class="Error"[/IF]/>
				</div>
				<div class="LigneForm">
					<label>N° Siret</label>
					<input type="text"  name="I_Siret" value="[IF [!Reset!]=][!I_Siret!][/IF]" style="text-transform:uppercase;"  tabindex="6" [IF [!I_Siret_Error!]]class="Error"[/IF] />
				</div>
				<div class="LigneForm">
					<label>N° TVA IntraComm</label>
					<input type="text"  name="I_TVAIntraComm" value="[IF [!Reset!]=][!I_TVAIntraComm!][/IF]" style="text-transform:uppercase;" tabindex="7"  [IF [!I_TVAIntraComm_Error!]]class="Error"[/IF] /></label>
				</div>		
				<div class="LigneForm">
					<label>Fax</label>
					<input type="text"  name="I_Fax" value="[IF [!Reset!]=][!I_Fax!][/IF]" tabindex="8"  [IF [!I_Fax_Error!]]class="Error"[/IF] />
				</div>		
			</div>
		</div>
		<h3>Coordonnees</h3>
		<div class="Panel Border">
			<div class="LigneForm">
				<label>Civilite <span class="obligatoire">*</span></label>
				<select name="I_Civilite" tabindex="9"  class="[IF [!I_Civilite_Error!]]Error[ELSE]selectfin[/IF]">
					<option value="">- Veuillez sélectionner -</option>
					<option value="Mademoiselle" [IF [!I_Civilite!]=Mademoiselle] selected="selected"[/IF]>Mademoiselle</option>
					<option value="Madame" [IF [!I_Civilite!]=Madame] selected="selected"[/IF]>Madame</option>
					<option value="Monsieur" [IF [!I_Civilite!]=Monsieur] selected="selected"[/IF]>Monsieur</option>
				</select>
			</div>
			<div class="LigneForm">
				<label>Pr&eacute;nom <span class="obligatoire">*</span></label>
				<input type="text" name="I_Prenom" value="[IF [!Reset!]=][!I_Prenom!][/IF]" tabindex="11"  [IF [!I_Prenom_Error!]]class="Error"[ELSE][/IF] />
			</div>
	
			<div class="LigneForm">
				<label>Nom <span class="obligatoire">*</span></label>
				<input type="text"  name="I_Nom" value="[IF [!Reset!]=][!I_Nom!][/IF]" tabindex="10"  style="text-transform:uppercase;" [IF [!I_Nom_Error!]]class="Error"[ELSE][/IF]/>
			</div>
			<div class="LigneForm">
				<label>Date de naissance <span class="obligatoire">*</span><br /> (Date au format jj/mm/aaaa)</label>
				<input type="text" name="I_DateNaissance" value="[IF [!Reset!]=][!I_DateNaissance!][/IF]" tabindex="12"  [IF [!I_DateNaissance_Error!]]class="Error"[ELSE][/IF]/>
			</div>
	
			<div class="LigneForm">
				<label>Téléphone <span class="obligatoire">*</span></label>
				<input type="text"  name="I_Tel" value="[IF [!Reset!]=][!I_Tel!][/IF]" tabindex="17"  [IF [!I_Tel_Error!]]class="Error"[ELSE][/IF]/>
			</div>
			<div class="LigneForm">
				<label  >Portable [IF [!AddUser!]=True][/IF]</label>
				<input type="text"  name="I_Portable" value="[IF [!Reset!]=][!I_Portable!][/IF]" tabindex="18" [IF [!I_Portable_Error!]]class="Error"[ELSE][/IF] />
			</div>
	
			<div class="LigneForm">
				<label>Adresse <span class="obligatoire">*</span></label>
				<textarea name="I_Adresse" cols="40" rows="4"  tabindex="13"  [IF [!I_Adresse_Error!]=1]class="Error"[ELSE][/IF]>[IF [!Reset!]=][!I_Adresse!][/IF]</textarea>
			</div>
			<div class="LigneForm">
				<label>Code postal <span class="obligatoire">*</span> </label>
				<input type="text" name="I_CodePostal" value="[IF [!Reset!]=][!I_CodePostal!][/IF]" tabindex="14"  [IF [!I_CodePostal_Error!]]class="Error"[/IF]/>
			</div>
			<div class="LigneForm">
				<label>Ville <span class="obligatoire">*</span></label>
				<input type="text"  name="I_Ville" value="[IF [!Reset!]=][!I_Ville!][/IF]" tabindex="15"  [IF [!I_Ville_Error!]]class="Error"[ELSE][/IF]/>
			</div>
			<div class="LigneForm">
				<label>Pays <span class="obligatoire">*</span></label>
				<select name="I_Pays" tabindex="16">
					[STORPROC Geographie/Pays/Nom=France|Pa|||Nom|ASC]
						<option value="[!Pa::Nom!]"  [IF [!I_Pays!]=[!Pa::Nom!]] selected="selected"[/IF]>[!Pa::Code!] - [!Pa::Nom!]</option>
					[/STORPROC]
				</select>
			</div>
		</div>
		<h3>Newsletter</h3>
		<div class="Panel">
			<div class="LigneForm Long">
				<input style="border:none" style="width:auto;margin-right:3px" type="checkbox" name="I_Newsletter"   value="1" tabindex="19"  [IF [!I_Newsletter!]]  checked="checked"[/IF] />
				Je souhaite recevoir les offres exclusives
			</div>
		</div>
	</div>

	<div class="BoutonsFin" >
		<input type="submit" tabindex="20"  class="[IF [!Systeme::User::Public!]=1]CreerCompte[ELSE]MettreAJour[/IF]" name="I_Inscription" value="Valider"  class="Valider" />
	</div>
</form>
<div class="LigneForm" style="[!TextProperties!]">
	Les champs marqu&eacute;s<span class="obligatoire">*</span> sont obligatoires.
</div>

// Surcouche JS
<script type="text/javascript">
	function afficheDiv(){
		obj=document.getElementById("Professionnel").style;
		if(document.form_inscription.ProParticulier[2].checked){
			obj.visibility='visible';
		}else{
			obj.visibility='hidden';
		}

		
	}

</script>
	
