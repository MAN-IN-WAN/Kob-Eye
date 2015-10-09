
	[STORPROC Systeme/User/[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]
	[STORPROC Systeme/User/[!Systeme::User::Id!]/Commercial|CCalPr|0|1]
		[NORESULT]
			[STORPROC ParcImmobilier/Commercial/Referent=1|CCalPr|0|1][/STORPROC]
		[/NORESULT]
	[/STORPROC]
	[IF [!Page!]=][!Page:=1!][/IF]
	[!NbParPage:=5!]
	[!LimitStart:=[![!Page:-1!]:*[!NbParPage!]!]!]
	
	
	[IF [!Envoi!]=EnvoiForm]
		//Verification des informations du formulaire
		[!C_Pr_Error:=0!]
		[IF [!C_Pr_Nom!]=][!C_Pr_Nom_Error:=1!][!C_Pr_Error:=1!][/IF]
		[IF [!C_Pr_Prenom!]=][!C_Pr_Prenom_Error:=1!][!C_Pr_Error:=1!][/IF]
		[IF [!C_Pr_Tel!]=&&[!C_Pr_MailContact!]=][!C_Pr_UneInf_Error:=1!][!C_Pr_Error:=1!][/IF]
		[IF [!C_Pr_Error!]=0]
			// Verification que ce prospect n'existe pas déjà
			// on test si le mail est renseigné
			[IF [!C_Pr_MailContact!]!=]
				[STORPROC ParcImmobilier/Denonciation/Nom=[!C_Pr_Nom!]&Prenom=[!C_Pr_Prenom!]&Mail=[!C_Pr_MailContact!]&Obsolete=0|DD|0|1]
					[!C_Pr_Connu_Error:=1!][!C_Pr_Error:=1!]
				[/STORPROC]
			[/IF]
			// et ou si le tel est renseigné
			[IF [!C_Pr_Tel!]!=]
				[STORPROC ParcImmobilier/Denonciation/Nom=[!C_Pr_Nom!]&Prenom=[!C_Pr_Prenom!]&Telephone1=[!C_Pr_Tel!]&Obsolete=0|DD|0|1]
					[!C_Pr_Connu_Error:=1!][!C_Pr_Error:=1!]
				[/STORPROC]
			[/IF]
		[/IF]
		[IF [!C_Pr_Error!]=0]
			[!C_Pr_Dbl_Error:=0!]
			// Demande de Jennifer le 11/09/2014 on détecte doublon possible sur le nom
			[STORPROC ParcImmobilier/Denonciation/Nom=[!C_Pr_Nom!]&Obsolete=0|DD|0|1]
				[!C_Pr_Dbl_Error:=1!]
			[/STORPROC]
		[/IF]
		[IF [!C_Pr_Dbl_Error!]=1&&[!C_Pr_Error!]=0]
			[STORPROC Systeme/Group/User/[!CLCONN:Id!]|CLGRP][/STORPROC]
			// Enregistrement de la demande de dénonciation
			[OBJ ParcImmobilier|Denonciation|Dnct]
			[METHOD Dnct|Set][PARAM]Nom[/PARAM][PARAM][!C_Pr_Nom!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Prenom[/PARAM][PARAM][!C_Pr_Prenom!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Telephone1[/PARAM][PARAM][!C_Pr_Tel!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Mail[/PARAM][PARAM][!C_Pr_MailContact!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]AutreRenseignement[/PARAM][PARAM][!C_Pr_AutreRenseignement!][/PARAM][/METHOD]
			[METHOD Dnct|AddParent][PARAM]ParcImmobilier/Commercial/[!CCalPr::Id!][/PARAM][/METHOD]
			// SERA RATTACHE PAR PRAGMA APRES VERIFICATION
			//			[METHOD Dnct|AddParent][PARAM]Systeme/User/[!Systeme::User::Id!][/PARAM][/METHOD]
			[IF [!Dnct::Verify!]]
				[METHOD Dnct|Save][/METHOD]
				//Envoi du mail au service commercial
				//[MODULE ParcImmobilier/Mail?Type=DenonciationEmiseDbl&Prescripteur=[!CLCONN::Id!]&Qui=[!Dnct::Id!]]
				[STORPROC Systeme/User/[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]
				[STORPROC Systeme/User/[!CLCONN::Id!]/Commercial|CCal|0|1][/STORPROC]
				<div class="BlocEnvoiDenonciation">
					<h3>Possible doublon, ce nom a déjà été dénoncé, le service commercial vérifie avec les autres informations fournies si c'est effectivement un doublon.<br /> Nous vous tenons informé le plus rapidement possible.</h3>
				</div>
				[LIB Mail|LeMail]
				[METHOD LeMail|Subject][PARAM]Doublon denonciation possible[/PARAM][/METHOD]
				[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
				[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
				[METHOD LeMail|To][PARAM][!CCalPr::Mail!][/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]knittel@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]msibra@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]taupin@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
				[METHOD LeMail|Body]
				[PARAM]
					Dénonciation émise doublon possible <br />
					Nom : [!C_Pr_Nom!]<br />
					Prenom : [!C_Pr_Prenom!]<br />
					Telephone1 : [!C_Pr_Tel!]<br />
					Mail : [!C_Pr_MailContact!]<br />
					AutreRenseignement : [!C_Pr_AutreRenseignement!]<br />
					Commercial : [!CCalPr::Nom!]<br /><br />
					Groupe Prescripteur : [!CLGRP::Nom!] / Prescripteur : [!CLCONN::Id!] - [!CLCONN::Nom!] [!CLCONN::Prenom!] <br /><br />
					=============================================================<br />
					PETIT MANUEL pour lier la dénonciation au prescripteur<br />
					==============================================================<br />
					La dénonciation a été créé, pour vérifier <br/>
					Aller dans Module ParcImmobilier / Onglet Dénonciation <br/> <br/>
					Taper dans le champ rechercher dans la barre grise le nom de la personne dénoncée <br/> <br/>
					<strong>Attention,</strong> si plusieurs résultats sortent, pensez à vérifier si certaines ne sont pas obsolètes (datant de plus de 3 mois)<br/>
					Si c'est un doublon : le commercial doit voir avec le prescripteur et éventuellement supprimer la dénonciation en trop <br/> <br/>
					Si ce n'est pas un doublon il vous faut manuellement l'attribuer au prescripteur.<br />
					Il vous faut aller dans le back-office aller dans module Systeme / Utilisateur <br />
					Url :  http://admin.pragma-immobilier.com/#/Systeme/User/[!CLCONN::Id!].htm<br /><br />
					En dessous de sa fiche au niveau dénonciation faite sélectionner et choisir la dénonciation <br />
					et Validez<br />
					============================================================================================<br />
				[/PARAM]
				[/METHOD]
				[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
				[METHOD LeMail|BuildMail][/METHOD]
				[METHOD LeMail|Send][/METHOD]
				[!Affichage:=Saisie!]
			[ELSE]	
				[STORPROC Systeme/User/[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]
				[STORPROC Systeme/Group/User/[!CLCONN:Id!]|CLGRP][/STORPROC]
				[STORPROC Systeme/User/[!CLCONN::Id!]/Commercial|CCal|0|1][/STORPROC]
				[LIB Mail|LeMail]
				[METHOD LeMail|Subject][PARAM]Erreur denonciation[/PARAM][/METHOD]
				[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
				[METHOD LeMail|To][PARAM][!CCalPr::Mail!][/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]taupin@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]msibra@pragma-immobilier.com[/PARAM][/METHOD]
				[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
				[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
				[METHOD LeMail|Body]
				[PARAM]
					Groupe Prescripteur : [!CLGRP::Nom!] / Prescripteur : [!CLCONN::Id!] - [!CLCONN::Nom!] [!CLCONN::Prenom!] <br /><br />
					Erreur sur dénonciation <br /><hr><br />
					Nom : [!C_Pr_Nom!]<br />
					Prenom : [!C_Pr_Prenom!]<br />
					Telephone1 : [!C_Pr_Tel!]<br />
					Mail : [!C_Pr_MailContact!]<br />
					AutreRenseignement : [!C_Pr_AutreRenseignement!]<br />
					<br /><hr><br />
					Commercial : [!CCalPr::Id!] - [!CCalPr::Nom!]<br />
					<br />Généralement ce sont les mails qui ont un format erroné
				[/PARAM]
				[/METHOD]
				[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
				[METHOD LeMail|BuildMail][/METHOD]
				[METHOD LeMail|Send][/METHOD]
				<div class="BlocEnvoiDenonciation">
					<h3>Une erreur s'est produite lors de l'enregistrement de votre dénonciation, elle n'a pas été enregistrée, nos services ont été informés de cette erreur. Nous vous recontactons dès que possible.</h3>
				</div>
				[!Affichage:=Saisie!]


			[/IF]



		[ELSE]
			//Si il y a des erreurs, on les affiche
			[IF [!C_Pr_Error!]]
				//Affichage des messages d erreur
				<div class="BlocError" > 
					<p>Merci de vérifier :</p>
					<ul>
						[IF [!C_Pr_UneInf_Error!]]<li>Le téléphone (et) ou le mail doivent être renseigné</li>[/IF]
						[IF [!C_Pr_Nom_Error!]]<li>Le nom n'est pas renseigné</li>[/IF]
						[IF [!C_Pr_Prenom_Error!]]<li>Le prénom n'est pas renseigné</li>[/IF]
						[IF [!C_Pr_Connu_Error!]]<li>Cette personne a déjà été dénoncée par un autre prescripteur</li>[/IF]
					</ul>
				</div>
				[MODULE ParcImmobilier/Denonciation/Creation?C_Pr_Error=1]
				<div class="RetourListe"><a href="[!SERVER::HTTP_REFERER!]" >Retour</a></div>
			
			[ELSE]
				[STORPROC Systeme/Group/User/[!CLCONN:Id!]|CLGRP][/STORPROC]
				
				// Enregistrement de la demande de dénonciation
	//			[!C_Pr_TypeLot:=!]
	///			[IF [!C_Pr_TypeSt!]][!C_Pr_TypeLot+=Studio!][/IF]	
	//			[IF [!C_Pr_TypeT1!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T1!][/IF]	
	//			[IF [!C_Pr_TypeT2!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T2!][/IF]	
	//			[IF [!C_Pr_TypeT3!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T3!][/IF]	
	//			[IF [!C_Pr_TypeT4!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T4!][/IF]	
	//			[IF [!C_Pr_TypeT5!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T5!][/IF]	
	//			[IF [!C_Pr_TypeVilla!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=Villa!][/IF]	
	//			[IF [!C_Pr_TypeLC!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=LC!][/IF]	
	//			[IF [!C_Pr_TypeTerrain!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=Terrain!][/IF]	
		
				[OBJ ParcImmobilier|Denonciation|Dnct]
				[METHOD Dnct|Set][PARAM]Nom[/PARAM][PARAM][!C_Pr_Nom!][/PARAM][/METHOD]
				[METHOD Dnct|Set][PARAM]Prenom[/PARAM][PARAM][!C_Pr_Prenom!][/PARAM][/METHOD]
				[METHOD Dnct|Set][PARAM]Telephone1[/PARAM][PARAM][!C_Pr_Tel!][/PARAM][/METHOD]
				[METHOD Dnct|Set][PARAM]Mail[/PARAM][PARAM][!C_Pr_MailContact!][/PARAM][/METHOD]
				[METHOD Dnct|Set][PARAM]AutreRenseignement[/PARAM][PARAM][!C_Pr_AutreRenseignement!][/PARAM][/METHOD]
				[METHOD Dnct|AddParent][PARAM]ParcImmobilier/Commercial/[!CCalPr::Id!][/PARAM][/METHOD]
				[METHOD Dnct|AddParent][PARAM]Systeme/User/[!Systeme::User::Id!][/PARAM][/METHOD]
				
				[IF [!Dnct::Verify!]]
					[METHOD Dnct|Save][/METHOD]
			
					//Envoi du mail au service commercial
					//[STORPROC ParcImmobilier/Denonciation/Nom=[!C_Pr_Nom!]&Prenom=[!C_Pr_Prenom!]&Ville=[!C_Pr_Ville!]|Den|0|1][/STORPROC]
					[MODULE ParcImmobilier/Mail?Type=DenonciationEmise&Prescripteur=[!CLCONN::Id!]&Qui=[!Dnct::Id!]]
					
				
					<div class="BlocEnvoiDenonciation">
						<h3>Message envoy&eacute; avec succ&egrave;s.<br />Un mail de confirmation vous a &eacute;t&eacute; envoy&eacute;.</h3>
					</div>
				[ELSE]
					[STORPROC Systeme/User/[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]
					[STORPROC Systeme/Group/User/[!CLCONN:Id!]|CLGRP][/STORPROC]
					[STORPROC Systeme/User/[!CLCONN::Id!]/Commercial|CCal|0|1][/STORPROC]
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM]erreur denonciation[/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!CCalPr::Mail!][/PARAM][/METHOD]
					[METHOD LeMail|Bcc][PARAM]taupin@pragma-immobilier.com[/PARAM][/METHOD]
					[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACTPRESCRIPTEUR!][/PARAM][/METHOD]
					[METHOD LeMail|Body]
					[PARAM]
						Groupe Prescripteur : [!CLGRP::Nom!] / Prescripteur : [!CLCONN::Id!] - [!CLCONN::Nom!] [!CLCONN::Prenom!] <br /><br />
						Erreur sur dénonciation <br /><hr><br />
						Nom : [!C_Pr_Nom!]<br />
						Prenom : [!C_Pr_Prenom!]<br />
						Telephone1 : [!C_Pr_Tel!]<br />
						Mail : [!C_Pr_MailContact!]<br />
						AutreRenseignement : [!C_Pr_AutreRenseignement!]<br />
						<br /><hr><br />
						Commercial : [!CCalPr::Id!] - [!CCalPr::Nom!]<br />
						<br />Généralement ce sont les mails qui ont un format erroné
					[/PARAM]
					[/METHOD]
					[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
					<div class="BlocEnvoiDenonciation">
						<h3>Une erreur s'est produite lors de l'enregistrement de votre dénonciation, elle n'a pas été enregistrée, nos services ont été informés de cette erreur. Nous vous recontactons dès que possible.</h3>
					</div>
					[!Affichage:=Saisie!]
				[/IF]
			[/IF]
		[/IF]

	[/IF]
	
	
	[IF [!Affichage!]=Saisie]
		[MODULE ParcImmobilier/Denonciation/Creation]
		<div class="RetourListe"><a href="[!SERVER::HTTP_REFERER!]" >Retour</a></div>
	[ELSE]
		[MODULE ParcImmobilier/Denonciation/Liste]
	[/IF]
//[ELSE]
//	[MODULE ParcImmobilier/Denonciation/DefaultOld]
//[/IF]