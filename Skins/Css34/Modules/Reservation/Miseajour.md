//[LIB Mail|LeMail]
//[METHOD LeMail|Subject][PARAM]culture et sport solidaires 34 = rapport des envois de mail[/PARAM][/METHOD]
//[METHOD LeMail|From][PARAM]rapport@cultureetsportsolidaires34.fr[/PARAM][/METHOD]
//[METHOD LeMail|ReplyTo][PARAM]pasdereponse@cultureetsportsolidaires34.fr[/PARAM][/METHOD]
//[METHOD LeMail|To][PARAM]direction@cultureetsportsolidaires34.fr[/PARAM][/METHOD]
// [METHOD LeMail|Body]
//	[PARAM]
		<h4>MISE A JOUR SPECTACLES par rapport aux évènements - Mis en place le 2016 04 04 à 9h </h4>
		<br />
		[!DateA:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!TMS::Now!])!] 23:59)!]!]
		[!DateDe:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!TMS::Now!])!] 00:00)!]!]
		[!Utils::getDate(d/m/Y - H:i,[!DateDe!])!] de à [!Utils::getDate(d/m/Y - H:i,[!DateA!])!]<br />
		[STORPROC Reservation/Evenement/DateCloture>=[!DateDe!]&&DateCloture<=[!DateA!]|Ev]
			[STORPROC Reservation/Spectacle/Evenement/[!Ev::Id!]|S]
					[!S::Update()!]
					<br />[!S::Nom!] : 
					<br /> Spectacle :  Date Prochaine Cloture Spectacle [!Utils::getDate(d/m/Y H:i,[!S::ProchaineDateCloture!] )!]- Date Spectacle : [!Utils::getDate(d/m/Y H:i,[!S::DateDebut!])!] 
					<br /> Evenement lu :  Date Prochaine Cloture Evenement : [!Utils::getDate(d/m/Y H:i,[!Ev::DateCloture!] )!]- Date Evenement  : [!Utils::getDate(d/m/Y H:i,[!Ev::DateDebut!])!]
					<br /><br />
			[/STORPROC]
		[/STORPROC]
//	[/PARAM]
// [/METHOD]
//[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
//[METHOD LeMail|BuildMail][/METHOD]
// [METHOD LeMail|Send][/METHOD]