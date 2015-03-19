<!--Boutique/Reference/Default-->
[MODULE Systeme/Structure/Gauche_Boutique]
[MODULE Systeme/Structure/Bienvenue]
[INFO [!Query!]|I]
//CONSTRUCTION REQUETE
[!REQUETE:=Boutique!]
[!PRODUIT_LINK:=[!Systeme::CurrentMenu::Url!]!]
//GESTION DES CATEGORIES
[STORPROC [!I::Historique!]|H|0|10]
	[IF [!H::DataSource!]=Categorie]
		[!REQUETE+=/[!H::DataSource!]/[!H::Value!]!]
		[!PRODUIT_LINK+=/[!H::Value!]!]
	[/IF]
	[IF [!H::DataSource!]=Genre][!GENRES:::=[!H::Value!]!][/IF]
[/STORPROC]
[!REQUETE+=/Produit!]
//GESTION DES GENRES
[COUNT [!GENRES!]|NbG]
[IF [!NbG!]>0]
	[!REQUETE+=/(!!]
	[!B:=0!]
	[IF [!NbG!]=1]
		//Ajout des enfants dans le cas d une selection de premier niveau
		[STORPROC [!GENRES!]|G]
			[STORPROC Boutique/Genre/[!G!]/Genre|Ge|0|100]
				[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
				[!REQUETE+=GenreId=[!Ge::Id!]!]
			[/STORPROC]
		[/STORPROC]
	[ELSE]
		[STORPROC [!GENRES!]|G][/STORPROC]
		[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
		[STORPROC Boutique/Genre/[!G!]|Genre][/STORPROC]
		[!REQUETE+=GenreId=[!Genre::Id!]!]
		
	[/IF]
	[!REQUETE+=!)!]
[/IF]
//GESTION DES MOTS CLEFS
[STORPROC [!Query!]|R|0|1][/STORPROC]
[STORPROC Boutique/Produit/Reference/[!R::Id!]|P|0|1][/STORPROC]
[STORPROC Boutique/Client/Reference/[!R::Id!]|C|0|1][/STORPROC]
[STORPROC Boutique/Genre/Produit/[!P::Id!]|G|0|1][/STORPROC]
[IF [!Systeme::User::Public!]!=1]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Pers|0|1][/STORPROC]
	<!--- contenu central -->
	<div class="centre">
		<div class="MonCompte"><h1>Confirmation</h1></div>
		<div><img src="/Skins/gamesavenue/Images/achat_etap4.png"></div>
		<div class="cmdeenreg">Votre commande a été enregistrée sous le numéro xxxx</div>
		<div class="cmdeconfirmtext">Méthode de règlement : </div>
		<div class="cmdeconfirmtext">Date de la commande : </div>
		<div class="cmdeconfirmtext">Date de livraison prévue :</div>
		<div class="cmdeconfirmtext">En fonction du mode de paiement</div>
		<div class="cmdeconfirmtext">Etablir le chèque à l'ordre de : </div>
		<div class="cmdeconfirmtext">Envoyer à : </div>
		<div class="cmdeconfirmtextbold">Votre commande ne sera envoyée qu'à réception du règlement</div>
		<div class="cmdeconfirmtext">Toute l'équipe de games-avenue.com vous remercie de votre commande</div>
		<div class="cmdeconfirmtext">Un email de confrimation vient de vous être envoyé.</div>

		<div style="overflow:hidden">
			<div style="float:right;">	
				<div class="btnRouge" style="padding-top:10px;">
					<div class="btnRougeGauche"></div>
					<div class="btnRougeCentre">
						//<a href="/Boutique/Commande/[!C::Reference!].print" class="btnRougeCentre" />
						<a href="/" class="btnRougeCentre" />J'imprime ma commande</a>
					</div>
					<div class="btnRougeDroite"></div>
				</div>	
			</div>
			<div style="float:right;">	
				<div class="btnRouge" style="padding-top:10px;">
					<div class="btnRougeGauche"></div>
					<div class="btnRougeCentre">
						<a href="/Accueil" class="btnRougeCentre" />
						Je retourne à l'accueil
						</a>
					</div>
					<div class="btnRougeDroite"></div>
				</div>	
			</div>
		</div>
	</div>

[ELSE]
	<div class="blocProduitPagesDescription"><img src="/Skins/gamesavenue/Images/achat_etap2.png"></div>
	[MODULE Systeme/Login?Menu=non]

[/IF]
