[STORPROC [!Query!]|CurrentObjet|0|1]
        [IF [!CurrentObjet::ObjectType!]=Projet]
            [!CurrentProjet:=[!CurrentObjet!]!]
        [ELSE]
            [STORPROC [!Query!]|S][/STORPROC]
            [INFO [!Query!]|I]
            [STORPROC [!I::Historique!]|H|0|1]
                [!Sess:=[!H::Value!]!]
            [/STORPROC]
            [STORPROC Formation/Session/[!Sess!]|CurrentObjet][/STORPROC]
            [STORPROC Formation/Projet/Session/[!Sess!]|CurrentProjet][/STORPROC]
        [/IF]
[/STORPROC]

    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            [IF [!Lien!]]
                <li >
                    <a href="/"><i class="fa fa-fw fa-dashboard"></i> Tous les projets</a>
                </li>
                <li >
                    <a href="/Projets/[!CurrentProjet::Id!]"><i class="fa fa-fw fa-dashboard"></i>Les sessions [!CurrentProjet::Nom!]</a>
                </li>
                [IF [!CurrentObjet::ObjectType!]=Session]
                    <li [IF [!Lien!]=Sessions/[!Sess!]]class="active"[/IF]>
                        <a href="/Sessions/[!Sess!]"><i class="fa fa-fw fa-bar-chart-o"></i> Ma session</a>
                        <ul>
                            <li [IF [!Lien!]~Sessions/[!Sess!]/Donnee]class="active"[/IF]>
                                <a href="/Sessions/[!Sess!]/Donnee"><i class="fa fa-fw fa-bar-chart-o"></i>Données participants</a>
                            </li>
                        </ul>
                    </li>
                [/IF]
            [/IF]
            <li [IF [!Lien!]~Systeme/Configuration]class="active"[/IF]>
                <a href="/Systeme/Configuration"><i class="fa fa-fw fa-gears"></i> Paramètres</a>
            </li>
            <li>
                <a href="/Systeme/Deconnexion"><i class="fa fa-fw fa-power-off"></i> Se déconnecter</a>
            </li>
        </ul>
    </div>
