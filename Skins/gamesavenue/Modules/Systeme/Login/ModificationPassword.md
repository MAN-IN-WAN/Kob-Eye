[IF [!Menu!]=oui]
	[MODULE Systeme/Structure/Gauche]
[/IF]
[!I_Error:=!]
[!CESTOK:=0!]
//  LA GESTION D'ERREUR
[IF [!P_Valid!]=Modifier]
	//On verifie les champs du formulaire
	[STORPROC Systeme/User/Mail=[!I_Mail!]|Pers|0|1|tmsCreate|DESC]	[/STORPROC]

	[IF [!I_Mail!]!=[!Pers::Mail!]]
		[!I_Error:=1!]
		[!T_Error:::=Mail inconnu!]
	[/IF]
	
	[IF [!Utils::md5([!I_OldPass!])!]!=[!Pers::Pass!]]
		[!I_Error:=1!]
		[!T_Error:::=Ancien mot de passe incorrect!]
	[/IF]
	[IF [!I_PassNew!]!=[!I_PassNewConfirm!]]
		[!I_Error:=1!]
 		[!T_Error:::=Confirmation du mot de passe incorrect!] 
	[/IF]
	[IF [!I_Error!]!=1]
		[METHOD Pers|Set]
			[PARAM]Pass[/PARAM]
			[PARAM][!I_PassNew!][/PARAM]
		[/METHOD]
		[METHOD Pers|Save][PARAM]0[/PARAM][/METHOD]
		[!CESTOK:=1!]
	[/IF]
[/IF]
[IF [!Menu!]=]
[ELSE]
	<div class="colonnecentre">
		<div class="RedactionnelFond">
[/IF]
<div class="Categorie">
		<h1>Modifier votre mot de passe</h1>
</div>
[IF [!I_Error!]=1]
	[BLOC Erreur|Erreur modification mot de passe]
		<ul style="list-style:none;">
		[STORPROC [!T_Error!]|T]
			<li>[!T!]</li>
		[/STORPROC]
		</ul>
	[/BLOC]
[ELSE]
[/IF]
[IF [!CESTOK!]=0]
	<div class="Categorie" style="padding-top:5px"><h2>Tous les champs sont obligatoires</h2></div>
	
	
	<form action="" method="POST" >
		<div class="LigneForm ">
			<label style="width:100px;">Votre mail</label>
			<input type="text" size="36" name="I_Mail" value="[!I_Mail!]"/>	
		</div>
		<div class="LigneForm ">
			<label style="width:100px;">Ancien mot de passe</label>
			<input type="text" size="36" name="I_OldPass" value="[!I_OldPass!]"/>	
		</div>
		<div class="LigneForm ">
			<label style="width:100px;">Nouveau mot de passe</label>
			<input type="text" size="36" name="I_PassNew" value="[!I_PassNew!]"/>	
		</div>
		<div class="LigneForm ">
			<label style="width:100px;">Confirmation mot de passe</label>
			<input type="text" size="36" name="I_PassNewConfirm" value="[!I_PassNewConfirm!]"/>	
		</div>
		<div class="LigneForm ">
			<labelstyle="width:100px;">&nbsp;</label>
			<div class="btnRouge">
				<div class="btnRougeGauche"></div>
				<div class="btnRougeCentre">
					<input type="submit" name="P_Valid" value="Modifier" class="btnRougeCentre" />
				</div>
				<div class="btnRougeDroite"></div>
			</div>
		</div>
	</form>
[ELSE]
	<div class="Categorie" style="padding-top:5px"><h2>Votre mot de passe a été modifié</h2></div>
[/IF]
[IF [!Menu!]=]
[ELSE]
	</div></div>
	[MODULE Systeme/Structure/Droite]
[/IF]

