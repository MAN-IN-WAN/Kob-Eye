<div class="row clearfix" style="border-bottom:1px dotted;">
        <div class="col-md-6" style="border-right:1px dotted;">
                <h3 >Déjà Client</h3><span class="petittexte"> (j'ai un compte, je m'identifie)</span>
                [MODULE Systeme/Login?Redirect=[!Redirect!]]
        </div>
        <div class="col-md-6">
                <h3 >Nouveau client</h3><span class="petittexte">(Je crée un compte client)</span>
                <div class="textecreation">Pour créer votre compte cliquez sur le bouton ci-dessous</div>
                <div class="control-group" style="margin-top:20px;">
                    <input name="C_Creation "type="submit" class="btn btn-primary Connexion" value="Je crée mon compte" onclick="$('#NewClient').css('display','block');" />
                </div>
        </div>
</div>

<div [IF [!I_Inscription!]=]style="display:none;"[/IF] id="NewClient">
    <h1>Création du compte </h1>
    [MODULE Systeme/Login/Inscription?Redirect=[!Redirect!]]
</div>
