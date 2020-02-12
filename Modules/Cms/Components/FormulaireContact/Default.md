[IF [!SendContact!]!=]
	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!C_Nom!]][ELSE][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Mail!]][ELSE][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Error!]]
		// Si il y a des erreurs, on les affiche
	    <div class="alert alert-error">
			<strong>Veuillez remplir les champs obligatoires suivants :</strong>
			<ul>
				[IF [!C_Nom_Error!]]<li>Merci de renseigner votre Nom</li>[/IF]
				[IF [!C_Mail_Error!]]<li>Merci de renseigner votre adresse email</li>[/IF]
				[IF [!C_Tel_Error!]]<li>Merci de renseigner votre n° de téléphone</li>[/IF]
				[IF [!C_Objet_Error!]]<li>Merci de renseigner le sujet de votre demande</li>[/IF]
				[IF [!C_Mess_Error!]]<li>Merci de laisser votre message</li>[/IF]
				[IF [!C_Calc_Error!]=1]<p>Calcul de vérification erroné</p>[/IF]
				[IF [!C_Code_Error!]]<li>Merci de renseigner votre code postal</li>[/IF]
				[IF [!C_Ville_Error!]]<li>Merci de renseigner votre Ville</li>[/IF]
			</ul>
	    </div>
	[ELSE]
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[IF [!CONTACTMAILCC!]!=][METHOD LeMail|Cc][PARAM][!CONTACTMAILCC!][/PARAM][/METHOD][/IF]
		[IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM][!CONTACTMAILBCC!][/PARAM][/METHOD][/IF]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					<font face="arial" color="#000000" size="2">
					[IF [!SUJET_ACTIF!]]
						<strong>Objet de la demande</strong> : [!C_Objet!]<br/>
					[/IF]
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!C_Nom!]</span> [!C_Prenom!]<br/>
					[IF [!ADRESSE_ACTIF!]]
						<strong>Adresse</strong> : [!C_Adresse!]<br/>
						<strong>Code postal</strong> : [!C_Code!]<br/>
						<strong>Ville</strong> : [!C_Ville!]<br/>
					[/IF]
					[IF [!TEL_ACTIF!]]
						<strong>Numéro de téléphone</strong> : [!C_Tel!]<br/>
					[/IF]
					[IF MESSAGE_ACTIF]
						<strong>Message</strong> : [UTIL BBCODE][!C_Mess!][/UTIL]<br /></font>
					[/IF]
					<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		// Mail de confirmation
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					Bonjour [!C_Prenom!] <span style="text-transform:uppercase">[!C_Nom!]</span>,<br />
					Nous avons bien reçu votre demande par email et vous remercions de votre confiance.<br />
					Nous traitons votre demande dans les plus brefs délais.
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		<div class="alert alert-success">Message envoy&eacute; avec succ&egrave;s.</div>
		<div class="alert alert-info">Un mail de confirmation vous a été adressé.</div>
        <div class="row">
            <input type="hidden" name="SendContact" value="1">
            <div class="col-md-12 "> 
                <a href="/" class="btn btn-vert Envoyer">Retour à l'accueil</a>
            </div>
        </div>
	[/IF]
