<h1>Arrêt du hub...</h1>
<div class="modal-body">
</div>

<script>
    $('#arret-modal').modal('show');
    [!Module::Formation::Shutdown()!]
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
                $('.modal-body').html('<span class="label label-success label-large">Le hub s\'est arrêté avec succès. Vous pouvez le débrancher.</span>');
            },'10000');
            clearInterval(i);
        });
    },500);
</script>