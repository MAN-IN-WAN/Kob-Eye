<h1>[!O::getDescription()!] [!O::getFirstSearchOrder()!]</h1>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class=" navbar-header">
            &nbsp;
            <a class="btn btn-warning navbar-btn popup " href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/Form" data-title="Modification [!C::getFirstSearchOder()!]">Modifier</a>
            <a class="btn btn-danger navbar-btn confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/Supprimer" data-title="Suppression [!C::getFirstSearchOder()!]" data-confirm="Êtes vous sur de vouloir supprimer [!O::getDescription()!] [!O::getFirstSearchOrder()!]" data-url="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]">Supprimer</a>
            [STORPROC [!O::getElementsByAttribute(link,,1)!]/type=fkey|P]
                [STORPROC [!O::Module!]/[!P::objectName!]/[!O::ObjectType!]/[!O::Id!]|Pa|0|10]
                    <a href="/[!Sys::getMenu([!P::objectModule!]/[!P::objectName!])!]/[!Pa::Id!]" class="btn-primary navbar-btn btn">Fiche [!P::objectDescription!] [!Pa::getFirstSearchOrder()!]</a>
                [/STORPROC]
            [/STORPROC]

        </div>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        [COUNT [!O::getFunctions()!]|NF]
        [IF [!NF!]>3]
            <ul class="nav navbar-nav">
                <!--<li><a href="#">Link</a></li>-->
                [STORPROC [!O::getFunctions()!]|F]
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Fonctions<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        [LIMIT 0|100]
                        <li><a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!F::Nom!]" class="popup popup-close">[IF [!F::title!]][!F::title!][ELSE][!F::Nom!][/IF]</a></li>
                        [/LIMIT]
                    </ul>
                </li>
                [/STORPROC]
                [/STORPROC]
            </ul>
        [ELSE]
            &nbsp;
            [STORPROC [!O::getFunctions()!]|F]
                <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!F::Nom!]" class="btn btn-info navbar-btn popup popup-close">[IF [!F::title!]][!F::title!][ELSE][!F::Nom!][/IF]</a>
            [/STORPROC]
        [/IF]
    </div>
</nav>
[IF [!O::Verify()!]][ELSE]
    [STORPROC [!O::Error!]|E]
        <div class="alert alert-danger">
            <ul>
                [LIMIT 0|100]
                <li>[!E::Message!]</li>
                [/LIMIT]
            </ul>
        </div>
    [/STORPROC]
    [STORPROC [!O::Warning!]|W]
        <div class="alert alert-warning">
            <ul>
                [LIMIT 0|100]
                <li>[!W::Message!]</li>
                [/LIMIT]
            </ul>
        </div>
    [/STORPROC]
[/IF]
<div class="row">
[COUNT [!O::getElementsByAttribute(fiche,,1)!]|NBC]
    <div class="[IF [!NBC!]>6]col-md-6[ELSE]col-md-12[/IF]">
