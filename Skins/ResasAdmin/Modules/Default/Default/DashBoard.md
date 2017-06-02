<div id="reload">
    <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn [IF [!CP!]>0]btn-danger[ELSE]btn-success[/IF] btn-block" href="/[!Sys::getMenu(Reservations/Reservation)!]">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    [COUNT Reservations/Reservation/Valide=1&DateDebut>[!Utils::getTodayMorning()!]&DateFin<[!Utils::getTodayEvening()!]|C]
                    <h4>[!C!] Réservation(s) aujourd'hui</h4>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block [IF [!OP!]>0]btn-danger[ELSE]btn-info[/IF]" href="/[!Sys::getMenu(Reservations/Facture)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    [COUNT Reservations/Facture/Valide=1&tmsCreate>[!Utils::getTodayMorning()!]&tmsCreate<[!Utils::getTodayEvening()!]|C]
                    <h4>[!C!] Facture(s) aujourd'hui</h4>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <div class="btn btn-warning btn-block">
                    <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                    <h4>[COUNT Reservations/Client|D][!D!] Clients</h4>
                    [COUNT Reservations/Client/Abonne=1|D]
                    <span class="text-muted">Dont [!D!] adhérents</span>
                </div>
            </div>
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
    <h2 class="sub-header">Réservations du jour</h2>
    [!Chemin:=Reservations/Reservation/Valide=1&DateDebut>[!Utils::getTodayMorning()!]&DateFin<[!Utils::getTodayEvening()!]!]
    [MODULE Systeme/Utils/List?Chemin=[!Chemin!]&Mini=1]
    <h2 class="sub-header">Factures du jour</h2>
    [!Chemin:=Reservations/Facture/Valide=1&tmsCreate>[!Utils::getTodayMorning()!]&tmsCreate<[!Utils::getTodayEvening()!]!]
    [MODULE Systeme/Utils/List?Chemin=[!Chemin!]&Mini=1]
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