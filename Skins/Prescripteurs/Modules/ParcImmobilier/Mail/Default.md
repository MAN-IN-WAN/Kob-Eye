// le prescripteur connecte
// suite au mail knittel du 30/04/2014
[!MailPrescripteur:=!]
[IF [!Prescripteur!]!=[!Systeme::User::Id!]&&[!Prescripteur!]!=&&[!Type!]!=OptionEchu]
	[STORPROC Systeme/User/[!Systeme::User::Id!]|Presc][/STORPROC]
	[!Prescripteur:=[!Presc::Id!]!]
	[!MailPrescripteur:=[!Presc::Mail!]!]
[ELSE]
	[IF [!Prescripteur!]!=]
		[STORPROC Systeme/User/[!Prescripteur!]|Presc][/STORPROC]
		[!MailPrescripteur:=[!Presc::Mail!]!]
	[ELSE]
		[!Prescripteur:=0!]
		[STORPROC Systeme/User/[!Systeme::User::Id!]|Presc][/STORPROC]
		[!Prescripteur:=[!Presc::Id!]!]
		[!MailPrescripteur:=[!Presc::Mail!]!]
	[/IF]
[/IF]
// Mail envoyé au différent interlocuteurs
[!LeSujet:=Prescripteurs : !]
[IF [!Type!]=Optionner]
	[!LeSujet+=Option émise !]
[/IF]
[IF [!Type!]=Reserver]
	[!LeSujet+=Réservation émise !]
	[!Message:=!]
[/IF]
[IF [!Type!]=Desoptionner]
	[!LeSujet+=Option annulée !]
	[!Message:=!]
[/IF]
[IF [!Type!]=ErreurOptionner]
	[!LeSujet+=Erreur sur Option !]
	[!Message:=!]
[/IF]
[IF [!Type!]=ErreurReserver]
	[!LeSujet+=Erreur sur réservation !]
	[!Message:=!]
[/IF]
[IF [!Type!]=OptionEchu]
	[!LeSujet+=Suppression d'Option échue!]
	[!Message:=!]
[/IF]
[IF [!Type!]=DenonciationEmise]
	[!LeSujet+=Dénonciation de Contact Prescripteur Extranet!]
	[!Message:=!]
[/IF]

[IF [!Type!]=DenonciationEchu]
	[!LeSujet+=Suppression dénonciation échue!]
	[!Message:=!]
[/IF]


// le lot concerne
[STORPROC ParcImmobilier/Lot/[!LeLot!]|StLot|0|1][/STORPROC]

// le type de lot t1 ---
[STORPROC ParcImmobilier/TypeLogement/Lot/[!StLot::Id!]|TypeLog|0|1][/STORPROC]

// action 
[STORPROC ParcImmobilier/Lot/[!LeLot!]/Action/|LAct|0|1|tmsCreate|DESC][/STORPROC]

// la résidence concernee
[STORPROC ParcImmobilier/Residence/TypeLogement/[!TypeLog::Id!]|Resid|0|1][/STORPROC]


//Envoi du mail au service commercial

[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM][!LeSujet!][/PARAM][/METHOD]

// suite au mail knittel du 30/04/2014
//[METHOD LeMail|From][PARAM][!Presc::Mail!][/PARAM][/METHOD]
//[METHOD LeMail|ReplyTo][PARAM][!Presc::Mail!][/PARAM][/METHOD]

[METHOD LeMail|From][PARAM][!MailPrescripteur!][/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM][!MailPrescripteur!][/PARAM][/METHOD]
	
// ici recherche du contact (commercial ou référent) du  prescripteur
[!CntPresc:=!]

[IF [!Type!]=DenonciationEchu||[!Type!]=DenonciationEmise]
	
	[STORPROC Systeme/User/[!Prescripteur!]/Commercial|CCal|0|1]
		[!CntPresc:=[!CCal::Id!]!]
		[METHOD LeMail|To][PARAM][!CCal::Mail!][/PARAM][/METHOD]
		
		[NORESULT]
			[STORPROC ParcImmobilier/Commercial/Referent=1|RCCal|0|1][/STORPROC]
			[!CntPresc:=[!RCCal::Id!]!]
			[METHOD LeMail|To][PARAM][!RCCal::Mail!][/PARAM][/METHOD]
		[/NORESULT]
	[/STORPROC]
	[METHOD LeMail|To][PARAM]knittel@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]msibra@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]taupin@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]cledelezir@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]tpichard@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]vanhuysse@pragma-immobilier.com[/PARAM][/METHOD]

 
