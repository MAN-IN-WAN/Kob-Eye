<div id="reload">
    <h1 class="page-header">Tableau de bord</h1>
    [STORPROC [!Sys::Modules!]|Mo]
        [STORPROC [!Sys::Menus!]/Alias~=[!Mo::Nom!]|M]
            <h3>[!Mo::Nom!]</h3>
            <div class="row placeholders">
                [LIMIT 0|8]
                [IF [!M::Alias!]!=Systeme/User/DashBoard]
                <div class="col-xs-6 col-sm-3 placeholder">
                    <a class="btn [SWITCH [!Pos!]|=][CASE 1]btn-success[/CASE][CASE 2]btn-warning[/CASE][CASE 3]btn-danger[/CASE][DEFAULT]btn-info[/DEFAULT][/SWITCH] btn-block" href="/[!M::Url!]">
                        <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
                        [COUNT [!M::Alias!]|C]
                        [INFO [!M::Alias!]|I]
                        <h4>[!C!] [!I::ObjectType!](s)</h4>
                        <span>[!M::Titre!]</span>
                    </a>
                </div>
                [/IF]
                [/LIMIT]
            </div>
        [/STORPROC]
    [/STORPROC]
</div>
