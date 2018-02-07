// Lecture Devis pour affichage
[!A_PLANNIFIE:=0!]		
[IF [!I_RDVDevis!]]

	[IF [!SERVER::REMOTE_ADDR!]!=178.22.145.106]

		////////////////// Demande de RDV
		////////////////// On verifie les champs du formulaire
		[!I_Error:=0!]
		[IF [!Utils::isMail([!I_Mail!])!]!=1][!I_Mail_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Nom!]=][!I_Nom_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Tel!]=][!I_Tel_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Ville!]=][!I_Ville_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Adresse!]=Tapez votre adresse||[!I_Adresse!]=][!I_Adresse_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_DateRDV!]=][!I_DateRDV_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Horaires!]=][!I_Horaires_Error:=1!][!I_Error:=1!][/IF]
	
		[IF [!hash!]]	
			[!VerifMD5:=[!Utils::md5([!Result!])!]!]
			[IF [!VerifMD5!]!=[!hash!]][!C_Code_Error:=1!][!I_Error:=1!][/IF]
		[/IF]
	
		[IF [!I_Error!]=1]
				
		[ELSE]
			// Enregistrement RDV
			// il faut se repositionner sur le devis
			[STORPROC Catalogue/Devis/[!Devis!]|Dv|0|1]
				[METHOD Dv|Set]
					[PARAM]Nom[/PARAM]
					[PARAM][!I_Nom!][/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]Email[/PARAM]
					[PARAM][!I_Mail!][/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]Ville[/PARAM]
					[PARAM][!I_Ville!][/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]Telephone[/PARAM]
					[PARAM][!I_Tel!][/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]Adresse[/PARAM]
					[PARAM][!I_Adresse!][/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]RdvConfirme[/PARAM]
					[PARAM]0[/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]Horaires[/PARAM]
					[PARAM][!I_Horaires!][/PARAM]
				[/METHOD]
				[METHOD Dv|Set]
					[PARAM]DateRdv[/PARAM]
					[PARAM][!I_DateRDV!][/PARAM]
				[/METHOD]
				[METHOD Dv|Save][/METHOD]
			[/STORPROC]
			// on acte la demande de plannification pour afficher le msg à l'internaute
			[!A_PLANNIFIE:=1!]
			// message rdv pour gazservice
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]Demande de RDV[/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM][!I_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM][!I_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACTSIMULATEUR!][/PARAM][/METHOD]
			[METHOD LeMail|Bcc][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			//demande du 4 juillet par mail
			[METHOD LeMail|Cc][PARAM]nimes@gaz-service.fr[/PARAM][/METHOD]
			//[METHOD LeMail|Bcc][PARAM]p.huys@gaz-service.fr[/PARAM][/METHOD] --> contact c'est phuys
			[METHOD LeMail|Bcc][PARAM]gcandella@abtel.fr[/PARAM][/METHOD]
			[METHOD LeMail|Body]
				[PARAM]
					[BLOC MailInterne]
					<font face="arial" color="#000000" size="2">
						<strong>Nouvelle demande de rendez-vous via le simulateur</strong> <br />
						//<strong>Adresse Ip</strong> :<span><a href="http://www.geoiptool.com/fr/?IP=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
						<strong>Demande de RDV envoyée par : <br /></strong>
						<strong>Nom : </strong> <span style="text-transform:uppercase">[!I_Nom!]</span>  <br />
						<strong>Tél : </strong> [!I_Tel!] <br />
						<strong>Mail : </strong> <span >[!I_Mail!]</span>  <br />
						<strong>Coordonnees</strong> : [!I_Adresse!]<br />[!I_Ville!]<br /><br />
						<strong>RDV demandé le : [DATE d.m.Y][!I_DateRDV!][/DATE]<br /> </strong>
						<strong>Plage horaire demandée : [IF [!I_Horaires!]=1]Matin[ELSE]Après midi [/IF]<br /></strong>
						<br /><br /><strong>Produits en réponse simulateur:<br /></strong>
						[STORPROC Catalogue/Devis/[!Devis!]/Produit|PDev]
							[!PDev::References!] - [!PDev::Titre!]<br />
						[/STORPROC]
						<br/>
						<br/>
						<a href="http://maps.google.com/?q=[!I_Adresse!],[!I_Ville!]">Voir sur Google Maps</a><br/>
						(Etant donné que le format des données fournies par le client n'est pas controlé, il se peut que le carte fournie par Google ne soit pas pertinente.)
					</font>
					[/BLOC]
				[/PARAM]
			[/METHOD]
			[METHOD LeMail|BuildMail][/METHOD]
			[METHOD LeMail|Send][/METHOD]
			
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]Demande de RDV[/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM][!CONF::MODULE::SYSTEME::CONTACTSIMULATEUR!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACTSIMULATEUR!][/PARAM][/METHOD]
			[METHOD LeMail|To][PARAM][!I_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|Body]
				[PARAM]
					[BLOC Mail]
					<font face="arial" color="#000000" size="2">
						<strong>Votre demande a bien été prise en compte.</strong><br /><br />
						Voici les informations que vous nous avez transmises : <br />
						Nom : <span style="text-transform:uppercase">[!I_Nom!]</span><br /> 
						Téléphone : [!I_Tel!]  <br />
						Adresse : [!I_Adresse!]<br />[!I_Ville!]<br /><br />
						<strong>Rendez-vous demandé le : [DATE d.m.Y][!I_DateRDV!][/DATE]<br /> </strong>
						<strong>Plage horaire demandée : [IF [!I_Horaires!]=1]Matin[ELSE]Après midi [/IF]<br /><br /></strong>
						Gaz service vous remercie et vous recontacte dès que possible.
					</font>
					[/BLOC]
				[/PARAM]
			[/METHOD]
			[METHOD LeMail|BuildMail][/METHOD]
			[METHOD LeMail|Send][/METHOD]
		[/IF]
	[/IF] // if ip d'abtel
[/IF]
<div class="RecapResultat">
	[!Filtre:=!]

	[STORPROC Catalogue/Simulateur/Devis/[!Devis!]|Sim|0|1]
		[!Chemin:=Catalogue/Simulateur/[!Sim::Id!]!]
		<div class="Simulateur">
			<div class="SimulateurDescriptif">
				<h1>Récapitulatif de votre [!Sim::Titre!]</h1>
			</div>
		[!FiltreNiveau:=!][!PropaneUniquement:=0!][!SolMuraleChoisi:=!][!EnergieChoisi:=!]
		[STORPROC [!Chemin!]/Etape/Publier=1|Etp]
			// lecture etape
			<div class="RecapEtap">
				[!Etp::LibelleReponse!][!ChxRep:=0!]
				// gestion des produits qui correspondent à plusieurs niveau de confort
				[STORPROC [!Chemin!]/Etape/[!Etp::Id!]/Question/Publier=1|Qst|||Ordre|ASC]
					// lecture Question
					[STORPROC [!Chemin!]/Etape/[!Etp::Id!]/Question/[!Qst::Id!]/Choix|Chx]
						// lecture Choix de réponse
						[STORPROC Catalogue/Devis/[!Devis!]/Reponse/Etape=[!Etp::Id!]&Question=[!Qst::Id!]&Reponse=[!Chx::Id!]|Rep]
							<span>[IF [!ChxRep!]=0][!ChxRep:=1!][ELSE],[/IF][!Chx::LibelleReponse!]</span>
							[IF [!Qst::ChoixPrime!]=1]
								// MODIFICATION 26/12/2013 pour chgt tva et autre reglementation
								[IF [!Chx::LibelleReponse!]~inférieur][!ChampPrime:=PrmEnrMoins!][ELSE][!ChampPrime:=PrmEnrPlus!][/IF]
								[IF [!TMS::Now!]>=1388530818][!ChampPrime:=PrimeEnergie!][/IF]
							[/IF]
							[IF [!Qst::ChampProduit!]=SolMurale||[!Qst::ChampProduit!]=Energie||[!Qst::ChampProduit!]=EmetteurChauffage]
								// Modification suite au mail du 12/03/2014
								[IF [!Qst::ChampProduit!]=SolMurale]
									[!SolMuraleChoisi:=[!Chx::ValeurChamp!]!]
								[/IF]
								[IF [!Qst::ChampProduit!]=Energie]	
									[!EnergieChoisi:=[!Chx::ValeurChamp!]!]
									[IF [!EnergieChoisi!]=1][!PropaneUniquement:=1!][/IF]

								[/IF]
								[IF [!Qst::ChampProduit!]=EmetteurChauffage]	
									// cas de radiateur et ou plancher chauffant
									[IF [!EmetteurChauffageChoisi!]=0][!Filtre+=[!Qst::ChampProduit!]=[!Chx::ValeurChamp!]!][/IF]
									
								[/IF]
							[ELSE]
								[IF [!Qst::ChampProduit!]!=]
									[IF [!Filtre!]!=][!Filtre+=&!][/IF]
									[!Filtre+=[!Qst::ChampProduit!]=[!Chx::ValeurChamp!]!]
								[ELSE]
									[IF [!Qst::Filtre!]!=0]	
										//cas particulier du niveau
										[IF [!Qst::Id!]=6][!FiltreNiveau:=[!Chx::ValeurChamp!]!][/IF]
										//cas particulier de l'énergie
										[IF [!Qst::Id!]=4]
											[IF [!Chx::Id!]=9][!Requete:=Catalogue/Categorie/3/Produit!][/IF]
											//[IF [!Chx::Id!]=8][!Requete:=Catalogue/Categorie/4/Produit!][/IF]
										[/IF]
										
	
									[/IF]
								[/IF]
							[/IF]
						[/STORPROC]
					[/STORPROC]
				[/STORPROC]
			</div>
		[/STORPROC]		
	[/STORPROC]
</div>
[IF [!Requete!]=][!Requete:=Catalogue/Produit!][/IF]
[IF [!Filtre!]!=]
	[!Requete+=/Publier=1&Simulateur=1&[!Filtre!]!]
[ELSE]
	[!Requete+=/Publier=1&Simulateur=1!]
[/IF]
[IF [!FiltreNiveau!]=1]
	[!Requete+=&Niveau1=1!]
[/IF]
[IF [!FiltreNiveau!]=2]
	[!Requete+=&Niveau2=1!]
[/IF]
[IF [!FiltreNiveau!]=3]
	[!Requete+=&Niveau3=1!]
[/IF]

[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]
La requete :: [!Requete!] <br />
[/IF]


// mail si non prise de rdv
[IF [!I_RDVDevis!]]
[ELSE]
	[STORPROC Catalogue/Devis/[!Devis!]|Dv|0|1][/STORPROC]
	[IF [!MailEnvoye!]]
		//mail déjà envoyé
	[ELSE]
		[IF [!SERVER::REMOTE_ADDR!]!=178.22.145.106]
		
			[METHOD Dv|Set]
				[PARAM]MailEnvoye[/PARAM]
				[PARAM]1[/PARAM]
			[/METHOD]
			[METHOD Dv|Save][/METHOD]
			// il n'a pas été demandé de rdv mais on envoie un mail
			// message simulation pour gazservice
			[LIB Mail|LeMail]
			[METHOD LeMail|Subject][PARAM]Simulation sur le site[/PARAM][/METHOD]
			[METHOD LeMail|From][PARAM][!I_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|ReplyTo][PARAM][!I_Mail!][/PARAM][/METHOD]
			[METHOD LeMail|To][PARAM][!CONF::MODULE::SYSTEME::CONTACTSIMULATEUR!][/PARAM][/METHOD]
			[METHOD LeMail|Bcc][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
			[METHOD LeMail|Cc][PARAM]nimes@gaz-service.fr[/PARAM][/METHOD]
			//[METHOD LeMail|Bcc][PARAM]p.huys@gaz-service.fr[/PARAM][/METHOD] --> contact c'est phuys
			//[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
			[METHOD LeMail|Body]
				[PARAM]
					[BLOC MailInterne]
					<font face="arial" color="#000000" size="2">
						<strong>Simulation effectuée</strong> <br />
						<br />MERCI DE VERIFIER QU'UN RDV N'A PAS ÉTE DEMANDÉ <br />
						//<strong>Adresse Ip</strong> :<span><a href="http://www.geoiptool.com/fr/?IP=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
						<strong>Nom : </strong> <span style="text-transform:uppercase">[!I_Nom!]</span>  <br />
						<strong>Mail : </strong> <span >[!I_Mail!]</span>  <br />
						<strong>Tél : </strong> [!I_Tel!] <br />
	
						<strong>Coordonnees</strong> : [!I_Adresse!]<br />[!I_Ville!]<br /><br />
						<br /><br /><strong>Produits en réponse simulateur:<br /></strong>
						[COUNT [!Requete!]|NbRes]
						[IF [!NbRes!]=0]
							Pas de produits correspondant à la simulation<br />
						[ELSE]
							[STORPROC [!Requete!]|RS|0|2|tmsCreate|DESC]
								[!RS::References!] - [!RS::Titre!]<br />
							[/STORPROC]
						[/IF]
					</font>
					[/BLOC]
				[/PARAM]
			[/METHOD]
			[METHOD LeMail|BuildMail][/METHOD]
			[METHOD LeMail|Send][/METHOD]
		[/IF]
	[/IF] // fin if abtel
[/IF]

<div class="RecapInfo">
<p>Ci-dessous une estimation d’après vos critères de sélection pour le remplacement de votre appareil de chauffage.<br />
Nous vous contactons dans les meilleurs délais par courriel ou par téléphone pour vous confirmer l’estimation de votre devis.<br />
Vous pouvez aussi prendre un rendez-vous à votre convenance avec nos services en fin de formulaire.</p>
</div>
<div class="RecapReponse">
	<div class="TitreRecap">Produits que nous vous conseillons</div>
	// compter le nombre de produit en resultat
	[COUNT [!Requete!]|NbRes]
	[IF [!NbRes!]=0]
		<div class="STitreRecap">Après étude de vos réponses et de vos souhaits nous vous conseillons de nous contacter afin de vous faire une réponse personnalisée : </div>
	[ELSE]
//		[IF [!NbRes!]>2][!NbRes:=2!][/IF]
//		<div class="STitreRecap">Après étude de vos réponses et de vos souhaits nous vous conseillons, les [!NbRes!] produits suivants : </div>
		<div class="STitreRecap">Après étude de vos réponses et de vos souhaits nous vous conseillons, les produits suivants : </div>
	[/IF]

	// une boucle par produit répondu
	// changement 26 décembre 2013 , prise en charge de date limite pour les taux de tva
	//[STORPROC Catalogue/Tauxtva/TauxSimulateur=1|Tx|0|1][!TauxTva:=[!Tx::Taux!]!][/STORPROC]


/// CHANGEMENT JANVIER 2014 SUITE À REUNION AVEC P. HUYS IL N'Y A PLUS DE TAUX DE TVA SPECIFIQUE AU SIMULATEUR
// ON APPLIQUE LE TAUX DU PRODUIT
//	[!TauxTva:=1!]
//	[STORPROC Catalogue/TypeTaux/Application=Simulateur|Ttva|0|1]
//		[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
//			[!TauxTva:=[!Tx::Taux!]!]
//		[/STORPROC]
//	[/STORPROC]
	
	[!Cpt:=0!]
[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]
	[!Requete!]
[/IF]


	[STORPROC [!Requete!]|RS|||tmsCreate|DESC]
		// Modif suite au mail du 12/03/2014
		[!Affiche:=1!]
		[IF [!RS::Energie!]!=2&&[!EnergieChoisi!]!=[!RS::Energie!]]
			[!Affiche:=0!]
			[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]
				//<br />je suis passée dans energie choisie ne correspond pas
			[/IF]

		[/IF]
		[IF [!RS::SolMurale!]=Sol||[!RS::SolMurale!]=Murale]
			//je fais comme ça car on teste sur un libellé avec des espaces
			[IF [!SolMuraleChoisi!]!=[!RS::SolMurale!]]
				[!Affiche:=0!]
				[IF [!SERVER::REMOTE_ADDR!]=178.22.145.106]
				//	<br />je suis passée dans solmurale choisie ne correspond pas
				[/IF]

			[/IF]
		[/IF]


		[IF [!Affiche!]=1]
			// Gestion TVa /// CHANGEMENT JANVIER 2014 SUITE À REUNION AVEC P. HUYS ON APPLIQUE LE TAUX DE PRODUIT
			[!TauxTva:=1!]
	
//			[STORPROC Catalogue/TypeTaux/[!RS::TypeTaux!]|Ttva|0|1]
			[STORPROC Catalogue/TypeTaux/Nom=[!RS::TypeTaux!]|Ttva|0|1]
				[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
					[!TauxTva:=[!Tx::Taux!]!]
				[/STORPROC]
			[/STORPROC]
	
			// on lie les produits proposés pour ce devis
			[METHOD RS|AddParent]
				[PARAM]Catalogue/Devis/[!Devis!][/PARAM]
			[/METHOD]
			[METHOD RS|Save][/METHOD]
			[STORPROC Catalogue/Categorie/Produit/[!RS::Id!]|Cat|0|1][/STORPROC]
			// création du lien vers fiche
			[!LienFiche:=[!Systeme::getMenu(Catalogue/Categorie/1)!]/!]
			[STORPROC Catalogue/Categorie/Categorie/[!Cat::Id!]|PCat|0|1]
				[!LienFiche+=[!PCat::Url!]/!]
			[/STORPROC]
			[!LienFiche+=[!Cat::Url!]/!]
			[!LienFiche+=Produit/[!RS::Url!]!]
			// calcul de toutes les infos dont on a besoin
			[!TotTTC:=[!RS::PrixTTC([!RS::PPHT!],[!TauxTva!])!]!]
	
			[IF [!RS::CreditImpot!]=0]
				[!MtCredidImpot:=!]
				[!LibMtCredidImpot:=non assujetti au crédit d'impôt!]
			[ELSE]
				[!LibMtCredidImpot:=[!RS::getPropriete(CreditImpot)!]!]
				[STORPROC [!LibMtCredidImpot::Values!]|V]
					[!VA:=[![!V!]:/::!]!]
					[IF [!RS::CreditImpot!]=[!VA::0!]]
						[!LibMtCredidImpot:=[!VA::1!]!]
					[/IF]
				[/STORPROC]
				[!MtCredidImpot:=[!RS::CalcCreditImpot([!TotTTC!])!]!]
	
			[/IF]
			// Ajout des montants ht
			[!TotHt:=[!RS::PPHT!]!]
			[!TotHt+=[!RS::PxPose!]!]
			[!TotHt+=[!RS::PxAccMont!]!]
			[IF [!RS::CertificatOffert!]=0][!TotHt+=[!RS::PxCertiConf!]!][/IF]
	
			[!TotalTout:=[!RS::PrixTTC([!TotHt!],[!TauxTva!])!]!]
			[IF [!RS::[!ChampPrime!]!]!=0]
				[!TotalTout-=[!RS::[!ChampPrime!]!]!]
			[/IF]
	
			<div class="UnProduitReponse[IF [!Pair!]=1]Pair[/IF]">
				[IF [!Pair!]=][!Pair:=1!][ELSE][!Pair:=!][/IF]
				<div class="Fiche">
					<div class="NomCategorie"><h2>[!Cat::Nom!]</h2></div>
					<div class="LeProduit">
						<div class="PhotoProduit">
							<img src="[IF [!RS::Image!]!=]/[!RS::Image!].limit.78x117.jpg[ELSE][!Domaine!]/Skins/[!Systeme::Skin!]/Img/defautProd.jpg.limit.78x117.jpg[/IF]" title="[!RS::Titre!]" alt="[!RS::Titre!]" />
						</div>
						<div class="DescProduit">
							[IF [!RS::Fabricant!]!=]
								[STORPROC Catalogue/Fabricant/[!RS::Fabricant!]|Fab|0|1][/STORPROC]
								<div class="UneInfoTitre">[!Fab::Nom!]</div>
							[/IF]
							[IF [!RS::Titre!]!=]<div class="UneInfoTitre [IF [!RS::Chapo!]=]MargB10[/IF]" >[!RS::Titre!]</div>[/IF]
							[IF [!RS::Chapo!]!=]<div class="UneInfoSousTitre " >[!RS::Chapo!]</div>[/IF]
							[IF [!RS::Dimensions!]!=]<div class="UneInfo" >- [!RS::Dimensions!]</div>[/IF]
							[IF [!RS::SolMurale!]!=]<div class="UneInfo">- [!RS::SolMurale!]</div>[/IF]
							[IF [!RS::Service!]!=]<div class="UneInfo">- [!RS::Service!]</div>[/IF]
							[IF [!RS::Evacuation!]!=]
								<div class="UneInfo">
									[SWITCH [!RS::Evacuation!]|=]
										[CASE CF]
											- Conduit Fumée
										[/CASE]
										[CASE FF]
											- Flux forcé
										[/CASE]
										[CASE VMC Gaz]
											- VMC Gaz
										[/CASE]
									[/SWITCH]
								</div>
							[/IF]
							[IF [!RS::CreditImpot!]!=0]
								<br /><div class="UneInfo">- Crédit d'impôt : - [!Utils::getPrice([!MtCredidImpot!])!] € HT</div>
								<div class="UneInfo">[!LibMtCredidImpot!][IF [!RS::CreditImpot!]!=0] de [!Cat::Nom!][/IF]</div>
							[ELSE]
								<br /><div class="UneInfo"><span style="font-style:italic;">[!LibMtCredidImpot!]</span></div>
							[/IF]
						</div>
					</div>
	
					<div class="lienFiche"><a href="/[!LienFiche!]" alt="Voir la fiche" target="_blank" >Détail du produit</a></div>
				</div>
				<div class="PreDevis">
					<div class="NomProduit"><h2>Pré Devis</h2></div>
					
					<div class="LbNValN12">
							<label class="DesignationDevis">DÉSIGNATION</label>
							<div class="MontantDevis">MONTANT</div>
					</div>
					<div class="ResultatTexte">
						- Remplacement de votre chaudière par une chaudière de marque <strong>[!Fab::Nom!]</strong>, modèle <strong>[!RS::Titre!]</strong> [!RS::Chapo!]<br />
						- Dépose de l'ancienne chaudière, rinçage et traitement de l'installation.<br />
						- Raccordement des tuyauteries.<br />
						- Remplissage de l'installation et mise en service officielle.<br />
						- Établissement du certificat de conformité modèle CC4  
					</div>
					<div class="HauteurFixe">
						<div class="LbNValN1">
							// label noir valeur noir
							<label>[!Cat::Nom!] HT</label>
							<div>[!RS::PPHT!] € HT</div>
						</div>
						//[IF [!Cat::Id!]!=4]
							<div class="ValorisationTTC">
								[!Cat::Nom!] valorisée au prix TTC de [!Utils::getPrice([!TotTTC!])!] € avec un taux de tva de [!TauxTva!] %
							</div>
						//[/IF]
						<div class="LbNValN">
							// label noir valeur noir
							<label>Forfait accessoires de montage</label>
							<div>[!Utils::getPrice([!RS::PxAccMont!])!] € HT</div>
						</div>
						<div class="LbNValN">
							// label noir valeur noir
							<label>Forfait pose</label>
							<div>[!Utils::getPrice([!RS::PxPose!])!] € HT</div>
						</div>
						<div class="LbVValV">
							// label vert valeur vert
							<label>Certificat de conformité modèle CC4  
								[IF [!RS::CertificatOffert!]!=0][IF [!Utils::getPrice([!RS::PxCertiConf!])!]!=0]
									à [!Utils::getPrice([!RS::PxCertiConf!])!]€ Ttc
								[/IF][/IF]
							</label>
							[IF [!RS::CertificatOffert!]=0]<div>[!RS::PxCertiConf!]</div>[ELSE]<div class="">OFFERT</div>[/IF]
						</div>
					</div>
					<div class="Total">
						// label noir valeur vert
						<label>Total <span class="apartir">(à partir de)</span></label>
						<div>
							[!Utils::getPrice([!TotalTout!])!] € TTC
						</div>
					</div>
	
					//<div class="TVA" style="font-style:italic;">* TVA [!TauxTva!]% concerne les particuliers pour une habitation de plus de 2 ans</div>
					
	
				</div>
			</div>
		[/IF]
	[/STORPROC]

	

</div>

<div id="Rdv">
	<div class="MentionsDebut">
		[IF [!NbRes!]!=0]
			Ceci n'est qu'un pré devis qui ne pourra être validé qu'après le passage <b>GRATUIT</b> d'un de nos techniciens.
		[ELSE]
			Le passage d'un de nos techniciens est <b>GRATUIT</b>.
		[/IF]
	</div>
	[IF [!A_PLANNIFIE!]=1]	
		<div class="BlocReponse"  >
			<div class="BlocReponseGauche">
				<strong>RDV Enregistré</strong>
				Une opératrice va vous recontacter pour confirmer le rdv.
				<br />Gaz Service vous remercie.
			</div>
			<div class="BlocReponseDroiteLast">
				<a href="/Simulateur/FicheDevis/[!Devis!]" title="Imprimer le pré-devis" alt="Imprimer le pré-devis" class="ImpressionDevis" target="_blank"  ></a>
				<a href="/" title="Accueil Gaz service" alt="Accueil Gaz service" class="RetourAccueilDevis" ></a>
			</div>	
	
		</div>	
	[ELSE]
		[IF [!I_Error!]=1]
			<div class="BlocErrorSim">
				<strong>Erreur dans votre formulaire :</strong>
				<ul>
					[IF [!I_Nom_Error!]]<li>Le nom est obligatoire</li>[/IF]
					[IF [!I_Mail_Error!]]<li>L'adresse mail est incorrecte</li>[/IF]
					[IF [!I_Ville_Error!]]<li>La ville est obligatoire</li>[/IF]
					[IF [!I_Adresse_Error!]]<li>Votre adresse est obligatoire</li>[/IF]
					[IF [!I_DateRDV_Error!]]<li>Merci de choisir la date de votre Rdv</li>[/IF]
					[IF [!I_Horaires_Error!]]<li>Merci de choisir la plage horaire de votre rdv</li>[/IF]
					[IF [!C_Code_Error!]]<li>Opération fausse</li>[/IF]
				</ul>
			</div>
		[/IF]
		<div class="PlanningRDV" >
			<h2>Planifier la visite <b>gratuite</b> d'un technicien</h2>
			<form action="/[!Lien!]#Rdv" method="post" enctype="multipart/form-data" name="form_Simulateur" class="form_PlanningRDV" >
				<div class="Coordonnes">
					<div class="LigneForm">
						<label>Nom <span class="obligatoire">*</span></label>
						<input type="text"   name="I_Nom" value="[IF [!I_Nom!]=][!Sim::Nom!][ELSE][!I_Nom!][/IF]" tabindex="10"  style="text-transform:uppercase;" [IF [!I_Nom_Error!]]class="Error"[ELSE][/IF]  />
					</div>
					<div class="LigneForm" style="margin-right:50px;">
						<label>Téléphone <span class="obligatoire">*</span></label>
						<input type="text"  name="I_Tel" value="[IF [!I_Tel!]=][!Sim::Telephone!][ELSE][!I_Tel!][/IF]" tabindex="15"  [IF [!I_Tel_Error!]]class="Error"[ELSE][/IF]/>
					</div>
					<div class="LigneForm">
						<label>Ville <span class="obligatoire">*</span></label>
						<input type="text"  name="I_Ville" value="[IF [!I_Ville!]=][!Sim::Ville!][ELSE][!I_Ville!][/IF]" tabindex="20"  [IF [!I_Ville_Error!]]class="Error"[ELSE][/IF]/>
						
					</div>
					<div class="LigneForm" style="margin-right:50px;">
						<label>Votre e-mail <span class="obligatoire">*</span></label>
						<input type="text"  name="I_Mail" value="[IF [!I_Mail!]=][!Sim::Email!][ELSE][!I_Mail!][/IF]" tabindex="25" [IF [!I_Mail_Error!]]class="Error"[/IF]/>				
					</div>
					<div class="LigneForm">
						<label>Adresse <span class="obligatoire">*</span></label>
						<input type="text"  name="I_Adresse" value="[IF [!I_Adresse!]=] Tapez votre adresse[ELSE][!I_Adresse!][/IF]" tabindex="30" onclick="if (this.value=='Tapez votre adresse') this.value='';" [IF [!I_Adresse_Error!]]class="Error"[ELSE][/IF] style="color:#ff0000;" />
						
					</div>
				</div>
				<div  style="overflow:hidden;">
					<h3>Votre Rendez-vous* :</h3>
					<div class="LigneForm"  style="float:none;">
						<label>Date<span class="obligatoire">*</span></label>
					
						//<input type="text"  class="ncalendar" name="I_DateRDV" value="[!I_DateRDV!]" tabindex="35" [IF [!I_Date_Error!]]class="Error"[/IF] />
						
						<input name='I_DateRDV' type='text' value='[!I_DateRDV!]' class='date date_toggled' style='display: inline;width:100px;text-align:center;' />
						<img src='[!Domaine!]/Skins/[!Systeme::Skin!]/Img/Calendrier/calendar.png' class='date_toggler' style='position: relative; top: 0; margin-left: 4px;' />
						<script type="text/javascript">
							window.addEvent('load', function() {
								new DatePicker('.date_toggled', {
								pickerClass: 'datepicker_dashboard',
								allowEmpty: true,
								toggleElements: '.date_toggler'
								
								});
							});
						</script>
	
					</div>
					
					<div class="" style="float:none;">
						
						<label style="float:left;padding-top:2px;padding-right:40px;">Matin*:</label>
						<input style="float:left;" type="Radio"  name="I_Horaires" [IF [!I_Horaires!]=1||[!I_Horaires!]=]checked="checked" [/IF] value="1" tabindex="35" />
						
						<label style="float:left;width:206px;padding-top:2px;">entre 8h30 et 13h30</label>
						<label style="float:left;padding-top:2px;padding-right:40px;">Après-midi*:</label>
						<input style="float:left;" type="Radio"  name="I_Horaires" [IF [!I_Horaires!]=2]checked="checked" [/IF] value="2" tabindex="36" />
						<label style="float:left;width:150px;padding-top:2px;">entre 13h30 et 18h00</label>
					</div>
					
				</div>
				<div class="MentionsFin">Vous pourrez préciser l'horaire lors de la confirmation de rendez-vous.</div>
				<div class="ADroite">
					//<div class="Operations">
						//<label>Merci de résoudre cette opération </label>
						//[IF [!Nb1!]=]
							//[!Nb1:=[!Utils::random(5)!]!]
							//[!Nb1+=4!]
							//[!Nb2:=[!Utils::random(4)!]!]
							//[IF [!Utils::random(1)!]][!Op:=-!][ELSE][!Op:=+!][/IF]
							//[!Tot:=[!Nb1!]!]
							//[IF [!Op!]=-][!Tot-=[!Nb2!]!][ELSE][!Tot+=[!Nb2!]!][/IF]
							//[!hash:=[!Utils::md5([!Tot!])!]!]
						//[/IF]
						//<input type="text" readonly="readonly"  name="Nb1"    value="[!Nb1!]" style="width:25px;float:none;text-align:center;" />
						//<input type="text"                      name="Op"     value="[!Op!]"  style="width:15px;float:none;text-align:center;"  />
						//<input type="text" readonly="readonly"  name="Nb2"    value="[!Nb2!]" style="width:25px;float:none;text-align:center;"/>
						//= <input type="text"                    name="Result" value="[!Result!]" style="width:25px;float:none;text-align:center;" [IF [!C_Code_Error!]] class="Error" [/IF] />
						//<input type="hidden" name="hash" value="[!hash!]" />
						
					//</div>
							
					<div class="Buttons">
						<button type="submit">Valider</button>
						<input type="hidden" name="I_RDVDevis" value="1" />
						<input type="hidden" name="Devis" value="[!Devis!]" />
						
						
					</div>
				</div>
		
			</form>
		</div>
	[/IF]
</div>		
