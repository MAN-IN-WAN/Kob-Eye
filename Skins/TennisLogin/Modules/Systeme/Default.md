[IF [!Zob!]=Valider]
<div class="alert alert-danger">
    Une erreur est survenue... Veuillez vérifier vos identifiants.
</div>
[/IF]

<form class="form col-md-12 center-block" method="POST">
    <div class="form-group">
        <input type="text" name="login" class="form-control input-lg" placeholder="Adresse email">
    </div>
    <div class="form-group">
        <input type="password"  name="pass" class="form-control input-lg" placeholder="Mot de passe">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-success btn-lg btn-block" name="Zob" value="Valider">
        <a href="/Systeme/Register" class="btn btn-warning btn-lg btn-block">Créer un compte</a>
        <a href="/Systeme/Password" class="">J'ai oublié mon mot de passe</a>
        <!--                        <span class="pull-right"><a href="#">S'enregs</a></span><span><a href="#">Need help?</a></span>-->
    </div>
</form>
