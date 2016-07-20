[IF [!CONTACTMAIL!]=]
	[!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]
[IF [!receiver!]!=]
	[!CONTACTMAILCC:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
	[SWITCH [!receiver!]|=]
		[CASE MC]
			[!CONTACTMAIL:=contact@f-onekites.com!]
		[/CASE]
		[CASE SF]
			[!CONTACTMAIL:=contact@f-onekites.com!]
		[/CASE]
		[CASE SD]
			[!CONTACTMAIL:=contact@f-onekites.com!]
		[/CASE]
		[CASE SI]
			[!CONTACTMAIL:=contact@f-onekites.com!]
		[/CASE]
		[CASE HR]
			[!CONTACTMAIL:=contact@f-onekites.com!]
		[/CASE]
		[DEFAULT]
			[!CONTACTMAIL:=contact@f-onekites.com!]
		[/DEFAULT]
	[/SWITCH]
[/IF]
[!SHOW_FORM:=1!]

<div class="container featured " style="margin-top:20x;">
	//<h3>Contact</h3>
	<form id="FormContact" method="post" action="/[!Lien!]" >
		<div class="col-lg-9 nopadding-left">
			[IF [!SendContact!]!=]
				//Verification des informations du formulaire
				[!C_Error:=0!]
				[IF [!agree!]=1][ELSE][!agree_Error:=1!][!C_Error:=1!][/IF]
				[IF [!FullName!]][ELSE][!FullName_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Utils::isMail([!Email!])!]][ELSE][!Email_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Country!]][ELSE][!Country_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Subject!]][ELSE][!Subject_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Message!]][ELSE][!Message_Error:=1!][!C_Error:=1!][/IF]
				[IF [!C_Error!]]
					// Si il y a des erreurs, on les affiche
					<div class="alert alert-danger">
						<strong>__CONTACT_ERRORS__</strong>
						<ul>
							[IF [!agree_Error!]]<li>__PLEASE_ACEEPT_ALL_TERMS_AND_CONDITIONS__</li>[/IF]
							[IF [!FullName_Error!]]<li>__ERROR_NAME__</li>[/IF]
							[IF [!Email_Error!]]<li>__ERROR_EMAIL__</li>[/IF]
							[IF [!Subject_Error!]]<li>__ERROR_SUBJECT__</li>[/IF]
							[IF [!Message_Error!]]<li>__ERROR_MESSAGE__</li>[/IF]
							[IF [!Country_Error!]]<li>__ERROR_COUNTRY__</li>[/IF]
						</ul>
					</div>
				[ELSE]
					// Sinon envoi du mail
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM][!Email!][/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!Email!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
					//[IF [!CONTACTMAILCC!]!=][METHOD LeMail|Cc][PARAM]myriam@abtel.fr[/PARAM][/METHOD][/IF]
					//[IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD][/IF]
					[METHOD LeMail|Body]
						[PARAM]
							[BLOC Mail]
								<font face="arial" color="#000000" size="2">
								<strong>Objet de la demande</strong> : [!Subject!]<br/>
								<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!FullName!]</span><br/>
								<strong>Adresse e-mail</strong> : [!Email!]<br/>
								<strong>Pays</strong> : [!Country!]<br/>
								<strong>Message</strong> : [UTIL BBCODE][!Message!][/UTIL]<br /></font>
								<strong>Adresse Ip</strong> : <span><a href="http://geotool.flagfox.net/?ip=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
							[/BLOC]
						[/PARAM]
					[/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
			
				<div class="col-lg-12">
				[IF [!newsletter!]]
					//Enregistrement à la newsletter
					[COUNT Newsletter/GroupeEnvoi/15/Contact/Email=[!Email!]|C]
					[IF [!C!]=0]
						[OBJ Newsletter|Contact|Con]
						// 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
						[STORPROC Newsletter/GroupeEnvoi/15/Contact/Email=[!Email!]|Con|0|1]
							[NORESULT]
								[METHOD Con|Set]
									[PARAM]Email[/PARAM][PARAM][!Email!][/PARAM]
								[/METHOD]
								[METHOD Con|Set]
									[PARAM]Nom[/PARAM][PARAM][!FullName!][/PARAM]
								[/METHOD]
								[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
								[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/15[/PARAM][/METHOD]
								[METHOD Con|Save][/METHOD]
								<div class="alert alert-success">__NEWSLETTER_SUSCRIBE_SUCCESS__</div>
							[/NORESULT]
						[/STORPROC]

						// 3 - enregistrement du message
						[OBJ Newsletter|Reception|Rec]
						[METHOD Rec|Set]
							[PARAM]Contenu[/PARAM]
							[PARAM][!Message!][/PARAM]
						[/METHOD]
						[METHOD Rec|Set]
							[PARAM]Destinatire[/PARAM]
							[PARAM][!CONTACTMAIL!][/PARAM]
						[/METHOD]
						[METHOD Rec|Set]
							[PARAM]Sujet[/PARAM]
							[PARAM][!Subject!][/PARAM]
						[/METHOD]
						[METHOD Rec|AddParent]
							[PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
						[/METHOD]
						[METHOD Rec|Save][/METHOD]
					[ELSE]
						<div class="alert alert-warning">__NEWSLETTER_SUSCRIBE_WARNING__</div>
					[/IF]
				[/IF]
					[STORPROC [!CONF::MODULE!]|Mod]
						[IF [!Key!]=NEWSLETTER]
							// 1 - on vérifie que le groupe existe, s'il n'existe pas on le créé
							[STORPROC Newsletter/GroupeEnvoi/10|GR|0|1]
							[/STORPROC]
					
							// 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
							[STORPROC Newsletter/GroupeEnvoi/10/Contact/Email=[!Email!]|Con|0|1]
								[NORESULT]
									[OBJ Newsletter|Contact|Con]
									[METHOD Con|Set]
										[PARAM]Email[/PARAM]
										[PARAM][!Email!][/PARAM]
									[/METHOD]
									[METHOD Con|Set]
										[PARAM]Nom[/PARAM]
										[PARAM][!FullName!][/PARAM]
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
								[PARAM][!Message!][/PARAM]
							[/METHOD]
							[METHOD Rec|Set]
								[PARAM]Destinatire[/PARAM]
								[PARAM][!CONTACTMAIL!][/PARAM]
							[/METHOD]
							[METHOD Rec|Set]
								[PARAM]Sujet[/PARAM]
								[PARAM][!Subject!][/PARAM]
							[/METHOD]
							[METHOD Rec|AddParent]
								[PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
							[/METHOD]
							[METHOD Rec|Save][/METHOD]
						[/IF]
					[/STORPROC]
					 </div>
						
					// Mail de confirmation
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM]noreply@f-onekites.com[/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!Email!][/PARAM][/METHOD]
					[METHOD LeMail|Body]
						[PARAM]
							[BLOC Mail]
								__HELLO__ [!FullName!],<br />
								__MSG_RECEPTION__
							[/BLOC]
						[/PARAM]
					[/METHOD]
					[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
					<div class="col-lg-12">
						<div class="alert alert-success">__MESSAGE_SENT_SUCCESSFULLY__</div>
					</div>
					[!SHOW_FORM:=0!]
				[/IF]
			[/IF]
			[IF [!SHOW_FORM!]=1]
			<div class="col-lg-4-1">
				<input type="text" class="form-control error formu" placeholder="__FULL_NAME__" name="FullName" value="[!FullName!]">
			</div>
			<div class="col-lg-4-2">
				<input type="text" class="form-control formu" placeholder="__EMAIL_ADDRESS__" name="Email" value="[!Email!]">
			</div>
			<div class="col-lg-4-3">
				<select name="Country" id="Country" class="form-control error formu">	
					<option selected> __COUNTRY__</option>
					[STORPROC Geographie/Pays|Pa|||Nom|ASC]
						<option value="[!Pa::Nom!]" [IF [!Country!]=[!Pa::Nom!]]selected[/IF]>[!Pa::Nom!] </option>
					[/STORPROC]
				</select>
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control formu" placeholder="__SUBJECT__" name="Subject" value="[!Subject!]">
			</div>
			<div class="col-lg-4-3">
   				<div class="form-control-contact">
					<select class="form-control" name="receiver" id="receiver">
						<option value="CONTACT" [IF [!receiver!]=CONTACT]selected="selected"[/IF]>__RECEIVER__</option>
						<option value="MC" [IF [!receiver!]=MC]selected="selected"[/IF]>Marketing / Communication</option>
						<option value="SF" [IF [!receiver!]=SF]selected="selected"[/IF]>Sales France</option>
						<option value="SD" [IF [!receiver!]=SD]selected="selected"[/IF]>Sales Deutshland</option>
						<option value="SI" [IF [!receiver!]=SI]selected="selected"[/IF]>Sales International</option>
						<option value="HR" [IF [!receiver!]=HR]selected="selected"[/IF]>Human Resources</option>
						<option value="SAV" [IF [!receiver!]=SAV]selected="selected"[/IF]>SAV</option>
					</select>
				</div>
			</div>
			<div class="col-lg-4-3">
				<textarea rows="13" id="textarea" class="form-control formu" placeholder="__MESSAGE__" name="Message" style="text-transform:none;">[!Message!]</textarea>
			</div>
			<div class="col-lg-4-3">
				<div class="checkbox">
					<label>
					<input type="checkbox" name="agree" value="1" [IF [!SendContact!]=1&&[!agree!]=][ELSE]checked="checked"[/IF]> __AGREE_ALL_TERMS_AND_CONDITIONS__
					</label>
				</div>
			</div>
			<div class="col-lg-4-3">
				<div class="checkbox">
					<label>
					<input type="checkbox" name="newsletter" value="1" [IF [!SendContact!]=1&&[!newsletter!]=][ELSE]checked="checked"[/IF]> __SUSCRIBE_TO_F-ONE_NEWSLETTER__
					</label>
				</div>
			</div>
			<div class="col-lg-4-3">
				<input type="hidden" name="SendContact" value="1" />
				<button class="btn btn-primary btn-send formu" type="submit" >__SEND_MESSAGE__</button>
			</div>
			<div class="alert alert-danger message" style="display:none;margin:50px 0;">__MESSAGE_SAV_CONTACT__</div>
			[/IF]
		</div>
	</form>
    	<div class="col-lg-3-1">
		<div class="etiquette">
        		CONTACT DETAILS
        	</div>
		<div class="adresse">
	        	<div class="adresse-icon"></div>
       			<h6>F-ONE SAS</h6>
        		<div>ZAC de la méditerranée<br />170, Route de la Foire<br />34470 PÉROLS - FRANCE</div>
			
			<!--<div class="adresse-fone"></div>
            		<p><strong>Phone :</strong> +33 (0)4 67 99 51 16</p>
            		<p><strong>Fax   :</strong> +33 (0)4 67 99 61 93</p>-->
	      </div>
		<div class="etiquette">
			__DETAILS_REGISTRATION__
		</div>
		<div class="details-register">
			<p>__TEXT_CONDITIONS__</p>
		</div>
    	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#receiver').change(function (event) {
				if ($(this).val()=="SAV") {
					$('.formu').css('display','none');
					$('.message').css('display','block');
				}else{
					$('.formu').css('display','block');
					$('.message').css('display','none');
				}
			});
		});
	</script>
</div>