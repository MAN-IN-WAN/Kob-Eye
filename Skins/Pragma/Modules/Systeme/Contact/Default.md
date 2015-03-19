[IF [!C_Sujet!]!=]
	<h1>Renseignements sur la vente d'appartements neufs</h1>
[ELSE]
	[IF [!C_User!]=]
		<h1>Vous désirez un renseignement</h1>
	[ELSE]
		[STORPROC Systeme/User/[!C_User!]|Us][/STORPROC]
		<h1>Vous souhaitez entrer en contact avec [!Us::Prenom!] [!Us::Nom!]</h1>
	[/IF]
[/IF]
[IF [!Envoi!]=EnvoiForm]
	//Verification des informations du formulaire
	[!C_Error:=0!]
	// version 2012 il  nous est demandé de laisser le formulaire le plus ouvert possible
	[IF [!C_Nom!]=][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Tel!]=][!C_Tel_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Mail!]=][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	//Si il y a des erreurs, on les affiche
	[IF [!C_Error!]]
		//Affichage des messages d erreur
		<div class="BlocError" > 
			<p>Veuillez remplir les champs obligatoires suivants :</p>
			<ul>
				[IF [!C_Nom_Error!]]<li>Votre nom</li>[/IF]
				[IF [!C_Mail_Error!]]<li>Votre adresse e-mail</li>[/IF]
				[IF [!C_Tel_Error!]]<li>Votre téléphone</li>[/IF]
			</ul>
		</div>
	[ELSE]
		// création du contact si inexistant
		[STORPROC Newsletter/GroupeEnvoi/1/Contact/Email=[!C_Mail!]|Ctc|0|1]
			[NORESULT]
				[OBJ Newsletter|Contact|Con]
				[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Libelle[/PARAM][PARAM][!C_Sexe!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Telephone[/PARAM][PARAM][!C_Tel!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Transaction[/PARAM][PARAM][!C_Transac!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Bien[/PARAM][PARAM][!C_Sujet!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Budget[/PARAM][PARAM][!C_Budget!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Campagne[/PARAM][PARAM][!C_Add!][/PARAM][/METHOD]
				[IF [!C_Add!]]
					[METHOD Con|Set][PARAM]AddWord[/PARAM][PARAM]1[/PARAM][/METHOD]
				[/IF]
				[METHOD Con|Set][PARAM]Adresse[/PARAM][PARAM][!C_Adress!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]CodePostal[/PARAM][PARAM][!C_CodPos!][/PARAM][/METHOD]
				[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
				[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/1[/PARAM][/METHOD]
				[METHOD Con|Save][/METHOD]
			[/NORESULT]
		[/STORPROC]

		// Enregistrement du message
		[STORPROC Newsletter/GroupeEnvoi/1/Contact/Email=[!C_Mail!]|Ctc|0|1]
			[OBJ Newsletter|Reception|Rec]
			[METHOD Rec|Set]
				[PARAM]Contenu[/PARAM]
				[PARAM][!C_Mess!][/PARAM]
			[/METHOD]
			[METHOD Rec|Set]
				[PARAM]Sujet[/PARAM]
				[PARAM]Fiche Contact : [!C_Sujet!][/PARAM]
			[/METHOD]
			[METHOD Rec|AddParent]
				[PARAM]Newsletter/Contact/[!Ctc::Id!][/PARAM]
			[/METHOD]
			[METHOD Rec|Save][/METHOD]
		[/STORPROC]
		
		//modification suite au mail de xavier delcher du 26/07/2012
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Demande de contact site Pragma: [UTIL STRIPSLASHES][!C_Sujet!][/UTIL][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
		[IF [!C_User!]=]
			[IF [!C_Sujet!]=Résidence - Les Teinturiers||[!C_Sujet!]=Les Teinturiers]
				[METHOD LeMail|To][PARAM]c.vidal@alcyone-immobilier.fr[/PARAM][/METHOD]
				[METHOD LeMail|Cc][PARAM]alexandra.bidon@sogeprom.com[/PARAM][/METHOD]
				[METHOD LeMail|Cc][PARAM][!CONF::MODULE::SYSTEME::CONTACTCOPIE!][/PARAM][/METHOD]
			[ELSE]
				//modification suite au mail de xavier delcher du 27/08/2012
				//[METHOD LeMail|To][PARAM]didier@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
				[METHOD LeMail|Cc][PARAM][!CONF::MODULE::SYSTEME::CONTACTCOPIE!][/PARAM][/METHOD]
			[/IF]
		[/IF]
		[IF [!C_User!]!=]
			[METHOD LeMail|To][PARAM][!Us::Mail!][/PARAM][/METHOD]
			[METHOD LeMail|Cc][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|Cc][PARAM][!CONF::MODULE::SYSTEME::CONTACTCOPIE!][/PARAM][/METHOD]
		[/IF]
		[IF [!C_Residence!]!=]
			[!Lemail:=!][!LaResidence:=!]
			[STORPROC ParcImmobilier/Residence/[!C_Residence!]|Resid]
				[!Lemail:=[!Resid::MailContactResidence!]!]
				[!LaResidence:=Contact au sujet de [!Resid::Titre!]!]
			[/STORPROC]
			[IF [!Lemail!]!=][METHOD LeMail|To][PARAM][!Lemail!][/PARAM][/METHOD][/IF]
		[/IF]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					<div>[!Us::Mail!]
						<u style="font-weight:bold;">Envoy&eacute; par</u> : [!C_Sexe!] <span style="text-transform:uppercase;">[!C_Nom!] </span> [!C_Prenom!]<br/>
						//<u style="font-weight:bold;">Int&eacute;ress&eacute;[IF [!C_Sexe!]!=Mr]e[/IF] par </u> : [!C_Transac!]<br/>
						<u style="font-weight:bold;">Adresse</u> : [!C_Adress!]<br/>
						<u style="font-weight:bold;">Code postal / Ville</u> : [!C_CodPos!] [!C_Ville!]<br/>
						[IF [!C_Tel!]!=]
							<u style="font-weight:bold;">Num&eacute;ro de t&eacute;l&eacute;phone</u> : [!C_Tel!]<br/>
						[/IF]
						<u style="font-weight:bold;">Adresse e-mail</u> : [!C_Mail!]<br/>
						[IF [!C_User!]=]
							<u style="font-weight:bold;">Nature achat r&eacute;sidence</u> : [!C_Nature!]<br />
							<u style="font-weight:bold;">Type de bien</u> : [!C_Appart!]<br/>
							<u style="font-weight:bold;">Budget</u> : [!C_Budget!]<br/>
							<u style="font-weight:bold;">Demande de doc papier</u> : [IF [!C_doc_papier!]=1]Oui[ELSE]Non[/IF]<br/>
						[/IF]
						<u style="font-weight:bold;">Sujet</u> : [!C_Sujet!]<br/>
						<u style="font-weight:bold;">Message</u> :[!C_Mess!]<br />[!LaResidence!]
						
					</div>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]

		//Mail de confirmation a l utilisateur
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!CONF::MODULE::SYSTEME::SOCIETE!] : [UTIL STRIPSLASHES][!C_Sujet!][/UTIL][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[IF [!C_User!]=]
			[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[ELSE]
			[METHOD LeMail|To][PARAM][!Us::Mail!][/PARAM][/METHOD]
		[/IF]
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
	<form class="FormContact" enctype="multipart/form-data"  method="post"  action="/[!Lien!]">
		<input type="hidden" name="C_Add" value="[IF [!C_Add!]!=][!C_Add!][ELSE][IF [!add!]!=][!add!][/IF][/IF]" />
		<input type="hidden" name="C_User"  value="[!C_User!]" />
		<input type="hidden" name="C_Residence"  value="[!C_Residence!]" />
		<div class="InfosComplete">
			<div class="InfosEtatCivil" [IF [!C_User!]!=]style="border-right:1px dotted #525252;padding-right:10px;"[/IF]>
				<div class="LigneForm"><h3>Merci de remplir les champs ci-dessous afin que nous puissons prendre contact avec vous.</h3></div>
				<div class="LigneForm">
					<div class="BoxCheck">
						<label class="Nature">Monsieur</label>
						<input type="radio" name="C_Sexe"  value="Mr" checked="checked" style="border:none;width:auto"/>
					</div>
					<div class="BoxCheck">
						<label class="Nature">Madame</label>
						<input type="radio" name="C_Sexe"  value="Mme" style="border:none;width:auto" [IF [!C_Sexe!]=Mme] checked="checked" [/IF]/>
					</div>
					<div class="BoxCheck">
						<label class="Nature">Mademoiselle</label>
						<input type="radio" name="C_Sexe"  value="Mlle" style="border:none;width:auto" [IF [!C_Sexe!]=Mlle] checked="checked" [/IF]/>
					</div>
				</div>
				<div class="LigneForm">
					<label>Votre nom <span class="obligatoire">*</span></label>
					<input type="text" name="C_Nom"  value="[!C_Nom!]" style="text-transform:uppercase;" [IF [!C_Nom_Error!]]class="inputError"[/IF] />
				</div>
				<div class="LigneForm">
					<label>Votre pr&eacute;nom</label>
					<input type="text" name="C_Prenom" value="[!C_Prenom!]"  />
				</div>
				<div class="LigneForm">
					<label >Votre adresse </label>
					<input type="text" name="C_Adress" value="[!C_Adress!]"  />
				</div>
				<div class="LigneForm">
					<label >Code postal</label>
					<input type="text" name="C_CodPos" value="[!C_CodPos!]" class="CodePos" />
				</div>
				<div class="LigneForm">
					<label>Ville</label>
					<input type="text" name="C_Ville" value="[!C_Ville!]" />
				</div>
				<div class="LigneForm">
					<label >N&ordm; de t&eacute;l&eacute;phone <span class="obligatoire">*</span></label>
					<input type="text" name="C_Tel" value="[!C_Tel!]" [IF [!C_Tel_Error!]]class="inputError"[/IF] />
				</div>
				<div class="LigneForm">
					<label >Adresse e-mail <span class="obligatoire">*</span></label>
					<input type="text" name="C_Mail" value="[!C_Mail!]" [IF [!C_Mail_Error!]]class="inputError"[/IF]  />
				</div>
			</div>
			[IF [!C_User!]=]
				<div class="InfosProjetImmobilier">
					<div class="LigneForm"><h3>Espace réservé à toutes vos questions. Nous vous garantissons une réponse rapide.</h3></div>
					<div class="LigneForm">
						<label class="Partie2">Votre budget </label>
						<select name="C_Budget" style="[IF [!C_Budget_Error!]]background-color:#FFDE01;[/IF]" >
							<option value="Non précisé" selected="selected">Choisissez</option>
							<option value="-120000" [IF [!C_Budget!]=-120000]selected="selected" [/IF]>Moins 120 000&euro;</option>
							<option value="121000/160000" [IF [!C_Budget!]=121000/160000]selected="selected" [/IF]>De 121 000&euro; &agrave; 160 000&euro;</option>
							<option value="161000/190000" [IF [!C_Budget!]=161000/190000]selected="selected" [/IF]>De 161 000&euro; &agrave; 190 000&euro;</option>		
							<option value="191000/260000" [IF [!C_Budget!]=191000/260000]selected="selected" [/IF]>De 191 000&euro; &agrave; 260 000&euro;</option>		
							<option value="261000/350000" [IF [!C_Budget!]=261000/350000]selected="selected" [/IF]>De 261 000&euro; &agrave; 350 000&euro;</option>		
							<option value="+350000" [IF [!C_Budget!]=+350000]selected="selected" [/IF]>Plus de 350 000&euro;</option>
						</select>
					</div>		
					<div class="LigneForm">
						<input type="checkbox" style="border:none;width:auto" [IF [!C_doc_papier!]=1]checked="checked"[/IF] name="C_doc_papier" value="1" />
						Cochez cette case si vous souhaitez recevoir une documentation papier...
					</div>
					<div class="LigneForm">
						<label style="width:auto;">Espace r&eacute;serv&eacute; &agrave; toutes vos questions. Nous vous garantissons une r&eacute;ponse rapide.</label>
					</div>
					<div class="LigneForm">
						<label  class="Partie2">Sujet de la demande</label>
						<input type="text" name="C_Sujet" value="[!C_Sujet!]"  />
					</div>
					<div class="LigneForm">
						<label   class="Partie2" style="vertical-align: top;">Message </label>
						<textarea cols="80" rows="8" name="C_Mess" style="[IF [!C_Mess_Error!]]background-color:#FFDE01;[/IF]" >[!C_Mess!]</textarea>
					</div>
				</div>
			[ELSE]
				// non projet immobilier mais info pour mail interne
				<div class="InfosProjetImmobilier" style="border:none;margin:0;">
					<div class="LigneForm"><h3>Votre message sera envoyé à : <br />[!Us::Prenom!] [!Us::Nom!] - [!Us::Fonction!] </h3></div>
					<div class="LigneForm">
						<label  class="Partie2">Sujet de la demande</label>
						<input type="text" name="C_Sujet" value="[!C_Sujet!]"  />
					</div>
					<div class="LigneForm">
						<label   class="Partie2" style="vertical-align: top;">Message </label>
						<textarea cols="80" rows="12" name="C_Mess" style="[IF [!C_Mess_Error!]]background-color:#FFDE01;[/IF]" >[!C_Mess!]</textarea>
					</div>
				</div>
			[/IF]
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
	</form>
[/IF]
<div class="BasPage" >
	[IF [!Systeme::DefaultLanguage!]=Anglais]
		<div class="Italic">According to the law n°78-17 of January 6th, 1978 relative to the computing, to the files and to the liberties, you have a right of access, rectification, abolition(deletion) of the information which concern you, which you can exercise in you adessant to PRAGMA - Stud Richter-CS 19501 - 80 Place Ernest Granier - 34960 Montpelier Cedex2.</div>
	[ELSE]
		<div class="Italic">Conform&eacute;ment &agrave; la loi n&deg;78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, vous disposez d'un droit d'acc&egrave;s, de rectification, de suppression des informations qui vous concernent, que vous pouvez exercer en vous adessant &agrave; l'agence de Montpellier : 
		<br />PragmA - 80, place Ernest Granier - 34960 Montpellier Cedex2.</div>
	[/IF]
</div>
