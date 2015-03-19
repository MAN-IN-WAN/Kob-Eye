[MODULE Systeme/Structure/Gauche]
<div id="Milieu">
	[MODULE Systeme/Menu/ImageMenu]
	<div id="Data">
		[INFO [!SERVER::HTTP_REFERER!]|I]
		[IF [!Tem!]=Envoyer]
			//On verifie les erreurs
			[IF [!C_Nom!]==]
				[!C_Error:=1!]
				[!C_Nom_Error:=1!]
			[/IF]
			[IF [!C_Prenom!]==]
				[!C_Error:=1!]
				[!C_Prenom_Error:=1!]
			[/IF]
			[IF [!C_Ami!]==]
				[!C_Error:=1!]
				[!C_Ami_Error:=1!]
			[/IF]
			[IF [!C_Adresse!]==]
				[!C_Error:=1!]
				[!C_Adresse_Error:=1!]
			[/IF]
			[IF [!C_Error!]!=1]
				[LIB Mail|LM]
				[METHOD LM|Subject][PARAM]Message de [!CONF::MODULE::SYSTEME::SOCIETE!] : [!C_Prenom!] [!C_Nom!] vous conseille une page sur [!Domaine!][/PARAM][/METHOD]
				[METHOD LM|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
				[METHOD LM|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
				[METHOD LM|To][PARAM][!C_Ami!][/PARAM][/METHOD]
				[METHOD LM|Body]
					[PARAM]
						[BLOC Mail]
							<div>
								Bonjour,<br />[!C_Prenom!] [!C_Nom!] vous conseille une page sur le site de La FNAE-ZUS.<br />Cliquez <a href="[!C_Adresse!]">ici</a> pour la consulter.
							</div>
						[/BLOC]
					[/PARAM]
				[/METHOD]
				[METHOD LM|Priority][PARAM]5[/PARAM][/METHOD]
				[METHOD LM|BuildMail][/METHOD]
				[METHOD LM|Send][/METHOD]
				<div class="BlocError">
					<h2>Votre message a bien &eacute;t&eacute; envoy&eacute;.</h2>
				</div>
			[ELSE]
				<div class="BlocError">
					<p>Merci de bien remplir tout les champs.</p>
				</div>
			[/IF]
		[/IF]
		<form action="" method="post" id="FormAmi">
			<h1>Envoyer &agrave; un ami</h1>
			[IF [!I::NbHisto!]=5]
				<p>Vous souhaitez envoyer la page : <span class="Bold">[!Rubrique!]</span> de la rubrique : <span class="Bold">[!TitreMenu!].</span></p>
			[ELSE]
				<p>Vous souhaitez envoyer la page : <span class="Bold">[!Rubrique!]</span>.</p>
			[/IF]
			<div class="LigneForm">
				<label>Votre nom*</label>
				<input type="text" name="C_Nom" value="[!C_Nom!]" class="[IF [!C_Nom_Error!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>Votre pr&eacute;nom*</label>
				<input type="text" name="C_Prenom" value="[!C_Prenom!]" class="[IF [!C_Prenom_Error!]]Error[/IF]"/>
			</div>
			<div class="LigneForm">
				<label>Mail destinataire*</label>
				<input type="text" name="C_Ami" value="[!C_Ami!]" class="[IF [!C_Ami_Error!]]Error[/IF]"/>
			</div>
				<div><input type="hidden" name="C_Adresse" value="[IF [!C_Adresse!]=][!SERVER::HTTP_REFERER!][ELSE][!C_Adresse!][/IF]"/></div>
			<div class="Obligatoire">
				<p>Les champs avec * sont obligatoires.</p>
			</div>
			<div>
				<input type="submit" name="Tem" value="Envoyer" />
			</div>
			<a href="[IF [!C_Adresse!]=][!SERVER::HTTP_REFERER!][ELSE][!C_Adresse!][/IF]" title="Retour">Retour</a>
		</form>
	</div>
</div>
<div class="Clear"></div>