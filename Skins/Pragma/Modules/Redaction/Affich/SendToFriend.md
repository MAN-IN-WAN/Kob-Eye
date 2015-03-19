<h1>Envoyer à un ami le lien ci-dessous</h1>

[IF [!Envoi!]=EnvoiFormSF]
	//On verifie les erreurs
	[IF [!C_Nom!]==]
		[!C_Error:=1!]
		[!C_Nom_Error:=1!]
	[/IF]
	[IF [!C_Ami!]==]
		[!C_Error:=1!]
		[!C_Ami_Error:=1!]
	[/IF]
	[IF [!n1:+[!n2!]!]!=[!tot!]][!Calc_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Error!]!=1]
		[COUNT Newsletter/Contact/Email=[!C_Ami!]|C]
		[IF [!C!]]
		[ELSE]
			[OBJ Newsletter|Contact|Con]
			[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!C_Ami!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Fonction[/PARAM][PARAM]DestinaireAmi[/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/4[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
		[/IF]
		[COUNT Newsletter/Contact/Email=[!C_Mail!]|C2]
		[IF [!C2!]]
		[ELSE]
			[OBJ Newsletter|Contact|Con]
			[METHOD Con|Set][PARAM]Email[/PARAM][PARAM][!C_Mail!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Fonction[/PARAM][PARAM]ExpediteurAmi[/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Nom[/PARAM][PARAM][!C_Nom!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Prenom[/PARAM][PARAM][!C_Prenom!][/PARAM][/METHOD]
			[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
			[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/4[/PARAM][/METHOD]
			[METHOD Con|Save][/METHOD]
		[/IF]
		[LIB Mail|LM]
		[METHOD LM|Subject][PARAM]Message de [!CONF::MODULE::SYSTEME::SOCIETE!] : [!C_Prenom!] [!C_Nom!] vous conseille une page sur [!Domaine!][/PARAM][/METHOD]
		[METHOD LM|From][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[METHOD LM|ReplyTo][PARAM][!CONF::MODULE::SYSTEME::CONTACT!][/PARAM][/METHOD]
		[METHOD LM|To][PARAM][!C_Ami!][/PARAM][/METHOD]
		[METHOD LM|Body]
			[PARAM]
				[BLOC Mail]
					<div>
						[IF [!Systeme::DefaultLanguage!]=Anglais]
							Hello,<br /> [!C_Prenom!] [!C_Nom!] advises you a page on the website of Pragma Immobilier.<br />  Click here : <a href="[!C_Adresse!]" >[!C_Adresse!] </a> to consult it.<br /><br />
							[IF [!C_Mess!]!=]Here the message <br />[!C_Mess!][/IF]
						[ELSE]
							Bonjour,<br />[!C_Prenom!] [!C_Nom!] vous conseille une page sur le site de Pragma Immobilier.<br />Cliquez ici :  <a href="[!C_Adresse!]" >[!C_Adresse!] </a> pour la consulter.<br /><br />
							[IF [!C_Mess!]!=]Voici son message <br />[!C_Mess!][/IF]
						[/IF]
					</div>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LM|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LM|BuildMail][/METHOD]
		[METHOD LM|Send][/METHOD]

		<div class="sndtof"><input type="text"  name="C_Adresse" value="[IF [!C_Adresse!]=][!SERVER::HTTP_REFERER!][ELSE][!C_Adresse!][/IF]" readonly="readonly" /></div>
		<div class="BlocEnvoi">
			<h3>Votre message a bien &eacute;t&eacute; envoy&eacute;.</h3>
		</div>
	[ELSE]
		<div class="BlocError" > 
			<p>Veuillez remplir les champs obligatoires suivants :</p>
			<ul>
				[IF [!C_Nom_Error!]]<li>Votre nom</li>[/IF]
				[IF [!C_Ami_Error!]]<li>Mail destinataire</li>[/IF]
				[IF [!Calc_Error!]=1]<li>Votre calcul est faux !</li>[/IF]
				</ul>
		</div>	
	[/IF]
[/IF]
[IF [!Envoi!]!=EnvoiFormSF||[!C_Error!]]
	<div id="EnvoiAmi">
		<form class="FormContactSndf" action="/[!Lien!]" method="post" enctype="application/x-www-form-urlencoded">
			<div class="sndtof"><input type="text"  name="C_Adresse" value="[IF [!C_Adresse!]=][!SERVER::HTTP_REFERER!][ELSE][!C_Adresse!][/IF]" readonly="readonly" /></div>

			<div class="InfosComplete">
				<div class="InfosInternaute">
					<div class="LigneForm"><h3>Merci de remplir les champs ci-dessous vous concernant.</h3></div>
					<div class="LigneForm">
						<label>Votre nom <span class="obligatoire"> *</span></label>
						<input type="text" name="C_Nom" value="[!C_Nom!]" class="[IF [!C_Nom_Error!]]Error[/IF]"/>
					</div>
					<div class="LigneForm">
						<label>Votre pr&eacute;nom</label>
						<input type="text" name="C_Prenom" value="[!C_Prenom!]" />
					</div>
					<div class="LigneForm">
						<label>Votre Mail</label>
						<input type="text" name="C_Mail" value="[!C_Mail!]"/>
					</div>
				</div>
				<div class="InfosAmi">
					<div class="LigneForm"><h3>Ci-dessous les informations de votre ami.</h3></div>
					<div class="LigneForm">
						<label>Mail destinataire<span class="obligatoire"> *</span></label>
						<input type="text" name="C_Ami" value="[!C_Ami!]" class="[IF [!C_Ami_Error!]]Error[/IF]"/>
					</div>
					<div class="LigneForm">
						<label   class="Partie2" style="vertical-align: top;">Message </label>
						<textarea cols="80" rows="4" name="C_Mess"  >[!C_Mess!]</textarea>
					</div>		
				</div>
			</div>
			<div class="LigneForm" style="padding-left:10px;">
				<p>Les champs marqu&eacute;s (<span class="obligatoire"> *</span>) sont obligatoires.</p>
			</div>
			<div class="LigneForm" style="padding-left:10px;">
				<p [IF [!Calc_Error!]]class="Error3"[/IF]>Résoudre l'opération ci-dessous pour valider votre commentaire</p>
				<input type="text" name="n1" id="n1" value="[!Utils::Random(9)!]"  maxlength="2" readonly="readonly"   style="font-weight:bold;float:none;width:15px;background:transparent;text-align:center;"/>+<input type="text" name="n2" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" style="font-weight:bold;float:none;width:15px;background:transparent;text-align:center;"/> =&nbsp; <input type="text" name="tot" value=""  maxlength="2"  style="float:none;width:20px;" class=" [IF [!Calc_Error!]]Error[/IF]" tabindex="5"/>
			</div>
			<div class="LigneForm ">
				<input type="hidden" name="Envoi" value="EnvoiFormSF" />
				<div class="lienBtnSndf">
					<div class="BtnGauche">	<input type="submit" value="Envoyer" ></div>
					<div class="BtnDroit"><a href="[!SERVER::HTTP_REFERER!]" title="Retour">Retour</a></div>
					
				</div>
			</div>
		</form>
	</div>
[/IF]
