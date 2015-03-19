[STORPROC Systeme/User/[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 
[STORPROC Systeme/User/[!Systeme::User::Id!]/Commercial|CCal|0|1]
	[NORESULT]
		[STORPROC ParcImmobilier/Commercial/Referent=1|CCal|0|1][/STORPROC]
	[/NORESULT]
[/STORPROC]

[IF [!C_Sujet!]=]
	<h1>Demande au service commercial</h1>
[ELSE]
	<h1>Demande à [!CCal::Prenom!] <span style="text-transform: uppercase">[!CCal::Nom!]</span></h1>
[/IF]
[IF [!C_Mail!]=][!C_Mail:=[!CLCONN::Mail!]!][/IF]
[IF [!Envoi!]=EnvoiForm]
	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!C_Mail!]=][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	//Si il y a des erreurs, on les affiche
	[IF [!C_Error!]]
		//Affichage des messages d erreur
		<div class="BlocError" > 
			<p>Veuillez remplir les champs obligatoires suivants :</p>
			<ul>
				[IF [!C_Mail_Error!]]<li>Votre adresse e-mail</li>[/IF]
			</ul>
		</div>
	[ELSE]
		[STORPROC Systeme/Group/User/[!CLCONN:Id!]|CLGRP][/STORPROC] 
		//Envoi du mail au service commercial ou au commercial du prescripteur connecté
		[!LeMaildEnvoi:=[!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!]!]
		[IF [!C_Lot!]!=||[!C_Sujet!]!=]
			[!LeMaildEnvoi:=[!CCal::Mail!]!]
		[/IF]
		[LIB PHPMailer|LeMail2]
		[METHOD LeMail2|setFrom][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail2|AddAddress][PARAM][!LeMaildEnvoi!][/PARAM][/METHOD]
		[METHOD LeMail2|set][PARAM]Subject[/PARAM][PARAM]Message de Pragma extranet prescripteurs : [!C_Sujet!][/PARAM][/METHOD]
		[METHOD LeMail2|MsgHTML]
			[PARAM]
				[BLOC Mail]
					<div>[!Us::Mail!]
						<u style="font-weight:bold;">Envoy&eacute; par</u> : [!CLCONN::Prenom!] [!CLCONN::Nom!] 
						<u style="font-weight:bold;">Adresse e-mail</u> : [!C_Mail!]<br/>
						<u style="font-weight:bold;">Sujet</u> : [!C_Sujet!]<br/>
						<u style="font-weight:bold;">Message</u> :<br />[!C_Mess!]<br /><br />
					</div>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail2|Send][/METHOD]



		//Mail de confirmation a l utilisateur
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de Pragma extranet prescripteurs : [!C_Sujet!][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!LeMaildEnvoi!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!LeMaildEnvoi!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					[IF [!Systeme::DefaultLanguage!]=Anglais]
						Hello [!C_Prenom!] [!C_Nom!],<br /> We received your demand by email and we thank you for your confidence.<br />We are going to handle your demand as soon as possible.
					[ELSE]
						Bonjour [!C_Prenom!] [!C_Nom!],<br />Nous avons bien re&ccedil;u votre demande par email et nous vous remercions de votre confiance.<br />Nous allons traiter votre demande d&egrave;s que possible.
					[/IF]
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		<div class="BlocEnvoi">
			<h3>Message envoy&eacute; avec succ&egrave;s.<br />Un mail de confirmation vous a &eacute;t&eacute; envoy&eacute;.</h3>
		</div>
	[/IF]
[/IF]

[IF [!Envoi!]!=EnvoiForm||[!C_Error!]]
	[IF [!LeSujet!]=lot]
		[STORPROC ParcImmobilier/Lot/[!LeLot!]|LS|0|1][/STORPROC] 
		[STORPROC ParcImmobilier/Residence/[!LaResidence!]|RESI|0|1][!RS::Titre!][/STORPROC] 
		[!LeMess:=Concerne le lot - [!LS::Identifiant!] - de la résidence [!RESI::Titre!]!] 
		[IF [!C_Sujet!]=]
			[!C_Sujet:=Demande du Prescripteur - [!CLCONN::Prenom!]-[!CLCONN::Nom!]!]
		[/IF]
		[IF [!C_Mess!]=]
			[!C_Mess:=[!LeMess!]!]
		[/IF]
	[/IF]
	[IF [!C_Mail!]=][!C_Mail:=[!CLCONN::Mail!]!][/IF]
	<div style="display:block;overflow:hidden;">
		<form class="FormContact" enctype="multipart/form-data"  method="post"  action="/[!Lien!]" style="border:none;">
			<div style="float:left;padding-right:78px;border-right:1px solid #0070BA;">
				<input type="hidden" name="C_Add" value="[IF [!C_Add!]!=][!C_Add!][ELSE][IF [!add!]!=][!add!][/IF][/IF]" />
				<input type="hidden" name="C_User"  value="[!C_User!]" />
				<input type="hidden" name="C_Lot"  value="[IF [!C_User!]=][!LeLot!][ELSE][!C_User!][/IF]" />
				<div class="InfosComplete">
					<div class="LigneForm"><h3>Merci de remplir les champs ci-dessous</h3></div>
					<div class="LigneForm">
						<label >Adresse e-mail <span class="obligatoire">*</span></label>
						<input type="text" name="C_Mail" value="[!C_Mail!]" [IF [!C_Mail_Error!]]class="inputError"[/IF]  />
					</div>
					<div class="LigneForm">
						<label  class="Partie2">Sujet de la demande</label>
						<input type="text" name="C_Sujet" value="[!C_Sujet!]"  />
					</div>
					<div class="LigneForm">
						<label   class="Partie2" style="vertical-align: top;">Message </label>
						<textarea cols="80" rows="8" name="C_Mess" style="[IF [!C_Mess_Error!]]background-color:#FFDE01;[/IF]" >[!C_Mess!]</textarea>
					</div>
					<div class="LigneForm" style="padding-left:10px;">
						Les champs marqu&eacute;s (<span class="obligatoire">*</span>) sont obligatoires.
					</div>
					<div class="LigneForm ">
						<input type="hidden" name="Envoi" value="EnvoiForm" />
						<div class="lienBtnCnt">
							<input type="submit" value="Envoyer">
						</div>
					</div>
				</div>
			</div>
			<div class="encoursPrescripteur" style="float:right;overflow:hidden;">
				[COMPONENT ParcImmobilier/PrescripteurEncours/]
			</div>
		</form>
	</div>
[/IF]
<div class="BasPage" >
	[IF [!Systeme::DefaultLanguage!]=Anglais]
		<div class="Italic">According to the law n°78-17 of January 6th, 1978 relative to the computing, to the files and to the liberties, you have a right of access, rectification, abolition(deletion) of the information which concern you, which you can exercise in you adessant to PRAGMA - Stud Richter-CS 19501 - 80 Place Ernest Granier - 34960 Montpelier Cedex2.</div>
	[ELSE]
		<div class="Italic">Conform&eacute;ment &agrave; la loi n&deg;78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, vous disposez d'un droit d'acc&egrave;s, de rectification, de suppression des informations qui vous concernent, que vous pouvez exercer en vous adessant &agrave; l'agence de Montpellier : 
		<br />PragmA - 80, place Ernest Granier - 34960 Montpellier Cedex2.</div>
	[/IF]
</div>

