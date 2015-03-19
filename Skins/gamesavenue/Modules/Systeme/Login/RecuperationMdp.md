[IF [!Menu!]=oui]
	[MODULE Systeme/Structure/Gauche]
[/IF]
[IF [!R_Valid!]=Récupération]
	//Verification de l'existence du mail dans la base
	[COUNT Boutique/Client/Mail=[!R_Mail!]|F]
	[IF [!F!]=1]
		[STORPROC Boutique/Client/Mail=[!R_Mail!]|U][/STORPROC]
		<div class="blocProduitPagesDescription">
			Un email vous a été expédié à cette adresse contenant votre nouveau mot de passe.
		</div>
		[LIB Mail|LeMail]
		[METHOD LeMail|To][PARAM][!R_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM]noreply@games-avenue.web[/PARAM][/METHOD]
		[METHOD LeMail|Subject][PARAM]Games-Avenue.com : Récupération de votre mot de passe[/PARAM][/METHOD]
		[METHOD LeMail|Body][PARAM]
			Bonjour,<br />
			Votre mot de passe a été réinitialisé.
			<hr/>
			MOT DE PASSE : [!U::changePassword()!]<br/>
			<hr/>
		[/PARAM][/METHOD]
		[METHOD LeMail|Send][/METHOD]
	[ELSE]
		[BLOC Erreur|Erreur]
			Cet email n'existe pas dans notre base de donnée, veuillez vérifier votre saisie.
		[/BLOC]
	[/IF]
[/IF]
[IF [!F!]!=1]
	<form name="recup" action="[!Action!]" method="POST">
		<div class="LigneForm" style="text-align:justify">
			Bonjour, merci de saisir le mot de passe utilisé lors de votre inscription à games-avenue.com. Si ce mot de passe est référencé sur notre site, vous allez recevoir votre nouveau mot de passe par mail.
			<br>Bien cordialement Votre Equipe Games-Avenue
		</div>
		<div class="LigneForm">
			<label>Adresse mail</label>
			<input type="text" size="36" name="R_Mail" value="[!R_Mail!]"/>
		</div>
		<div class="LigneForm">
			<div class="btnRouge">
				<div class="btnRougeGauche"></div>
				<div class="btnRougeCentre">
					<input type="submit" name="R_Valid" value="Récupération" class="btnRougeCentre" />
				</div>
				<div class="btnRougeDroite"></div>
			</div>
			<input type="hidden" name="R_Valid" value="Récupération"/>
		</div>
	</form>
[/IF]
[IF [!Menu!]=Non]
[ELSE]
	[MODULE Systeme/Structure/Droite]
[/IF}