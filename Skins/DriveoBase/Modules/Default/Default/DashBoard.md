[COUNT Boutique/Produit|NbP]
[COUNT Boutique/Produit/Complet=0|NbPB]
[COUNT Boutique/Categorie/*|NbC]
[COUNT Boutique/Categorie/*/Actif=0|NbCB]
[COUNT Boutique/Marque|NbM]
[COUNT Boutique/Marque/Display=0|NbMB]
<div id="reload">
    <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-success btn-block" href="/[!Sys::getMenu(Boutique/Produit)!]">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <h4>[!NbP!] Produits</h4>
                    <span>Dont [!NbPB!] non complets</span>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block btn-info" href="/[!Sys::getMenu(Boutique/Categorie)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    <h4>[!NbC!] Catégorie(s)</h4>
                    <span>Dont [!NbCB!] non publiées</span>
                </a>
            </div>

            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block btn-warning" href="/[!Sys::getMenu(Boutique/Marque)!]">
                    <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                    <h4>[!NbM!] Marques / Laboratoires</h4>
                    <span>Dont [!NbMB!] non publiées</span>
                </a>
            </div>
          </div>

          <h2 class="sub-header">Derniers produits</h2>
          [MODULE Systeme/Utils/List?Chemin=Boutique/Produit&Limit=5&Mini=1]
          <h2 class="sub-header">Dernières marques</h2>
          [MODULE Systeme/Utils/List?Chemin=Boutique/Marque&Limit=5&Mini=1]
</div>
