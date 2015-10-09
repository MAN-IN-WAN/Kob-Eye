 <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-success btn-block" href="/[!Sys::getMenu(Boutique/Commande)!]">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <h4>[COUNT Boutique/Commande/Valide=1|C][!C!] Commandes(s)</h4>
                    <!--<span class="text-muted">Something else</span>-->
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-info btn-block" href="/[!Sys::getMenu(Pharmacie/Ordonnance)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    <h4>[COUNT Pharmacie/Ordonnance|O][!O!] Ordonnance(s)</h4>
                    <!--<span class="text-muted">Something else</span>-->
                </a>
            </div>
        <!--
            <div class="col-xs-6 col-sm-3 placeholder">
                <div class="btn btn-warning btn-block">
                    <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                    <h4>[COUNT Parc/Client/[!ParcClient::Id!]/Host/*/Apache|D][!D!] Configuration(s) Apache</h4>
                    <span class="text-muted">Something else</span>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <div class="btn btn-danger btn-block">
                    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    <h4>[COUNT Parc/Client/[!ParcClient::Id!]/Host/*/Ftpuser|D][!D!] Compte(s) FTP</h4>
                    <span class="text-muted">Something else</span>
                </div>
            </div>
        -->
          </div>

          <h2 class="sub-header">Commandes</h2>
          [MODULE Boutique/Commande/List]
          <h2 class="sub-header">Ordonnances</h2>
          [MODULE Pharmacie/Ordonnance/List]
