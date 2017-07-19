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
    <a href="/[!Sys::getMenu(Reservations/Reservation)!]/Ajouter" data-title="Ajouter une réservation" class="btn btn-danger pull-right btn-lg"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Ajouter un(e) Réservation</a>
    <a href="/[!Sys::getMenu(Reservations/Reservation)!]/ResaJournee" data-title="Imprimer les réservations" class="btn btn-info pull-right btn-lg" style="margin:auto 5px;"><span class="glyphicon glyphicon-print" aria-hidden="true"  target="_blank"></span> Voir les réservations</a>
    <div class="row">
        <div class="col-md-12">
            <h3>Sélectionnez une date</h3>
            <div id="datepicker-wrap" class="input-group date">
                <input type="text" class="form-control" id="datepicker" value="[DATE d/m/Y][!TMS::Now!][/DATE]"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
            </div>
        </div>
    </div>

    <script>
        $('#datepicker-wrap').datepicker({
            format:"dd/mm/yyyy",
            language: "fr",
            autoclose:true
        }).on('changeDate',onChangeDate);

        function onChangeDate(e) {

            console.log('change date',e);
            $.ajax({
                url: "/Reservations/Reservation/getDispo.json",
                data: {
                    date: (e)?Math.floor(new Date(e.date).getTime()/1000):Math.floor(new Date().getTime()/1000)
                },
                method: 'POST'
            }).done(function (response) {
                //mise à jour des cours
                $('.horaire-tennis').removeClass('disabled-alt');
                $('.horaire-tennis').removeClass('warning');

                $('.horaire-wrapper:last-child .horaire-tennis').addClass('warning');
                console.log('RESPONSE',response);

                for ( var r in response.data){
                    if(response.data[r].HeureFin == 0) response.data[r].HeureFin = 24;
                    for (var j=response.data[r].HeureDebut; j<=response.data[r].HeureFin; j++ ){
                        console.log('test time', r, j ,parseInt(response.data[r].HeureDebut),parseInt(response.data[r].MinuteDebut),'FIN',parseInt(response.data[r].HeureFin),parseInt(response.data[r].MinuteFin));
                        //calcul 30 min avant
                        if (parseInt(response.data[r].MinuteDebut)==0){
                            $('#date-' + response.data[r].Court + '-' + (parseInt(response.data[r].HeureDebut)-1) + '-30').addClass('warning');
                        }else{
                            $('#date-' + response.data[r].Court + '-' + parseInt(response.data[r].HeureDebut) + '-00').addClass('warning');
                        }

                        if ( (j==parseInt(response.data[r].HeureDebut) && parseInt(response.data[r].MinuteDebut)==0 )
                            || (j<parseInt(response.data[r].HeureFin)&&j>parseInt(response.data[r].HeureDebut))
                            || (j==parseInt(response.data[r].HeureFin) && parseInt(response.data[r].MinuteFin)==30)) {
                            console.log('desactivation '+'#date-' + response.data[r].Court + '-' + parseInt(j) + '-00')
                            $('#date-' + response.data[r].Court + '-' + parseInt(j) + '-00').addClass('disabled-alt');
                            $('#date-' + response.data[r].Court + '-' + parseInt(response.data[r].HeureDebut) + '-00').attr('href','/[!Sys::getMenu(Reservations/Reservation)!]/'+response.data[r].Id);
                            $('#date-' + response.data[r].Court + '-' + parseInt(response.data[r].HeureDebut) + '-00').attr('title',response.data[r].Client);
                        }
                        if (j < parseInt(response.data[r].HeureFin)){
                            console.log('desactivation '+'#date-' + response.data[r].Court + '-' + parseInt(j) + '-30');
                            $('#date-'+response.data[r].Court+'-'+parseInt(j)+'-30').addClass('disabled-alt');
                            $('#date-'+response.data[r].Court+'-'+parseInt(j)+'-30').attr('href','/[!Sys::getMenu(Reservations/Reservation)!]/'+response.data[r].Id);
                            $('#date-'+response.data[r].Court+'-'+parseInt(j)+'-30').attr('title',response.data[r].Client);
                        }
                    }
                }
            }).fail(function (reponse){
                console.log('erreur ajax',reponse);
            });

            //Mise à jour de la date
            var today = new Date();
            today.setHours(0);
            today.setMinutes(0);
            today.setSeconds(0);
            console.log('DATE '+Math.floor(today.getTime()/1000));
            $('.dateform').val((e)?Math.floor(new Date(e.date).getTime()/1000):Math.floor(today.getTime()/1000));
        }
        $(function () {
            onChangeDate();
        });
    </script>

    <div class="row">

        [COUNT Reservations/Court|Nb]
        [IF [!Nb!]>=4]
        [!NbCol:=6!]
        [ELSE]
        [!NbCol:=[!Nb!]!]
        [/IF]
        [STORPROC Reservations/Court|C|0|10]
        [STORPROC Reservations/TypeCourt/Court/[!C::Id!]|TC][/STORPROC]
        [!S:=[!TC::getOneChild(Service)!]!]

        <form action="/[!Lien!]/Reserver" method="POST">
            <input type="hidden" name="Date" value="" class="dateform" />
            <input type="hidden" name="Court" value="[!C::Id!]" id="courform" />
            <div class="col-md-[!12:/[!NbCol!]!]">
                <h4>[!C::Titre!]</h4>
                <div class="row">
                    [STORPROC [!S::getHoraires()!]|H]
                    [!splitH:=[!Utils::explode(:,[!H!])!]!]
                    <div class="col-xs-6 horaire-wrapper">
                        <a class="horaire-tennis alt" id="date-[!C::Id!]-[!splitH::0!]-[!splitH::1!]" name="HeureDebut" href="/[!Sys::getMenu(Reservations/Reservation)!]/Ajouter">[!H!]</a>
                    </div>
                    [IF [!Utils::modulo([!Key!],2)!]=1]
                </div>
                <div class="row">
                    [/IF]
                    [/STORPROC]
                </div>
            </div>
        </form>
        [/STORPROC]
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
    //var timeout = setInterval(reloadPage, 20000);
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