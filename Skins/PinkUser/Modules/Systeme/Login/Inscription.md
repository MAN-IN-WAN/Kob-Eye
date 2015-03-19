[IF [!I_Inscription!]!=]
	////////////////// Si déjà connecté on ne peut plus modifier son mail / idenfiant
	[IF [!Systeme::User::Public!]=0]
		[!I_Pseudonyme:=[!Systeme::User::Mail!]!]
		[!I_Mail:=[!Systeme::User::Mail!]!]
	[/IF]
	////////////////// On verifie les champs du formulaire
	[IF [!Utils::isMail([!I_Mail!])!]!=1][!I_Mail_Error:=1!][!I_Error:=1!][/IF]
	[IF [!Systeme::User::Public!]=1]
		// Uniquement à la création
		[IF [!I_Pass!]=][!I_Pass_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Pass2!]!=[!I_Pass!]][!I_Pass2_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Mail2!]!=[!I_Mail!]][!I_Mail2_Error:=1!][!I_Error:=1!][/IF]
	[/IF]
	
	[IF [!hash!]]	
		[!VerifMD5:=[!Utils::md5([!Result!])!]!]
		[IF [!VerifMD5!]!=[!hash!]][!C_Code_Error:=1!][!I_Error:=1!][/IF]
	[/IF]

	//////////////// Les champs sont OK on procède à la création ou à l'update
	[IF [!I_Error!]=]
		///////////////// Deja connecté = Modification | Sinon = Création nouveau client
		[STORPROC Pink/PkUser/UserId=[!Systeme::User::Id!]|Pers|0|1]
			[!ModeCreation:=0!]
			[NORESULT]
				[!ModeCreation:=1!]
				[OBJ Pink|PkUser|Pers]
			[/NORESULT]
		[/STORPROC]
		///////////////// On remplit tous les champs
		[METHOD Pers|Set][PARAM]Mail[/PARAM][PARAM][!I_Mail!][/PARAM][/METHOD]
		[METHOD Pers|Set][PARAM]Tel[/PARAM][PARAM][!I_Tel!][/PARAM][/METHOD]
		[METHOD Pers|Set][PARAM]Pass[/PARAM][PARAM][!I_Pass!][/PARAM][/METHOD]
		[METHOD Pers|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
		
//		[STORPROC [!Pers::Proprietes!]|Prop]
//			[IF [!Prop::Nom!]!=UserId&&[!Prop::Nom!]!=ConnexionLe]
//				[METHOD Pers|Set]
//					[PARAM][!Prop::Nom!][/PARAM]
//					[PARAM][!I_[!Prop::Nom!]!][/PARAM]
//				[/METHOD]
//			[/IF]
//		[/STORPROC]
//		[IF [!ModeCreation!]=0]
//			[METHOD Pers|Set]
//				[PARAM]ConnexionLe[/PARAM][PARAM][!TMS::Now!][/PARAM]
//			[/METHOD]
//		[/IF]
		///////////////// Mot de passe + Identifiant (uniquement en création)
//		[IF [!ModeCreation!]=1]
//			[METHOD Pers|Set]
//				[PARAM]Pseudonyme[/PARAM]
//				[PARAM][!I_Mail!][/PARAM]
//			[/METHOD]
//			[METHOD Pers|Set]
//				[PARAM]Pass[/PARAM]
//				[PARAM][!I_Pass!][/PARAM]
//			[/METHOD]
//			[METHOD Pers|Set]
//				[PARAM]Actif[/PARAM]
//				[PARAM][!AutoConnexion!][/PARAM]
//			[/METHOD]
//		[/IF]
		[IF [!Pers::Verify(1)!]||[!ModeCreation!]=0]
			////////////// Enregistrement
			[IF [!ModeCreation!]=1]
				[METHOD Pers|SaveUser][PARAM]1[/PARAM][/METHOD]
				[MODULE Systeme/Mail/Inscription?Obj=[!Pers!]&Pass=[!I_Pass!]]
			[ELSE]
				[METHOD Pers|updateUser][/METHOD]
			[/IF]


			[IF [!ModeCreation!]=1]
				[CONNEXION [!I_Mail!]|[!I_Pass!]]
				// Redirection
				[IF [!Redirect!]=][!Redirect:=[!Lien!]!][/IF]
				[REDIRECT][!Redirect!][/REDIRECT]
			[ELSE]
				<div class="Message">Votre compte a été créé avec succès</div>
			[/IF]
			
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
				<ul class="Error">
					[IF [!I_Tel_Error!]]<li>Le téléphone est obligatoire</li>[/IF]
					[IF [!I_Mail_Error!]]<li>L'adresse mail est incorrecte</li>[/IF]
					[IF [!I_Mail2_Error!]]<li>Les adresses mails ne correspondent pas</li>[/IF]
					[IF [!I_Pass_Error!]]<li>Le mot de passe ne peut pas être vide</li>[/IF]
					[IF [!I_Pass2_Error!]]<li>Les mots de passe ne correspondent pas</li>[/IF]
					[IF [!C_Code_Error!]]<li>Opération fausse</li>[/IF]
				</ul>
			[/BLOC]
		[ELSE]
			[BLOC Erreur|Liste des erreurs]
				<ul class="Error">
					[STORPROC [!Pers::Error!]|E]
						<li>Erreur d'enregistrement : [!E::Champ!] : [!E::Message!]</li>
					[/STORPROC]
				</ul>
			[/BLOC]
		[/IF]
	[/IF]
[ELSE]
	///////////////////// En cas de modification du compte
	[STORPROC Pink/PkUser/UserId=[!Systeme::User::Id!]|ClientEdit|0|1]

		// Tous les champs client
		[STORPROC [!ClientEdit::Proprietes!]|Prop]
			[IF [!Prop::Nom!]!=UserId&&[!Prop::Nom!]!=ConnexionLe]
				[!I_[!Prop::Nom!]:=[!ClientEdit::[!Prop::Nom!]!]!]
			[/IF]
		[/STORPROC]

	[/STORPROC]
[/IF]
<form action="/[!Lien!]" method="post" enctype="multipart/form-data" name="form_inscription" class="Inscription" >
	<div class="row">
		<div class="col-md-12 creation">
			<h2 class="creaCompte">Je cr&eacute;e mon compte </h2>
		</div>			
	</div>
	<div class="row">
		<div class="col-md-12 ">
			<h3 class="ssTitre">Identifiants</h3>
		</div>
	</div>
	<div class="row idenfiant">
		<div class="col-md-3">
			<label>Votre e-mail <span class="obligatoire">*</span></label>
		</div>
		<div class="col-md-9">
			<input type="text"  name="I_Mail" value="[IF [!Reset!]=][!I_Mail!][/IF]" tabindex="10" [IF [!I_Mail_Error!]]class="Error"[/IF]/>				
		</div>
	</div>
	<div class="row idenfiant">
		<div class="col-md-3">
			<label>Confirmer e-mail <span class="obligatoire">*</span></label>
		</div>
		<div class="col-md-9">
			<input type="text" name="I_Mail2" value="[IF [!Systeme::User::Public!]=1][IF [!Reset!]=][!I_Mail2!][/IF][ELSE][!I_Mail!][/IF]" tabindex="20"  [IF [!I_Mail2_Error!]]class="Error"[/IF]/>
		</div>
	</div>
	<div class="row idenfiant">
		<div class="col-md-3">
			<label>Votre mot de passe <span class="obligatoire">*</span></label>
		</div>
		<div class="col-md-9">
			<input type="password" name="I_Pass" value="[IF [!Reset!]=][!I_Pass!][/IF]"  tabindex="30"  [IF [!I_Pass_Error!]]class="Error"[/IF]/>
		</div>
	</div>
	<div class="row idenfiant">
		<div class="col-md-3">
			<label>Confirmer MDP <span class="obligatoire">*</span></label>
		</div>
		<div class="col-md-9">
			<input type="password"  name="I_Pass2" value="[IF [!Reset!]=][!I_Pass2!][/IF]"  tabindex="40"  [IF [!I_Pass2_Error!]]class="Error"[/IF]/>
		</div>
	</div>
	<div class="row idenfiant">
		<div class="col-md-3 ">
			<label>Téléphone <span class="obligatoire">*</span></label>
		</div>
		<div class="col-md-9">
			<input type="text"  name="I_Tel" value="[IF [!Reset!]=][!I_Tel!][/IF]" tabindex="130"  [IF [!I_Tel_Error!]]class="Error"[ELSE][/IF]/>
		</div>
	</div>
	<div class="row idenfiant calcul">
		<div class="col-md-3 ">
			<label >Merci de résoudre cette opération*</label>
		</div>
		<div class="col-md-9">
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
	<div class="row">
		<div class="col-md-12"> 
			<p class="LesChamps"><span class="obligatoire">*</span>Champs obligatoires</p>
		</div>
	</div>
	<div class="row">
		<input type="hidden" name="SendContact" value="1">
		<div class="col-md-8"> 
            
        </div>
		<div class="col-md-4"> 
			<input type="submit" tabindex="210"  class="btn btn-grisDroite ConnexionConnexion [IF [!Systeme::User::Public!]=1]CreerCompte[ELSE]MettreAJour[/IF]" name="I_Inscription" value="Valider"  />
		</div>
	</div>

</form>
	
