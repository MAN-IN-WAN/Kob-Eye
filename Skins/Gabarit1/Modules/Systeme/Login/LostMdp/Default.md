[MODULE Systeme/Structure/Droite]
<div id="Milieu">
	<h1>Retrouver mon mot de passe</h1>
	<form method="post" action="" id="OubliMdp">	
		[IF [!retrouvemdp!]="ok"]
			[STORPROC Systeme/User/Mail=[!Mail!]|U]
				[LIB Random|Rand]
				[!MaVar:=[!Rand::Generate(8)!]!]
				[METHOD U|Set]
					[PARAM]Pass[/PARAM]
					[PARAM][!MaVar!][/PARAM]
				[/METHOD]
				//[!DEBUG::U!]
				[METHOD U|Save][/METHOD]
				<h3>Votre nouveau mot de passe a &eacute;t&eacute; envoy&eacute; sur votre messagerie.<br />Gardez-le pr&eacute;cieusement !</h3>
				[LIB Mail|LeMail]
				[METHOD LeMail|Subject]
					[PARAM]Mon nouveau mot de passe[/PARAM]
				[/METHOD]
				[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
				[METHOD LeMail|ReplyTo][PARAM]noreply@expressiv.net[/PARAM][/METHOD]
				[METHOD LeMail|To][PARAM][!U::Mail!][/PARAM][/METHOD]
				[METHOD LeMail|Body]
					[PARAM]
						[BLOC Mail]
							Ci-dessous vos nouveaux identifiants :<br/>
							- Nom d'utilisateur : [!U::Login!]<br/>
							- Mot de passe : [!MaVar!]<br/>
						[/BLOC]
					[/PARAM]
				[/METHOD]
				[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
				[METHOD LeMail|BuildMail][/METHOD]
				[METHOD LeMail|Send][/METHOD]
				[NORESULT]
					<div class="BlocError">
						<p>D&eacute;sol&eacute;, cette adresse email est inconnue.</p>
					</div>
					<a href="/Systeme/Login/LostMdp/Test" title="Retrouver mon mot de passe">Cliquez ici pour r&eacute;essayer</a>
				[/NORESULT]
			[/STORPROC]
		[ELSE]
			<p>Entrez ici votre adresse email, et si nous trouvons un compte utilisateur associ&eacute; &agrave; votre adresse email, nous vous enverrons un mail contenant un lien vous permettant de r&eacute;initialiser votre mot de passe.</p>
			<div class="LigneForm">
				<label for="Mail" id="Mail">Adresse e-mail</label>
				<input type="text" name="Mail" value="[!Mail!]" />
				<input type="submit" class="BtnMail" name="CONNEXION" value="Envoyer" />
			</div>
			<input name="retrouvemdp" id="retrouvemdp" value="ok" type="hidden"/>
		[/IF]	
	</form>
</div>
<div class="Clear"></div>