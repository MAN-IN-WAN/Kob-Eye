[MODULE Systeme/Ariane]
[MODULE Systeme/Structure/Gauche]	
<div id="Milieu">
	[MODULE Systeme/Menu/ImageMenu]
	<h1 style="padding-bottom:5px;border-bottom:1px dotted #3F3F3F;">Retrouver mon mot de passe</h1>
	<form method="post" action="">	
		[IF [!retrouvemdp!]="ok"]
			<div class="OubliMdp">
				[STORPROC Systeme/User/Mail=[!Mail!]|U]
					[LIB Random|Rand]
					[!MaVar:=[!Rand::Generate(8)!]!]
					[METHOD U|Set]
						[PARAM]Pass[/PARAM]
						[PARAM][!MaVar!][/PARAM]
					[/METHOD]
					//[!DEBUG::U!]
					[METHOD U|Save][/METHOD]
						<p style="color:#288E44;font-size:20px;font-weight:bold;line-height:25px;">Votre nouveau mot de passe<br />a &eacute;t&eacute; envoy&eacute; sur votre messagerie.<br />Gardez-le pr&eacute;cieusement !</p>
						<a href="/Mon-Compte" title="Retrouver mon mot de passe" class="Lien">Cliquez ici pour tenter de vous connecter</a>
						[LIB Mail|LeMail]
						[METHOD LeMail|Subject]
							[PARAM]Mon nouveau mot de passe Fiduciaire Parisienne[/PARAM]
						[/METHOD]
						[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
						[METHOD LeMail|ReplyTo][PARAM]noreply@fidu.fr[/PARAM][/METHOD]
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
						<a href="/Systeme/Login/LostMdp" title="Retrouver mon mot de passe"  class="Lien">Cliquez ici pour r&eacute;essayer</a>
					[/NORESULT]
				[/STORPROC]
			</div>
		[ELSE]
			<div class="OubliMdp">
				<p>Entrez ici votre adresse email , et si nous trouvons un compte utilisateur associ&eacute; à votre adresse email, nous vous enverrons un mail contenant un lien vous permettant de r&eacute;initialiser votre mot de passe.</p>
				<div class="LigneForm">
					<label for="Mail" id="Mail">Adresse e-mail</label>
					<input type="text" name="Mail" value="[!Mail!]" />
				</div>
				[BLOC Bouton|width:120px;margin-left:385px;||text-align:center;width:70px;|]
					<input name="retrouvemdp" id="retrouvemdp" value="ok" type="hidden"/>
					<input type="submit" class="BoutonMail" name="CONNEXION" value="Envoyer" />
				[/BLOC]
			</div>			
		[/IF]	
	</form>
</div>
<div class="Clear"></div>