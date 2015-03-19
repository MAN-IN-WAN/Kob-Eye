<!--Boutique/Produit/ Proposition d'un produit-->
// si on est connecté et que l'on vient de cliquer sur ajouter une annonce
[IF [!Systeme::User::Public!]!=1]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1][/STORPROC]
	[IF [!C_Valid!]!=]
		//On verifie les champs du formulaire
		[!Reset:=!]
		[!I_Error:=!]
		[IF [!C_Nom!]=][!C_Nom_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Editeur!]=][!C_Editeur_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Console!]=][!C_Console_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Genre!]=][!C_Genre_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Description!]=][!C_Description_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Annee!]=][!C_Annee_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Joueur!]=][!C_Joueur_Error:=1!][!I_Error:=1!][/IF]
		[IF [!C_Age!]=][!C_Age_Error:=1!][!I_Error:=1!][/IF]
		[IF [!I_Error!]!=1]
			//Si pas d erreur enregistrement 
			// faire l'enregistrement de l'annonce !!!!!!!!!!!!!!!!
			[OBJ Boutique|Produit|Prod]
			[METHOD Prod|Set]
				[PARAM]Nom[/PARAM]
				[PARAM][!C_Nom!][/PARAM]
			[/METHOD]
			[METHOD Prod|Set]
				[PARAM]Description[/PARAM]
				[PARAM][!C_Description!][/PARAM]
			[/METHOD]
			[METHOD Prod|Set]
				[PARAM]Editeur[/PARAM]
				[PARAM][!C_Editeur!][/PARAM]
			[/METHOD]
			[METHOD Prod|Set]
				[PARAM]Age[/PARAM]
				[PARAM][!C_Age!][/PARAM]
			[/METHOD]
			[METHOD Prod|Set]
				[PARAM]Annee[/PARAM]
				[PARAM][!C_Annee!][/PARAM]
			[/METHOD]
			[METHOD Prod|Set]
				[PARAM]Joueur[/PARAM]
				[PARAM][!C_Joueur!][/PARAM]
			[/METHOD]
			[METHOD Prod|Set]
				[PARAM]Actif[/PARAM]
				[PARAM]0[/PARAM]
			[/METHOD]
			[IF [!Prod::Verify(1)!]]
				[METHOD Prod|AddParent][PARAM]Boutique/Genre/[!C_Genre!][/PARAM][/METHOD]
				[METHOD Prod|AddParent][PARAM]Boutique/Categorie/[!C_Console!][/PARAM][/METHOD]
				[METHOD Prod|Save][/METHOD]
				[!Reset:=1!]
				//Envoi d'un mail à l'administrateur
				[LIB Mail|LeMail]
				[METHOD LeMail|To][PARAM]myriam@abtel.fr[/PARAM][/METHOD]
				[METHOD LeMail|From][PARAM]noreply@games-avenue.web[/PARAM][/METHOD]
				[METHOD LeMail|Body][PARAM]
					Un produit vient d'être proposé<br/>Reférence : [!Prod::Reference!]/[!C_Nom!]/ pour la console :&nbsp;&nbsp;[STORPROC Boutique/Categorie/[!C_Console!]|Cat|0|1][!Cat::Nom!][/STORPROC]
				[/PARAM][/METHOD]
				[METHOD LeMail|Send][/METHOD]
			[/IF]
			
		[/IF]
	[/IF]
	[MODULE Systeme/Structure/Gauche]
	<!--- contenu central -->
	<div class="centre">
		<div class="ContenuPages">
			// BLOCK SAISIE PRODUIT
			<div class="VendeurBlocGauche">
				[IF [!C_Valid!]=||[!I_Error!]=1]
					<div class="MonCompte"><h1>Proposer un nouveau produit</h1></div>
					<form id="SaisieProduit" enctype="application/x-www-form-urlencoded"  method="post">
						<input type="hidden" name="I_ClientConnecte" value="[!Pers::Id!]" />
						[IF [!I_Error!]=1]
							<div style="margin:5px">
								<span class="blocProduitPagesTitre blocambiance_color">Veuillez remplir les champs obligatoires suivants :</span><br>
								[IF [!C_Nom!]=]Le titre du produit[/IF]
								[IF [!C_Description!]=]<br>La description[/IF]
								[IF [!C_Editeur!]=]<br/>L'éditeur[/IF]
								[IF [!C_Annee!]=]<br/>L'année[/IF]
								[IF [!C_Joueur!]=]<br/>Les joueurs[/IF]
								[IF [!C_Age!]=]<br/>L'âge des joueurs[/IF]
								[IF [!C_Console!]=]<br/>La console[/IF]
								[IF [!C_Genre!]=]<br/>Le genre[/IF]
							</div>
						[/IF]
						<div class="LigneForm">
							<span style="color:#ff0000;">les champs suivis d'une * sont obligatoires</span>
						</div>
						<div class="LigneForm">
							<label>Titre du jeu <span style="color:#ff0000;">*</span></label>
							<input type="text" name="C_Nom" [IF [!Reset!]=]value="[!C_Nom!]"[ELSE]value=""[/IF] style="text-transform:uppercase;width:250px;" class="[IF [!C_Nom_Error!]]Error[/IF]" />
						</div>
						<div class="LigneForm">
							<label>Editeur<span style="color:#ff0000;">*</span></label>
							<input type="text" name="C_Editeur" [IF [!Reset!]=]value="[!C_Editeur!]"[ELSE]value=""[/IF] style="text-transform:uppercase;width:250px;" class="[IF [!C_Editeur_Error!]]Error[/IF]" />
						</div>
						<div class="LigneForm ">
							<label>Console<span style="color:#ff0000;">*</span></label>			<select name="C_Console" [IF [!C_Console_Error!]]class="Error"[ELSE]Class="selectfin" [/IF]/>
								[IF [!Reset!]!=][!C_Console:=!][/IF]
								<option [IF [!C_Console!]=]selected[/IF] value="">Choisissez une console dans la liste</option>
								[STORPROC Boutique/Categorie/Nom=Jeux Video|CATEG]
									[STORPROC Boutique/Categorie/Categorie/[!CATEG::Id!]|CATEG2]
										<option  [IF [!C_Console!]=[!CATEG2::Id!]]selected [/IF]  value=[!CATEG2::Id!] >[!CATEG2::Nom!]</option>
									[/STORPROC]
								[/STORPROC]
							</select>
						</div>
						<div class="LigneForm ">
							<label>Genre<span style="color:#ff0000;">*</span></label>
							[IF [!Reset!]!=][!C_Genre:=!][/IF]

							<select name="C_Genre" [IF [!C_Genre_Error!]]class="Error"[ELSE]Class="selectfin" [/IF]/>
								<option [IF [!C_Genre!]=]selected[/IF] value="">Choisissez un genre dans la liste</option>
								[STORPROC Boutique/Genre|G|0|100|Nom|ASC]
									<option  [IF [!C_Genre!]=[!G::Id!]]selected[/IF] value=[!G::Id!] >[!G::Nom!]</option>
									[STORPROC Boutique/Genre/[!G::Id!]/Genre|G2|0|100|Nom|ASC]
										<option  [IF [!C_Genre!]=[!G2::Id!]]selected [/IF] value=[!G2::Id!] >--&nbsp;&nbsp;[!G2::Nom!]</option>
									[/STORPROC]
								[/STORPROC]
							</select>
						</div>
						<div class="LigneForm ">
							<label>Description<span style="color:#ff0000">*</span></label>
							<textarea name="C_Description" cols="40" rows="4" [IF [!C_Description_Error!]]class="Error"[ELSE]class="LigneForm"[/IF]>[IF [!Reset!]=][!C_Description!][/IF]</textarea>
						</div>
						<div class="LigneForm">
							<label>Date d'édition<span style="color:#ff0000;">*</span></label>
							<input type="text" name="C_Annee"  [IF [!Reset!]=]value="[!C_Annee!]"[ELSE]value=""[/IF] style="text-transform:uppercase;width:250px;" class="[IF [!C_Annee_Error!]]Error[/IF]" />
						</div>
						<div class="LigneForm">
							<label>Nombre de Joueurs<span style="color:#ff0000;">*</span></label>
							<input type="text" name="C_Joueur"   [IF [!Reset!]=]value="[!C_Joueur!]"[ELSE]value=""[/IF] style="text-transform:uppercase;width:250px;" class="[IF [!C_Joueur_Error!]]Error[/IF]" />
						</div>
						<div class="LigneForm">
							<label>Age des Joueurs<span style="color:#ff0000;">*</span></label>
							<input type="text" name="C_Age"   [IF [!Reset!]=]value="[!C_Age!]"[ELSE]value=""[/IF] style="width:250px;" class="[IF [!C_Age_Error!]]Error[/IF]" />
						</div>
						<div class="LigneForm ">
							<label>&nbsp;</label>
							<div class="btnRouge">
								<div class="btnRougeGauche"></div>
								<div class="btnRougeCentre">
									<input type="submit" name="C_Valid" value="Valider" class="btnRougeCentre" />
								</div>
								<div class="btnRougeDroite"></div>
							</div>
						</div>
					</form>
				[ELSE]
					<div class="MonCompte">
						<h1>Proposer un nouveau produit</h1>
						<h2>Votre proposition vient d'être transmise à l'équipe de GamesAvenue, un mail vous sera envoyé très prochainement pour vous informer de la suite donner à votre proposition.</h2>
					</div>					
				[/IF]
			</div>
			// BLOCK DE DROITE
			[MODULE Systeme/Structure/Droite]
		</div>

	</div>
[ELSE]
	[MODULE Systeme/Login]

[/IF]

