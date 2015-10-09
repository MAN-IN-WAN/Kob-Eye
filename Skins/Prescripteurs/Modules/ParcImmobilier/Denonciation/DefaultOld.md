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
	[IF [!C_Pr_Ville!]=][!C_Pr_Ville_Error:=1!][!C_Pr_Error:=1!][/IF]
	[IF [!C_Pr_Tel!]=][!C_Pr_Tel_Error:=1!][!C_Pr_Error:=1!][/IF]
	//[IF [!C_Pr_MailContact!]=][!C_Pr_MailContact_Error:=1!][!C_Pr_Error:=1!][/IF]
	[IF [!C_Pr_Error!]=0]
		// Verification que ce prospect n'existe pas déjà
		[STORPROC ParcImmobilier/Denonciation/Nom=[!C_Pr_Nom!]&Prenom=[!C_Pr_Prenom!]&Ville=[!C_Pr_Ville!]|DD|0|1]
			[!C_Pr_Connu_Error:=1!][!C_Pr_Error:=1!]
		[/STORPROC]
	//	[STORPROC ParcImmobilier/Denonciation/Mail=[!C_Pr_MailContact!]|DD|0|1]
	//		[!C_Pr_Connu_Error:=1!][!C_Pr_Error:=1!]
	//	[/STORPROC]
	[/IF]
	//Si il y a des erreurs, on les affiche
	[IF [!C_Pr_Error!]]
		//Affichage des messages d erreur
		<div class="BlocError" > 
			<p>Merci de vérifier :</p>
			<ul>
				[IF [!C_Pr_MailContact_Error!]]<li>Le mail n'est pas renseigné</li>[/IF]
				[IF [!C_Pr_Nom_Error!]]<li>Le nom n'est pas renseigné</li>[/IF]
				[IF [!C_Pr_Prenom_Error!]]<li>Le prénom n'est pas renseigné</li>[/IF]
				[IF [!C_Pr_Ville_Error!]]<li>La ville n'est pas renseigné</li>[/IF]
				[IF [!C_Pr_Tel_Error!]]<li>Le téléphone n'est pas renseigné</li>[/IF]
				[IF [!C_Pr_Connu_Error!]]<li>Cette personne a déjà été dénoncée par un autre prescripteur</li>[/IF]
			</ul>
		</div>
		[MODULE ParcImmobilier/Denonciation/Creation?C_Pr_Error=1]
    		<div class="RetourListe"><a href="[!SERVER::HTTP_REFERER!]" >Retour</a></div>
	
	[ELSE]
		[STORPROC Systeme/Group/User/[!CLCONN:Id!]|CLGRP][/STORPROC]
		
		// Enregistrement de la demande de dénonciation
		[!C_Pr_TypeLot:=!]
    		[IF [!C_Pr_TypeSt!]][!C_Pr_TypeLot+=Studio!][/IF]	
		[IF [!C_Pr_TypeT1!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T1!][/IF]	
		[IF [!C_Pr_TypeT2!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T2!][/IF]	
		[IF [!C_Pr_TypeT3!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T3!][/IF]	
		[IF [!C_Pr_TypeT4!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T4!][/IF]	
		[IF [!C_Pr_TypeT5!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=T5!][/IF]	
		[IF [!C_Pr_TypeVilla!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=Villa!][/IF]	
		[IF [!C_Pr_TypeLC!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=LC!][/IF]	
		[IF [!C_Pr_TypeTerrain!]][IF [!C_Pr_TypeLot!]!=][!C_Pr_TypeLot+=-!][/IF][!C_Pr_TypeLot+=Terrain!][/IF]	

		[OBJ ParcImmobilier|Denonciation|Dnct]
			[METHOD Dnct|Set][PARAM]Civilite[/PARAM][PARAM][!C_Pr_Sexe!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Nom[/PARAM][PARAM][!C_Pr_Nom!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Prenom[/PARAM][PARAM][!C_Pr_Prenom!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Adresse1[/PARAM][PARAM][!C_Pr_Adresse1!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Adresse2[/PARAM][PARAM][!C_Pr_Adresse2!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]CodePostal[/PARAM][PARAM][!C_Pr_CodePostal!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Ville[/PARAM][PARAM][!C_Pr_Ville!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Telephone1[/PARAM][PARAM][!C_Pr_Tel!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Telephone2[/PARAM][PARAM][!C_Pr_Tel2!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Telephone3[/PARAM][PARAM][!C_Pr_Tel3!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Fax[/PARAM][PARAM][!C_Pr_Fax!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Mail[/PARAM][PARAM][!C_Pr_MailContact!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]TypeLot[/PARAM][PARAM][!C_Pr_TypeLot!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Surface[/PARAM][PARAM][!C_Pr_Surface!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]VilleRecherche[/PARAM][PARAM][!C_Pr_VilleRecherche!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Quartier[/PARAM][PARAM][!C_Pr_Quartier!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Residence[/PARAM][PARAM][!C_Pr_Residence!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Budget[/PARAM][PARAM][!C_Pr_Budget!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Motifs[/PARAM][PARAM][!C_Pr_Motifs!][/PARAM][/METHOD]
			[METHOD Dnct|Set][PARAM]Livraison[/PARAM][PARAM][!C_Pr_Livraison!][/PARAM][/METHOD]
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
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]erreur denonciation[/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM]myriam@abtel.fr[/PARAM][/METHOD]

			[METHOD LeMail|To][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
			[METHOD LeMail|Body]
			[PARAM]
				Type=DenonciationEmise&Prescripteur=[!CLCONN::Id!]&Qui=[!Dnct::Id!]<br />
				[!C_Pr_Sexe!]<br />
				[!C_Pr_Nom!]<br />
				Prenom[!C_Pr_Prenom!]<br />
				Adresse1[!C_Pr_Adresse1!]<br />
				Adresse2[!C_Pr_Adresse2!]<br />
				CodePostal[!C_Pr_CodePostal!]<br />
				Ville[!C_Pr_Ville!]<br />
				Telephone1[!C_Pr_Tel!]<br />
				Telephone2[!C_Pr_Tel2!]<br />
				Telephone3[!C_Pr_Tel3!]<br />
				Fax[!C_Pr_Fax!]<br />
				Mail[!C_Pr_MailContact!]<br />
				TypeLot[!C_Pr_TypeLot!]<br />
				Surface[!C_Pr_Surface!]<br />
				VilleRecherche[!C_Pr_VilleRecherche!]<br />
				Quartier[!C_Pr_Quartier!]<br />
				Residence[!C_Pr_Residence!]<br />
				Budget[!C_Pr_Budget!]<br />
				Motifs[!C_Pr_Motifs!]<br />
				Livraison[!C_Pr_Livraison!]<br />
				AutreRenseignement[!C_Pr_AutreRenseignement!]<br />
				Commercial/[!CCalPr::Id!]<br />
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


[IF [!Affichage!]=Saisie]
	[MODULE ParcImmobilier/Denonciation/Creation]
   	<div class="RetourListe"><a href="[!SERVER::HTTP_REFERER!]" >Retour</a></div>
[ELSE]
    	[MODULE ParcImmobilier/Denonciation/Liste]
[/IF]
