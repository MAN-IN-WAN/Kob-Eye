<div class="DataContenu">
	//Connexion du user existant
	[IF [!Systeme::User::Public!]]
		<div class="EnteteCompte">
			[BLOC Rounded|background-color:#eaeaea;|margin-bottom:5px;|height:79px;padding:5px 5px 5px 85px;background-image:url(/Skins/NatomShop/Img/Icones/IconeCompte.jpg);background-repeat:no-repeat;]
				<h1>Cr&eacute;er votre compte</h1>
			[/BLOC]
		</div>
		<form action="" method="post">
			<p class="IntroCompte">Identifiez vous pour faire partie de la communaut&eacute; Kob-Eye</p>

			<h2>Vous avez d&eacute;j&agrave; un compte, identifiez-vous</h2>
				
			<div class="Connexion">
				<div class="LoginBloc">
					<label>Votre adresse e-mail : </label>
					<input type="text" name="login" value="[!login!]" tabindex="a"/>
				</div>
					
				<div class="LoginBloc">
					<label>Votre mot de passe : </label>
					<input type="password" name="pass" value="[!pass!]" tabindex="b"/>
				</div>
				<input type="hidden" name="goto" value="[!Lien!]" />
				[BLOC Bouton|width:112px;margin-top:-5px;float:left;||text-align:center;width:70px;|]
					<input type="submit" name="CONNEXION" value="Connexion" class="BoutonConnexion" tabindex="c"/>
				[/BLOC]	
			</div>
				
		</form>
		<p>
			<a href="/Systeme/Interface/Bloc/Login/LostMdp" title="Mot-de-passe oubli&eacute;" class="OubliMdp">Mot de passe oubli&eacute;</a>
		</p>
	

		[IF [!C_Formulaire!]=OK]//Si le formulaire est envoye
			[COUNT Systeme/User/Mail=[!C_Mail!]|Test]
			[IF [!Test!]!=0]
				<div class="AlerteMessage">
					[!C_Error:=1!]
					[BLOC Rounded|background-color:#eaeaea;|margin-bottom:5px;|padding:5px;]
					<p><span>Vous &ecirc;tes d&eacute;j&agrave; inscrit !</span><br />
					Veuillez utiliser le formulaire de connexion ci-dessus pour acc&eacute;der &agrave; votre compte ou demander un nouveau de passe encliquant sur "mot de passe oublié".</p>
					[/BLOC]
				</div>
			[ELSE]
				//On génère le client
				[OBJ Systeme|User|U]
				[METHOD U|Set][PARAM]Login[/PARAM][PARAM][!C_Login!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Adresse[/PARAM][PARAM][!C_Adresse!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]CodPos[/PARAM][PARAM][!C_CodPos!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Pays[/PARAM][PARAM][!C_Pays!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Tel[/PARAM][PARAM][!C_Tel!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Mail[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Pass[/PARAM][PARAM][!C_Pass!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Pays[/PARAM][PARAM][!C_Pays!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Skin[/PARAM][PARAM]KobEye[/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
				[METHOD U|AddParent][PARAM]Systeme/Group/11817[/PARAM][/METHOD]
				//Verification de tous les champs et enregistrement
				[IF [!U::Verify!]=0]
					[METHOD U|Save][/METHOD]
					[METHOD Cust|Set]
						[PARAM]Utilisateur[/PARAM]
						[PARAM][!U::Id!][/PARAM]
					[/METHOD]
					[METHOD Cust|Save][/METHOD]
					//Affection du numero de serie dans le cas ou l'utilisateur l'a renseigné
					//Inscription à la newsletter
					[IF [!Newsletter!]=Oui]
						[COUNT Newsletter/GroupeEnvoi/1/Contact/Email=[!C_Mail!]|Test]
						[IF [!Test!]][ELSE]
							[OBJ Newsletter|Contact|Co]
							[METHOD Co|Set]
								[PARAM]Email[/PARAM]
								[PARAM][!C_Mail!][/PARAM]
							[/METHOD]
							[METHOD Co|Set]
								[PARAM]Client[/PARAM]
								[PARAM]1[/PARAM]
							[/METHOD]
							[METHOD Co|Set]
								[PARAM]Activite[/PARAM]
								[PARAM][!Cust::Activite!][/PARAM]
							[/METHOD]
							[METHOD Co|Set]
								[PARAM]Specialite[/PARAM]
								[PARAM][!Cust::Specialite!][/PARAM]
							[/METHOD]
							[METHOD Co|Set]
								[PARAM]Version[/PARAM]
								[PARAM][!Cust::Version!][/PARAM]
							[/METHOD]
							[METHOD Co|Set]
								[PARAM]Modele[/PARAM]
								[PARAM][!Cust::Modele!][/PARAM]
							[/METHOD]
							[METHOD Co|Set]
								[PARAM]Os[/PARAM]
								[PARAM][!Cust::Os!][/PARAM]
							[/METHOD]
							[METHOD Co|AddParent]
								[PARAM]Newsletter/GroupeEnvoi/1[/PARAM]
							[/METHOD]
							[METHOD Co|Save][/METHOD]
						[/IF]
					[/IF]
					
					//Envoi de mail a admin et au client
					[MODULE Boutique/Client/SendMail?Cust=[!Cust!]]
			
					<div class="AlerteMessage" style="width:450px;">
						[CONNEXION [!C_Mail!]|[!C_Pass!]]
						[REDIRECT][!Lien!][/REDIRECT]
						[BLOC Rounded|background-color:#eaeaea;|margin-bottom:5px;widh:450px;|padding:5px;]
						<p>
						Cr&eacute;ation de compte effectu&eacute;e avec succ&eacute;s !<br />
						Une confirmation par mail vient d&acute;&ecirc;tre envoy&eacute;e &agrave; l&acute;adresse suivante : <span>[!C_Mail!].</span><br />
						Veuillez utiliser le formulaire de connexion ci-dessus pour acc&eacute;der &agrave; votre compte.<br />
						Conform&eacute;ment &agrave; la loi, vous pouvez &agrave; tout moment modifier ou supprimer les renseignements contenus
						dans votre Espace perso.
						</p>
						[/BLOC]
					</div>
				[ELSE]
					[!C_Error:=1!]
					[IF [!ClientAlreadyHaveUser!]]
						<div class="AlerteMessage">
							[BLOC Rounded|background-color:#eaeaea;|margin-bottom:5px;|padding:5px;]
							<p><span>Le num&eacute;ro de s&eacute;rie que vous avez saisi est d&eacute;j&agrave; affect&eacute; &agrave; un autre utilisateur.</span><br />
							Merci de v&eacute;rifier vos informations ou de contacter Geckomedia</p>
							[/BLOC]
						</div>
					[ELSE]
						<ul class="Error">
							<li><h5>Certaines de vos informations personnelles sont incorrectes ou absentes  : Veuillez vérifier la saisie des champs suivants :</h5></li>
							[STORPROC [!Cust::Error!]|R]
								<li>[!R::Message!]</li>
								//Generation d une variable d error pour informer le champ en question
								[!C_[!R::Prop!]_Error:=1!]
							[/STORPROC]
						[IF [!C_Pass!]!=[!C_PassC!]||[!C_Pass!]=]
							<li><h5>Les mots de passe ne correspondent pas. Veuillez les saisir à nouveau.</h5></li>	
							[!C_Pass_Error:=1!]
						[/IF]
						</ul>
					[/IF]
				[/IF]
			[/IF]	
		[/IF]
		//Sinon, si le formulaire n est pas envoye 
		[IF [!C_Formulaire!]!=OK||[!C_Error!]=1]	
			<form action="/[!Lien!]" method="post" class="Inscription">		
				<h2>Vous &ecirc;tes un nouvel utilisateur ?<br />Inscrivez vous sur Kob-Eye</h2>
			
				<p class="Obligatoire">Les champs marqu&eacute;s (\*) sont obligatoires</p>
		
				<p class="TitreIdentif">Identification</p>

				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Votre adresse e-mail  * : </label>
						<input type="text" name="C_Mail" value="[!C_Mail!]" class="[IF [!C_Mail_Error!]]Error[/IF]" tabindex="1"/>
					</div>
				</div>
				<div class="LigneCompte">						
					<div class="CompteBloc">
						<label>Nom * : </label>
						<input type="text" name="C_Nom"  value="[!C_Nom!]" style="text-transform:uppercase;" class="[IF [!C_Nom_Error!]]Error[/IF]" tabindex="3"/>
					</div>
			
					<div class="CompteBlocDroite">
						<label>Pr&eacute;nom * :</label>
						<input type="text" name="C_Prenom" value="[!C_Prenom!]" class="[IF [!C_Prenom_Error!]]Error[/IF]" tabindex="4"/>
					</div>
				</div>
		
				<p class="TexteInscription"><span>Votre login et mot de passe von vous servir pour vous connecter &agrave; votre espace</p>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Login  * : </label>
						<input type="text" name="C_Login" value="[!C_Login!]" class="[IF [!C_Login_Error!]]Error[/IF]" tabindex="5"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Votre mot de passe * : </label>
						<input type="password" name="C_Pass" value="[!C_Pass!]" class="[IF [!C_Pass_Error!]]Error[/IF]" tabindex="6"/>
					</div>
			
					<div class="CompteBlocDroite">
						<label>Confirmation  * : </label>
						<input type="password" name="C_PassC" value="[!C_PassC!]" class="[IF [!C_Pass_Error!]]Error[/IF]" tabindex="7"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Adresse  * : </label>
						<input type="text" name="C_Adresse" value="[!C_Adresse!]" class="[IF [!C_Adresse_Error!]]Error[/IF]" tabindex="10"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Code postal * : </label>
						<input type="text" name="C_CodPos"  value="[!C_CodPos!]" style="text-transform:uppercase;" class="[IF [!C_CodPos_Error!]]Error[/IF]" tabindex="12"/>
					</div>
					<div class="CompteBlocDroite">
						<label>Ville * : </label>
						<input type="text" name="C_Ville"  value="[!C_Ville!]" class="[IF [!C_Ville_Error!]]Error[/IF]" tabindex="13"  style="text-transform:uppercase;"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Pays * :</label>
						<select>
						<option value="">...</option>
							[STORPROC Geographie/Pays|Pa|0|2000|Nom|ASC]
								<option  selected="selected"  value="[!C_Pays!]">[!Pa::Nom!]</option>
							[/STORPROC]
						</select>
						//[MODULE Geographie/Pays/getList?Var=C_Pays&Valeur=[!C_Pays!]&Tab=14]
					</div>
					<div class="CompteBlocDroite">
						<label>T&eacute;l&eacute;phone * : </label>
						<input type="text" name="C_Tel"  value="[!C_Tel!]" class="[IF [!C_Tel_Error!]]Error[/IF]" tabindex="15"/>
					</div>
				</div>
				<div class="LigneInfos">
					<input type="checkbox" class="Checkbox" name="Newsletter" value="Oui" tabindex="33"/>
					<p>Je souhaite &ecirc;tre inform&eacute; r&eacute;guli&egrave;rement par e-mail de toute l'information correspondant aux nouveaux services et aux offres disponibles sur le site</p>
				</div>
		
				<div>
					<input type="hidden" name="C_Formulaire" id="C_Formulaire" value="OK" />
					[BLOC Bouton|width:112px;margin-top:10px;float:left;||text-align:center;width:70px;|]
						<input type="submit" name="ENVOYER" value="ENVOYER" tabindex="34"/>
					[/BLOC]
				</div>
			</form>
		[/IF]
	[ELSE]
		[MODULE Systeme/User/InfoUser]
	[/IF]
</div>