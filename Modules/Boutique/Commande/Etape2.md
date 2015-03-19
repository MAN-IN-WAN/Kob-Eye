// On redirige automatiquement à l'étape 3 si on est déjà connecté
[IF [!Systeme::User::Public!]=0]
	[REDIRECT]Boutique/Commande/Etape3[/REDIRECT]
[/IF]

// Panier vide on redirige vers la 1
[OBJ Boutique|Client|Cli]
[!Panier:=[!Cli::getPanier()!]!]
[STORPROC [!Panier::LignesCommandes!]|Pan]
	[NORESULT]
		[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
	[/NORESULT]
[/STORPROC]


<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="Step1">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="Step2Active">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="Step3">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="Step4">4 - Paiment</a>
</div>
<div class="CommandeEtape2">
	<h1>Mon Identification</h1>
	
	<div class="Identification" style="oveflow:hidden;">
	
		<div class="ColonneIdentification">
	
			<div class="ColonneIdentificationClient">
				<h2>Déjà Client</h2><span class="petittexte"> (j'ai un compte, je m'identifie)</span>
				[MODULE Systeme/Login?Redirect=Boutique/Commande/Etape3]
			</div>
			<div class="ColonneIdentificationNew">
				<h2>Nouveau client</h2><span class="petittexte">(Je crée un compte client)</span>
				<div class="textecreation">Pour créer votre compte cliquez sur le bouton ci-dessous</div>
				<div class="BoutonsCentre">
					<input name="C_Creation" type="submit" class="Connexion" value="Creation" onclick="$('NewClient').tween('display','block');" />
				</div>
			</div>
	
		</div>
	
		<div class="ColonneCreationCompte" id="NewClient" [IF [!I_Inscription!]][ELSE]style="display:none;"[/IF]>
	
			[MODULE Systeme/Login/Inscription?Redirect=Boutique/Commande/Etape3]
		</div>
	</div>
</div>