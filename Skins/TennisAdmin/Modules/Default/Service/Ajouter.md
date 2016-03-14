[MODULE Systeme/Utils/Form/FullForm]
<script>
    //on écoute les changements de valeur
    var type = $('[name="Form_Type"] option:checked').val();
    $('[name="Form_Type"]').change(function () {
        type = $('[name="Form_Type"] option:checked').val();
        console.log('chanegemtnde valeur',type);
        switchType(type);
    });

    //on récupère la valeur du champ Type
    console.log('valeur de type',type);

    function switchType(val){
        //on cache toutes les propriétés conditionnelles
        $('.group-Duree').css('display','none');
        $('.group-DateFinCotisation').css('display','none');
        $('.group-CourtId').css('display','none');
        $('.group-TypeCourtId').css('display','none');
        $('.group-TarifAbonnes').css('display','none');
        $('.group-TarifCreuse').css('display','none');
        $('.group-TarifInvite').css('display','none');
        $('.group-GestionInvite').css('display','none');
        $('.group-SaisieQuantite').css('display','none');

        switch (val){
            case 'Reservation':
                $('.group-Duree').css('display','block');
                $('.group-CourtId').css('display','block');
                $('.group-TarifAbonnes').css('display','block');
                $('.group-TarifCreuse').css('display','block');
                $('.group-TarifInvite').css('display','block');
                $('.group-GestionInvite').css('display','block');
                break;
            case 'Abonnement':
                $('.group-Duree').css('display','block');
                break;
            case 'Cotisation':
                $('.group-TypeCourtId').css('display','block');
                $('.group-DateFinCotisation').css('display','block');
                break;
            case 'Produit':
                $('.group-TarifAbonnes').css('display','block');
                $('.group-SaisieQuantite').css('display','block');
                //$('.group-TypeCourtId').css('display','block');
                $('.group-CourtId').css('display','block');
                break;
        }
    }
    switchType(type);

</script>