			<div class="nav-collapse">

				<ul class="nav navbar-inverse">
					[STORPROC [!Systeme::Menus!]|M]
					<li class="item-10[!Pos!] [IF [!M::Url!]~[!Systeme::CurrentMenu::Url!]||[!M::Url!]=[!Systeme::CurrentMenu::Url!]]current active[/IF]">
						<a href="/[!M::Url!]">[!M::Titre!]</a>
					</li>
					[/STORPROC]
				</ul>
				[IF [!Systeme::User::Public!]]
				<form class="form-inline pull-right" method="POST">
					<input type="text" name="login" class="input-small search-query" placeholder="Adresse mail">
					<input type="password"name="pass" class="input-small search-query" placeholder="Mot de passe">
					<button type="submit" class="btn btn-danger">Connexion</button>
				</form>
				[ELSE]
					<div class="pull-right">
					Bonjour, [!Systeme::User::Nom!] [!Systeme::User::Prenom!] <a href="/Systeme/Deconnexion" class="btn btn-danger">Déconnexion</a>
					</div>
				[/IF]

			</div>
