[HEADER JS]Skins/AdminV2/Js/cal.js[/HEADER]
[HEADER JS]Skins/AdminV2/Js/datepicker.js[/HEADER]
[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
[STORPROC [!Query!]|ModelC][/STORPROC]
[IF [!Action!]!=]
	<div id="Container">
		<div id="Arbo">
			[BLOC Panneau][/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau]
				[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
					//Maintenant on ouvre le fichier en ecriture
					[!Compte:=0!]
					[!Erreur:=0!][!Abon:=0!]
					[!Existe:=0!]
					// RECHERCHE DU CLIENT PAR SON ADRESSE MAIL
					[STORPROC Boutique/Client/Mail=[!C_Mail!]|CL2|0|1]
						[!Existe+=1!][!CREATIONCLI:=0!]
						[NORESULT]
							// SI CLIENT INEXISTANT ON LE CREE
							[OBJ Boutique|Client|Cli]
							[METHOD Cli|Set][PARAM]Civilite[/PARAM][PARAM][!C_Civilite!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Adresse[/PARAM][PARAM][!C_Adresse!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]CodePostal[/PARAM][PARAM][!C_CodePostal!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Pays[/PARAM][PARAM][!C_Pays!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Mail[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Tel[/PARAM][PARAM][!C_Tel!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Portable[/PARAM][PARAM][!C_Mobile!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Pseudonyme[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Pass[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
							[METHOD Cli|Save][PARAM]1[/PARAM][/METHOD]
							[!CREATIONCLI:=1!]
						[/NORESULT]
					[/STORPROC]
					// JE RELIS LE CLIENT AVANT DE CREER L'ABONNEMENT
					[STORPROC Boutique/Client/Nom=[!C_Nom!]&Prenom=[!C_Prenom!]&Mail=[!C_Mail!]|CL|0|1]
						[IF [!CREATIONCLI!]=1]
							[STORPROC Newsletter/Contact/Email=[!C_Mail!]|Con]
								[NORESULT]
									[OBJ Newsletter|Contact|Con]
								[/NORESULT]
							[/STORPROC]
							[METHOD Con|Set]
								[PARAM]Email[/PARAM][PARAM][!C_Mail!][/PARAM]
							[/METHOD]
							[METHOD Con|Set]
								[PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM]
							[/METHOD]							
							[METHOD Con|Set]
								[PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM]
							[/METHOD]							
							[METHOD Con|Set]
								[PARAM]Adresse[/PARAM][PARAM][!C_Adresse!][/PARAM]
							[/METHOD]							
							[METHOD Con|Set]
								[PARAM]CodePostal[/PARAM][PARAM][!C_CodePostal!][/PARAM]
							[/METHOD]							
							[METHOD Con|Set]
								[PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM]
							[/METHOD]							
							[METHOD Con|Set]
								[PARAM]Telephone[/PARAM][PARAM][!C_Pays!][/PARAM]
							[/METHOD]							
							[METHOD Con|Set]
								[PARAM]Mobile[/PARAM][PARAM][!C_Mobile!][/PARAM]
							[/METHOD]
							[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
							//groupe "clients"
							[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/7[/PARAM][/METHOD]
							[METHOD Con|Save][/METHOD]
				
							// Enregistrement première adresse LIVRAISON + FACTURATION (uniquement en création)
							[OBJ Boutique|Adresse|AdrPers]
							[METHOD AdrPers|Set]
								[PARAM]Civilite[/PARAM][PARAM][!C_Civilite!][/PARAM]
							[/METHOD]
							[METHOD AdrPers|Set]
								[PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM]
							[/METHOD]
							[METHOD AdrPers|Set]
								[PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM]
							[/METHOD]
							[METHOD AdrPers|Set]
								[PARAM]Adresse[/PARAM][PARAM][!C_Adresse!][/PARAM]
							[/METHOD]
							[METHOD AdrPers|Set]
								[PARAM]CodePostal[/PARAM][PARAM][!C_CodePostal!][/PARAM]
							[/METHOD]
							[METHOD AdrPers|Set]
								[PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM]
							[/METHOD]
							[METHOD AdrPers|Set]
								[PARAM]Pays[/PARAM][PARAM][!C_Pays!][/PARAM]
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
						[IF [!C_Produit!]!=]
							[!AboCree:=0!]
							[STORPROC Boutique/Client/[!CL::Id!]/Service|Abo2|0|1]
								[NORESULT]
									// creation abonnement
									[OBJ Boutique|Service|Abo] 
									[METHOD Abo|Set]
										[PARAM]Nom[/PARAM]
										[PARAM][!C_Nom!][/PARAM]
									[/METHOD]
									[METHOD Abo|Set]
										[PARAM]DateDebut[/PARAM]
										[PARAM][!C_Debut!][/PARAM]
									[/METHOD]
									[METHOD Abo|Set]
										[PARAM]DateFin[/PARAM]
										[PARAM][!C_Fin!][/PARAM]
									[/METHOD]
									[METHOD Abo|Set]
										[PARAM]Remise[/PARAM]
										[PARAM]20[/PARAM]
									[/METHOD]
									[METHOD Abo|AddParent]		
										[PARAM]Boutique/Client/[!CL::Id!][/PARAM]
									[/METHOD]
									[METHOD Abo|AddParent]		
										[PARAM]Boutique/Produit/[!C_Produit!][/PARAM]
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
						[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
						[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
						[METHOD LeMail|Subject][PARAM]Lovepaper: votre abonnement à notre site[/PARAM][/METHOD]	
						[METHOD LeMail|Body]
							[PARAM]
								[BLOC Mail]
									Bonjour [!C_Civilite!] [!C_Prenom!] [!C_Nom!],<br />
									[IF [!CREATIONCLI!]=1]
										Love Paper vient de créer votre compte client sur son nouveau site. ainsi que votre abonnement<br />
										Pour cela nous avons attribué un mot de passe que nous vous invitons à modifier en cliquant sur le lien figurant en bas de ce mail.<br />
										<hr/>
										Vous trouverez ci-dessous un récapitulatif de vos coordonnées, de votre identifiant de connexion et de votre nouveau mot de passe.<br />
										[!C_Civilite!]&nbsp;&nbsp;[!C_Prenom!]&nbsp;&nbsp;[!C_Nom!]<br/>
										[!C_Adresse!]<br/>
										[!C_CodePostal!]&nbsp;&nbsp;[!C_Ville!]<br/>
										Tél [!C_Tel!]<br/><br/>
										Mobile [!C_Mobile!]<br/><br/>
										Identifiant : [!C_Mail!]<br/>
										Mot de passe : [!C_Nom!]<br/>
										<br/>Lien pour accéder à votre compte et modifier votre mot de passe. <br /><br />
										http://loisirscreatifs.kirigami.fr/RecupPass?Mail=[!8!]&CodeVerif=[!Us::CodeVerif!]<br /><br />
			
									[ELSE]
										[IF [!AboCree!]=1]Love Paper vient de créer votre abonnement.[/IF]
									[/IF]
									[IF [!AboCree!]=1]
										<br/>En tant qu'abonné vous bénéficiez de 20% de réduction sur l'ensemble de la boutique Loisir Créatif.<br />Les prix affichés lorsque vous vous connecterez à partir de votre compte client  tiennent déjà compte de cette réduction. <br />Nous espérons que notre nouveau site vous plaira et nous tenons à votre dispostion si vous avez la moindre question.<br />
									[/IF]
									<hr/>
									Toute l'équipe de LovePaper vous remercie de votre confiance et de votre fidélité.<br/><br/>
									<hr/>
								[/BLOC]
							[/PARAM]
						[/METHOD]
						[METHOD LeMail|Send][/METHOD]
					[/STORPROC]
				---------------------------------------------------
				ok tout est créé
				[/BLOC]
			[/BLOC]
		</div>
	</div>
[ELSE]
	<div id="Container">
		<div id="Arbo">
			[BLOC Panneau][/BLOC]
		</div>
		<div id="Data">
			[BLOC Panneau]
				[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
					<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>
<h1>Création du client, user, adresses s'il n'existe pas, Création de son abonnement et envoi d'un mail automatique </h1>
					<form  action="" method="post" name="frm" >
						<table>
							<tr>
								<td class="nomPropriete">Mail</td> 
								<td class="valeurPropriete"><input type="text" name="C_Mail" value="[!C_Mail!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Civilite</td>
								<td class="valeurPropriete">
									<select name="C_Civilite" class="Champ">
										<option value="">- Veuillez sélectionner -</option>
										<option value="Mademoiselle" [IF [!C_Civilite!]=Mademoiselle] selected="selected"[/IF]>Mademoiselle</option>
										<option value="Madame" [IF [!C_Civilite!]=Madame] selected="selected"[/IF]>Madame</option>
										<option value="Monsieur" [IF [!C_Civilite!]=Monsieur] selected="selected"[/IF]>Monsieur</option>
									</select>	
								</td>
							</tr>
							<tr>
								<td class="nomPropriete">Prenom</td> 
								<td class="valeurPropriete"><input type="text" name="C_Prenom" value="[!C_Prenom!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Nom</td> 
								<td class="valeurPropriete"><input type="text" name="C_Nom" value="[!C_Nom!]" class="Champ"><br /></td>
							</tr>
							<tr>
								<td class="nomPropriete">Adresse</td> 
								<td class="valeurPropriete">
									<textarea COLS="50" ROWS="10" class="Champ" name="C_Adresse" class="Champ">[!C_Adresse!]</textarea>
								</td>
							</tr>
							<tr>
								<td class="nomPropriete">CodePostal</td> 
								<td class="valeurPropriete"><input type="text" name="C_CodePostal" value="[!C_CodePostal!]" class="Champ">
								</td>
							</tr>
							<tr>
								<td class="nomPropriete">Ville</td> <td class="valeurPropriete"><input type="text" name="C_Ville" value="[!C_Ville!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Pays</td>
								<td class="nomPropriete">
									<select name="C_Pays" >
										[STORPROC Geographie/Pays|Pa|||Nom|ASC]
											<option value="[!Pa::Nom!]"  [IF [!C_Pays!]=&&[!Pa::Nom!]=France] selected="selected" [ELSE][IF [!C_Pays!]=[!Pa::Nom!]] selected="selected"[/IF][/IF]>[!Pa::Code!] - [!Pa::Nom!]</option>
										[/STORPROC]
									</select>
								</td>
							</tr>
							<tr>
								<td class="nomPropriete">Téléphone</td> 
								<td class="valeurPropriete"><input type="text" name="C_Tel" value="[!C_Tel!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Mobile</td> 
								<td class="valeurPropriete"><input type="text" name="C_Mobile" value="[!C_Mobile!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Abonnement</td> 
								<td class="valeurPropriete">
									<select name="C_Produit" >
										[STORPROC Boutique/Categorie/92/Produit/Actif=1|Ab|||Nom|ASC]
											<option value="[!Ab::Nom!]"  [IF [!C_Produit!]=[!Ab::Nom!]]selected="selected"[/IF]>[!Ab::Nom!]</option>
										[/STORPROC]
									</select>
								</td>
							</tr>
							<tr>
								<td class="nomPropriete">Début</td> 
								<td class="valeurPropriete"><input type="text" name="C_Debut" value="[!C_Debut!]" class="ncalendar"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Fin</td> 
								<td class="valeurPropriete"><input type="text" name="C_Fin" value="[!C_Fin!]" class="ncalendar">
							</tr>
						</table>					
					</div>
					<td class="valeurPropriete"><input type="hidden" name="Action" value="Creation"/>
					<div class="JSFormButton" style="overflow:hidden;height:60px;margin-right:6px;">
						<td class="valeurPropriete"><input type="submit" class="KEBouton"  value="Enregistrer" name="SaveObject" style="float:right;"/>
						<a href="[IF [!popup!]=true]#[ELSE][!Query!][/IF]" class="KEBouton" [IF [!popup!]=true]  onclick="Fl.closePopup();return false;"[/IF] style="width:75px;float:left;margin-left:7px;">Fermer</a>
					</div>
				</form>
			
			[/BLOC]

		[/BLOC]
	</div>
[/IF]

	<script type="text/javascript">
		Fl.toggleMce();
		Fl.toggleConditionals();
		Fl.toggleCalendars();
		Fl.toggleColorPickers();
	</script>
