<!-- Block user information module HEADER -->
<div id="header_user" >
	<div id="header_user_info">
		<div class="nav-item hidden-phone">
			<div class="item-top">
				[IF [!Systeme::User::Public!]]
                __DEFAULT_WELCOME_MESSAGE__ [!CurrentMagasin::Nom!]
                [ELSE]
				<span>Bienvenue [!CurrentClient::Civilite!] [!Systeme::User::Nom!] [!Systeme::User::Prenom!]</span>
                <a href="/Systeme/Deconnexion" class="btn btn-primary">Déconnexion</a>
				[/IF]
			</div>
		</div>
		<div class="nav-item" id="your_account">
			<div class="item-top">
				<a href="/Etape2" title="Votre compte	">__MY_ACCOUNT__</a>
			</div>
		</div>
	</div>
</div>
