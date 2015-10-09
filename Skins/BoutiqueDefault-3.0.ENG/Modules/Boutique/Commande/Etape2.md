[!Mag:=[!CurrentMagasin!]!]
[!De:=[!CurrentDevise!]!]
// Acheteur connecté
[!CLCONN:=[!CurrentClient!]!]

// On redirige automatiquement à l'étape 3 si on est déjà connecté
[IF [!Systeme::User::Public!]=0]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape3)!][/REDIRECT]
[/IF]

// Panier vide on redirige vers la 1
[OBJ Boutique|Client|Cli]
[!Panier:=[!Cli::getPanier()!]!]
[STORPROC [!Panier::LignesCommandes!]|Pan|0|1]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
	[/NORESULT]
[/STORPROC]

<div class="block">
    <h3 class="title_block">Identification</h3>
    [IF [!Mag::EtapeAffiche!]]
    <div class="EtapesCommande row">
        <div class="col-md-3 FondStep1Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="btn btn-primary btn-block"><span class="badge badge-warning">1</span> Panier</a></div>
        <div class="col-md-3 FondStep2"><a href="#nogo" class=" btn btn-primary btn-block"><span class="badge badge-success">2</span> Identification</a></div>
        [IF [!Mag::EtapeLivraison!]]<div class="col-md-3 FondStep3"><a href="#nogo" class=" btn btn-warning btn-block "><span class="badge badge-success">3</span> Livraison</a></div>[/IF]
        [IF [!Mag::EtapePaiement!]]<div class="col-md-3 FondStep4"><a href="#nogo" class=" btn btn-warning btn-block "><span class="badge badge-success">4</span> Paiement</a></div>[/IF]
    </div>

    [/IF]

    <div class="block_content">
        [MODULE Systeme/Login/CreaCompte?Redirect=[!Systeme::getMenu(Boutique/Commande/Etape3)!]]
    </div>
</div>
