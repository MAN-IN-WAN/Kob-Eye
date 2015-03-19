[IF [!C_Connexion!]]
	[CONNEXION [!C_Login!]|[!C_Pass!]]
	[REDIRECT][!Lien!][/REDIRECT]
[/IF]

<div class="EnteteComposant EnteteMonCompte">
	Mon Compte
</div>

<div class="ContenuComposant ContenuComposantMonCompte">
	[IF [!Systeme::User::Public!]=1]
		<form action="/[!Lien!]" method="post">
			<div class="LigneForm">
				<input type="text" id="ComponentMonCompteLogin" name="C_Login" />
			</div>
			<div class="LigneForm">
				<input type="password" id="ComponentMonComptePass" name="C_Pass" />
			</div>
			<div class="LigneBoutons">
				<input type="submit" name="C_Connexion" value="Connexion" />
			</div>
		</form>
	[ELSE]
		[!Menu:=[!Systeme::getMenu(Systeme/User)!]!]
		<ul>
			<li><a href="/[!Menu!]/Modifier">Modifier mes données</a></li>
			<li><a href="/[!Menu!]/Adresses?Type=Livraison">Mes adresses de livraison</a></li>
			<li><a href="/[!Menu!]/Adresses?Type=Facturation">Mes adresses de facturation</a></li>
			<li><a href="/[!Menu!]/Historique">Mon historique de commandes</a></li>
		</ul>
	[/IF]
</div>

// Surcouche JS
<script type="text/javascript">
	window.addEvent('domready', function() {
		FieldDefaultText( $('ComponentMonCompteLogin'), 'Entrez votre adresse e-mail' );
		FieldDefaultText( $('ComponentMonComptePass'), 'Password' );
	});
</script>