[STORPROC [!Chemin!]|Cat|0|1][/STORPROC]
[HEADER]
	<link rel="canonical" href="[!Domaine!]/[!Lien!]" />
[/HEADER]
[TITLE]GamesAvenue[/TITLE]
[DESCRIPTION]GamesAvenue[/DESCRIPTION]
<!--- colonne de gauche + le contenu -->
<div class="colonneGauche">
	[MODULE Redaction/Navigation]
//	[MODULE Publicite/PubColonne]
	[MODULE Boutique/Interface/DernierAvis]
</div> <!-- fin colonne gauche-->
<!--- contenu central -->
<div class="centre">
	<div class="contact">
		<div class="Categorie">
			<h1>[!Cat::Nom!]</h1>
			<div class="Description">[!Cat::Chapo!]</div>
			<div>
				[IF [!Cat::Description!]]
					<p>[!Cat::Description!]</p>
				[/IF]
			</div>
		</div>
		<div class="Article">
			[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art|0|10|Id|ASC]	
				<div class="ArtText" style="margin-bottom:10px;padding-top:5px;">
					<p style="display:block;border-bottom:1px solid #ff0000;font-family:Arial,verdana;padding:0 0 10px 0;">[!Art::Contenu!]</p>
				</div>		
			[/STORPROC]
			<p>GamesAvenue - 109 avenue Charles de Gaulle - 84130 LE PONTET</p>
			<p>Tel :&nbsp;&nbsp;+33 (0) - Fax : +33 (0) </p>
			[IF [!youyou!]=ValiContact]
				//Verification des informations du formulaire
				[!C_Error:=0!]
				[IF [!C_Nom!]][ELSE][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
				[IF [!C_Mail!]][ELSE][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
				[IF [!C_Tel!]][ELSE][!C_Tel_Error:=1!][!C_Error:=1!][/IF]
				[IF [!C_Objet!]][ELSE][!C_Objet_Error:=1!][!C_Error:=1!][/IF]
				[IF [!C_Mess!]][ELSE][!C_Mess_Error:=1!][!C_Error:=1!][/IF]	
				[IF [!C_TypeObjet!]][ELSE][!C_TypeObjet_Error:=1!][!C_Error:=1!][/IF]	
				//Si il y a des erreurs, on les affiche
				[IF [!C_Error!]]
					<div class="BlocError">
						<p>Veuillez remplir les champs obligatoires suivants :</p>
						<ul style="padding-left:0;margin-left:0;">
							[IF [!C_Nom_Error!]]<li style="padding-left:0;margin-left:0;">Merci de renseigner votre Nom</li>[/IF]
							//[IF [!C_Prenom_Error!]]<li style="padding-left:0;margin-left:0;">Merci de renseigner votre pr&eacute;nom</li>[/IF]
							[IF [!C_Mail_Error!]]<li style="padding-left:0;margin-left:0;">Merci de renseigner votre adresse email</li>[/IF]
							[IF [!C_Tel_Error!]]<li style="padding-left:0;margin-left:0;">Merci de renseigner votre n&deg; de t&eacute;l&eacute;phone</li>[/IF]
							[IF [!C_TypeObjet_Error!]]<li style="padding-left:0;margin-left:0;">Merci de choisir l'objet de votre demande</li>[/IF]
							[IF [!C_Objet_Error!]]<li style="padding-left:0;margin-left:0;">Merci de renseigner le sujet de votre demande</li>[/IF]
							[IF [!C_Mess_Error!]]<li style="padding-left:0;margin-left:0;">Merci de laisser votre message</li>[/IF]
						</ul>
					</div>
				[ELSE]
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM][!C_TypeObjet!] : [!C_Objet!][/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
					[METHOD LeMail|Body]
						[PARAM]
							[BLOC Mail]
								<font face="arial" color="#000000" size="2">
								<strong>Adresse Ip</strong> : <span><a href="http://geotool.flagfox.net/?ip=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
								<strong>Objet de la demande</strong> : [!C_TypeObjet!]<br/>
								<strong>Envoy&eacute; par</strong> : <span style="text-transform:uppercase;">[!C_Nom!] </span><br/>
								<strong>[!C_Prenom!]</strong><br/>
								<strong>Num&#233;ro de t&#233;l&#233;phone</strong> : [!C_Tel!]<br/>
								<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>
								<strong>Sujet</strong> : [!C_Objet!]<br/>
								<strong>Message</strong> : [!C_Mess!]<br /></font>
							[/BLOC]
						[/PARAM]
					[/METHOD]
					[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
					//Mail de confirmation
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM]Games Avenue : votre message[/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
					[METHOD LeMail|Body]
						[PARAM]
							[BLOC Mail]
								Bonjour [!C_Prenom!] [!C_Nom!],<br />nous avons bien re&ccedil;u votre demande par email et vous remercions de votre confiance.<br />Nous traitons votre demande dans les plus brefs d&eacute;lais.
								<hr />
								Hello [!C_Prenom!] [!C_Nom!],<br />thank you for the email you send us.<Br />We will process your request as soon as possible.
							[/BLOC]
						[/PARAM]
					[/METHOD]
					[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
					<br />
					<h2>Message envoy&eacute; avec succ&egrave;s.</h2>
					<br />
					<p>Un mail de confirmation vous a &eacute;t&eacute; adress&eacute;.</p><br /><br />
				[/IF]
			[/IF]
			[IF [!youyou!]!=ValiContact||[!C_Error!]]//Si le formulaire est envoye avec succes
				<form id="FormContact" enctype="application/x-www-form-urlencoded"  method="post" action="">
					<div class="LigneForm">
						<label class="[IF [!C_Nom_Error!]]ErrorLab[/IF]">Nom <span style="color:#ff0000;">*</span></label>
						<input type="text" name="C_Nom"  [IF [!C_Nom!]=] value="[!C_Nom!]" [ELSE] value="[!C_Nom!]" [/IF] style="text-transform:uppercase;width:250px;" class="[IF [!C_Nom_Error!]]Error[/IF]" />
					</div>
					<div class="LigneForm">
						<label>Pr&eacute;nom</label>
						<input type="text" name="C_Prenom" [IF [!C_Prenom!]=] value="[!C_Prenom!]" [ELSE] value="[!C_Prenom!]" [/IF] style="width:250px;" />
					</div>
					<div class="LigneForm">
						<label class="[IF [!C_Tel_Error!]]ErrorLab[/IF]">Num&eacute;ro de t&eacute;l&eacute;phone <span style="color:#ff0000;">*</span></label>
						<input type="text" name="C_Tel" [IF [!C_Tel!]=] value="[!C_Tel!]" [ELSE] value="[!C_Tel!]" [/IF] style="width:250px;"  class="[IF [!C_Tel_Error!]]Error[/IF]"/>
					</div>
					<div class="LigneForm">
						<label class="[IF [!C_Mail_Error!]]ErrorLab[/IF]">Adresse e-mail <span style="color:#ff0000;">*</span></label>
						<input type="text" name="C_Mail" [IF [!C_Mail!]=] value="[!C_Mail!]" [ELSE] value="[!C_Mail!]" [/IF] style="width:250px;" class="[IF [!C_Mail_Error!]]Error[/IF]" />
					</div>
					<div class="LigneForm">
						<label class="[IF [!C_TypeObjet_Error!]]ErrorLab[/IF]">Objet de la demande <span style="color:#ff0000;">*</span></label>
						<select name="C_TypeObjet" class="[IF [!C_TypeObjet_Error!]]Error[/IF]" style="width:250px;">
							<option value="">choisissez</option>
							<option value="demande de devis" [IF [!C_TypeObjet!]="demande de devis"] selected="selected"[/IF]>Inscription</option>
							<option value="demande de partenariat" [IF [!C_TypeObjet!]="demande de partenariat"] selected="selected"[/IF]>demande de partenariat</option>
							<option value="demande de stage/emploi" [IF [!C_TypeObjet!]="demande de stage/emploi"] selected="selected"[/IF]>Paiement</option>
							<option value="autre" [IF [!C_TypeObjet!]=autre] selected="selected"[/IF]>autre</option>
						</select>
					</div>
					<div class="LigneForm">
						<label class="[IF [!C_Objet_Error!]]ErrorLab[/IF]">Sujet <span style="color:#ff0000;">*</span></label>
						<input type="text" name="C_Objet" [IF [!C_Objet!]=] value="[!C_Objet!]" [ELSE] value="[!C_Objet!]" [/IF] style="width:250px;" class="[IF [!C_Objet_Error!]]Error[/IF]" >
					</div>
					<div class="LigneForm">
						<label class="[IF [!C_Mess_Error!]]ErrorLab[/IF]">Message <span style="color:#ff0000;">*</span></label>
						<textarea cols="" rows="6" name="C_Mess" class="[IF [!C_Mess_Error!]]Error[/IF]" style="width:250px;" >[IF [!C_Mess!]=][!C_Mess!][ELSE][!C_Mess!][/IF]</textarea>
					</div>
					<div class="LigneForm">
						<input type="hidden" name="youyou" value="ValiContact" />
						<div class="btnGrisFonce" style="padding-top:10px;">
							<div class="btnGrisFonceGauche">&nbsp;</div>
							<div class="btnGrisFonceCentre"><input  type="submit" name="" value="Envoyer"  title="Envoyer le mail" class="btnGrisFonceCentre" style="width:80px;"/></div>
							<div class="btnGrisFonceDroite">&nbsp;</div>
						</div>						
						



					</div>
					<div class="LigneForm">
						Les champs marqu&eacute;s (\*) sont obligatoires.
					</div>
					<div class="LigneForm">
						Conform&eacute;ment &agrave; la loi n°78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, vous disposez d'un droit d'acc&egrave;s, de rectification, de suppression des informations qui vous concernent, que vous pouvez exercer en vous adessant &agrave; :
						<p>GamesAvenue - 109 avenue Charles de Gaulle - 84130 LE PONTET</p>
						<p>Tel :&nbsp;&nbsp;+33 (0) - Fax : +33 (0) </p>
					</div>
				</form>
			[/IF]
		</div>
	</div>

</div>