[ELSE]

	[STORPROC Systeme/User/[!Systeme::User::Id!]/Commercial|CCal|0|1]
		[!CntPresc:=[!CCal::Id!]!]
		[METHOD LeMail|To][PARAM][!CCal::Mail!][/PARAM][/METHOD]

	[/STORPROC]

	[METHOD LeMail|To][PARAM]giboire@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]malisani@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]taupin@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]knittel@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]msibra@pragma-immobilier.com[/PARAM][/METHOD]

	[METHOD LeMail|To][PARAM]bastien.leandri@sogeprom.com[/PARAM][/METHOD]

	[METHOD LeMail|To][PARAM]myriam.abdellaoui@sogepromsud.com[/PARAM][/METHOD]
				 
	[METHOD LeMail|To][PARAM]gregory.meunier@sogepromsud.com[/PARAM][/METHOD]


	[STORPROC ParcImmobilier/Commercial/Referent=1|CCalR|0|1][/STORPROC]
	[METHOD LeMail|To][PARAM][!CCalR::Mail!][/PARAM][/METHOD]
	[IF [!CntPresc!]!=][ELSE][!CntPresc:=[!CCalR::Id!]!][/IF]

	[METHOD LeMail|To][PARAM]bourdel@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]vanhuysse@pragma-immobilier.com[/PARAM][/METHOD]

	[METHOD LeMail|To][PARAM]cledelezir@pragma-immobilier.com[/PARAM][/METHOD]
	[METHOD LeMail|To][PARAM]tpichard@pragma-immobilier.com[/PARAM][/METHOD]



[/IF]
[STORPROC ParcImmobilier/Commercial/[!CntPresc!]|LCCal|0|1][/STORPROC]

// suite au mail knittel du 30/04/2014
//[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]

[METHOD LeMail|Body]
	[PARAM]
		[BLOC Mail]
			Bonjour,<br />
			<div>
				<u><b>Commercial Référent :</b> [!LCCal::Prenom!] [!LCCal::Nom!]</u><br /><br />
			
				[IF [!Type!]=Optionner]
					[!Tms:=[!LAct::tmsCreate!]!]
					//   [!Tms+=86400!]
					[!Tms+=172800!]
					Nous vous informons qu'une <u><b>option</b></u>  a été émise par  : [!Presc::Prenom!] [!Presc::Nom!]<br /><br />
					valable jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE],<br />
					Résidence [!Resid::Titre!] - Lot  [!StLot::Identifiant!]<br /> 
				[/IF]
				[IF [!Type!]=Reserver]
					Nous vous informons qu'une <u><b>réservation</b></u>  a été émise par  : [!Presc::Prenom!] [!Presc::Nom!]<br /><br />
					Résidence [!Resid::Titre!] - Lot  [!StLot::Identifiant!]<br /> 
				[/IF]
				[IF [!Type!]=Desoptionner]
					Nous vous informons de l'<u><b>annulation</b></u> de l'option émise par  : [!Presc::Prenom!] [!Presc::Nom!]<br /><br />
					Résidence  [!Resid::Titre!] - Lot  [!StLot::Identifiant!]<br /> 
					Le lot est désormais à nouveau disponible à la vente
				[/IF]
				[IF [!Type!]=OptionEchu]
					[STORPROC Systeme/User/[!Prescripteur!]|Prs|0|1][/STORPROC]
					Nous vous informons de la <u><b>suppression</b></u> de l'option émise par : [!Prs::Prenom!] [!Prs::Nom!] <br /><br />
					Résidence  [!Resid::Titre!] - Lot  [!StLot::Identifiant!]<br /> 
					Cette option est arrivée à échéance. 
					<br />Le lot est à nouveau disponible à la vente  
				[/IF]
				
				[IF [!Type!]=DenonciationEmise]
					[!Personne:=!]
					[STORPROC Systeme/User/[!Prescripteur!]|Prs|0|1][/STORPROC]
					[STORPROC ParcImmobilier/Denonciation/[!Qui!]|Den|0|1]
						[STORPROC ParcImmobilier/Commercial/Denonciation/[!Den::Id!]|Cc|0|1][/STORPROC]
						[!Commercial:=[!Cc::Prenom!] [!Cc::Nom!]!]
						[!Personne:=[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!] [!Den::Ville!]!]
						<br />Nous vous informons de la dénonciation émise par : [!Prs::Prenom!] [!Prs::Nom!] <br /><br />
						Concernant 	[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!]<br />
						[!Den::Adresse1!] <br />[!Den::Adresse2!] <br />
						[IF [!Den::CodePostal!]!=0] [!Den::CodePostal!][/IF] [!Den::Ville!]<br />
						Téléphones : [!Den::Telephone1!] [IF [!Den::Telephone2!]!=] - [!Den::Telephone2!][/IF][IF [!Den::Telephone3!]!=] - [!Den::Telephone3!][/IF]<br />
						[IF [!Den::Fax!]!=]Fax : [!Den::Fax!]<br /> [/IF]
						[IF [!Den::Mail!]!=]Mail : [!Den::Mail!]<br />[/IF]
						<br />Types recherchés : [!Den::TypeLot!] <br />
						Surface : [!Den::Surface!] <br />
						Investissement envisagé : [!Den::Budget!] <br />
						Situation : [!Den::VilleRecherche!] [!Den::Quartier!] [!Den::Residence!]<br />
						Motifs : [!Den::Motifs!] <br />
						Date Livraison souhaitée : [!Den::Livraison!] <br />
						[!Den::AutreRenseignement!]
					[/STORPROC]


				[/IF]
				[IF [!Type!]=DenonciationEchu]
					[!Personne:=!]
					[STORPROC Systeme/User/[!Prescripteur!]|Prs|0|1][/STORPROC]
					[STORPROC ParcImmobilier/Denonciation/[!Qui!]|Den|0|1]
						[!Personne:=[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!] [!Den::Ville!]!]
						Nous vous informons de la <u><b>suppression</b></u> de la dénonciation émise par : [!Prs::Prenom!] [!Prs::Nom!] <br /><br />
						Concernant 	[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!]<br />
						[!Den::Adresse1!] <br />
						[!Den::Adresse2!] <br />
						[IF [!Den::CodePostal!]!=0] [!Den::CodePostal!][/IF] [!Den::Ville!]<br />
						Téléphones : [!Den::Telephone1!] [IF [!Den::Telephone2!]!=] - [!Den::Telephone2!][/IF][IF [!Den::Telephone3!]!=] - [!Den::Telephone3!][/IF]<br />
						[IF [!Den::Fax!]!=]Fax : [!Den::Fax!]<br /> [/IF]
						[IF [!Den::Mail!]!=]Mail : [!Den::Mail!]<br />[/IF]
						<br />Types recherchés : [!Den::TypeLot!] <br />
						Surface : [!Den::Surface!] <br />
						Investissement envisagé : [!Den::Budget!] <br />
						Situation : [!Den::VilleRecherche!] [!Den::Quartier!] [!Den::Residence!]<br />
						Motifs : [!Den::Motifs!] <br />
						Date Livraison souhaitée : [!Den::Livraison!] <br />
						[!Den::AutreRenseignement!]
					[/STORPROC]
				[/IF]
			</div>
		[/BLOC]
	[/PARAM]
