<div class="Redaction">
	<h1>Envoyer à un ami</h1>
</div>
[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[IF [!C_Envoi!]=1]
	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!C_Nom!]=][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Prenom!]=][!C_Prenom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Mail!]=][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Sujet!]=][!C_Sujet_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Mess!]][ELSE][!C_Mess_Error:=1!][!C_Error:=1!][/IF]	
	//Si il y a des erreurs, on les affiche
	[IF [!C_Error!]]
		<div class="BlocError">
			<p>Veuillez remplir les champs obligatoires suivants :</p>
			<ul>
				[IF [!C_Nom_Error!]]<li>Votre Nom</li>[/IF]
				[IF [!C_Prenom_Error!]]<li>Votre Pr&eacute;nom </li>[/IF]
				[IF [!C_Mail_Error!]]<li>L'adresse e-mail de votre ami</li>[/IF]
				[IF [!C_Sujet_Error!]]<li>Le sujet de votre demande</li>[/IF]
				[IF [!C_Mess_Error!]]<li>Merci de laisser un message</li>[/IF]
			</ul>
		</div>
	[ELSE]
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM][!C_Sujet!][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONF::GENERAL::INFO::ADMIN_MAIL!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					<font face="arial" color="#000000" size="2">
						<strong>Envoy&eacute; par</strong> : <span style="text-transform:uppercase;">[!C_Nom!] </span> [!C_Prenom!]<br/>
						<strong>Sujet</strong> : [!C_Sujet!]<br/>
						<strong>Message</strong> :  [!Utils::nl2br([!C_Mess!])!]<br />
						<strong>Je vous recommande ce lien</strong> : <a href="[!Domaine!]/[!C_Lien!]">[!Domaine!]/[!C_Lien!]</a>
					</font>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		<br />
		<h3 style="padding-left:20px;font-size:14px;">Message envoy&eacute; avec succ&egrave;s.</h3>
		<br />
	[/IF]
[/IF]
[IF [!C_Envoi!]!=1||[!C_Error!]=1]
	<div class="CpContact">
		<form enctype="application/x-www-form-urlencoded"  method="post" action="/[!Lien!]" id="FormContact">
			<div class="LigneForm" >
				<label>Voici le lien que vous envoyez à un ami
				<input type="text" name="C_Lien"  value="[IF [!C_Lien!]=][!Page!][ELSE][!C_Lien!][/IF]" /></label>
			</div>
			<div class="LigneForm">
				<label>Nom<span class="Obligatoire"> * </span>
				<input type="text" name="C_Nom" value="[!C_Nom!]" style="text-transform:uppercase;" class="[IF [!C_Nom_Error!]]Error[/IF]"/></label>
			</div>
			<div class="LigneForm">
				<label>Prénom<span class="Obligatoire"> * </span>
				<input type="text" name="C_Prenom" value="[!C_Prenom!]" class="[IF [!C_Prenom_Error!]]Error[/IF]"/></label>		
			</div>
			<div class="LigneForm">
				<label>A Envoyer à<span class="Obligatoire"> * </span>
				<input type="text" name="C_Mail" value="[!C_Mail!]" class="[IF [!C_Mail_Error!]]Error[/IF]"/></label>
			</div>
			<div class="LigneForm">
				<label>Sujet<span class="Obligatoire"> * </span>
				<input type="text" name="C_Sujet" value="[IF [!C_Sujet!]=][!Sujet!][ELSE][!C_Sujet!][/IF]" class="[IF [!C_Sujet_Error!]]Error[/IF]"/></label>
			</div>
			<div class="LigneForm">
				<label>Votre message<span class="Obligatoire"> * </span>
				<textarea cols="40" rows="5" name="C_Mess" style="margin-left:0;" class="[IF [!C_Mess_Error!]]Error[/IF]">[!C_Mess!]</textarea></label>
			</div>
			<div class="LigneForm">
				Les champs marqués (<span class="Obligatoire"> * </span>) sont obligatoires.
			</div>
			<div class="BoutonsCentre">
				<input type="hidden" name="C_Envoi" value="1" />
				<input type="submit" name="Valider" value="Envoyer" class="ValiderCpContact" >




			</div>
	
		</form>
	</div>

[/IF]