[/IF]
[IF [!SendContact!]=||[!C_Error!]] 
	<div class="row contactContenu">
		<h1>Contactez-nous</h1>
		<form id="FormContact" method="post" action="/[!Lien!]" class="form-horizontal col-md-12">
			<div class="row">
				<div class="control-group  [IF [!C_Nom_Error!]]error[/IF] ">
					<div class="col-md-3 col-sm-4 col-xs-12"> 
						<label class="control-label" for="C_Nom">Nom <span class="obligatoire">*</span></label>
					</div>
					<div class="col-md-9 col-sm-8 col-xs-12"> 
						<input type="text" id="C_Nom" name="C_Nom" class="Contact" style="text-transform:uppercase" value="[!C_Nom!]" required/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="control-group  [IF [!C_Mail_Error!]]error[/IF]">
					<div class="col-md-3 col-sm-4 col-xs-12"> 
						<label class="control-label" for="C_Mail">Adresse e-mail <span class="obligatoire">*</span></label>
					</div>
					<div class="col-md-9 col-sm-8 col-xs-12"> 
						<div class="input-prepend">
							<span class="add-on"><i class="icon-envelope"></i></span>
							<input type="text" id="C_Mail" name="C_Mail" value="[!C_Mail!]" class="Contact" required/>
						</div>
					</div>
				</div>
			</div>
			[IF [!PRENOM_ACTIF!]]
                <div class="row">
                    //<div class="control-group  [IF [!C_Prenom_Error!]]error[/IF]">
                    <div class="control-group"  >
                        <div class="col-md-3 col-sm-4 col-xs-12"> 
                            <label class="control-label" for="C_Prenom">Prénom</label>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12"> 
                            <input type="text" name="C_Prenom" class="Contact" value="[!C_Prenom!]" />
                        </div>
                    </div>
                </div>
			[/IF]
			[IF [!TEL_ACTIF!]]
                <div class="row">
                    //<div class="control-group [IF [!C_Tel_Error!]]error[/IF]">
                    <div class="control-group" >
                        <div class="col-md-3 col-sm-4 col-xs-12"> 
                            <label class="control-label" for="C_Tel">Numéro de téléphone</label>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12"> 
                            <input type="text" name="C_Tel" class="Contact"  value="[!C_Tel!]"/>
                        </div>
                    </div>
                </div>
			[/IF]
			[IF [!ADRESSE_ACTIF!]]
                <div class="row">
                    <div class="control-group"  >
                        <div class="col-md-3 col-sm-4 col-xs-12"> 
                            <label class="control-label" for="C_Adresse">Adresse</label>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12"> 
                            <input type="text" name="C_Adresse" id="C_Adresse" value="[!C_Adresse!]" class="Contact" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    //<div class="control-group  [IF [!C_Code_Error!]]error[/IF]">
                    <div class="control-group"  >
                        <div class="col-md-3 col-sm-4 col-xs-12"> 
                            <label class="control-label" for="C_Code">Code postal </label>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12"> 
                            <input type="text" name="C_Code" id="C_Code" value="[!C_Code!]"  class="Contact" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    //<div class="control-group [IF [!C_Ville_Error!]]error[/IF]">
                    <div class="control-group" >
                        <div class="col-md-3 col-sm-4 col-xs-12"> 
                            <label class="control-label" for="C_Ville">Ville</label>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12"> 
                            <input type="text" name="C_Ville" id="C_Ville" value="[!C_Ville!]" class="Contact" />
                        </div>
                    </div>
                </div>
			[/IF]
			[IF [!SUJET_ACTIF!]]
				<div class="row">
					//<div class="control-group  [IF [!C_Objet_Error!]]error[/IF]">
					<div class="control-group"  >
						<div class="col-md-3 col-sm-4 col-xs-12"> 
							<label class="control-label" for="C_Objet">Sujet </label>
						</div>
						<div class="col-md-9 col-sm-8 col-xs-12"> 
							<input type="text" name="C_Objet" value="[!C_Objet!]"  class="Contact" />
						</div>
					</div>
				</div>
			[/IF]
			[IF [!MESSAGE_ACTIF!]]
                <div class="row">
                    <div class="control-group [IF [!C_Mess_Error!]]error[/IF]">
                        <div class="col-md-3 col-sm-4 col-xs-12"> 
                            <label class="control-label" for="C_Mess">Message </label>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12"> 
                            <textarea cols="20" rows="6" name="C_Mess" class="Contact" >[!C_Mess!]</textarea>
                        </div>
                    </div>
                </div>
			[/IF]
			</div>
			[IF [!CAPTCHA_ACTIF!]]
				<div class="row">
					<div class="control-group last [IF [!C_Calc_Error!]]error[/IF]">
						<div class="col-md-12 margeOP"> 
							<p>Les champs marqués (<span class="Obligatoire">*</span>) sont obligatoires.</p>
							<br />
							<div class="capt">
								<label class="control-label" for="C_Nom">Merci de résoudre l'opération suivante avant de valider <span class="obligatoire">*</span></label>
								<input class="Op" type="text" name="n3" id="n3" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" size="5" />
								+
								<input class="Op" type="text" name="n4" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" size="5" />
								=
								<input class="Op" type="text" name="tot2" value=""  maxlength="2" class="[IF [!Calc2_Error!]]Error[/IF]" required size="5" />
							</div>
						</div>
					</div>
				</div>
			[/IF]
			<div class="row">
				<input type="hidden" name="SendContact" value="1">
				<div class="col-md-12 sendContact"> 
					<button type="submit" class="btn btn-primary">Envoyer</button>
				</div>
			</div>
		</form>
[/IF]


