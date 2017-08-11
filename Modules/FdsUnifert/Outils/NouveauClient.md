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
					[!Existe:=0!]
					// RECHERCHE DU CLIENT PAR SON Code de Gestion
					[STORPROC FdsUnifert/Client/Code=[!C_Code!]|CL2|0|1]
						[!Existe+=1!][!CREATIONCLI:=0!]
						[NORESULT]
							// SI CLIENT INEXISTANT ON LE CREE
							[OBJ FdsUnifert|Client|Cli]
							[METHOD Cli|Set][PARAM]Societe[/PARAM][PARAM][!C_Societe!][/PARAM][/METHOD]
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
							[METHOD Cli|Set][PARAM]Code[/PARAM][PARAM][!C_Code!][/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
							[METHOD Cli|Set][PARAM]Pass[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
							[METHOD Cli|Save][PARAM]1[/PARAM][/METHOD]
							[!CREATIONCLI:=1!]
						[/NORESULT]
					[/STORPROC]
					// JE RELIS LE CLIENT AVANT DE CREATION USER
					[!ReqCli:=FdsUnifert/Client/Code=[!C_Code!]!]
					[STORPROC [!ReqCli!]|CL|0|1]
						// JE RELIS LE USER AVANT D'ENVOYER LE MAIL
						[STORPROC Systeme/User/[!CL::UserId!]|Us|0|1][/STORPROC]
						[LIB Mail|LeMail]
						[METHOD LeMail|From][PARAM]noreply@unifert.fr[/PARAM][/METHOD]
						//[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
						[METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
						[METHOD LeMail|Subject][PARAM]Accès à votre espace client[/PARAM][/METHOD]	
						[METHOD LeMail|Body]
							[PARAM]
								[BLOC Mail]
									Bonjour [!C_Civilite!] [!C_Prenom!] [!C_Nom!],<br />
									[IF [!CREATIONCLI!]=1]
										Unifert vient de créer votre compte client sur son nouveau site. <br />
										Pour cela nous avons attribué un mot de passe que nous vous invitons à modifier en cliquant sur le lien figurant en bas de ce mail.<br />
										<hr/>
										Vous trouverez ci-dessous un récapitulatif de vos coordonnées, de votre identifiant de connexion et de votre nouveau mot de passe.<br />
										[!C_Civilite!]&nbsp;&nbsp;[!C_Prenom!]&nbsp;&nbsp;[!C_Nom!]<br/>
										[!C_Adresse!]<br/>
										[!C_CodePostal!]&nbsp;&nbsp;[!C_Ville!]<br/>
										Tél [!C_Tel!]<br/><br/>
										Mobile [!C_Mobile!]<br/><br/>
										Identifiant : [!C_Code!]<br/>
										Mot de passe : [!C_Nom!]<br/>
										<br/>Lien pour accéder à votre compte et modifier votre mot de passe. <br /><br />
										http://www.unifert.fr/RecupPass?Mail=[!C_Mail!]&CodeVerif=[!Us::CodeVerif!]<br /><br />
									[/IF]
									<hr/>
									Toute l'équipe d'Unifert vous remercie de votre confiance.<br/><br/>
									<hr/>
								[/BLOC]
							[/PARAM]
						[/METHOD]
						[METHOD LeMail|Send][/METHOD]
						
					[/STORPROC]
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
					<h1>Création du client, user, adresses s'il n'existe pas et envoi d'un mail automatique </h1>
					<form  action="" method="post" name="frm" >
						<table>
							<tr>
								<td class="nomPropriete">Code</td> 
								<td class="valeurPropriete"><input type="text" name="C_Code" value="[!C_Code!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Société</td> 
								<td class="valeurPropriete"><input type="text" name="C_Societe" value="[!C_Societe!]" class="Champ"></td>
							</tr>
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
								<td class="valeurPropriete"><input type="text" name="C_Pays" value="[!C_Pays!]" class="Champ"></td>	
							</tr>
							<tr>
								<td class="nomPropriete">Téléphone</td> 
								<td class="valeurPropriete"><input type="text" name="C_Tel" value="[!C_Tel!]" class="Champ"></td>
							</tr>
							<tr>
								<td class="nomPropriete">Mobile</td> 
								<td class="valeurPropriete"><input type="text" name="C_Mobile" value="[!C_Mobile!]" class="Champ"></td>
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
