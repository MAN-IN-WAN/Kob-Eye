            <div class="breadcrumb-wrapper no-print">
                <div class="container">
                    <div class="row">
                        <div class="span12">
                            <ul class="breadcrumb pull-left">
                                <li><a href="/">Accueil</a></li>
                            </ul>
                            <div class="account pull-right">
                                <ul class="nav nav-pills">
                                    <li><a href="http://admin.bertrandimmobilier.fr/">Connexion</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- HEADER -->
            <div class="header-print">
                <p class="print-title">Bertrand Immobilier</p>
                <p>Syndic, ventes, gestion locative et locations saisonnières et à l'année <br> location@bertrandimmobilier.fr | 04.67.50.79.12 | 1 Boulevard Sarrail 34250 Palavas-les-Flots</p>
            </div>
            <div id="header-wrapper" class="no-print">
                <div id="header">
                    <div id="header-inner">
                        <div class="container">
                            <div class="navbar">
                                <div class="navbar-inner">
                                    <div class="row">
                                        <div class="logo-wrapper span12">
                                            <a href="#nav" class="hidden-desktop" id="btn-nav">Toggle navigation</a>
                                            <div class="logo">
                                                <a href="/" title="Home">
                                                    <img src="/Skins/BertrandImmobilier/images/logo.png" alt="Retour à l'accueil" style="margin-top: -1px" >
                                                </a>
                                            </div>
                                            <div class="site-slogan">
                                                <a class="btn btn-primary btn-large list-your-property arrow-right cd-btn" style="float:right;margin-top: -5px" href="javascript:;">Etre rappelé !</a>
                                                <span>Syndic, Ventes, Gestion locative<br>Locations saisonnières et à l'année</span><br>
                                                <a href="tel:+33.467507912" style="font-size: 25px;line-height: 30px"><span>04.67.50.79.12</span></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="mailto:syndic@bertrandimmobilier.fr">syndic@bertrandimmobilier.fr</a> -
                                                <a href="mailto:location@bertrandimmobilier.fr">location@bertrandimmobilier.fr</a> -
                                                <a href="mailto:transaction@bertrandimmobilier.fr">transaction@bertrandimmobilier.fr</a>
                                                <a href="https://www.facebook.com/bertrandimmobilierpalavas" target="_blank" title="Visiter la page facebook de Bertrand Immobilier" class="header-facebook"></a>
                                            </div>
                                        </div>
                                    </div><!-- /.row -->
                                </div><!-- /.navbar-inner -->
                            </div><!-- /.navbar -->
                        </div><!-- /.container -->
                    </div><!-- /#header-inner -->
                </div><!-- /#header -->
            </div><!-- /#header-wrapper -->
            <!-- NAVIGATION -->
            <div id="navigation"  class="no-print">
                <div class="container">
                    <div class="navigation-wrapper">
                        <div class="navigation clearfix-normal">

                            <ul class="nav">
                                [STORPROC [!Systeme::Menus!]/MenuPrincipal=1|menu]
                                <li>
                                    <a href="[IF [!menu::Url!]!=nos_activites]/[!menu::Url!][ELSE]javascript:;[/IF]" [IF [!menu::Url!]==nos_activites]class="nolink"[/IF]>[!menu::Titre!]</a>
                                    [STORPROC [!menu::Menus!]|menu2]
                                    <ul>
                                        [LIMIT 0|100]<li><a href="/[!menu::Url!]/[!menu2::Url!]">[!menu2::Titre!]</a></li>[/LIMIT]
                                    </ul>
                                    [/STORPROC]
                                </li>
                                [/STORPROC]
                            </ul><!-- /.nav -->
                        </div><!-- /.navigation -->
                    </div><!-- /.navigation-wrapper -->
                </div><!-- /.container -->
            </div><!-- /.navigation -->
