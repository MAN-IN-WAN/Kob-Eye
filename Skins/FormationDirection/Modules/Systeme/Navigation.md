[STORPROC [!Query!]|S][/STORPROC]
        [INFO [!Query!]|I]
        [STORPROC [!I::Historique!]|H|0|1]
            [!Parcours:=[!H::Value!]!]
        [/STORPROC]

    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li >
                <a href="/"><i class="fa fa-fw fa-dashboard"></i> Tous les parcours</a>
            </li>
            <li [IF [!Lien!]=Projets/[!Parcours!]]class="active"[/IF]>
                [STORPROC Formation/Projet/[!Parcours!]|P|0|1][/STORPROC]
                <a href="/Projets/[!Parcours!]"><i class="fa fa-fw fa-bar-chart-o"></i> Parcours [!P::Nom!]</a>
            </li>
            <li>
                <a href="/Systeme/Deconnexion"><i class="fa fa-fw fa-power-off"></i> Se déconnecter</a>
            </li>
        </ul>
    </div>
