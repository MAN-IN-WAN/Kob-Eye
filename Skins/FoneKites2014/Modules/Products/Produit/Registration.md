[HEADER]
<script src='https://www.google.com/recaptcha/api.js'></script>
[/HEADER]

[IF [!CONTACTMAIL!]=]
	[!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]
[!SHOW_FORM:=1!]

<div class="baseline">
	<div class="container nopadding-right nopadding-left">
		<h1 class="title_baseline"></h1>
//		<h1 class="title_baseline">PRODUCT REGISTRATION</h1>
//		<p> ENTER YOUR INFORMATION PRODUCT TO GET THE WARRANTY EVERYTIME</p>
	</div>
</div>

<div class="container conbot nopadding-right nopadding-left">
	<form id="FormRegistration" method="post" action="/[!Lien!]" >
//		<h3>Form registration</h3>
		
		<div class="col-lg-9 nopadding-left">
		[IF [!SendRegistration!]]
			//Verification des informations du formulaire
			[!C_Error:=0!]
			[IF [!agree!]=1][ELSE][!agree_Error:=1!][!C_Error:=1!][/IF]
			[IF [!email!]!=&&[!Utils::isMail([!email!])!]][ELSE][!email_Error:=1!][!C_Error:=1!][/IF]
			[IF [!firstname!]!=][ELSE][!firstname_Error:=1!][!C_Error:=1!][/IF]
			[IF [!name!]!=][ELSE][!name_Error:=1!][!C_Error:=1!][/IF]
			[IF [!adresse!]!=][ELSE][!adresse_Error:=1!][!C_Error:=1!][/IF]
			[IF [!ville!]!=][ELSE][!ville_Error:=1!][!C_Error:=1!][/IF]
			[IF [!pays!]!=][ELSE][!pays_Error:=1!][!C_Error:=1!][/IF]
			[IF [!product_mode!]!=][ELSE][!product_mode_Error:=1!][!C_Error:=1!][/IF]
			[IF [!size!]!=][ELSE][!size_Error:=1!][!C_Error:=1!][/IF]
			[IF [!shop!]!=][ELSE][!shop_Error:=1!][!C_Error:=1!][/IF]
			[IF [!purchase!]!=][ELSE][!purchase_Error:=1!][!C_Error:=1!][/IF]

			//check captcha
			[OBJ Products|Registration|R2]
			[IF [!R2::checkCaptcha()!]][ELSE][!captcha_Error:=1!][!C_Error:=1!][/IF]

			[IF [!C_Error!]]
				// Si il y a des erreurs, on les affiche
					<div class="alert alert-danger">
						<strong>__PLEASE_FILL_THE_FIELDS__</strong>
						<ul>
							[IF [!agree_Error!]]<li>__PLEASE_ACEEPT_ALL_TERMS_AND_CONDITIONS__</li>[/IF]
							[IF [!email_Error!]]<li>__PLEASE_FILL_YOUR_EMAIL_ADDRESS__</li>[/IF]
							[IF [!firstname_Error!]]<li>__PLEASE_FILL_YOUR_FIRSTNAME__</li>[/IF]
							[IF [!name_Error!]]<li>__PLEASE_FILL_YOUR_NAME__</li>[/IF]
							[IF [!adresse_Error!]]<li>__PLEASE_FILL_YOUR_ADDRESS__</li>[/IF]
							[IF [!ville_Error!]]<li>__PLEASE_FILL_YOUR_CITY__</li>[/IF]
							[IF [!pays_Error!]]<li>__PLEASE_FILL_YOUR_COUNTRY__</li>[/IF]
							[IF [!product_mode_Error!]]<li>__PLEASE_FILL_THE_PRODUCT_MODE__</li>[/IF]
							[IF [!size_Error!]]<li>__PLEASE_FILL_THE_PRODUCT_SIZE__</li>[/IF]
							[IF [!shop_Error!]]<li>__PLEASE_FILL_YOUR_SHOP__</li>[/IF]
							[IF [!purchase_Error!]]<li>__PLEASE_FILL_YOUR_PURCHASE__</li>[/IF]
							[IF [!captcha_Error!]]<li>__PLEASE_CHECK_THE_CATPCHA__</li>[/IF]
						</ul>
					</div>
			[ELSE]
				//enregistrement
				[OBJ Products|Registration|R]
				[METHOD R|Set][PARAM]FirstName[/PARAM][PARAM][!firstname!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]LastName[/PARAM][PARAM][!name!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]Email[/PARAM][PARAM][!email!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]Address[/PARAM][PARAM][!adresse!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]City[/PARAM][PARAM][!ville!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]Country[/PARAM][PARAM][!pays!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]Product[/PARAM][PARAM][!product_mode!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]ProductSerial[/PARAM][PARAM][!kitserialnumber!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]Size[/PARAM][PARAM][!size!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]Shop[/PARAM][PARAM][!shop!][/PARAM][/METHOD]
				[METHOD R|Set][PARAM]DatePurchased[/PARAM][PARAM][!purchase!][/PARAM][/METHOD]
				//[METHOD R|Set][PARAM]Message[/PARAM][PARAM][!message!][/PARAM][/METHOD]
				[METHOD R|Save][/METHOD]
				
				
				<div class="col-lg-12">
				[IF [!newsletter!]]
					//Enregistrement à la newsletter
					[COUNT Newsletter/GroupeEnvoi/13/Contact/Email=[!email!]|C]
					[IF [!C!]=0]
						[OBJ Newsletter|Contact|Con]
						[METHOD Con|Set]
							[PARAM]Email[/PARAM][PARAM][!email!][/PARAM]
						[/METHOD]
						[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
						[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/13[/PARAM][/METHOD]
						[METHOD Con|Save][/METHOD]
						<div class="alert alert-success">__NEWSLETTER_SUSCRIBE_SUCCESS__</div>
					[ELSE]
						<div class="alert alert-warning">__NEWSLETTER_SUSCRIBE_WARNING__</div>
					[/IF]
				[/IF]
				</div>
				
				// Sinon envoi du mail
				[LIB Mail|LeMail]
				[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Registration[/PARAM][/METHOD]
				[METHOD LeMail|From][PARAM][!email!][/PARAM][/METHOD]
				[METHOD LeMail|ReplyTo][PARAM][!email!][/PARAM][/METHOD]
				[METHOD LeMail|To][PARAM]contact@f-onekites.com[/PARAM][/METHOD]
				//[IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD][/IF]
				[METHOD LeMail|Body]
					[PARAM]
						[BLOC Mail]
							<font face="arial" color="#000000" size="2">
							<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!firstname!] [!name!]</span><br/>
							<strong>Adresse e-mail</strong> : [!email!]<br/>
							<strong>adresse</strong> : [!adresse!]<br/>
							<strong>ville / pays</strong> : [!ville!] [!pays!]<br/>
							<strong>kitserialnumber</strong> : [!kitserialnumber!]<br/>
							<strong>product_mode</strong> : [!product_mode!]<br/>
							<strong>Size</strong> : [!size!]<br/>
							<strong>Shop</strong> : [!shop!]<br/>
							<strong>Purchase</strong> : [!purchase!]<br/>
							//<strong>Message</strong> : [UTIL BBCODE][!message!][/UTIL]<br /></font>
						[/BLOC]
					[/PARAM]
				[/METHOD]
				[METHOD LeMail|BuildMail][/METHOD]
				//[METHOD LeMail|Send][/METHOD]
				// Mail de confirmation
				[LIB Mail|LeMail]
				[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
				[METHOD LeMail|From][PARAM]noreply@f-onekites.com[/PARAM][/METHOD]
				[METHOD LeMail|ReplyTo][PARAM]noreply@f-onekites.com[/PARAM][/METHOD]
				[METHOD LeMail|To][PARAM][!email!][/PARAM][/METHOD]
				[METHOD LeMail|Body]
					[PARAM]
						[BLOC Mail]
							__HELLO__ [!Name!],<br />
							__SUSCRIBE_SUCCESSFULL__
						[/BLOC]
					[/PARAM]
				[/METHOD]
				[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
				[METHOD LeMail|BuildMail][/METHOD]
				[METHOD LeMail|Send][/METHOD]
				<div class="col-lg-12">
					<div class="alert alert-success">__SUSCRIBE_SUCCESSFULL__</div>
				</div>
				[!SHOW_FORM:=0!]
			[/IF]
		[/IF]
			
			[IF [!SHOW_FORM!]=1]	
			<div class="col-lg-4-1">
				<input type="text" class="form-control" placeholder="__FIRST_NAME__" name="firstname" value="[!firstname!]">
			</div>
			<div class="col-lg-4-2">
				<input type="text" class="form-control" placeholder="__LAST_NAME__" name="name" value="[!name!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__EMAIL_ADDRESS__" name="email" value="[!email!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__ADDRESS__" name="adresse" value="[!adresse!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__CITY__" name="ville" value="[!ville!]">
			</div>
			<div class="col-lg-4-3">
				<select name="pays" id="pays" class="form-control">	
					<option value="" selected> __COUNTRY__</option>
					[STORPROC Geographie/Pays|Pa|||Nom|ASC]
						<option value="[!Pa::Nom!]" [IF [!pays!]=[!Pa::Nom!]]selected[/IF]>[!Pa::Nom!] </option>
					[/STORPROC]
				</select>
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__PRODUCT_MODE__" name="product_mode" value="[!product_mode!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__KIT_SERIAL_NUMBER__" name="kitserialnumber" value="[!kitserialnumber!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__SIZE__" name="size" value="[!size!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__SHOP_WHERE_PURCHASED__" name="shop" value="[!shop!]">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__DATE_OF_PURCHASE__" name="purchase" value="[!purchase!]">
			</div>
			//<div class="col-lg-4-3">
			//	<textarea rows="13" id="textarea" class="form-control" placeholder="__A_MESSAGE_IF_YOU_WANT__" name="message" style="text-transform: none;">[!firstname!]</textarea>
			//</div>
			<div class="g-recaptcha pull-right" data-sitekey="6Ld11yMTAAAAAGKZL9Jm2bPdgZuWMax3m30T_RG7"></div>
			<div class="col-lg-4-3">
				<div class="checkbox">
					<label>
					<input type="checkbox" name="agree" value="1" [IF [!agree!]]checked="checked"[/IF]> __AGREE_ALL_TERMS_AND_CONDITIONS__
					</label>
				</div>
			</div>
			<div class="col-lg-4-3">
				<div class="checkbox">
					<label>
					<input type="checkbox" name="newsletter" value="1" [IF [!newsletter!]]checked="checked"[/IF]> __SUSCRIBE_TO_F-ONE_NEWSLETTER__
					</label>
				</div>
			</div>
			<div class="col-lg-4-3">
				<input class="btn btn-primary btn-send" type="submit" name="SendRegistration" value="__SEND_MESSAGE__" />
			</div>
			[/IF]
		</div>
	</form>
	<!-- Form
	================================================== -->
	<div class="col-lg-3-1">
		<div class="etiquette">
			__DETAILS_REGISTRATION__
		</div>
		<div class="details-register">
			<p>__TEXT_REGISTRATION__</p>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$("input[type=text]").prop('disabled','disabled');
			$("body").delay(10,function(){
				$("input[type=text]").prop('disabled','');
			});
		});
	</script>
</div>
 