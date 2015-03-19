<div class="DataContenu">
	//Connexion du user existant
	[IF [!Systeme::User::Public!]]
		<div class="EnteteCompte">
			[BLOC Rounded|background-color:#eaeaea;|margin-bottom:5px;|height:79px;padding:5px 5px 5px 85px;background-image:url(/Skins/NatomShop/Img/Icones/IconeCompte.jpg);background-repeat:no-repeat;]
				<h1>Cr&eacute;er votre compte</h1>
			[/BLOC]
		</div>
		<form action="" method="post">
			<p class="IntroCompte">Pour pouvoir r&eacute;aliser des achats sur NatomShop, vous devez avoir un compte utilisateur et &ecirc;tre connect&eacute; avec celui-ci.</p>

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
				[METHOD U|Set][PARAM]Login[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Adresse[/PARAM][PARAM][!C_Adresse!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]CodPos[/PARAM][PARAM][!C_CodPos!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Pays[/PARAM][PARAM][!C_Pays!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Tel[/PARAM][PARAM][!C_Tel!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Mail[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Pass[/PARAM][PARAM][!C_Pass!][/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Skin[/PARAM][PARAM]NatomShop[/PARAM][/METHOD]
				[METHOD U|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
				[METHOD U|AddParent][PARAM]Systeme/Group/3[/PARAM][/METHOD]
				//Test si le client a saisi un Numero de serie correct
				[!NumSerieError:=0!]
				[IF [!C_NumSer!]!=]
					[COUNT Boutique/Serial/Code=[!C_NumSer!]-[!C_NumSer1!]-[!C_NumSer2!]-[!C_NumSer3!]|NumSerieExiste]
					[IF [!NumSerieExiste!]]
						//On cherche le client associe
						[COUNT Boutique/Client/NatomSerie=[!C_NumSer!]-[!C_NumSer1!]-[!C_NumSer2!]-[!C_NumSer3!]|Var]
						//Si le client existe
						[IF [!Var!]]
							[STORPROC Boutique/Client/NatomSerie=[!C_NumSer!]-[!C_NumSer1!]-[!C_NumSer2!]-[!C_NumSer3!]|Cust|0|1][/STORPROC]
							//On verifie que le client ne possede pas deja un utilisateur
							[IF [!Cust::Utilisateur!]]
								[!ClientAlreadyHaveUser:=1!]
							[/IF]
						[ELSE]
							[OBJ Boutique|Client|Cust]
						[/IF]
					[ELSE]
						[!NumSerieError:=2!]
						[OBJ Boutique|Client|Cust]
					[/IF]
				[ELSE]
					[!PanOk:=0!]
					[STORPROC [!Panier!]|P]
						[IF [!P::GenereNumSerie!]=1]
							[!PanOk:=1!]
						[/IF]
					[/STORPROC]
					//[IF [!PanOk!]=0][!NumSerieError:=1!][/IF]
					[OBJ Boutique|Client|Cust]
				[/IF]

				//CHECK OS
				[IF [!C_Os!]=][!C_Os_Error:=1!][/IF]

				//CREATION CLIENT
				[METHOD Cust|Set][PARAM]Societe[/PARAM][PARAM][!C_Societe!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Civilite[/PARAM][PARAM][!C_Civilite!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD] 
				[METHOD Cust|Set][PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Activite[/PARAM][PARAM][!C_Acti!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Specialite[/PARAM][PARAM][!C_Specia!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Version[/PARAM][PARAM][!C_Version!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Revendeur[/PARAM][PARAM]NATOMSHOP[/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Mail[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Adresse[/PARAM][PARAM][!C_Adresse!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Adresse2[/PARAM][PARAM][!C_Adresse2!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]CodPos[/PARAM][PARAM][!C_CodPos!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Ville[/PARAM][PARAM][!C_Ville!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Pays[/PARAM][PARAM][!C_Pays!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Tel[/PARAM][PARAM][!C_Tel!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]NumTva[/PARAM][PARAM][!C_Tva!][/PARAM][/METHOD]
				[METHOD Cust|Set][PARAM]Os[/PARAM][PARAM][!C_Os!][/PARAM][/METHOD]
				
				//CREATION DE L @ DE LIVRAISON
				[OBJ Boutique|Livraison|Livr]
				[IF [!C_AdrFact!]=Idem][ELSE]
					[METHOD Livr|Set][PARAM]Societe[/PARAM][PARAM][!C_SocieteLiv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]Nom[/PARAM][PARAM][!C_NomLiv!][/PARAM][/METHOD] 
					[METHOD Livr|Set][PARAM]Prenom[/PARAM][PARAM][!C_PrenomLiv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]Adresse[/PARAM][PARAM][!C_AdresseLiv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]Adresse2[/PARAM][PARAM][!C_Adresse2Liv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]CodPos[/PARAM][PARAM][!C_CodPosLiv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]Ville[/PARAM][PARAM][!C_VilleLiv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]Pays[/PARAM][PARAM][!C_PaysLiv!][/PARAM][/METHOD]
					[METHOD Livr|Set][PARAM]Tel[/PARAM][PARAM][!C_TelLiv!][/PARAM][/METHOD]
				[/IF]

				//Verification de tous les champs et enregistrement
				[IF [!U::Verify!]&&[!Cust::Verify!]&&[!ClientAlreadyHaveUser!]=&&[!C_Pass!]=[!C_PassC!]&&[!C_Pass!]!=&&[!NumSerieError!]=0]
					[METHOD U|Save][/METHOD]
					[METHOD Cust|Set]
						[PARAM]Utilisateur[/PARAM]
						[PARAM][!U::Id!][/PARAM]
					[/METHOD]
					[METHOD Cust|Save][/METHOD]

					//Ajout de l'adresse de livraison
					[IF [!Livr::Verify!]&&[!C_AdrFact!]!=Idem]
						[METHOD Livr|AddParent][PARAM]Boutique/Client/[!Cust::Id!][/PARAM][/METHOD]
						[METHOD Livr|Save][/METHOD]
					[/IF]
					
					//Affection du numero de serie dans le cas ou l'utilisateur l'a renseigné
					[IF [!C_NumSer!]!=]
						//On teste si le num existe
						[COUNT Boutique/Serial/Code=[!C_NumSer!]-[!C_NumSer1!]-[!C_NumSer2!]-[!C_NumSer3!]|Num]
						//Verification des configuration du revendeur
						//Si oui, on l enregistre
						[IF [!Num!]]
							[METHOD Cust|Set]
								[PARAM]NatomSerie[/PARAM]
								[PARAM][!C_NumSer!]-[!C_NumSer1!]-[!C_NumSer2!]-[!C_NumSer3!][/PARAM]
							[/METHOD]
							[METHOD Cust|Save][/METHOD]
							//On affecte le num au client
							[STORPROC Boutique/Serial/Code=[!C_NumSer!]-[!C_NumSer1!]-[!C_NumSer2!]-[!C_NumSer3!]|Ns|0|1]
								[METHOD Ns|Set][PARAM]Set[/PARAM][PARAM]1[/PARAM][/METHOD]
								[METHOD Ns|AddParent][PARAM]Boutique/Client/[!Cust::Id!][/PARAM][/METHOD]
								[METHOD Ns|Save][/METHOD]
							[/STORPROC]
							[STORPROC Boutique/Revendeur/Serial/[!Ns::Id!]|Re|0|1][/STORPROC]
							[!Autorise:=1!]
							[IF [!Re::DateExpireActive!]&&[!Re::DateExpire!]<[!TMS::Now!]][!Autorise:=0!][/IF]
							[IF [!Autorise!]=1]
								//On ajoute les activations par defaut
								[STORPROC [!Re::NbActivationDefaut!]|Ac]
									[OBJ Boutique|Activation|Act]
									[METHOD Act|AddParent]
										[PARAM]Boutique/Client/[!Cust::Id!][/PARAM]
									[/METHOD]
									[METHOD Act|Set]
										[PARAM]Nom[/PARAM]
										[PARAM]***ACTIVATION PAR DEFAUT***[/PARAM]
									[/METHOD]
									[METHOD Act|Save][/METHOD]
								[/STORPROC]
							[/IF]
						[/IF]
					[/IF]
					
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
						[IF [!C_AdrFact!]!=Idem]
							[IF [!Livr::Verify!]][ELSE]
								<li><h5>Erreur dans les informations de l'adresse de livraison : Veuillez vérifier la saisie des champs suivants :</h5></li>
								[STORPROC [!Livr::Error!]|E]
									<li>[!E::Message!]</li>
									//Generation d une variable d error pour informer le champ en question
									[!C_[!E::Prop!]Liv_Error:=1!]
								[/STORPROC]
							[/IF]
						[/IF]
						[IF [!C_Pass!]!=[!C_PassC!]||[!C_Pass!]=]
							<li><h5>Les mots de passe ne correspondent pas. Veuillez les saisir à nouveau.</h5></li>	
							[!C_Pass_Error:=1!]
						[/IF]
						[IF [!Autorise!]=0]
							<li><h5>Veuillez contacter votre revendeur [!Re::Nom!] pour connaître les possibilités d'extension de votre compte.(Compte expiré)</h5></li>	
						[/IF]
						[IF [!NumSerieError!]=1]
							<li><h5>Veuillez saisir un numéro de série Natom ou ajouter le logiciel Natom à votre panier.</h5></li>	
						[/IF]
						[IF [!NumSerieError!]=2]
							<li><h5>Votre Numéro de Série Natom est invalide. Veuillez saisir un numéro de série Natom valide ou ajouter le logiciel Natom à votre panier.</h5></li>	
						[/IF]
						[IF [!C_Os_Error!]]
							<li><h5>Veillez renseigner la version de votre systême d'exploitation.</h5></li>	
						[/IF]
						</ul>
					[/IF]
				[/IF]
			[/IF]	
		[/IF]
		//Sinon, si le formulaire n est pas envoye 
		[IF [!C_Formulaire!]!=OK||[!C_Error!]=1]	
			<form action="/[!Lien!]" method="post" class="Inscription">		
				<h2>Vous &ecirc;tes un nouvel utilisateur ?<br />Cr&eacute;ez votre compte sur Natomshop pour commander et suivre vos achats</h2>
			
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
						<label>Civilit&eacute; * :</label>
						<select name="C_Civilite" class="[IF [!C_Civilite_Error!]]Error[/IF]" tabindex="2">
							<option value=""  selected="selected">choisissez</option>
							[OBJ Boutique|Client|Cli]
								[STORPROC [!Cli::Proprietes!]|Prop]
									[IF [!Prop::Nom!]=Civilite]
										[STORPROC [!Prop::Values!]|Vali]
											<option [IF [!C_Civilite!]=[!Vali!]] selected="selected" [/IF] value="[!Vali!]">[!Vali!]</option>
										[/STORPROC]
									[/IF]
								[/STORPROC]
						</select>
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
		
				<p class="TexteInscription"><span>Choisissez votre mot de passe</span> (4 caract&egrave;res minimum).<br />Ce mot de passe vous permettra d'acc&eacute;der aux espaces s&eacute;curis&eacute;s pour le suivi de votre commande et &agrave; vos services personnalis&eacute;s.</p>
					
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Votre mot de passe * : </label>
						<input type="password" name="C_Pass" value="[!C_Pass!]" class="[IF [!C_Pass_Error!]]Error[/IF]" tabindex="5"/>
					</div>
			
					<div class="CompteBlocDroite">
						<label>Confirmation  * : </label>
						<input type="password" name="C_PassC" value="[!C_PassC!]" class="[IF [!C_Pass_Error!]]Error[/IF]" tabindex="6"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Activit&eacute; :</label>
						<select name="C_Acti" class="[IF [!C_Acti_Error!]]Error[/IF]" tabindex="7">
							<option value=""  selected="selected">choisissez</option>
							<option value="autre">Autre</option>
							[STORPROC Boutique/Activite|Act|0|100|Nom|ASC]
								<option [IF [!C_Acti!]=[!Act::Id!]] selected="selected" [/IF] value="[!Act::Id!]">[!Act::Nom!]</option>
							[/STORPROC]
						</select>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Sp&eacute;cialit&eacute; :</label>
						<select name="C_Specia" class="[IF [!C_Specia_Error!]]Error[/IF]" tabindex="8">
							<option value=""  selected="selected">choisissez</option>
							<option value="aucune">Aucune</option>
							[STORPROC Boutique/Specialite|Spec|0|100|Nom|ASC]
								<option [IF [!C_Specia!]=[!Spec::Id!]] selected="selected" [/IF] value="[!Spec::Id!]">[!Spec::Nom!]</option>
							[/STORPROC]
						</select>
					</div>
				</div>
				<p class="TitreAdresse">Votre adresse de facturation</p>
		
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Raison sociale : </label>
						<input type="text" name="C_Societe" value="[!C_Societe!]" tabindex="9"  style="text-transform:uppercase;"/>
					</div>			
				</div>
		
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Adresse  * : </label>
						<input type="text" name="C_Adresse" value="[!C_Adresse!]" class="[IF [!C_Adresse_Error!]]Error[/IF]" tabindex="10"/>
					</div>

					<div class="CompteBlocDroite">
						<label>Adresse (suite) </label>
						<input type="text" name="C_Adresse2"  value="[!C_Adresse2!]" class="[IF [!C_Adresse2_Error!]]Error[/IF]"  tabindex="11"/>
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
						[MODULE Geographie/Pays/getList?Var=C_Pays&Valeur=[!C_Pays!]&Tab=14]
					</div>
					<div class="CompteBlocDroite">
						<label>T&eacute;l&eacute;phone * : </label>
						<input type="text" name="C_Tel"  value="[!C_Tel!]" class="[IF [!C_Tel_Error!]]Error[/IF]" tabindex="15"/>
					</div>
				</div>
		
				<div class="LigneCompte">
					<div class="CompteBlocTVA">
						<label>N&deg; TVA intracommunautaire : </label>
						<input type="text" name="C_Tva" value="[!C_Tva!]" tabindex="16"/>
					</div>
				</div>
		
				<div class="LigneCompte">
					<input type="checkbox" class="Checkbox" name="C_AdrFact" [IF [!C_AdrFact!]=Idem||[!C_AdrFact!]=] checked="checked" [/IF] value="Idem" tabindex="17"/><span>Votre adresse de livraison est la m&ecirc;me que l'adresse de facturation</span>
				</div>
		
		
				<p class="TitreAdresse">Votre adresse de livraison (si elle est diff&eacute;rente de l'adresse de facturation)</p>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Raison sociale : </label>
						<input type="text" name="C_SocieteLiv" value="[!C_SocieteLiv!]" class="[IF [!C_SocieteLiv_Error!]]Error[/IF]" tabindex="17" style="text-transform:uppercase;"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Nom * : </label>
						<input type="text" name="C_NomLiv"  value="[!C_NomLiv!]" style="text-transform:uppercase;" class="[IF [!C_NomLiv_Error!]]Error[/IF]" tabindex="18"/>
					</div>
				
					<div class="CompteBlocDroite">
						<label>Pr&eacute;nom * :</label>
						<input type="text" name="C_PrenomLiv" value="[!C_PrenomLiv!]" class="[IF [!C_PrenomLiv_Error!]]Error[/IF]" tabindex="19"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Adresse  * : </label>
						<input type="text" name="C_AdresseLiv" value="[!C_AdresseLiv!]" class="[IF [!C_AdresseLiv_Error!]]Error[/IF]" tabindex="20"/>
					</div>
					<div class="CompteBloc">
						<label>Adresse  (Suite)  : </label>
						<input type="text" name="C_Adresse2Liv" value="[!C_Adresse2Liv!]" class="[IF [!C_Adresse2Liv_Error!]]Error[/IF]" tabindex="20"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Code postal * : </label>
						<input type="text" name="C_CodPosLiv"  value="[!C_CodPosLiv!]" class="[IF [!C_CodPosLiv_Error!]]Error[/IF]" tabindex="21"/>
					</div>
		
					<div class="CompteBlocDroite">
						<label>Ville * : </label>
						<input type="text" name="C_VilleLiv"  value="[!C_VilleLiv!]" class="[IF [!C_VilleLiv_Error!]]Error[/IF]" tabindex="22"/>
					</div>
				</div>
				<div class="LigneCompte">
					<div class="CompteBloc">
						<label>Pays * :</label>
						[MODULE Geographie/Pays/getList?Var=C_PaysLiv&Valeur=[!C_PaysLiv!]&Tab=23]
					</div>
					<div class="CompteBlocDroite">
						<label>T&eacute;l&eacute;phone * : </label>
						<input type="text" name="C_TelLiv"  value="[!C_TelLiv!]" class="[IF [!C_TelLiv_Error!]]Error[/IF]" tabindex="24"/>
					</div>
				</div>
				
				//SI LE CLIENT A UNE VERSION DE NATOM DANS SON PANIER ALORS ON N'AFFICHE PAS LE FORMULAIRE
				[!AffichFormSerie:=1!]
				[STORPROC [!Panier!]|Pa][IF [!Pa::GenereNumSerie!]][!AffichFormSerie:=0!][/IF][/STORPROC]
				[IF [!AffichFormSerie!]]
					[BLOC Rounded2|background-color:#eaeaea;|margin-bottom:5px;margin-top:10px;|padding:5px;]
						<h2>Num&eacute;ro de s&eacute;rie Natom :</h2>
						
						<div class="LigneNumNatom [IF [!NumSerieError!]>0]Error[/IF]">
							<input type="text" name="C_NumSer" value="[!C_NumSer!]" tabindex="25" maxlength="4"/> - <input type="text" name="C_NumSer1" value="[!C_NumSer1!]" tabindex="26" maxlength="4"/> - <input type="text" name="C_NumSer2" value="[!C_NumSer2!]" tabindex="27" maxlength="4"/> - <input type="text" name="C_NumSer3" value="[!C_NumSer3!]" tabindex="28" maxlength="4"/> <span>(par exemple : AZER - 7FGT - 4HYJ - DFGH)</span>
						</div>
			
						<p>Seuls les d&eacute;tenteurs d'un num&eacute;ro de s&eacute;rie Natom peuvent acheter des planches Natom.<br />
							<a href="#nogo" title="Num&eacute;ro de s&eacute;rie Natom">O&ugrave; trouver mon num&eacute;ro de s&eacute;rie Natom ?</a>
						</p>
			
						<div class="LigneCompte">
							<div class="CompteBloc" style="float:none;">
								<label>Plateforme * :</label>
								<select name="C_Os" class="[IF [!C_Os_Error!]]Error[/IF]" tabindex="29">
									<option value=""  selected="selected">choisissez</option>
									[STORPROC Boutique/Os|Oss]
										<option [IF [!C_Os!]=[!Oss::Nom!]] selected="selected" [/IF] value="[!Oss::Nom!]">[!Oss::Nom!]</option>
									[/STORPROC]
								</select>
							</div>
						</div>
						<div class="LigneCompte">
							<div class="CompteBloc" style="float:none;">
								<label>Version de Natom :</label>
								<select name="C_Version" class="[IF [!C_Version_Error!]]Error[/IF]" tabindex="30">
									<option value=""  selected="selected">choisissez</option>
									[STORPROC Boutique/Version|Vers]
										<option [IF [!C_Version!]=[!Vers::Titre!]] selected="selected" [/IF] value="[!Vers::Titre!]">[!Vers::Titre!]</option>
									[/STORPROC]
								</select>
							</div>
						</div>
					[/BLOC]
				[/IF]
		
				<div class="LigneInfos">
					<input type="checkbox" class="Checkbox" name="Newsletter" value="Oui" tabindex="33"/>
					<p>Je souhaite &ecirc;tre inform&eacute; r&eacute;guli&egrave;rement par e-mail de toute l'information correspondant aux nouveaux services et aux offres disponibles sur le site</p>
				</div>
		
				<div>
					<input type="hidden" name="C_Formulaire" id="C_Formulaire" value="OK" />
					[BLOC Bouton|width:112px;margin-top:10px;float:left;||text-align:center;width:70px;|]
						<input type="submit" class="BoutonEnvoyer" name="ENVOYER" value="ENVOYER" tabindex="34"/>
					[/BLOC]
				</div>
			</form>
		[/IF]
	[ELSE]
		[MODULE Systeme/User/InfoUser]
	[/IF]
</div>