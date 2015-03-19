[TITLE]Admin Kob-Eye | Importation d'un fichier[/TITLE]
[MODULE Systeme/Interfaces/FilAriane]
[OBJ Newsletter|Contact|ModelC]
<div id="Container">
	<div id="Arbo">
		[BLOC Panneau][/BLOC]
	</div>
	<div id="Data">
		[BLOC Panneau]
			[BLOC Panneau|background:white;position:relative;overflow:hidden;padding:5px;]
				<div class="BigTitle" style="font-weight:bold;;-moz-border-radius:5px 5px 0px 0px;clear:both;">Propriétés</div>
				[STORPROC [!Query!]|Objet|0|1]
					[IF [!Action!]!=""]
						//Maintenant on ouvre le fichier en ecriture
						[!Compte:=0!]
						[!Erreur:=0!]
						[!Existe:=0!]
						[!realname:=[!Utils::getFileName([!Form_Adresse_Upload!])!]!]
						[STORPROC Explorateur/_Dossier/Home/[!Systeme::User::Id!]/Newsletter/_Fichier/[!realname!]|File][/STORPROC]
						
						//On configure le php.ini pour ouvrir une page de plus de 150Mo
						[INI memory_limit]80M[/INI]
						[INI max_execution_time]3600[/INI]
						[SWITCH [!action_import!]|=]
							[CASE blacklist]
								[STORPROC [!ModelC::explodeCSV([!File::Contenu!])!]|Ligne|0|100000]
								
									[IF [!Utils::isMail([!Ligne!])!]]
										[STORPROC [!Query!]/Contact/Email=[!Ligne!]|C|0|1]
											[!C::Delete!]
											[!Compte+=1!]
										[/STORPROC]
									[ELSE]
										<li>Erreur [!Ligne!] --> [!Utils::isMail([!Ligne!])!]</li>
									[/IF]
								[/STORPROC]
								---------------------------------------------------
								[!Compte!] Contacts blacklist avec succes.
							[/CASE]
							[CASE ajout]
								[SWITCH [!Type!]|=]
									[CASE CSV]
										[STORPROC [!ModelC::explodeCSV([!File::Contenu!])!]|Ligne|0|100000]
										
											//Enregistrement des nouveaux contacts
											[!Ligne:=[!Utils::Clean([!Ligne!])!]!]
											[OBJ Newsletter|Contact|Con]
											[METHOD Con|Set]
												[PARAM]Email[/PARAM]
												[PARAM][!Ligne!][/PARAM]
											[/METHOD]
											[METHOD Con|AddParent]
												[PARAM]Newsletter/GroupeEnvoi/[!Objet::Id!][/PARAM]
											[/METHOD]
											[IF [!Utils::isMail([!Ligne!])!]]
												//Verification de l'existence de l'utilisateur dans le groupe
												[COUNT Newsletter/GroupeEnvoi/[!Objet::Id!]/Contact/Email=[!Ligne!]|T]
												[IF [!T!]=0]
													[METHOD Con|Save][/METHOD]
													[!Compte+=1!]
												[ELSE]
													[!Existe+=1!]
												[/IF]
											[ELSE]
												<li>Erreur [!Ligne!] --> SYNTAXE : [!Utils::isMail([!Ligne!])!] </li>
												[!Erreur+=1!]
											[/IF]
										[/STORPROC]
										---------------------------------------------------
										<li>[!Compte!] Contacts ajouts avec succes.</li>
										<li>[!Erreur!] contacts en erreur.</li>
										<li>[!Existe!] contacts déja existants.</li>
									[/CASE]
									[CASE CSVE]
										[STORPROC [!ModelC::explodeCSV([!File::Contenu!])!]|Ligne|0|100000]
										
											//Enregistrement des nouveaux contacts
											[!Ligne:=[!Utils::Clean([!Ligne!])!]!]
											[!Ligne:=[!Ligne:/,!]!]
											[OBJ Newsletter|Contact|Con]
											[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!Ligne::0!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Nom[/PARAM][PARAM][!Ligne::1!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Prenom[/PARAM][PARAM][!Ligne::2!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Tel[/PARAM][PARAM][!Ligne::3!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Fax[/PARAM][PARAM][!Ligne::4!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Mobile[/PARAM][PARAM][!Ligne::5!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Societe[/PARAM][PARAM][!Ligne::6!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]CodePostal[/PARAM][PARAM][!Ligne::7!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Ville[/PARAM][PARAM][!Ligne::8!][/PARAM][/METHOD]
											[METHOD Con|Set][PARAM]Pays[/PARAM][PARAM][!Ligne::9!][/PARAM][/METHOD]
											[METHOD Con|AddParent]
												[PARAM]Newsletter/GroupeEnvoi/[!Objet::Id!][/PARAM]
											[/METHOD]
											[IF [!Utils::isMail([!Ligne!])!]]
												//Verification de l'existence de l'utilisateur dans le groupe
												[COUNT Newsletter/GroupeEnvoi/[!Objet::Id!]/Contact/Email=[!Ligne!]|T]
												[IF [!T!]=0]
													[METHOD Con|Save][/METHOD]
													[!Compte+=1!]
												[ELSE]
													[!Existe+=1!]
												[/IF]
											[ELSE]
												<li>Erreur [!Ligne!] --> SYNTAXE : [!Utils::isMail([!Ligne!])!] </li>
												[!Erreur+=1!]
											[/IF]
										[/STORPROC]
										---------------------------------------------------
										<li>[!Compte!] Contacts ajouts avec succes.</li>
										<li>[!Erreur!] contacts en erreur.</li>
										<li>[!Existe!] contacts déja existants.</li>
									[/CASE]
								[/SWITCH]
							[/CASE]
						[/SWITCH]
					[ELSE]
						//Maintenant on ouvre le fichier en ecriture
						<form enctype="multipart/form-data" action="" method="post" name="frm" >
						<div class="Propriete">
							<div class="ProprieteTitre">Type CSV : </div>
							<div class="ProprieteValeur">&nbsp;
								<select class="Champ" name="Type">
									<option value="CSV">Csv Normal avec uniquement le mail (Séprateur de champ :"," Séparateur de ligne "Retour chariot Pas de séparateur de texte")</option>
									<option value="CSVE">CSV avec Email,Nom,Prenom,Tel,Fax,Mobile,Societe,CodePostal,Ville,Pays (Séprateur de champ :"," Séparateur de ligne "Retour chariot" et Pas de séparateur de texte)</option>
								</select>
							</div>
						</div>
						<div class="Propriete">
							<div class="ProprieteTitre">Action : </div>
							<div class="ProprieteValeur">&nbsp;
								<input type="radio" name="action_import" value="ajout" checked="checked" />Ajouter
								<input type="radio" name="action_import" value="blacklist" />Blacklist
							</div>
						</div>
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
				[/STORPROC]

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
