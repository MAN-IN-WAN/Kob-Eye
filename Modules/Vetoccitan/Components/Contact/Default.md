[IF [!CONTACTMAIL!]=]
	[!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]
[IF [!SendContact!]!=]

	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!C_Nom!]][ELSE][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Mail!]][ELSE][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!SUJET_ACTIF!]]
		[IF [!C_Objet!]][ELSE][!C_Objet_Error:=1!][!C_Error:=1!][/IF]
	[/IF]
	[IF [!MESSAGE_ACTIF!]]
		[IF [!C_Mess!]][ELSE][!C_Mess_Error:=1!][!C_Error:=1!][/IF]
	[/IF]	
	[IF [!ADRESSE_ACTIF!]]
		[IF [!C_Code!]][ELSE][!C_Code_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Ville!]][ELSE][!C_Ville_Error:=1!][!C_Error:=1!][/IF]
	[/IF]	
	[IF [!MESSAGE_ACTIF!]]
		[IF [!C_Mess!]][ELSE][!C_Mess_Error:=1!][!C_Error:=1!][/IF]
	[/IF]	
	[IF [!CAPTCHA_ACTIF!]]
		[IF [!n3:+[!n4!]!]!=[!tot2!]][!Calc_Error:=1!][!C_Error:=1!][/IF]
	[/IF]


	[IF [!C_Error!]]
		// Si il y a des erreurs, on les affiche
		<div class="BlocError">
			<strong>Veuillez remplir les champs obligatoires suivants :</strong>
			<ul>
				[IF [!C_Nom_Error!]]<li>Merci de renseigner votre Nom</li>[/IF]
				[IF [!C_Mail_Error!]]<li>Merci de renseigner votre adresse email</li>[/IF]
				[IF [!C_Tel_Error!]]<li>Merci de renseigner votre n° de téléphone</li>[/IF]
				[IF [!C_Objet_Error!]]<li>Merci de renseigner le sujet de votre demande</li>[/IF]
				[IF [!C_Mess_Error!]]<li>Merci de laisser votre message</li>[/IF]
				[IF [!Calc_Error!]=1]<p>Calcul de vérification erroné</p>[/IF]
				[IF [!C_Code_Error!]]<li>Merci de renseigner votre code postal</li>[/IF]
				[IF [!C_Ville_Error!]]<li>Merci de renseigner votre Ville</li>[/IF]
			</ul>
		</div>
	[ELSE]
		// Sinon envoi du mail
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
					<strong>Adresse Ip</strong> : <span><a href="http://www.geoiptool.com/fr/?IP=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
					[IF [!SUJET_ACTIF!]]
					<strong>Objet de la demande</strong> : [!C_Objet!]<br/>
					[/IF]
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!C_Nom!]</span> [!C_Prenom!]<br/>
					[IF [!TEL_ACTIF!]]
					<strong>Numéro de téléphone</strong> : [!C_Tel!]<br/>
					[/IF]
					<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>
					[IF MESSAGE_ACTIF]
					<strong>Message</strong> : [UTIL BBCODE][!C_Mess!][/UTIL]<br /></font>
					[/IF]
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!C_Nom!]</span> [!C_Prenom!]<br/>
					
					[IF [!ADRESSE_ACTIF!]]
						<strong>Adresse</strong> : [!C_Adresse!]<br/>
						<strong>Code postal</strong> : [!C_Code!]<br/>
						<strong>Ville</strong> : [!C_Ville!]<br/>
					[/IF]
					<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>
					
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]

		// Enregistrement Contact + Message
		[STORPROC [!CONF::MODULE!]|Mod]
			[IF [!Key!]=NEWSLETTER]
				// 1 - on vérifie que le groupe existe, s'il n'existe pas on le créé
				[STORPROC Newsletter/GroupeEnvoi/Titre=Contacts par le site|GR|0|1]
					[NORESULT]
						[OBJ Newsletter|GroupeEnvoi|GR]
						[METHOD GR|Set]
							[PARAM]Titre[/PARAM]
							[PARAM]Contacts par le site[/PARAM]
						[/METHOD]
						[METHOD GR|Save][/METHOD]
					[/NORESULT]
				[/STORPROC]
		
				// 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
				[STORPROC Newsletter/Contact/Email=[!C_Mail!]|Con|0|1]
					[NORESULT]
						[OBJ Newsletter|Contact|Con]
						[METHOD Con|Set]
							[PARAM]Email[/PARAM]
							[PARAM][!C_Mail!][/PARAM]
						[/METHOD]
						[METHOD Con|Set]
							[PARAM]Nom[/PARAM]
							[PARAM][!C_Nom!][/PARAM]
						[/METHOD]
						[METHOD Con|Set]
							[PARAM]Prenom[/PARAM]
							[PARAM][!C_Prenom!][/PARAM]
						[/METHOD]
						[METHOD Con|Set]
							[PARAM]Telephone[/PARAM]
							[PARAM][!C_Tel!][/PARAM]
						[/METHOD]
						[METHOD Con|AddParent]
							[PARAM]Newsletter/GroupeEnvoi/[!GR::Id!][/PARAM]
						[/METHOD]
						[METHOD Con|Save][/METHOD]
					[/NORESULT]
				[/STORPROC]
				
				// 3 - enregistrement du message
				[OBJ Newsletter|Reception|Rec]
				[METHOD Rec|Set]
					[PARAM]Contenu[/PARAM]
					[PARAM][!C_Mess!][/PARAM]
				[/METHOD]
				[METHOD Rec|Set]
					[PARAM]Sujet[/PARAM]
					[PARAM][!C_Objet!][/PARAM]
				[/METHOD]
				[METHOD Rec|AddParent]
					[PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
				[/METHOD]
				[METHOD Rec|Save][/METHOD]
			[/IF]
		[/STORPROC]
			
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
		<div class="blocMessage">
			<h3>Message envoy&eacute; avec succ&egrave;s.</h3>
			<p>Un mail de confirmation vous a été adressé.</p><br /><br />
		</div>
	[/IF]
[/IF]

[IF [!SendContact!]=||[!C_Error!]] 
	<div class="CpContact">
		<form id="FormContact" method="post" action="/[!Lien!]">
			<div class="LigneForm">
				<label>Nom<span class="Obligatoire">*</span></label>
				<input type="text" name="C_Nom"  style="text-transform:uppercase" [IF [!C_Nom!]=] value="[!C_Nom!]" [ELSE] value="[!C_Nom!]" [/IF] class="[IF [!C_Nom_Error!]]Error[/IF]" />
			</div>
		
				<div class="LigneForm">
					<label>Prénom</label>
						<input type="text" name="C_Prenom" [IF [!C_Prenom!]=] value="[!C_Prenom!]" [ELSE] value="[!C_Prenom!]" [/IF] />
				</div>
			
			
			<div class="LigneForm">
				<label>Numéro de téléphone</label>
				<input type="text" name="C_Tel" [IF [!C_Tel!]=] value="[!C_Tel!]" [ELSE] value="[!C_Tel!]" [/IF] class="[IF [!C_Tel_Error!]]Error[/IF]"/>
			</div>
			
			
				<div class="LigneForm">
					<label>Adresse<span class="Obligatoire">*</span></label>
					<input type="text" name="C_Adresse" [IF [!C_Adresse!]=] value="[!C_Adresse!]" [ELSE] value="[!C_Adresse!]" [/IF] class="[IF [!C_Adresse_Error!]]Error[/IF]"/>
				</div>
				<div class="LigneForm">
					<label>Code postal <span class="Obligatoire">*</span></label>
					<input type="text" name="C_Code" [IF [!C_Code!]=] value="[!C_Code!]" [ELSE] value="[!C_Code!]" [/IF] class="[IF [!C_Code_Error!]]Error[/IF]" />
				</div>
				<div class="LigneForm">
					<label>Ville<span class="Obligatoire">*</span></label>
					<input type="text" name="C_Ville" [IF [!C_Ville!]=] value="[!C_Ville!]" [ELSE] value="[!C_Ville!]" [/IF] class="[IF [!C_Ville_Error!]]Error[/IF]" />
				</div>
		
			<div class="LigneForm">
				<label>Adresse e-mail <span class="Obligatoire">*</span></label>
				<input type="text" name="C_Mail" [IF [!C_Mail!]=] value="[!C_Mail!]" [ELSE] value="[!C_Mail!]" [/IF] class="[IF [!C_Mail_Error!]]Error[/IF]" />
			</div>
			
				<div class="LigneForm">
					<label>Sujet <span class="Obligatoire">*</span></label>
					<input type="text" name="C_Objet" [IF [!C_Objet!]=] value="[!C_Objet!]" [ELSE] value="[!C_Objet!]" [/IF] class="[IF [!C_Objet_Error!]]Error[/IF]" >
				</div>
			
			
				<div class="LigneForm">
					<label>Message <span class="Obligatoire">*</span></label>
					<textarea cols="80" rows="6" name="C_Mess" class="[IF [!C_Mess_Error!]]Error[/IF]">[IF [!C_Mess!]=][!C_Mess!][ELSE][!C_Mess!][/IF]</textarea>
				</div>
			
			
				<div class="LigneForm" style="text-align: right;padding:20px 0 0;width: 595px;">
		            <label style="text-align:left;width:434px;font-variant:small-caps;font-weight:bold;color:#e00025;">Merci de résoudre l'opération ci-dessous avant de valider <span class="obligatoire">*</span></label>
		            <input type="text" name="n3" id="n3" value="[!Utils::Random(9)!]" style="color:#e00025;font-weight:bold;width:20px;border:none;" size="2" maxlength="2" readonly="readonly"/> + <input type="text" name="n4" value="[!Utils::Random(9)!]"  size="2"   style="color:#e00025;font-weight:bold;width:20px;border:none;text-align:right;"  maxlength="2" readonly="readonly"/><span style="width:40px;text-align:center;">&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;</span><input type="text" name="tot2" value=""  maxlength="2"  style="color:#e00025;font-weight:bold;width:20px;border:1px solid #cccccc;"   class=" [IF [!Calc2_Error!]]Error[/IF]" />
				</div>
			
			<div class="Buttons">
				<button type="submit">Valider</button>
				<input type="hidden" name="SendContact" value="1" />
			</div>
		</form>

		<p>Les champs marqués (<span class="Obligatoire">*</span>) sont obligatoires.</p>
		<p class="ContactTel">Vous pouvez aussi nous contacter par :<br />
		Tel : [!Systeme::User::Tel!]<br />
		Fax : [!Systeme::User::Fax!]</p>
		
		<p>
			Conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
			vous disposez d'un droit d'accès, de rectification, de suppression des informations qui vous concernent que vous pouvez exercer en vous adressant à
			[!Systeme::User::Nom!] - [!Systeme::User::Adresse!] - [!Systeme::User::CodPos!] [!Systeme::User::Ville!] - [!Systeme::User::Pays!].
		</p>
		
		<p>
			[!TEXT_BAS!]
		</p>
	</div>
[/IF]
