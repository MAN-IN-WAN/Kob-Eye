<h1>[!O::getDescription()!] [!O::getFirstSearchOrder()!]</h1>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class=" navbar-header">
            <a class="btn btn-warning navbar-btn popup " href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/Form" data-title="Modification [!C::getFirstSearchOder()!]">Modifier</a>
            <a class="btn btn-danger navbar-btn confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/Supprimer" data-title="Suppression [!C::getFirstSearchOder()!]" data-confirm="Êtes vous sur de vouloir supprimer [!O::getDescription()!] [!O::getFirstSearchOrder()!]" data-url="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]">Supprimer</a>
            [STORPROC [!O::getElementsByAttribute(link,,1)!]/type=fkey|P]
                [STORPROC [!O::Module!]/[!P::objectName!]/[!O::ObjectType!]/[!O::Id!]|Pa|0|10]
                    <a href="/[!Sys::getMenu([!P::objectModule!]/[!P::objectName!])!]/[!Pa::Id!]" class="btn-primary navbar-btn btn">Fiche [!P::objectDescription!] [!Pa::getFirstSearchOrder()!]</a>
                [/STORPROC]
            [/STORPROC]

        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        [COUNT [!O::getFunctions()!]|NF]
        [IF [!NF!]>3]
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!--<li><a href="#">Link</a></li>-->
                    [STORPROC [!O::getFunctions()!]|F]
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Fonctions<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            [LIMIT 0|100]
                            <li><a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!F::Nom!]" class="popup [IF [!F::type!]=form]fonction[ELSE]popup-close[/IF]">[IF [!F::title!]][!F::title!][ELSE][!F::Nom!][/IF]</a></li>
                            [/LIMIT]
                        </ul>
                    </li>
                    [/STORPROC]
                </ul>
            </div>
        [ELSE]
            [STORPROC [!O::getFunctions()!]|F]
                <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!F::Nom!]" class="btn btn-info navbar-btn popup [IF [!F::type!]=form]fonction[ELSE]popup-close[/IF]">[IF [!F::title!]][!F::title!][ELSE][!F::Nom!][/IF]</a>
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

        <!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#Property" aria-controls="Property" role="tab" data-toggle="tab">Détails</a></li>
    [STORPROC [!O::getElementsByAttribute(fiche,,1)!]|P]
        [SWITCH [!P::type!]|=]
            [CASE fkey]
                [IF [!P::card!]=long||[!P::recursive!]]
                <li role="fiche-[!P::name!]" ><a href="#fiche-[!P::name!]" aria-controls="fiche-[!P::name!]" role="tab" data-toggle="tab">[!P::parentDescription!]</a></li>
                [/IF]
            [/CASE]
        [/SWITCH]
    [/STORPROC]
    [STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
        [LIMIT 0|10]
            [IF [!Lang::DEFAULT!]=1][ELSE]
                <li role="[!Key!]" ><a href="#[!Key!]" aria-controls="[!Key!]" role="tab" data-toggle="tab">[!Key!]</a></li>
            [/IF]
        [/LIMIT]
    [/STORPROC]
</ul>




        <!-- Tab panes -->
<div class="tab-content" style="overflow: hidden;">
    <div role="presentation" class="tab-pane active" id="Property">

        [COUNT [!O::getElementsByAttribute(fiche,,1)!]|NBC]
        <div class="[IF [!NBC!]>6]col-md-6[ELSE]col-md-12[/IF]">
            [STORPROC [!O::getElementsByAttribute(fiche,,1)!]|P]
                [IF [!P::type!]!=fkey&&(![!P::recursive!]!=+[!P::card!]!=long!)]
                    [MODULE Systeme/Utils/Fiche/getInput?P=[!P!]]
                    [IF [!NBC!]>6&&[!Pos!]=[!Math::Floor([!NBC:/2!])!]]
                        </div>
                        <div class="col-md-6">
                    [/IF]
                 [/IF]
            [/STORPROC]
        </div>
    </div>
    [STORPROC [!O::getElementsByAttribute(fiche,,1)!]|P]
        <!-- Tab panes -->
        [SWITCH [!P::type!]|=]
            [CASE fkey]
                [IF [!P::card!]=long||[!P::recursive!]]
                <div role="fiche-[!P::name!]" class="tab-pane" id="fiche-[!P::name!]">
                    <div class="form-group group-[!P::name!] [IF [!Error_[!P::name!]!]] has-error[/IF]">
                        <div class="row" style="margin: 0;">
                            [MODULE Systeme/Utils/List/MiniList?Chemin=[!P::objectModule!]/[!P::objectName!]/[!O::ObjectType!]/[!O::Id!]&P=[!P!]&O=[!O!]]
                        </div>
                    </div>
                </div>
                [/IF]
            [/CASE]
        [/SWITCH]
    [/STORPROC]
    [STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
        [IF [!Lang::DEFAULT!]=1][ELSE]
            <div role="[!Key!]" class="tab-pane" id="[!Key!]">
                [STORPROC [!O::getElementsByAttribute(form,,1,[!Key!])!]|P]
                    [IF [!P::special!]=multi]
                        [MODULE Systeme/Utils/Fiche/getInput?P=[!P!]&LANG=[!Key!]-]
                    [/IF]
                [/STORPROC]
            </div>
        [/IF]
    [/STORPROC]
</div>
<br />
<hr>
[STORPROC [!O::getChildElements()!]|C]
<div >
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        [LIMIT 0|100]
            [IF [!C::hidden!]][ELSE]
                [COUNT [!O::Module!]/[!O::ObjectType!]/[!O::Id!]/[!C::objectName!]|NB]
                <li role="presentation" [IF [!Pos!]=1]class="active"[/IF]><a href="#[!C::objectName!]" aria-controls="[!C::objectName!]" role="tab" data-toggle="tab">[!C::objectDescription!] ([!NB!])</a></li>
            [/IF]
        [/LIMIT]
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        [LIMIT 0|100]
            [IF [!C::hidden!]][ELSE]
                <div role="tabpanel" class="tab-pane [IF [!Pos!]=1]active[/IF]" id="[!C::objectName!]">
                    <nav class="navbar navbar-default">
                        <div class="container-fluid">
                            <div class=" navbar-header">
                                &nbsp;
                                <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!C::objectName!]/Form" data-title="Ajouter [!C::objectDescription!]" class="btn btn-success popup navbar-btn"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Ajouter un(e) [!C::objectDescription!]</a>
                                <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!O::Id!]/[!C::objectName!]/Select" data-title="Selectionner [!C::objectDescription!]" class="btn btn-warning popup navbar-btn"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Sélectionner un(e) [!C::objectDescription!] existant</a>
                            </div>
                        </div>
                    </nav>
                    [MODULE Systeme/Utils/List?Chemin=[!O::Module!]/[!O::ObjectType!]/[!O::Id!]/[!C::objectName!]&Popup=[!C::popup!]]
                </div>
            [/IF]
        [/LIMIT]
    </div>

</div>
[/STORPROC]