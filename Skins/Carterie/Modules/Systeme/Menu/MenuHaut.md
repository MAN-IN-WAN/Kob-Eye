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
			<a href="/Mon-compte" title="Connexion" >Connexion</a>
		[ELSE]
			<a href="/Systeme/Deconnexion" title="déconnexion" style="border-right:none;"  >Déconnexion</a>
		[/IF]
	</div>
	<div class="MenuDroitHautBtn">
		<a href="/LovePaper" title="Loisirs créatif" class="onglet1">SITE LOISIRS<BR />CRÉATIFS</a>
		<a href="/EspacePro" title="Espace Pro" class="onglet2">ESPACE PRO<br />Professionnel, consulter notre<br />catalogue</a>
	</div>
	[IF [!Systeme::User::Public!]!=1]
		<div class="MenuSousDroitHaut">Bienvenue<br /><span class="connecte"> [!Cli::Prenom!] [!Cli::Nom!]</span></div>
	[/IF]
	<div class="MenuDroitBas">
		<div class="input-append">
			<form action="/Rechercher" method="post" name="rechercheproduit">
				<input class="span3" id="appendedInput" type="text" placeholder="Rechercher" name="Recherche" />
				<button type="submit" class="btn btn-loupe"></button>
			</form>
		</div>
	</div>
	[IF [!NbArticle!]>0]<a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]">[/IF]<div class="MenuDroitTitrePanier">MON PANIER</div>
	//<div class="MenuDroitTitrePanier">MON PANIER</div>
	<div class="MenuDroitContenuPanier">
		[IF [!NbArticle!]>0]
			[!NbArticle!] article[IF [!NbArticle!]>1]s[/IF]
		[ELSE]
			Votre panier est vide
		[/IF]
	</div>

</div>