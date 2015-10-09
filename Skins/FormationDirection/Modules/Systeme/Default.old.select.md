[IF [!Region!]]
//on enregistre la region en session
[COOKIE Set|CurrentRegion|Region]
[REDIRECT][/REDIRECT]
[/IF]





[IF [!Sys::User::isRole(GLOBAL)!]]
[MODULE Systeme/SelectProjet]
[/IF]
[IF [!Sys::User::isRole(REGION)!]]
[IF [!CurrentRegion!]=]

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Sélectionnez votre région</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Région
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <form class="form-horizontal" id="newSessionForm" method="POST">
        <div class="form-group">
            <label for="inputRegion" class="col-sm-2 control-label">Région</label>
            <div class="col-sm-10">
                <select class="form-control" id="inputRegion" placeholder="Sélectionnez une région" name="Region">
                    [STORPROC Formation/Region|R]
                    <option value="[!R::Id!]" [IF [!Pos!]=1]selected="selected"[/IF]>[!R::Nom!]</option>
                    [/STORPROC]
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success submit">Continuer</button>
    </form>
</div>
[ELSE]
[MODULE Systeme/SelectProjet]
[/IF]
[/IF]
[IF [!Sys::User::isRole(INTER-REGION)!]]
[IF [!CurrentRegion!]=]
<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Sélectionnez votre inter-région</small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Inter-Région
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <form class="form-horizontal" id="newSessionForm" method="POST">
        <div class="form-group">
            <label for="inputRegion" class="col-sm-2 control-label">Région</label>
            <div class="col-sm-10">
                <select class="form-control" id="inputRegion" placeholder="Sélectionnez une région" name="Region">
                    [STORPROC Formation/InterRegion|R]
                    <option value="[!R::Id!]" [IF [!Pos!]=1]selected="selected"[/IF]>[!R::Nom!]</option>
                    [/STORPROC]
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success submit">Continuer</button>
    </form>
</div>
[ELSE]
[MODULE Systeme/SelectProjet]
[/IF]
[/IF]
