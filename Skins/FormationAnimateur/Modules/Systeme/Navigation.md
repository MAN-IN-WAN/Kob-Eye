[STORPROC [!Query!]|S][/STORPROC]

    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li >
                <a href="/"><i class="fa fa-fw fa-dashboard"></i> Toutes les sessions</a>
            </li>
            <li [IF [!Lien!]=Sessions/[!S::Id!]]class="active"[/IF]>
                <a href="/Sessions/[!S::Id!]"><i class="fa fa-fw fa-bar-chart-o"></i> Ma session</a>
                <ul>
                    <li [IF [!Lien!]~Sessions/[!S::Id!]/Donnee]class="active"[/IF]>
                        <a href="/Sessions/[!S::Id!]/Donnee"><i class="fa fa-fw fa-bar-chart-o"></i>Données participants</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/Systeme/Deconnexion"><i class="fa fa-fw fa-power-off"></i> Se déconnecter</a>
            </li>
        </ul>
    </div>
