// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]

// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

// Récupère la commande du client
[!Com:=[!CLCONN::getPanier()!]!]
[IF [!Com::Id!]=]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
[/IF]

// Récupère le paiement et son type
[!P:=[!Com::getPaiement()!]!]
[IF [!P!]]
	[STORPROC Boutique/TypePaiement/Paiement/[!P::Id!]|TP|0|1|Id|DESC][/STORPROC]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
	[/NORESULT]
[/IF]


        [IF [!Mag::EtapeAffiche!]]
               <div class="EtapesCommande">
                       <div class="span3 FondStep1Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="btn btn-inverse btn-large btn-block"><span class="badge badge-protector">1</span> Panier</a></div>
                       <div class="span3 FondStep2"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">2</span> Identification</a></div>
                       [IF [!Mag::EtapeLivraison!]]<div class="span3 FondStep3"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape3)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">3</span> Livraison</a></div>[/IF]
                       [IF [!Mag::EtapePaiement!]]<div class="span3 FondStep4"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">4</span> Paiement</a></div>[/IF]
               </div>
       
       [/IF]
<div class="CommandeEtape4">
    <div>
	<h1>Mon paiement</h1>
	[!Plugin:=[!TP::getPlugin()!]!]
	[!Plugin::getCodeHTML([!P!])!]
    </div>
</div>	

