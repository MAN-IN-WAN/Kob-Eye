[IF [!action!]=ouvrir]
    [!Module::Sesame::Ouverture()!]
    [!serrure:=1!]
[ELSE]
    [!serrure:=0!]
[/IF]
<div id="reload">
    <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn [IF [!serrure!]>0]btn-danger[ELSE]btn-success[/IF] btn-block" href="?action=ouvrir">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <h4>Etat de la serrure</h4>
                    <span>[IF [!serrure!]]La serrure est ouverte[ELSE]La serrure est fermée[/IF]</span>
                </a>
            </div>
           <!--
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block [IF [!OP!]>0]btn-danger[ELSE]btn-info[/IF]" href="/[!Sys::getMenu(Pharmacie/Ordonnance)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    <h4>[!O!] Ordonnance(s)</h4>
                    <span>Dont [!OP!] non preparée(s)</span>
                </a>
            </div>
            -->
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
</div>
[IF [!RELOAD!]!=1]
<script>

    //auto reload
    var timeout = setInterval(reloadPage, 2000);
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