<h1>Redemarrage du hub...</h1>
<div class="modal-body">
</div>

<script>
    $('#arret-modal').modal('show');
    [!Module::Formation::Reboot()!]
    var o = '.';
    var i = setInterval(function () {
        $.ajax({
            dataType: "json",
            url: '/Systeme/Test.json'
        }).done(function () {
            $('.modal-body').html('<span class="label label-warning label-large">Arrêt en cours '+o+'</span>');
            if (o != ".....") o+=".";
            else o = ".";
        }).fail(function () {
            var t = setTimeout(function () {
                var j = setInterval(function () {
                    $.ajax({
                        dataType: "json",
                        url: '/Systeme/Test.json'
                    }).fail(function () {
                        $('.modal-body').html('<span class="label label-warning label-large">Démarrage en cours '+o+'</span>');
                        if (o != ".....") o+=".";
                        else o = ".";
                    }).done(function () {
                        var t = setTimeout(function () {
                            window.location.href = "/";
                        },'3000');
                        $('.modal-body').html('<span class="label label-success label-large">Le hub a démarré avec succés ! Redirection vers l\'accueil.</span>');
                        clearInterval(j);
                    });
                },1000);
                $('.modal-body').html('<span class="label label-success label-large">Le hub s\'est arrêté avec succès. Veuillez patienter pour le redemarrage.</span>');
            },'15000');
            clearInterval(i);
        });
    },500);
</script>