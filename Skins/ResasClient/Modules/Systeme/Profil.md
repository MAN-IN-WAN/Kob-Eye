<div style="overflow:hidden;">
    [!O:=[!Module::Reservations::getCurrentClient()!]!]
    <h2>Modification du compte</h2>
    <div id="msg"></div>
    <form method="POST" id="clientForm">
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
            padding-top: 100px !important;
        }
    }
</style>