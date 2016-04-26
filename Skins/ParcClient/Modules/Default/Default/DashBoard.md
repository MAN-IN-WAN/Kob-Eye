[COUNT Boutique/Commande/Valide=1&Cloture=0|C]
[COUNT Boutique/Commande/Valide=1&Prepare=0&Expedie=0&Cloture=0|CP]
[COUNT Pharmacie/Ordonnance/Etat<4|O]
[COUNT Pharmacie/Ordonnance/Etat<2|OP]
<div id="reload">
    <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn [IF [!CP!]>0]btn-danger[ELSE]btn-success[/IF] btn-block" href="/[!Sys::getMenu(Boutique/Commande)!]">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <h4>[!C!] Commandes(s)</h4>
                    <span>Dont [!CP!] non preparée(s)</span>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block [IF [!OP!]>0]btn-danger[ELSE]btn-info[/IF]" href="/[!Sys::getMenu(Pharmacie/Ordonnance)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    <h4>[!O!] Ordonnance(s)</h4>
                    <span>Dont [!OP!] non preparée(s)</span>
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
</div>
[IF [!RELOAD!]!=1]
<script>

    //auto reload
    var timeout = setInterval(reloadPage, 5000);
    function reloadPage () {
        //window.location.href = '/[!Query!]';
        $.ajax({
            url: '/Systeme/User/DashBoard.htm?RELOAD=1',
            context: $( '#reload' )
        }).done(function(data) {
            $( '#reload').html(data);
            $( this ).addClass( 'active' );
        });
    }
</script>
[/IF]