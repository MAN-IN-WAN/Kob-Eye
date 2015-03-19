<div id="Login">
	<h2>S'identifier</h2>
	[IF [!Login!]=Go]<div class="erreur">Identifiants incorrects</div>[/IF]
	<form action="/[!Lien!]" method="post">
		<div class="LigneForm">
			<label for="form_login">Identifiant</label>
			<input type="text" name="login" id="form_login" />
		</div>
		<div class="LigneForm">
			<label for="form_pass">Mot de passe</label>
			<input type="password" name="pass" id="form_pass"/>
		</div>
		<div>
			<input type="submit" name="Login" value="Valider" class="Connexion" />
		</div>
	</form>
</div>
