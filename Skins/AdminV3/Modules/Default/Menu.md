<div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <button data-target=".ke-modules-menu" data-toggle="collapse" class="btn btn-navbar" type="button">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="ke-modules-menu nav-collapse collapse">
            <ul class="nav">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Modules<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        [STORPROC [!CONF::MODULE!]|Mod]
                            <li class="dropdown-submenu">
                                <a  class="dropdown-toggle" data-toggle="dropdown" href="/[!Mod::NAME!]">[!Mod::TITLE!]</a>
                                [STORPROC [!Module::[!Mod::NAME!]::Db::AccessPoint!]|Obj]
                                    <ul class="dropdown-menu">
                                        <li><a href="/[!Mod::NAME!]">Accueil du module</a></li>
                                        <li class="divider"></li>
                                        [LIMIT 0|100]
                                            <li>
                                                <a href="/[!Mod::NAME!]/[!Obj::titre!]">
                                                    [IF [!Obj::Description!]][!Obj::Description!][ELSE][!Obj::titre!][/IF]
                                                </a>
                                            </li>
                                        [/LIMIT]
                                    </ul>
                                [/STORPROC]
                            </li>
                        [/STORPROC]
                    </ul>
                </li>
                <li><a href="/Systeme/Configuration">Configuration</a></li>
                <li><a href="/Systeme/Statistiques">Statistiques</a></li>
            </ul>
            <div id="InfosConnexion" class="pull-right">
                Vous êtes connecté en tant que <strong>[!Systeme::User::Login!]</strong> |
                <a href="/Systeme/Deconnexion">Déconnexion</a>
            </div>
        </div>
    </div>
</div>