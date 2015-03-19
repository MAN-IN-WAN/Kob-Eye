[IF [!Menu!]=||[!Menu!]=oui][MODULE Systeme/Structure/Gauche][/IF]
[IF [!C_Valid!]=Connexion]
	[CONNEXION [!C_Login!]|[!C_Pass!]]
	[IF [!Systeme::User::Public!]=1][REDIRECT][!Lien!][/REDIRECT][/IF]
[/IF]
<!--- contenu central -->
<div class="colonnecentre">
	<div class="RedactionnelFond">
		[IF [!Systeme::User::Public!]!=1&&[!P_Valid!]=]
			<div class="Categorie" style="padding-top:5px"><h1>Bienvenue dans votre espace client</h1></div>
			<div class="connexion_liste"  style="padding-left:10px;padding-top:5px;">
				De cette page, vous pouvez : <br/>
				<ul class="connexion_liste" style="padding-left:10px;">
					<li>
						<a href="/Mon_Compte/ChangeMotDePasse?Menu=oui">Modifier votre mot de passe</a></li>
					<li>
						<a href="/Mon_Compte/Gestion_Annonces">
							Voir vos annonces
					</li>
					<li>
						<a href="/Mon_Compte/Historique">
							Voir votre historique
					</li>
					<li>
						<a href="/Mon_Compte/Nouvelle_Annonce">
							Saisir une annonce
					</li>
					<li>
						<a href="/Mon_Compte/Proposer_Produit">
							Proposer un produit
					</li>
					<li >
						<a href="/Mon_Compte/Deconnexion">
							Vous déconnecter
						</a>
					</li>
				</ul>
			</div>
			[MODULE Systeme/Login/ModificationInformations?Modif=True]

		[ELSE]
			[IF [!P_Valid!]=]
				[IF [!Connexion!]=ErreurConnexion]
					[BLOC Erreur|Erreur]
						Identifiants de connexion incorrects.
					[/BLOC]
					<div class="Categorie">
						<h1>R&eacute;cup&eacute;ration de votre mot de passe</h1>
					</div>	// on inclus le formulaire de récupération de mot de passe
					[MODULE Systeme/Login/RecuperationMdp]
					// on inclus le formulaire de création de compte

				[/IF]
				// on inclus le formulaire de connexion
				[!C_Titre:=Connexion!]
				<div class="Categorie"><h1 >connexion à mon compte</h1></div>
				[MODULE Systeme/Login/ConnexionForm?Action=Systeme/Login]
				
				[!C_Titre:=Modifier votre mot de passe!]
				// on inclus le formulaire de récupération de mot de passe
				[MODULE Systeme/Login/ModificationPassword]
				<div class="Categorie"><h1>Créer un compte</h1></div>
				<div class="texteItalic11R">Les champs suivis d une * sont obligatoires</div>
				[!C_Titre:=!]
				[MODULE Systeme/Login/InscriptionForm?AddUser=True]
			[ELSE]
				[MODULE Systeme/Login/InscriptionForm?Modif=True]
			[/IF]
			
		[/IF]
	</div>
</div>
[MODULE Systeme/Structure/Droite]
