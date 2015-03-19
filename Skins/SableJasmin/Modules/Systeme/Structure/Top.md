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


[!Panier:=[!Cli::getPanier()!]!]
[STORPROC [!Panier::LignesCommandes!]|Ligne]
	[!NbArticle+=[!Ligne::Quantite!]!]
[/STORPROC]
<div class="EntetePied">
	<div class="Bandeau">
		<a style="position:absolute;top:0;left:0;right:0;bottom:0" href="/"></a>
		<div class="MenuHautR">
			<ul>
				<li style="background:none;">
					<a href="/Contact" class="MiniContact">Contact</a>
				</li>
				<li>
					<a href="http://blog.sable-et-jasmin.com" class="MiniBlog" target="_blank" >Blog</a>
				</li>
				<li>
					<a href="http://www.facebook.com/share?url=[!Domaine!]/[!Lien!]" class="Facebook">Facebook</a>
				</li>
				<li>
					<a href="http://www.twitter.fr/share?url=[!Domaine!]/[!Lien!]" class="Twitter">Twitter</a>
				</li>
				<li>
					<a href="/Envoyer-a-un-ami?C_Lien=[!Lien!]" class="SendFriend">SendFriend</a>
				</li>
				<li>
					[IF [!Systeme::User::Public!]=1]<a href="/mon_compte" class="HConnexion">Connexion</a>[ELSE]<a href="/Systeme/User/Deconnexion" class="HConnexion">Déconnexion</a>[/IF]
				</li>
			</ul>
		</div>
		<div class="EnteteTel">[!Mag::Tel!]<div class="EnteteTelCout">Coût d'un appel local</div></div>
		<div class="EnteteConnexion">
			[IF [!Systeme::User::Public!]=0]
			Bienvenue [!CLCONN::Prenom!] [!CLCONN::Nom!]
			[/IF]
		</div>
		[IF [!NbArticle!]>0]
			<a href="/Boutique/Commande/Etape1" title="Voir votre panier"><div class="PanierTop">

				<div>
					[!NbArticle!] article[IF [!NbArticle!]>1]s[/IF]
				</div>
				<div style="text-align:right;">[!Math::Price([!Ligne::MontantTTC!])!] €</div>
			</div></a>
		[/IF]

	</div>
	
</div>

