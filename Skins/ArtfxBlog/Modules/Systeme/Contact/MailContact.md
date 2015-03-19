[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM]Message de [!CONF::MODULE::SYSTEME::BLOG!] concernant : [!C_Sujet!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
[METHOD LeMail|Body]
	[PARAM]
		<html>
			<body style="width:100%;">
				<div >
					<u style="font-weight:bold;">Envoy&eacute; par</u> : <span style="text-transform:uppercase;">[!C_Nom!] </span> [!C_Prenom!]<br/>
					<u style="font-weight:bold;text-transform:uppercase;">Adresse</u> : [!C_Adresse!]<br/>
					<u style="font-weight:bold;text-transform:uppercase;">Code postal</u> : [!C_CodPos!]<br/>
					<u style="font-weight:bold;text-transform:uppercase;">Ville</u> : [!C_Ville!]<br/>
					<u style="font-weight:bold;text-transform:uppercase;">Pays</u> : [!C_Pays!]<br/>
					<u style="font-weight:bold;">Num&#233;ro de t&#233;l&#233;phone</u> : [!C_Tel!]<br/>
					<u style="font-weight:bold;">Adresse e-mail</u> : [!C_Mail!]<br/>
					<u style="font-weight:bold;">Sujet</u> : [!C_Sujet!]<br/>
					<hr />
				</div>
				[!C_Mess!]
				<hr />
			</body>
		</html>
	[/PARAM]
[/METHOD]
[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]
	
//Mail de confirmation
[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM]Message de [!CONF::MODULE::SYSTEME::BLOG!] concernant : [!C_Sujet!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
[METHOD LeMail|Body]
	[PARAM]
		[BLOC Mail]
			Bonjour [!C_Prenom!] [!C_Nom!],<br />
			Nous avons bien re&ccedil;u votre demande par email concernant [!C_Sujet!] et  nous vous remercions de votre confiance.<br />		
			Nous allons traiter votre demande d&egrave;s que possible.<br />
			L'equipe Artfx
		[/BLOC]
	[/PARAM]
[/METHOD]
[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]