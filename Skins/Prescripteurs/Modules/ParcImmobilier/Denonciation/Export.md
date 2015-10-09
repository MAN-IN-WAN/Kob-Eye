[IF [!Systeme::User::Login!]=CommercialAdm]
	[COUNT Systeme/User/[!Systeme::User::Id!]/Denonciation|Total]
	[!Requete:=Systeme/User/[!Systeme::User::Id!]/Denonciation!]
	[!ADMINVUE:=0!]
	
	// Production CSV
	[OBJ ParcImmobilier|TypeLogement|TL]
	[!TL::sendHeader()!]
	[!Titre:=Export dénonciation du [!Utils::getDate(d.m.Y,[!start!])!]!]
	//[!Titre!]<br />
	[!TL::addLigne([!Titre!])!] 
	
	
	
	[STORPROC ParcImmobilier/Commercial|CCa]
		[!Entete:=1!]
		[STORPROC Systeme/User/Commercial/[!CCa::Id!]|Pres]
			[STORPROC ParcImmobilier/Denonciation/Prescripteur=[!Pres::Id!]&Obsolete=0|De]
	//		[STORPROC ParcImmobilier/Denonciation/Prescripteur=[!Systeme::User::Id!]|De]
				[IF [!Entete!]=1]
					[!Ligne:=Commercial;Prescripteur;Nom;Prenom;Telephone;Mail;Date dénonciation!]
					[!TL::addLigne([!Ligne!])!] 
					[!Entete:=[!Entete:=1!]!]
				[/IF]
				[!Ligne:=[!CCa::Nom!];[!Pres::Nom!];[!De::Nom!];[!De::Prenom!];[!De::Telephone1!];[!De::Mail!];[!Utils::getDate(d.m.Y,[!De::tmsCreate!])!]!]
				[!TL::addLigne([!Ligne!])!] 
				
			[/STORPROC]
		[/STORPROC]
	[/STORPROC]
[ELSE]
	
	
[/IF]	