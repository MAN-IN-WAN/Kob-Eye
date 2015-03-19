[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
[STORPROC [!Query!]|ModelC][/STORPROC]

<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>
					[IF [!Action!]!=""]
						//Maintenant on ouvre le fichier en ecriture
						[!Compte:=0!]
						[!Erreur:=0!][!Abon:=0!]
						[!Existe:=0!]
						[!realname:=[!Utils::getFileName([!Form_Adresse_Upload!])!]!]
						[STORPROC Explorateur/_Dossier/Home/[!Systeme::User::Id!]/Boutique/[!realname!]|File][/STORPROC]
						
						//On configure le php.ini pour ouvrir une page de plus de 150Mo
						[INI memory_limit]80M[/INI]
						[INI max_execution_time]3600[/INI]
						//[STORPROC [!ModelC::explodeCSV([!File::Contenu!])!]|Ligne2|0|1]
						[STORPROC [![!File::Contenu!]:/%RC%!]|Ligne2|0|100]
							//Enregistrement des nouveaux client
							//[!Ligne:=[!Utils::Clean([!Ligne2!])!]!]
							[!Ligne:=[![!Ligne2!]:/;!]!]
							// RECHERCHE DU CLIENT PAR SON ADRESSE MAIL
							[STORPROC Boutique/Client/Mail=[!Ligne::8!]|CL2|0|1]
								[!Existe+=1!][!CREATIONCLI:=0!]
								[NORESULT]
									// SI CLIENT INEXISTANT ON LE CREE
									[OBJ Boutique|Client|Cli]
									[METHOD Cli|Set][PARAM]Civilite[/PARAM][PARAM][!Ligne::0!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Prenom[/PARAM][PARAM][!Ligne::1!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Nom[/PARAM][PARAM][!Ligne::2!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Adresse[/PARAM][PARAM][!Ligne::3!]<br />[!Ligne::4!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]CodePostal[/PARAM][PARAM][!Ligne::5!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Ville[/PARAM][PARAM][!Ligne::6!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Pays[/PARAM][PARAM][!Ligne::7!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Mail[/PARAM][PARAM][!Ligne::8!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Tel[/PARAM][PARAM][!Ligne::9!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Portable[/PARAM][PARAM][!Ligne::10!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Pseudonyme[/PARAM][PARAM][!Ligne::8!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Pass[/PARAM][PARAM][!Ligne::2!][/PARAM][/METHOD]
									[METHOD Cli|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
//NE SERP PAS FAIT DANS PHP
//[METHOD Cli|Save][/METHOD]
//[METHOD Cli|AddParent][PARAM]Systeme/Group/6[/PARAM][/METHOD]
									[METHOD Cli|Save][PARAM]1[/PARAM][/METHOD]
									CREE [!Ligne::8!] <br/>[!CREATIONCLI:=1!]
								[/NORESULT]
							[/STORPROC]
							// JE RELIS LE CLIENT AVANT DE CREER L'ABONNEMENT
							[STORPROC Boutique/Client/Nom=[!Ligne::2!]&Prenom=[!Ligne::1!]&Mail=[!Ligne::8!]|CL|0|1]
								[IF [!CREATIONCLI!]=1]
									[STORPROC Newsletter/Contact/Email=[!Ligne::8!]|Con]
										[NORESULT]
											[OBJ Newsletter|Contact|Con]
										[/NORESULT]
									[/STORPROC]
									[METHOD Con|Set]
										[PARAM]Email[/PARAM][PARAM][!Ligne::8!][/PARAM]
									[/METHOD]
									[METHOD Con|Set]
										[PARAM]Nom[/PARAM][PARAM][!Ligne::2!][/PARAM]
									[/METHOD]							
									[METHOD Con|Set]
										[PARAM]Prenom[/PARAM][PARAM][!Ligne::1!][/PARAM]
									[/METHOD]							
									[METHOD Con|Set]
										[PARAM]Adresse[/PARAM][PARAM][!Ligne::3!]<br />[!Ligne::4!][/PARAM]
									[/METHOD]							
									[METHOD Con|Set]
										[PARAM]CodePostal[/PARAM][PARAM][!Ligne::5!][/PARAM]
									[/METHOD]							
									[METHOD Con|Set]
										[PARAM]Ville[/PARAM][PARAM][!Ligne::6!][/PARAM]
									[/METHOD]							
									[METHOD Con|Set]
										[PARAM]Telephone[/PARAM][PARAM][!Ligne::9!][/PARAM]
									[/METHOD]							
									[METHOD Con|Set]
										[PARAM]Mobile[/PARAM][PARAM][!Ligne::10!][/PARAM]
									[/METHOD]
									[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
									//groupe "clients"
									[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/7[/PARAM][/METHOD]
									[METHOD Con|Save][/METHOD]
						
									// Enregistrement première adresse LIVRAISON + FACTURATION (uniquement en création)
									[OBJ Boutique|Adresse|AdrPers]
									[METHOD AdrPers|Set]
										[PARAM]Civilite[/PARAM][PARAM][!Ligne::0!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]Nom[/PARAM][PARAM][!Ligne::2!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]Prenom[/PARAM][PARAM][!Ligne::1!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]Adresse[/PARAM][PARAM][!Ligne::3!]<br />[!Ligne::4!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]CodePostal[/PARAM][PARAM][!Ligne::5!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]Ville[/PARAM][PARAM][!Ligne::6!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]Pays[/PARAM][PARAM][!Ligne::7!][/PARAM]
									[/METHOD]
									[METHOD AdrPers|Set]
										[PARAM]Type[/PARAM][PARAM]Livraison[/PARAM]
									[/METHOD]
									[METHOD AdrPers|AddParent][PARAM]Boutique/Client/[!CL::Id!][/PARAM][/METHOD]
									[METHOD AdrPers|Save][/METHOD]
									[!AdrFact:=[!AdrPers::getClone()!]!]
									[METHOD AdrFact|Set]
										[PARAM]Type[/PARAM][PARAM]Facturation[/PARAM]
									[/METHOD]
									[METHOD AdrFact|AddParent][PARAM]Boutique/Client/[!CL::Id!][/PARAM][/METHOD]
									[METHOD AdrFact|Save][/METHOD]
								[/IF]


								[IF [!Ligne::11!]!=]
									[!AboCree:=0!]
									[STORPROC Boutique/Client/[!CL::Id!]/Service/Nom=[!Ligne::11!]|Abo2|0|1]
										[NORESULT]
											// y a un abonnement
											[OBJ Boutique|Service|Abo] 
											[METHOD Abo|Set]
												[PARAM]Nom[/PARAM]
												[PARAM][!Ligne::11!][/PARAM]
											[/METHOD]
											[METHOD Abo|Set]
												[PARAM]DateDebut[/PARAM]
												[PARAM][!Ligne::12!][/PARAM]
											[/METHOD]
											[METHOD Abo|Set]
												[PARAM]DateFin[/PARAM]
												[PARAM][!Ligne::13!][/PARAM]
											[/METHOD]
											[METHOD Abo|Set]
												[PARAM]Remise[/PARAM]
												[PARAM]20[/PARAM]
											[/METHOD]
											[METHOD Abo|AddParent]		
												[PARAM]Boutique/Client/[!CL::Id!][/PARAM]
											[/METHOD]
											[METHOD Abo|AddParent]		
												[PARAM]Boutique/Magasin/1[/PARAM]
											[/METHOD]
											[METHOD Abo|Save][/METHOD]
											[!Abon+=1!][!AboCree:=1!]
										[/NORESULT]
									[/STORPROC]
								[/IF]
								// JE RELIS LE USER AVANT D'ENVOYER LE MAIL
								[STORPROC Systeme/User/[!CL::UserId!]|Us|0|1][/STORPROC]
								[LIB Mail|LeMail]
								[METHOD LeMail|From][PARAM]noreply@kirigami.fr[/PARAM][/METHOD]
								[METHOD LeMail|To][PARAM][!Ligne::8!][/PARAM][/METHOD]
								[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
								[METHOD LeMail|Subject][PARAM]Lovepaper:Ré-Inscription nouveau site[/PARAM][/METHOD]	
								[METHOD LeMail|Body]
									[PARAM]
										[BLOC Mail]
											Bonjour [!Ligne::0!] [!Ligne::1!] [!Ligne::2!],<br />
											Love Paper vient de recréer votre compte client sur son nouveau site.<br />
											Pour cela nous avons dû vous vous attribuer un nouveau mot de passe que nous vous invitons à modifier en cliquant sur le lien figurant en bas de ce mail.<br />
											<hr/>
											Vous trouverez ci-dessous un récapitulatif de vos coordonnées, de votre identifiant de connexion et de votre nouveau mot de passe.<br />
											[!Ligne::0!]&nbsp;&nbsp;[!Ligne::1!]&nbsp;&nbsp;[!Ligne::2!]<br/>
											[!Ligne::3!]<br/>
											[!Ligne::5!]&nbsp;&nbsp;[!Ligne::6!]<br/>
											Tél [!Ligne::9!]<br/><br/>
											Mobile [!Ligne::10!]<br/><br/>
											Identifiant : [!Ligne::8!]<br/>
											Mot de passe : [!Ligne::2!]<br/>
											[IF [!AboCree!]=1]
												<br/>En tant qu'abonné vous bénéficiez de 20% de réduction sur l'ensemble de la boutique Loisir Créatif.<br />Les prix affichés lorsque vous vous connecterez à partir de votre compte client  tiennent déjà compte de cette réduction. <br />Nous espérons que notre nouveau site vous plaira et nous tenons à votre dispostion si vous avez la moindre question.<br />
											[/IF]
											<hr/>
											Toute l'équipe de LovePaper vous remercie de votre confiance et de votre fidélité.<br/><br/>
											<hr/>
											<br/>Lien pour accéder à votre compte et modifier votre mot de passe. <br /><br />
											http://loisirscreatifs.kirigami.fr/RecupPass?Mail=[!Ligne::8!]&CodeVerif=[!Us::CodeVerif!]<br /><br />
										[/BLOC]
									[/PARAM]
								[/METHOD]
								[METHOD LeMail|Send][/METHOD]
							[/STORPROC]

						[/STORPROC]
						---------------------------------------------------
						<li>[!Compte!] Clients ajouts avec succes.</li>
						<li>[!Existe!] Clients déja existants.</li>
						<li>[!Abon!] Abonnements créés.</li>
					[ELSE]
						//Maintenant on ouvre le fichier en ecriture
						<form enctype="multipart/form-data" action="" method="post" name="frm" >
						<div class="Propriete">
							<div class="ProprieteTitre">Fichier CSV : </div>
								<div class="ProprieteValeur">
								<div id="Form_Adresse_Upload_DivUpload">
								<div class="Content">
									<div class="UploadProgress">
										<img src="/Skins/AdminV2/Img/fancy/progress/bar.gif" class="progress current-progress" />
										<div class="current-text"></div>
									</div>
									<a class="Browse" href="#">Attacher un fichier</a>
								</div>
								<div class="Result" style="display:none">
									<input type="text" class="Champ"  id="Form_Adresse_Upload" name="Form_Adresse_Upload" value="" style="display:none;"/>
									<a class="Toggle">Changer de fichier</a>
									<span class="FileName"></span>
								</div>
							</div>
							<ul id="Form_Adresse_Upload_List" style="display:none"></ul>
							<script type="text/javascript">
								var Cook = Cookie.read('KE_SESSID');
								Fl.makeUpload("Form_Adresse_Upload_DivUpload", "Form_Adresse_Upload_List",Cook,"[!Module::Actuel::Nom!]","[!ObjectTT!]"[IF [!Type!]=Popup],true[/IF]);
							</script>
							</div>
						</div>
						
						<input type="hidden" name="Action" value="Importer"/>
					[/IF]

			[/BLOC]
			<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
				[IF [!action_import!]=]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Annuler</a>
					<input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
				[ELSE]
					<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
				[/IF]
			</div>
			</form>
		[/BLOC]
	</div>
</div>
