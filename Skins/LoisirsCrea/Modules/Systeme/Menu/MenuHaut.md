[STORPROC Boutique/Magasin/Actif=1|Mag|0|1][/STORPROC]
[IF [!Systeme::User::Public!]=0]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]
[/IF]
// Utilisateur (Connecté ou non ?)
[IF [!Systeme::User::Public!]=1]
	[OBJ Boutique|Client|Cli]
[ELSE]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1]
		[NORESULT]
			[OBJ Boutique|Client|Cli]
		[/NORESULT]
	[/STORPROC]
[/IF]

[!NbArticle:=0!]
[!Panier:=[!Cli::getPanier()!]!]
[STORPROC [!Panier::LignesCommandes!]|Ligne]
	[!NbArticle+=[!Ligne::Quantite!]!]
[/STORPROC]
<div class="MenuDroit">
	<div class="MenuDroitHaut">
		<a href="/Mon-compte" title="Mon compte">Mon compte</a>
		[IF [!Systeme::User::Public!]=1]
			<a href="/Mon-compte" title="Connexion" style="border-right:none;"  >Connexion</a>
		[ELSE]
			<a href="/Systeme/Deconnexion" title="déconnexion" style="border-right:none;"  >Déconnexion</a>
		[/IF]
	</div>
	<div class="MenuDroitHautBtn">
		<a href="http://www.carterie.kirigami.fr" title="Collection Love Paper" target="_blank" >COLLECTION<BR />LOVE PAPER</a>
		<a href="/EspaceRevendeur" title="Espace Pro" target="_blank" >ESPACE REVENDEUR<br />Professionnel, consulter notre<br />catalogue</a>
	</div>
	[IF [!Systeme::User::Public!]!=1]
		<div class="MenuSousDroitHaut">Bienvenue<br /><span class="connecte"> [!Cli::Prenom!] [!Cli::Nom!]</span></div>
	[/IF]
	<div class="MenuDroitBas">
		<div class="row input-append">
			<form action="/Rechercher" method="post" name="rechercheproduit">
				<div class="col-md-10" style="margin:0 0 10px 0;padding:0"> 
					<input id="appendedInput" name="Recherche" placeholder="Rechercher..."  value="[!Recherche!]" style="height:30px;width:252px;"  />
				</div>
				<div class="col-md-2" style="margin:0 0 10px 0;padding:0"> 
					<button type="submit" class="btn btn-loupe"></button>
				</div>
			</form>
		</div>

//		<div class="input-append">
//				<input id="appendedInput" type="text" placeholder="Rechercher" name="Recherche" />
//				<button type="submit" class="btn btn-loupe"></button>
			
	</div>
	[IF [!NbArticle!]>0]<a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]">[/IF]<div class="MenuDroitTitrePanier">MON PANIER</div>
	<div class="MenuDroitContenuPanier">
		[IF [!NbArticle!]>0]
			[!NbArticle!] article[IF [!NbArticle!]>1]s[/IF]
		[ELSE]
			Votre panier est vide
		[/IF]
	</div>[IF [!NbArticle!]>0]</a>[/IF]

</div>