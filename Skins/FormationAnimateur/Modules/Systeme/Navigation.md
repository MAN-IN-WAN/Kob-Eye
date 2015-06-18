[STORPROC [!Query!]|S][/STORPROC]

    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li >
                <a href="/"><i class="fa fa-fw fa-dashboard"></i> Toutes les sessions</a>
            </li>
            <li [IF [!Lien!]=[!Sys::CurrentMenu::Url!]/[!S::Id!]]class="active"[/IF]>
                <a href="/[!Sys::CurrentMenu::Url!]/[!S::Id!]"><i class="fa fa-fw fa-bar-chart-o"></i> Ma session</a>
            </li>
            <li>
                <a href="/Systeme/Deconnexion"><i class="fa fa-fw fa-power-off"></i> Se déconnecter</a>
            </li>
        </ul>
    </div>
