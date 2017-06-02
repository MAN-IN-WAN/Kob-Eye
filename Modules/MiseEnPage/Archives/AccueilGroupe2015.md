//Modèle pour affichage de l'accueil GroupeAbtel
<div class="row noMargin">
	<div class="col-md-3">
		// colonne projet
		<h2>LES PROJETS</h2>
	</div>

	<div class="col-md-3 pull-right">
		//colonne actus
		<h2>LES ACTUALITÉS</h2>
	</div>
</div>

[COUNT [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|Nb]
<div class="row noMargin">
	<div class="col-md-12 BasdeLigneEntite">
	[IF [!Nb!]]
		<nav role="navigation" id="PrincipalNavigation">
			<ul>
				[STORPROC [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|M|0|20]
					<li class="menuprincipal [IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF]">
						[IF [!M::Url!]~http]
							<a href="[!M::Url!]" target="_blank" >[!M::Titre!]</a>
						[ELSE]
							<a href="/[!M::Url!]" >[!M::Titre!]</a>
						[/IF]
					</li>
				[/STORPROC]
			</ul>
		</nav>
	[ELSE]
	
		[STORPROC Abtel/Entite/Publier=1|E|||Ordre|ASC]
			<div class="pull-left" style="background-color:[!E::CodeCouleur!];">
				[IF [!E::URL!]!=]<a href="[!E::URL!]" alt="[!E::Nom!]" target="_blank">[/IF]
				<h2>[!E::Nom!]</h2>
				<div class="Description">
					[!E::Description!]
				</div>
				[IF [!E::URL!]!=]</a>[/IF]
			</div>
		[/STORPROC]
	[/IF]
	[IF [!Query!]=][ELSE][!Chemin:=[!Query!]!][/IF]
	[STORPROC [!Chemin!]|Cat|0|1]
		[STORPROC [!Chemin!]/Article|A|0|1]
			<div class="pull-left" style="background-color:#858585;">
				<h2 class="redac">[!Cat::Nom!]</h2>
				<h3>[!A::Titre!]</h3>
				<div class="Description">
					[!A::Contenu!]
				</div>
			</div>
		[/STORPROC]
	[/STORPROC]
	</div>
</div>