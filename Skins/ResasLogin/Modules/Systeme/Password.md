<div style="overflow:hidden;">
    <h2>Mot de passe oublié</h2>
    <div id="msg"></div>
    <form method="POST" id="clientForm">
        <div class="form-group group-Email [IF [!Error_Email!]] has-error[/IF]">
            <label for="Form_Email" class="col-sm-5 control-label">Adresse e-mail</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" id="Form_Email" name="Form_Email" placeholder="" value="[!Form_Email!]">
            </div>
        </div>
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
                url: "/Systeme/User/RetrievePassword.json",
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
            }).fail(function (response) {
                console.log('fail ',response);
            });
        });
    });
</script>