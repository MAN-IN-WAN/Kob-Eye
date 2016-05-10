 <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-success btn-block" href="/[!Sys::getMenu(Parc/Domain)!]">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <h4>[COUNT [!ParcClient::getDomain()!]|D][!D!] Domaine(s)</h4>
                    <!--<span class="text-muted">Something else</span>-->
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-info btn-block" href="/[!Sys::getMenu(Parc/Host)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    <h4>[COUNT [!ParcClient::getHost()!]|D][!D!] Hébergement(s)</h4>
                    <!--<span class="text-muted">Something else</span>-->
                </a>
            </div>
            [IF [!Sys::User::isRole(PARC_REVENDEUR)!]]
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-warning btn-block" href="/[!Sys::getMenu(Parc/Client)!]">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                    <h4>[COUNT [!ParcClient::getClient()!]|D][!D!] Client(s)</h4>
                    <!--<span class="text-muted">Something else</span>-->
                </a>
            </div>
            [/IF]
            <!--
            <div class="col-xs-6 col-sm-3 placeholder">
                <div class="btn btn-danger btn-block">
                    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                    <h4>[COUNT Parc/Client/[!ParcClient::Id!]/Host/*/Ftpuser|D][!D!] Compte(s) FTP</h4>
                    <span class="text-muted">Something else</span>
                </div>
            </div>
        -->
          </div>

          //<h2 class="sub-header">Domaines</h2>
          //[MODULE Systeme/Utils/List?Data=[!ParcClient::getDomain()!]]
          //<h2 class="sub-header">Hébergements</h2>
          //[MODULE Systeme/Utils/List?Data=[!ParcClient::getHost()!]]
         //[IF [!Sys::User::isRole(PARC_REVENDEUR)!]]
         //<h2 class="sub-header">Clients</h2>
         //[MODULE Systeme/Utils/List?Data=[!ParcClient::getClient()!]]
        //[/IF]
