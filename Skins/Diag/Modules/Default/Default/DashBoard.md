[COUNT Parc/Client|C]
[COUNT Parc/Domain|D]
[COUNT Parc/Server|S]

<div id="reload">
    <h1 class="page-header">Tableau de bord</h1>
          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn [IF [!CP!]>0]btn-danger[ELSE]btn-success[/IF] btn-block" href="/[!Sys::getMenu(Parc/Client)!]">
                    <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                    <h4>[!C!] Clients(s)</h4>
                </a>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block [IF [!OP!]>0]btn-danger[ELSE]btn-info[/IF]" href="/[!Sys::getMenu(Parc/Domain)!]">
                    <span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
                    <h4>[!D!] Domaines(s)</h4>
                </a>
            </div>
        
            <div class="col-xs-6 col-sm-3 placeholder">
                <a class="btn btn-block [IF [!OP!]>0]btn-danger[ELSE]btn-info[/IF]" href="/[!Sys::getMenu(Parc/Server)!]">
                    <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                    <h4>[!S!] Serveurs</h4>
                </a>
            </div>
         <!--   <div class="col-xs-6 col-sm-3 placeholder">
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