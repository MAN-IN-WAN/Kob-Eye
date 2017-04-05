[IF [!priceToPay!]]
    [OBJ ParcImmobilier|Reservation|myReservation]
    [!myReservation::Debut:=[!startAt!]!]
    [!myReservation::Fin:=[!endAt!]!]
    [!myReservation::Prix:=[!priceToPay!]!]
    [!myReservation::LotQuery:=[!Query!]!]
    [STORPROC [!Query!]|R|0|1]
        [!myReservation::AddParent([!R!])!]
    [/STORPROC]
    [COOKIE Set|myReservation|myReservation]

    [REDIRECT]Commande/Identification[/REDIRECT]
[/IF]

[STORPROC [!Query!]|residence|0|1]
<div class="widget sidebar no-print">
    <div class="property-filter no-print">
        <div class="content reservation">
            <h3>Location à la semaine</h3>
            <div class="prix-location">A partir de <span>[!residence::BasseSaison!] €</span>par semaine.</div>
            <h3 style="color:#fff">Dates de location</h3>

            <form method="post" action="" id="datepicker-location">
                <div class="input-daterange">
                    <div class="beds control-group">
                        <div class="controls">
                            <input type="text" name="startAt" id="startAt" placeholder="Arrivée" class="dateHolidayDepart">
                        </div><!-- /.controls -->
                    </div>
                    <div class="beds control-group">
                        <div class="controls">
                            <input type="text" name="endAt" id="endAt" class="dateHolidayFin" id="" placeholder="Départ">
                        </div><!-- /.controls -->
                    </div>
                </div>
                <div class="form-error"></div>
                <div class="form-actions">
                    <input type="button" id="submitDate" value="Obtenir le prix" class="btn btn-primary btn-large">
                </div><!-- /.form-actions -->
                <div class="divToPay">
                    <h3 style="color:#555;margin-bottom: 5px">Montant de la location</h3>
                    <input type="hidden" id="totalPrice" name="totalPrice" value="">
                    <div id="showTotalPrice"></div>
                    <h3 style="color:#555;margin-bottom: 5px">Prix à payer</h3>
                    <input type="hidden" id="priceToPay" name="priceToPay" value="">
                    <div id="showPrice"></div>
                    <input type="submit" value="Réserver" class="btn btn-primary btn-large">
                </div>
            </form>
        </div><!-- /.content -->
    </div>

    <div style="height:25px"></div>
    <div class="content">
        <a class="btn btn-primary btn-large" style="width:232px" href="javascript:window.print();">Imprimer cette annonce</a>
    </div>
    <div class="content">
        <div class="saison-prix">
            <h3>Tarifs</h3>
            <table>
                [IF [!residence::BasseSaison!]]
                    <tr>
                        <td>Basse saison : </td>
                        <td>[!residence::BasseSaison!] €</td>
                    </tr>
                [/IF]
                [IF [!residence::MoyenneSaison!]]
                    <tr>
                        <td>Moyenne saison : </td>
                        <td>[!residence::MoyenneSaison!] €</td>
                    </tr>
                [/IF]
                [IF [!residence::HauteSaison!]]
                    <tr>
                        <td>Haute saison : </td>
                        <td>[!residence::HauteSaison!] €</td>
                    </tr>
                [/IF]
                [IF [!residence::TresHauteSaison!]]
                    <tr>
                        <td>Trés haute saison : </td>
                        <td>[!residence::TresHauteSaison!] €</td>
                    </tr>
                [/IF]
            </table>
        </div>
    </div>
    <div class="content">
        <div class="saison-prix">
            <h3>Périodes [!Utils::getYear()!]</h3>

                [!debut2015:=[!Utils::getTms(01/01/[!Utils::getYear()!])!]!]
                [!fin2015:=[!Utils::getTms(31/12/[!Utils::getYear()!])!]!]
                [!now:=[!Utils::getTms([!Utils::getDate(d/m/Y,[!Com::tmsCreate!])!])!]!]
                [STORPROC ParcImmobilier/Residence/[!residence::Id!]/Periode/DateDebut>[!debut2015!]&DateDebut<[!fin2015!]&DateDebut>[!now!]|periode|0|100|DateDebut|ASC]
                    <p style="margin:0px 20px 10px 20px">
                        <strong>[!periode::Nom!]</strong><br>
                        A partir du [DATE d/m/Y][!periode::DateDebut!][/DATE]
                    </p>
                [/STORPROC]

        </div>
    </div>
</div>
<script>

    var datesDisabled = [STORPROC [!Query!]|r][!r::getBusyDays([!residence::Id!])!][/STORPROC];

    var endDate = new Date();
    endDate.setFullYear(endDate.getFullYear());
    endDate.setMonth(11);
    endDate.setDate(31);

    $('#datepicker-location .input-daterange').datepicker({
        language    : 'fr',
        startDate   : 'today',
        datesDisabled: datesDisabled,
        endDate     : endDate,
        weekStart   : 6,
        autoclose   : false,
        daysOfWeekDisabled : [0,1,2,3,4,5]
    }).on('changeDate', function (e) {
        $("#startAt").css('background-color','#fff');
        $("#endAt").css('background-color','#fff');
    });

    $("#submitDate").click(function(){

        if($("#startAt").val().length ==0)
        {
            $("#startAt").css('background-color','#edb1b1');

        }else if($("#endAt").val().length ==0) {

            $("#startAt").css('background-color','#fff');
            $("#endAt").css('background-color','#edb1b1');

        }else{

            $("#startAt").css('background-color','#fff');
            $("#endAt").css('background-color','#fff');

            $.ajax({
                url: '/[!Lien!]/getSaisonPrice.json',
                data: {
                    start       : $("#startAt").val(),
                    end         : $("#endAt").val(),
                    id          : [!residence::Id!]
                }
            }).done(function( data ) {

                if(data.error)
                {
                    $(".form-error").html(data.msg);
                    $(".divToPay").hide();
                    $(".form-error").show('slow');
                }else {
                    $(".form-error").hide();
                    $("#priceToPay").val(data.amount);
                    $("#showPrice").html(data.amount + " €");

                    $("#totalPrice").val(data.totalPrice);
                    $("#showTotalPrice").html(data.totalPrice + " €");

                    $(".divToPay").show('slow');
                }
            });
        }

    });



</script>
[/STORPROC]