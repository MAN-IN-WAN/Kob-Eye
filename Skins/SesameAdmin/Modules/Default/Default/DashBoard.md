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
                    [IF [!serrure!]>0]
                    <h4>La porte est ouverte</h4>
                    [ELSE]
                    <h4>Ouvrir la porte</h4>
                    [/IF]
                    <span>La serrure se débloque pendant 3 secondes</span>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block [IF [!OP!]>0]btn-danger[ELSE]btn-info[/IF]" href="/[!Sys::getMenu(Sesame/QrCode)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    [COUNT Sesame/QrCode|O]
                    <h4>[!O!] Historique(s)</h4>
                    <span>Consulter l'historique complet</span>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-warning btn-block" href="/[!Sys::getMenu(Sesame/PassePartout)!]">
                    <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                    <h4>[COUNT Sesame/PassePartout|D][!D!] Passe-Partout(s)</h4>
                    <span>Consulter tous les passe-partouts</span>
                </a>
            </div>
          </div>

    <h2 class="sub-header">Historique</h2>
    [MODULE Systeme/Utils/MiniList?Chemin=Sesame/QrCode]
    <h2 class="sub-header">Passe-Partouts</h2>
    [MODULE Systeme/Utils/MiniList?Chemin=Sesame/PassePartout]
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