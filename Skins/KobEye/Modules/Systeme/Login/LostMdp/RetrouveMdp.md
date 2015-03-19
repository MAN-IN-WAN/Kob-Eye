[IF [!Code!]!=]
	[STORPROC Systeme/User/CodeVerif=[!Code!]|U]
		[LIB Random|Rand]
		[!MaVar:=[!Rand::Generate(8)!]!]
		[METHOD U|Set]
			[PARAM]Pass[/PARAM]
			[PARAM][!MaVar!][/PARAM]
		[/METHOD]
		//[!DEBUG::U!]
		[METHOD U|Save][/METHOD]
			[BLOC Rounded|background-color:#eaeaea;text-align:center;|width:500px;margin:auto;|padding:5px;]
				<p style="color:#288E44;font-size:20px;font-weight:bold;line-height:25px;">Votre nouveau mot de passe<br />a &eacute;t&eacute; envoy&eacute; sur votre messagerie.<br />
				Gardez-le pr&eacute;cieusement !</p>
			[/BLOC]
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject]
				[PARAM]Mon nouveau mot de passe Natomshop[/PARAM]
			[/METHOD]
			[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM]noreply@natomshop.com[/PARAM][/METHOD]
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
			<div class="ErrorSys">
				D&eacute;sol&eacute;, cet utilisateur est inconnu
			</div>
		[/NORESULT]
	[/STORPROC]
	
[ELSE]
	<div class="ErrorSys">
		D&eacute;sol&eacute;, code invalide.
	</div>
[/IF]
