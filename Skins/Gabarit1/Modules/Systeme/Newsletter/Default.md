<div id="Newsletter">
	<h3>Inscription &agrave; la newsletter</h3>
	[IF [!FormSys_Valid!]=OK&&[!Form_Mail!]&&[!Utils::isMail([!Form_Mail!])!]]
		//On compte le nombre de mails enregistres et on exclut les doublons
		[COUNT Newsletter/GroupeEnvoi/1/Contact/Email=[!Form_Mail!]|Test]
		[IF [!Test!]]
			<div class="BlocError"><p>Vous &ecirc;tes d&eacute;j&agrave; inscrit(e) !<p></div>
		[ELSE]
		//creation de lobjet contact a enregistrer a la newsletter
			[OBJ Newsletter|Contact|Con]
			[METHOD Con|Set]
				[PARAM]Email[/PARAM]
				[PARAM][!Form_Mail!][/PARAM]
			[/METHOD]
			[METHOD Con|AddParent]
				[PARAM]Newsletter/GroupeEnvoi/1[/PARAM]
			[/METHOD]
			[METHOD Con|Save][/METHOD]
			//[!DEBUG::Con!]
		//Envoi du mail lorsque le mail est enregistre
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]Enregistrement a la newsletter[/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|To][PARAM][!Form_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|Body]
				[PARAM]
					[BLOC Mail]
						<br />Bonjour,<br />f&eacute;licitation, vous &ecirc;tes inscrit(e) &agrave; notre newsletter.<br />
					[/BLOC]
				[/PARAM]
			[/METHOD]
			[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
			[METHOD LeMail|BuildMail][/METHOD]
			[METHOD LeMail|Send][/METHOD]
		//Message de confirmation ou d erreur
			<p class="Message">Vous &ecirc;tes d&eacute;sormais inscrit(e) <br />&agrave; la newsletter.</p>
		[/IF]
	[ELSE]
		<form action="#Newsletter" method="post" >
			[IF [!Utils::isMail([!Form_Mail!])!]!=1&&[!FormSys_Valid!]=OK&&[!Form_Mail!]!=]
				<div class="BlocError"><p>Veuillez saisir une adresse email valide.</p></div>
			[/IF]
			[IF [!Form_Mail!]=&&[!FormSys_Valid!]=OK]
				<div class="BlocError"><p>Veuillez saisir votre adresse email.</p></div>
			[/IF]
			<div class="LigneForm">
				<input type="text" name="Form_Mail" value="E-mail" onclick="this.value='';" class="MailNew" />
				<input type="submit" name="FormSys_Valid" value="OK" />
			</div>
		</form>
	[/IF]
	<a href="/Desinscription-newsletter" title="D&eacute;sinscription Newsletter">Se d&eacute;sinscrire</a>
</div>