[STORPROC [!O::getElementsByAttribute(fiche,,1)!]|P]

    [SWITCH [!P::type!]|=]
        [CASE duration]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-6 control-label">[!P::description!]</label>
            <div class="col-sm-6">
                <select class="form-control" id="Form_[!P::name!][]" name="Form_[!P::name!]">
                    <option value=""></option>
                    <option value="30" [IF [!DF!]=30]selected="selected"[/IF]>30 minutes</option>
                    <option value="60" [IF [!DF!]=60]selected="selected"[/IF]>1 heure</option>
                    <option value="90" [IF [!DF!]=90]selected="selected"[/IF]>1 heure 30</option>
                    <option value="120" [IF [!DF!]=120]selected="selected"[/IF]>2 heures</option>
                    <option value="180" [IF [!DF!]=180]selected="selected"[/IF]>3 heures</option>
                    <option value="360" [IF [!DF!]=360]selected="selected"[/IF]>6 heures</option>
                    <option value="480" [IF [!DF!]=480]selected="selected"[/IF]>8 heures</option>
                    <option value="720" [IF [!DF!]=720]selected="selected"[/IF]>12 heures</option>
                    <option value="1440" [IF [!DF!]=1440]selected="selected"[/IF]>1 jour</option>
                    <option value="2880" [IF [!DF!]=2880]selected="selected"[/IF]>2 jours</option>
                    <option value="10080" [IF [!DF!]=10080]selected="selected"[/IF]>1 semaine</option>
                    <option value="20160" [IF [!DF!]=20160]selected="selected"[/IF]>2 semaines</option>
                    <option value="43200" [IF [!DF!]=43200]selected="selected"[/IF]>1 mois</option>
                    <option value="86400" [IF [!DF!]=86400]selected="selected"[/IF]>2 mois</option>
                    <option value="129600" [IF [!DF!]=129600]selected="selected"[/IF]>3 mois</option>
                    <option value="262800" [IF [!DF!]=262800]selected="selected"[/IF]>6 mois</option>
                    <option value="525600" [IF [!DF!]=525600]selected="selected"[/IF]>1 an</option>
                </select>
            </div>
        </div>
        [/CASE]
        [CASE boolean]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-6 control-label">[!P::description!]</label>
            <div class="col-sm-6">
                <input type="checkbox" name="Form_[!P::name!]" [IF [!DF!]]checked="checked"[/IF] class="switch" value="1" disabled="disabled">
            </div>
        </div>
        [/CASE]
        [CASE datetime]
            [IF [!Form_[!P::name!]!]>0]
                [!DF:=[!Form_[!P::name!]!]!]
            [ELSE]
                [IF [!P::value!]>0]
                    [!DF:=[!Utils::getDate(d/m/Y H:i:s,[!P::value!])!]!]
                [ELSE]
                    [IF [!P::Default!] != 0]
                        [!DF:=[!Utils::getDate(d/m/Y H:i:s,[!TMS::Now!])!]!]
                    [ELSE]
                        [!DF:=[!Utils::getDate(d/m/Y H:i:s,1)!]!]
                    [/IF]
                [/IF]
            [/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
        <label class="col-sm-6 control-label">[!P::description!]</label>
        <div class="col-sm-6">
            [!DF!]
            //<input type="text" class="form-control datetimepicker" id="inputDate" placeholder="Sélectionnez une date" value="[!DF!]"  name="Form_[!P::name!]">
        </div>
        </div>
        [/CASE]
        [CASE date]
        [IF [!Form_[!P::name!]!]>0][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!Utils::getDate(d/m/Y,[!P::value!])!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label class="col-sm-6 control-label">[!P::description!]</label>
            <div class="col-sm-6">
                [!DF!]
                //<input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[!DF!]"  name="Form_[!P::name!]">
            </div>
        </div>
        [/CASE]
        [CASE fkey]
            [IF [!P::card!]=long]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                    <label class="col-sm-6 control-label">[!P::parentDescription!]</label>
                    <div class="col-sm-6">
                        <div class="row">
                        [STORPROC [!P::objectModule!]/[!P::objectName!]|C]
                            <div class="col-md-6">
                                [!C::getFirstSearchOrder()!] [!C::getSecondSearchOrder()!]
                                [IF [!O::Id!]]
                                    [COUNT [!P::objectModule!]/[!P::objectName!]/[!C::Id!]/[!O::ObjectType!]/[!O::Id!]|DF]
                                [ELSE]
                                    [!DF:=0!]
                                [/IF]
                                <input type="checkbox" name="Form_[!P::name!][]" [IF [!DF!]]checked="checked"[/IF] class="switch " value="[!C::Id!]">
                            </div>
                        [/STORPROC]
                        </div>
                    </div>
                </div>
            [ELSE]
                [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                    <label class="col-sm-6 control-label">[!P::parentDescription!]</label>
                    <div class="col-sm-6">
                        <select class="form-control" id="Form_[!P::name!][]" name="Form_[!P::name!]" disabled="disabled">
                            <option value=""></option>
                            [STORPROC [!P::objectModule!]/[!P::objectName!]|C]
                            <option value="[!C::Id!]" [IF [!DF!]=[!C::Id!]]selected="selected"[/IF]>[!C::getFirstSearchOrder()!] [!C::getSecondSearchOrder()!]</option>
                            [/STORPROC]
                        </select>
                    </div>
                </div>
            [/IF]
        [/CASE]
        [CASE image]
        [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
        <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label for="Form_[!P::name!]" class="col-sm-6 control-label">[!P::description!]</label>
            <div class="col-sm-6">
                [IF [!DF!]]
                <img src="/[!DF!]"   class="img-responsive" style="max-height: 200px;"/>
                [ELSE]
                <i>Pas d'image</i>
                [/IF]
            </div>
        </div>
        [/CASE]
        [CASE password]
            [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                <label for="Form_[!P::name!]" class="col-sm-6 control-label">[!P::description!]</label>
                <div class="col-sm-6">
                    **********
//                    <input type="password" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
                </div>
            </div>
        [/CASE]
        [CASE raw]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label for="Form_[!P::name!]" class="control-label">[!P::description!]</label>
            <div>
                <pre class="prettyprint">[IF [!Form_[!P::name!]!]][UTIL SPECIALCHARS][!Form_[!P::name!]!][/UTIL][ELSE][UTIL SPECIALCHARS][!P::value!][/UTIL][/IF]</pre>
            </div>
            </div>
        [/CASE]
        [CASE html]
            <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
            <label for="Form_[!P::name!]" class="control-label">[!P::description!]</label>
            <div>
                [IF [!Form_[!P::name!]!]][!Form_[!P::name!]!][ELSE][!P::value!][/IF]
            </div>
            </div>
        [/CASE]
        [DEFAULT]
            [IF [!Form_[!P::name!]!]][!DF:=[!Form_[!P::name!]!]!][ELSE][!DF:=[!P::value!]!][/IF]
            [IF [!P::Values!]]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                    <label class="col-sm-6 control-label">[!P::description!]</label>
                    <div class="col-sm-6">
                        [!DF!]
                        //<select class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]">
                        //    [STORPROC [!P::Values!]|C]
                        //    <option value="[!C!]" [IF [!DF!]=[!C!]]selected="selected"[/IF]>[!C!]</option>
                        //    [/STORPROC]
                        //</select>
                    </div>
                </div>
            [ELSE]
                <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                  <label for="Form_[!P::name!]" class="col-sm-6 control-label">[!P::description!]</label>
                  <div class="col-sm-6">
                      [!DF!]
                    //<input type="text" class="form-control" id="Form_[!P::name!]" name="Form_[!P::name!]" placeholder="" value="[!DF!]">
                  </div>
                </div>
            [/IF]
        [/DEFAULT]
    [/SWITCH]
    [IF [!NBC!]>6&&[!Pos!]=[!Math::Floor([!NBC:/2!])!]]
        </div>
        <div class="col-md-6">
    [/IF]
[/STORPROC]
    </div>
<!--    <div class="col-md-2">
        <a class="btn btn-warning btn-block popup " href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/Form" data-title="Modification [!C::getFirstSearchOder()!]">Modifier</a>
        <a class="btn btn-danger btn-block confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/Supprimer" data-title="Suppression [!C::getFirstSearchOder()!]" data-confirm="Êtes vous sur de vouloir supprimer [!O::getDescription()!] [!O::getFirstSearchOrder()!]" data-url="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]">Supprimer</a>
        <hr></hr>
        [STORPROC [!O::getElementsByAttribute(link,,1)!]/type=fkey|P]
            [STORPROC [!O::Module!]/[!P::objectName!]/[!O::ObjectType!]/[!O::Id!]|Pa|0|10]
            <a href="/[!Sys::getMenu([!P::objectModule!]/[!P::objectName!])!]/[!Pa::Id!]" class="btn-primary btn-block btn">Fiche [!P::objectDescription!] [!Pa::getFirstSearchOrder()!]</a>
            [/STORPROC]
        [/STORPROC]

    </div>-->
</div>
<br />
<hr>
[STORPROC [!O::getChildElements()!]|C]
<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        [LIMIT 0|100]
        [COUNT [!O::Module!]/[!O::ObjectType!]/[!O::Id!]/[!C::objectName!]|NB]
        <li role="presentation" [IF [!Pos!]=1]class="active"[/IF]><a href="#[!C::objectName!]" aria-controls="[!C::objectName!]" role="tab" data-toggle="tab">[!C::objectDescription!] ([!NB!])</a></li>
        [/LIMIT]
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        [LIMIT 0|100]
        <div role="tabpanel" class="tab-pane [IF [!Pos!]=1]active[/IF]" id="[!C::objectName!]">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class=" navbar-header">
                        &nbsp;
                        <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!C::objectName!]/Form" data-title="Ajouter [!C::objectDescription!]" class="btn btn-success popup navbar-btn"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Ajouter un(e) [!C::objectDescription!]</a>
                    </div>
                </div>
            </nav>
            [MODULE Systeme/Utils/List?Chemin=[!O::Module!]/[!O::ObjectType!]/[!O::Id!]/[!C::objectName!]&Popup=[!C::popup!]]
        </div>
        [/LIMIT]
    </div>

</div>
[/STORPROC]