[/METHOD]
[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]

//Envoi du mail au prescripteur
[LIB Mail|LeMail]
[METHOD LeMail|Subject][PARAM][!LeSujet!][/PARAM][/METHOD]
[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]

[IF [!Type!]=OptionEchu]
	[METHOD LeMail|To][PARAM][!MailPrescripteur!][/PARAM][/METHOD]
[ELSE]

	[METHOD LeMail|To][PARAM][!MailPrescripteur!][/PARAM][/METHOD]


[/IF]
[METHOD LeMail|Bcc][PARAM]knittel@pragma-immobilier.com[/PARAM][/METHOD]
[METHOD LeMail|Bcc][PARAM]msibra@pragma-immobilier.com[/PARAM][/METHOD]


[METHOD LeMail|Body]
	[PARAM]
		[BLOC Mail]
			<div>
				Bonjour,<br />
				[IF [!Type!]=Optionner]
					[!Tms:=[!LAct::tmsCreate!]!]
					//   [!Tms+=86400!]
					[!Tms+=172800!]
					Nous vous informons que l'option que vous avez émise pour<br />
					Résidence [!Resid::Titre!] - Lot [!StLot::Identifiant!]<br /> 
					est valable jusqu'au [DATE d/m/Y H:00][!Tms!][/DATE]<br /> 
					<br />Vous pouvez à tout moment supprimer cette option.
					<br />Si vous ne réservez pas le lot avant la date butoire de validité, ce lot sera à nouveau disponible à la vente.<br />
					
				[/IF]
				[IF [!Type!]=Reserver]
					Nous vous informons que la réservation que vous avez émise pour <br />
					Résidence [!Resid::Titre!] - Lot [!StLot::Identifiant!]<br /> 
					a bien été enregistrée.
					<br />Si vous souhaitez annuler cette réservation vous devez prendre contact avec 
					[!LCCal::Prenom!] [!LCCal::Nom!] [!LCCal::Mail!] [!LCCal::Telephone!]
					
				[/IF]
				[IF [!Type!]=Desoptionner]
					Nous vous confirmons l'annulation de l'option concernant 
					Résidence [!Resid::Titre!] - Lot  [!StLot::Identifiant!]<br /> 
					
				[/IF]

				[IF [!Type!]=OptionEchu]
					//[STORPROC Systeme/User/Action/[!LAction!]|Prs|0|1][/STORPROC]
					Nous vous informons que l'option que vous avez émise  concernant le lot<br /> <br /> 
					Résidence [!Resid::Titre!] - Lot [!StLot::Identifiant!]<br /> 
					est arrivée à échéance. <br /> 
					<br />Le lot a été remis à la vente.  
				[/IF]
				[IF [!Type!]=DenonciationEmise]
					[!Personne:=!]
					[STORPROC Systeme/User/[!Prescripteur!]|Prs|0|1][/STORPROC]
					[STORPROC ParcImmobilier/Denonciation/[!Qui!]|Den|0|1]
						[!MinTms:=[!Den::tmsCreate!]!]
						[!MinTms+=7948800!]
						[!Personne:=[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!] [!Den::Ville!]!]
					[/STORPROC]
					<br />Nous vous informons que la dénonciation  que vous avez émise a bien été enregistré.<br /><br />
					Elle concerne [!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!]<br />
					[!Den::Adresse1!] <br />
					[!Den::Adresse2!] <br />
					[IF [!Den::CodePostal!]!=0] [!Den::CodePostal!][/IF] [!Den::Ville!]<br />
					Téléphones : [!Den::Telephone1!] [IF [!Den::Telephone2!]!=] - [!Den::Telephone2!][/IF][IF [!Den::Telephone3!]!=] - [!Den::Telephone3!][/IF]<br />
					[IF [!Den::Fax!]!=]Fax : [!Den::Fax!]<br /> [/IF]
					[IF [!Den::Mail!]!=]Mail : [!Den::Mail!]<br />[/IF]
					<br />Types recherchés : [!Den::TypeLot!] <br />
					Surface : [!Den::Surface!] <br />
					Investissement envisagé : [!Den::Budget!] <br />
					Situation : [!Den::VilleRecherche!] [!Den::Quartier!] [!Den::Residence!]<br />
					Motifs : [!Den::Motifs!] <br />
					Date Livraison souhaitée : [!Den::Livraison!] <br />
					[!Den::AutreRenseignement!]<br /><br />
					Cette dénonciation est valable jusqu'au [DATE d/m/Y H:00][!MinTms!][/DATE].<br />
				[/IF]

				[IF [!Type!]=DenonciationEchu]
					[!Personne:=!]
					[STORPROC Systeme/User/[!Prescripteur!]|Prs|0|1][/STORPROC]
					[STORPROC ParcImmobilier/Denonciation/[!Qui!]|Den|0|1]
						[!Personne:=[!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!] [!Den::Ville!]!]
					[/STORPROC]
					Nous vous informons que la dénonciation  que vous avez émise est arrivée à échéance.
					Elle concerne [!Den::Civilite!] [!Den::Prenom!] [!Den::Nom!]<br />
					[!Den::Adresse1!] <br />
					[!Den::Adresse2!] <br />
					[IF [!Den::CodePostal!]!=0] [!Den::CodePostal!][/IF] [!Den::Ville!]<br />
					Téléphones : [!Den::Telephone1!] [IF [!Den::Telephone2!]!=] - [!Den::Telephone2!][/IF][IF [!Den::Telephone3!]!=] - [!Den::Telephone3!][/IF]<br />
					[IF [!Den::Fax!]!=]Fax : [!Den::Fax!]<br /> [/IF]
					[IF [!Den::Mail!]!=]Mail : [!Den::Mail!]<br />[/IF]
					<br />Types recherchés : [!Den::TypeLot!] <br />
					Surface : [!Den::Surface!] <br />
					Investissement envisagé : [!Den::Budget!] <br />
					Situation : [!Den::VilleRecherche!] [!Den::Quartier!] [!Den::Residence!]<br />
					Motifs : [!Den::Motifs!] <br />
					Date Livraison souhaitée : [!Den::Livraison!] <br />
					[!Den::AutreRenseignement!]<br /><br />

					<br />La dénonciation a été supprimée .
				[/IF]

				<br /><br />Bien cordialement
				<br />Le service commercial de Pragma 

			</div>

		[/BLOC]
	[/PARAM]
[/METHOD]
[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
[METHOD LeMail|BuildMail][/METHOD]
[METHOD LeMail|Send][/METHOD]