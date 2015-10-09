<section id="promotetop" class="">
    <div class="container">
        <div class="row-fluid">
            <div class="customhtml leo-customhtml-ptop  ">
                <div class="block_content clearfix">
                    <div class="container-fluid">
                        <div class="banner-welcome-wrap row-fluid clearfix">
                            <div class="banner-welcome span6">
                                [STORPROC [!Systeme::CurrentMenu::getChildren(Donnee/Type=Html)!]|D|0|1|tmsCreate|DESC]
                                    <p>[!D::Html!]</p>
                                    [NORESULT]
                                        [STORPROC [!Systeme::DefaultMenu::getChildren(Donnee/Type=Html)!]|D|0|1|tmsCreate|DESC]
                                        <p>[!D::Html!]</p>
                                        [/STORPROC]
                                    [/NORESULT]
                                [/STORPROC]
                            </div>
                            <div class="tel-adresse span3">
                                <a href="tel:[!Systeme::User::Tel!]" class="btn btn-success btn-large btn-block pull-right"><b>Tel: [!Systeme::User::Tel!]</b></a>
                            </div>
                            <div class="tel-adresse span3">
                                <a title="itinéraire" onclick="javascript:itineraire('[!Systeme::User::Adresse!] [!Systeme::User::CodPos!] [!Systeme::User::Ville!]');" href="#" class="btn btn-warning pull-right btn-block btn-large"><b>Itinéraire depuis votre position</b></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function itineraire(adresse){
        if (navigator.geolocation)
        {
            navigator.geolocation.getCurrentPosition( function (position) {
                        destination = adresse;
                        googleDirectionsURL = "http://maps.google.com/?saddr=" + position.coords.latitude + "," + position.coords.longitude + "&daddr="+destination;
                        document.location.href = googleDirectionsURL;
                    },
                    function (error)
                    {
                        switch(error.code)
                        {
                            case error.TIMEOUT:
                                alert ('Délai d\'attente dépassé.');
                                break;
                            case error.POSITION_UNAVAILABLE:
                                alert ('Impossible de déterminer votre position');
                                break;
                            case error.PERMISSION_DENIED:
                                alert ('Authorisation requise');
                                break;
                            case error.UNKNOWN_ERROR:
                                alert ('Erreur inconnue');
                                break;
                        }
                    }
            );
        }
        else
            alert("Votre navigateur ne gère pas la géolocalisation.");
    }
</script>