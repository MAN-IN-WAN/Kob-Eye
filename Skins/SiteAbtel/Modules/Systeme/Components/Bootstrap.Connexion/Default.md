<div class="connexionComp">
	<div class="[!NOMDIV!]">
		<h2><span class="titleWrap">[!TITRE!]</span></h2>
		[IF [!C_Connexion!]!=]
			[CONNEXION [!C_Login!]|[!C_Pass!]]
			[IF [!Systeme::User::Public!]=1]
				[BLOC Erreur|Erreur de connexion]
					<ul>
						<li>Vos identifiants ne sont pas reconnus</li>
					</ul>
				[/BLOC]
			[ELSE]
				[IF [!Redirect!]=][!Redirect:=[!Lien!]!][/IF]
				[REDIRECT][!Redirect!][/REDIRECT]
			[/IF]
		[/IF]
		[IF [!SSTITRE!]]
			<p>[!SSTITRE!]</p>
		[/IF]
		<form id="FormContact" method="post" action="/[!Lien!]" class="form-horizontal">
			<div class="LigneForm">
				<input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" placeholder="Login"/>
			</div>
			<div class="LigneForm">
				<input type="password" name="C_Pass" id="C_Pass" placeholder="Mot de passe"/>
			</div>
			<div class="BoutonsCentre">
				<input name="C_Connexion" type="submit" class="Connexion" value="Accedez à votre service" />
			</div>
		</form>
	</div>
</div>