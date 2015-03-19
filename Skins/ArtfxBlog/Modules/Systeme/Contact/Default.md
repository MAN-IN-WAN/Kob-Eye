<div id="Contact">
	<h1>Contact</h1>
	[IF [!FormContact!]=OK]
		//Verification des informations du formulaire
		[!C_Error:=0!]
		[IF [!C_Nom!]=][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Prenom!]=][!C_Prenom_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Mail!]=][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Sujet!]=][!C_Sujet_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Mess!]=][!C_Mess_Error:=1!][!C_Error:=1!][/IF]	
		//Si il y a des erreurs, on les affiche
		[IF [!C_Error!]]
			<div class="BlocError">
				<p>Veuillez remplir les champs obligatoires suivants :</p>
				<ul>
					[IF [!C_Nom_Error!]]<li>Votre Nom</li>[/IF]
					[IF [!C_Prenom_Error!]]<li>Votre Pr&eacute;nom </li>[/IF]
					[IF [!C_Mail_Error!]]<li>Votre adresse email</li>[/IF]
					[IF [!C_Sujet_Error!]]<li>Le sujet de votre demande</li>[/IF]
					[IF [!C_Mess_Error!]]<li>Merci de laisser un message</li>[/IF]
				</ul>
			</div>
		[ELSE]
			[MODULE Systeme/Contact/MailContact]	
			<h4 style="margin-left:0;">Message envoy&eacute; avec succ&egrave;s.<br />Un mail de confirmation vous a &eacute;t&eacute; envoy&eacute;.</h4>
		[/IF]
	[/IF]
	[IF [!FormContact!]!=OK||[!C_Error!]]
		//Si le formulaire n est pas envoye
		[LANG Systeme/Interface/Bloc/Textes]
		<form enctype="application/x-www-form-urlencoded"  method="post" action="" >
			//<p class="Date"><span class="Bold">[!CONF::MODULE::SYSTEME::SOCIETE!]</span><br />[!CONF::MODULE::SYSTEME::ADRESSE!]<br />[!CONF::MODULE::SYSTEME::VILLE!]<br />[!CONF::MODULE::SYSTEME::TEL!]<br />[!CONF::MODULE::SYSTEME::FAX!]</p>
			<p class="Bold" style="margin-bottom:30px;">Si vous avez des questions ou besoin d'informations compl&eacute;mentaires, contactez-nous par l'intermédiaire du formulaire ci-dessous :</p>
			<div class="LigneForm">
				<label>[!TXT_BRO_NOM!]</label>
				<input type="text" name="C_Nom"  value="[!C_Nom!]" style="text-transform:uppercase;" class="[IF [!C_Nom!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_PRENOM!]</label>
				<input type="text" name="C_Prenom" value="[!C_Prenom!]" class="[IF [!C_Prenom!]]Error[/IF]" />
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_ADRESSE!]</label>
				<input type="text" name="C_Adresse" value="[!C_Adresse!]" class="[IF [!C_Adresse!]]Error[/IF]" />
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_CODPOS!]</label>
				<input type="text" name="C_CodPos" value="[!C_CodPos!]" class="[IF [!C_CodPos!]]Error[/IF]" />
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_VILLE!]</label>
				<input type="text" name="C_Ville" value="[!C_Ville!]" class="[IF [!C_Ville!]]Error[/IF]" />
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_PAYS!]</label>
				<input type="text" name="C_Pays" value="[!C_Pays!]" class="[IF [!C_Pays!]]Error[/IF]" />
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_TEL!]</label>
				<input type="text" name="C_Tel" value="[!C_Tel!]" class="[IF [!C_Tel!]]Error[/IF]"/>
			</div>		
			<div class="LigneForm">
				<label>[!TXT_BRO_MAIL!]</label>
				<input type="text" name="C_Mail" value="[!C_Mail!]" class="[IF [!C_Mail!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_SUJET!] *</label>
				<input type="text" name="C_Sujet" value="[!C_Sujet!]" class="[IF [!C_Sujet!]]Error[/IF]" id="Sujet"/>
			</div>
			<div class="LigneForm">
				<label>[!TXT_BRO_MESSAGE!]</label>
				<textarea rows="5" cols="5" name="C_Mess" class="[IF [!C_Mess!]]Error[/IF]">[!C_Mess!]</textarea>
			</div>
			<p class="Obligatoire">[!TXT_BRO_OBLIG!]</p>			
			<div>
				<input type="hidden" name="FormContact" id="C_Formulaire" value="OK" />
				<input type="submit" value="" class="BtnComment" name="youyou"/>
			</div>
		</form>
		<p class="Italic">Conform&eacute;ment &agrave; la loi n&deg;78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, vous disposez d'un droit d'acc&egrave;s, de rectification, de suppression des informations qui vous concernent, que vous pouvez exercer en vous adessant &agrave; : ArtFx - 921, rue de la Croix de Lavit - 34090 Montpellier.</p>
	[/IF]
</div>