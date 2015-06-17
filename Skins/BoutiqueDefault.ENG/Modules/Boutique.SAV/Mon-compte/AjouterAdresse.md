// Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::getMenu(Boutique/Mon-compte)!][/REDIRECT][/IF]

<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	<div style="margin:10px;">
		<h2>Ajout d'une adresse de [!Type!]</h2>
		// Enregistrement de l'adresse
		[OBJ Boutique|Adresse|Adr]
		[IF [!I_Valid!]!=]
			// Propriétés
			<div class="BlocError">
				<ul>
				[STORPROC [!Adr::Proprietes!]|Prop]
					[METHOD Adr|Set]
						[PARAM][!Prop::Nom!][/PARAM]
						[PARAM][!I_[!Prop::Nom!]!][/PARAM]
					[/METHOD]
					
					[IF [!I_[!Prop::Nom!]!]=]
						<li>Merci de renseigner le champ [!Prop::Nom!].</li>
						[!Error:=1!]
					[/IF]
				[/STORPROC]
				</ul>
			</div>
			// Client
			[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli]
				[METHOD Adr|AddParent][PARAM]Boutique/Client/[!Cli::Id!][/PARAM][/METHOD]
				[NORESULT]
					Client non trouvé !!!
					[!Error:=1!]
				[/NORESULT]
			[/STORPROC]
			[IF [!Error!]!=1]
				// Enregistre
				[METHOD Adr|Save][/METHOD]
				// Redirection
				[REDIRECT][!Systeme::getMenu(Boutique/Mon-compte)!]/Adresses?Type=[!Adr::Type!][/REDIRECT]
			[/IF]
		[/IF]
 
		[MODULE Boutique/Adresse/Form?Type=[!Type!]]
	</div>
</div>
