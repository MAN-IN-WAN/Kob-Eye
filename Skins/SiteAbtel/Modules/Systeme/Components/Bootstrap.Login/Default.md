<div id="Login" class="">
	[IF [!Systeme::User::Public!]]
		<h2>[!TITRE!]</h2>
		[IF [!C_Connexion!]!=]
			[CONNEXION [!C_Login!]|[!C_Pass!]]
			[IF [!Systeme::User::Public!]=1]
				<h3 class="login_error">Vos identifiants ne sont pas reconnus</h3>
				<script type="text/javascript">
					setTimeout(function(){
						$('#Login').addClass('active error');
						$('#LoginBG').addClass('active');
					},100);
					
				</script>
			[ELSE]
				[IF [!REDIRECTURL!]=][!Redirect:=[!Lien!]!][ELSE][!Redirect:=[!REDIRECTURL!]!][/IF]
				[REDIRECT][!Redirect!][/REDIRECT]
			[/IF]
		[/IF]
		<form action="/[!Lien!]" method="post" id="connexion">
			<div class="LigneForm">
				<label>Login</label>
				<input type="text" name="C_Login" id="C_Login" value="[!C_Login!]" />
				<div class="clear"></div>
			</div>
			<div class="LigneForm">
				<label>Mot de passe</label>
				<input type="password" name="C_Pass" id="C_Pass" />
				<div class="clear"></div>
			</div>
			<div class="BoutonsCentre">
				<input name="C_Connexion" type="submit" class="Connexion" value="Connexion" />
			</div>
		</form>
	[ELSE]
		<a href="/Systeme/Deconnexion" class="Connexion deconnexion">Deconnexion</a>
	[/IF]
</div>
<div id="LoginBG"></div>
<script type="text/javascript">
	$('.loginShow').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$('#Login').addClass('active');
		$('#LoginBG').addClass('active');
	});
	$('#LoginBG').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$('#Login').removeClass('active');
		$('#LoginBG').removeClass('active');
	});
</script>