[IF [!Systeme::User::Public!]]
	<form action="/[!Lien!]" method="post">
		<h2>Espace membres</h2>
		<div class="BlocMembres">
			<input type="text" name="login" value="Identifiant" onclick="this.value='';" />
		</div>
		<div class="BlocMembres">
			<input type="password" name="pass" value="Motdepasse" onclick="this.value='';"/>
		</div>
		<div class="BlocMembres">
			<input type="submit" name="CONNEXION" value="" class="Connexion" />
		</div>
		<div class="Clear"></div>
	</form>
	<a href="/Mot-Passe" title="Retrouver mon mot de passe" class="Mdp">J'ai oubli&eacute; mon mot de passe</a>
[ELSE]
	[STORPROC Systeme/User/[!Systeme::User::Id!]|Us]
		<div class="Connecte">Bonjour [!Us::Prenom!] [!Us::Nom!] !</div>
		<div class="Deconnecte">
			<a href="/Systeme/Login/Deconnexion" title="D&eacute;connexion" class="LienDeco">Se d&eacute;connecter</a>
		</div>
	[/STORPROC]
[/IF]
