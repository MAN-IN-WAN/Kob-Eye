<div style="overflow:hidden;">
<h2>Création du compte</h2>

    <div class="alert alert-info" role="alert">
        <br>
        Les informations nominatives concernant l'utilisateur sont destinées à l'usage interne de la société.<br>
        En aucun cas, la société ne les divulguera à des tiers à des fins de publicité ou de promotion.<br>
        L'utilisateur du site est toutefois informé que, conformément à l'article 27 de la loi Informatique et Libertés du 6 janvier 1978, les réponses données au formulaire présent sur le site, permettant à l'utilisateur d'y déposer ses coordonnées pour recevoir des informations pourront être exploitées par la société Dôme du Foot.<br>
        L'utilisateur du site dispose en permanence d'un droit d'accès et de rectification portant sur ces données en utilisant la page contact de ce site.
    </div>
    <div id="msg"></div>
    <form method="POST" id="clientForm">
        [OBJ Reservations|Client|O]
        [MODULE Systeme/Utils/Form?O=[!O!]]
        <input type="hidden" class="btn btn-success btn-block" name="ValidForm" value="1" />
        <input type="submit" class="btn btn-success btn-block" id="createClient" name="Valider" value="Valider" />
    </form>
    <a href="/" class="btn btn-danger btn-block">Retour</a>
</div>
<script>
    $(document).ready(function(e) {
        $('#clientForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: "json",
                url: "/Systeme/Save.json",
                data: $('#clientForm').serialize(),
                method: 'POST'
            }).done(function (response) {
                if (response.success){
                    $('#clientForm').css('display','none');
                    $('#msg').html(response.message);
                }else{
                    $('#msg').html(response.message);
                }
                console.log(response);
            });
        });
    });
</script>
<style>
    @media screen and (min-width: 768px) {
        .modal-dialog {
            padding-top: 50px !important;
        }
    }
</style>