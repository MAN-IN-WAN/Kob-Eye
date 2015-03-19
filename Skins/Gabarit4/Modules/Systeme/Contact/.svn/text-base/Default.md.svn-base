[MODULE Systeme/Structure/Droite]
<div id="Milieu">
	[MODULE Systeme/Ariane]
	<h1>Page Contact</h1>
	[IF [!youyou!]=Envoyer]
		//Verification des informations du formulaire
		[!C_Error:=0!]
		[IF [!C_Nom!]=][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Prenom!]=][!C_Prenom_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Mail!]=][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
		[IF [!C_Tel!]!=||[!C_TelP!]!=][ELSE][!C_Tel_Error:=1!][!C_Error:=1!][/IF]
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
					[IF [!C_Tel_Error!]]<li>Votre n&deg; de t&eacute;l&eacute;phone</li>[/IF]
					[IF [!C_Sujet_Error!]]<li>Le sujet de votre demande</li>[/IF]
					[IF [!C_Mess_Error!]]<li>Merci de laisser un message</li>[/IF]
				</ul>
			</div>
		[ELSE]
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]Demande d'informations[/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|Body]
				[PARAM]
					<html>
						<body>
							<table width="500" cellpadding="5" cellspacing="5">
								<tr>
									<td>
										<br />
										<font face="arial" color="#000000" size="2">
										<strong>Envoy&eacute; par</strong> : <span style="text-transform:uppercase;">[!C_Nom!] </span> [!C_Prenom!]<br/>
										//<strong>Num&#233;ro de t&#233;l&#233;phone</strong> : [!C_Tel!]<br/>
										<strong>Adresse e-mail</strong> : [!C_Mail!]<br/>
										<strong>Sujet</strong> : [!C_Sujet!]<br/>
										<strong>Message</strong> : [!C_Mess!]<br /></font>
									</td>
								</tr>
							<table>
						</body>
					</html>
				[/PARAM]
			[/METHOD]
			[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
			[METHOD LeMail|BuildMail][/METHOD]
			[METHOD LeMail|Send][/METHOD]
			//Mail de confirmation
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]Message du site : [UTIL STRIPSLASHES][!C_Sujet!][/UTIL][/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|Body]
				[PARAM]
					[BLOC Mail]
						Bonjour [!C_Prenom!] [!C_Nom!],<br />Nous avons bien re&ccedil;u votre demande par email et nous vous remercions de votre confiance.<br />Nous allons traiter votre demande d&egrave;s que possible.
					[/BLOC]
				[/PARAM]
			[/METHOD]
			[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
			[METHOD LeMail|BuildMail][/METHOD]
			[METHOD LeMail|Send][/METHOD]
			<h3>Message envoy&eacute; avec succ&egrave;s.<br />Un mail de confirmation vous a &eacute;t&eacute; adress&eacute;.</h3>
		[/IF]
	[/IF]
	[IF [!youyou!]!=Envoyer||[!C_Error!]]//Si le formulaire est envoye avec succes
		<form id="FormContact" enctype="application/x-www-form-urlencoded"  method="post" action="">
			<div class="LigneForm">
				<label>Nom*</label>
				<input type="text" name="C_Nom"  value="[!C_Nom!]" style="text-transform:uppercase;" class="[IF [!C_Nom_Error!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>Pr&eacute;nom*</label>
				<input type="text" name="C_Prenom" value="[!C_Prenom!]" class="[IF [!C_Prenom_Error!]]Error[/IF]"/>		
			</div>
			<div class="LigneForm">
				<label>N&ordm; de t&eacute;l&eacute;phone</label>
				<input type="text" name="C_Tel" value="[!C_Tel!]" class="[IF [!C_Tel_Error!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>Adresse e-mail*</label>
				<input type="text" name="C_Mail" value="[!C_Mail!]" class="[IF [!C_Mail_Error!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>Sujet*</label>
				<input type="text" name="C_Sujet" value="[!C_Sujet!]" class="[IF [!C_Sujet_Error!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>Votre message*</label>
				<textarea cols="5" rows="5" name="C_Mess" class="[IF [!C_Mess_Error!]]Error[/IF]">[!C_Mess!]</textarea>
			</div>
			<div class="Obligatoire">
				<p>Les champs marqu&eacute;s (\*) sont obligatoires.</p>
			</div>
			<div>
				<input type="submit" name="youyou" value="Envoyer" class="BtnContact" />
			</div>
			<p class="Italic">Conform&eacute;ment &agrave; la loi n°78-17 du 6 janvier 1978 relative &agrave; l'informatique, aux fichiers et aux libert&eacute;s, vous disposez d'un droit d'acc&egrave;s, de rectification, de suppression des informations qui vous concernent, que vous pouvez exercer en vous adessant &agrave; : La Bambouseraie - Domaine de Prafrance - 30140 - G&eacute;n&eacute;rargues Anduze.</p>
		</form>
	[/IF]
</div>
<div class="Clear"></div>