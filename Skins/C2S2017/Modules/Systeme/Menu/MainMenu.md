<div id="menuContainer" class="container">
        <div class="row">
                <nav class="navbar navbar-default menuPrincipal" role="navigation">
                        <!-- formats téléphone -->
                        <div class="visible-xs hidden-md hidden-lg hidden-sm menutelephone ">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-menuprincipal">
                                        <span class="sr-only"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                </button>
                                <a class="navbar-brand hidden-md hidden-lg hidden-sm" href="#"></a>
                        </div>
                        <!-- Menu autre format-->
                        <div class="collapse navbar-collapse navbar-menuprincipal ">
                                <ul class="navbar-nav menuPrincipal">
                                        [STORPROC [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|M]
                                         	<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF]" [IF [!M::Icone!]!=]style="position:relative;"[/IF]   >
												[IF [!M::Icone!]!=]<img src="[!Domaine!]/[!M::Icone!]" alt="[!M::Titre!]" title="[!M::Titre!]" style="	position:absolute;left:35%;bottom:8%;" />[/IF]
                                                [IF [!M::Url!]~http]
                                                        <a href="[!M::Url!]" target="_blank">[!M::Titre!]</a>
                                                [ELSE]
                                                        <a href="/[!M::Url!]">
                                                              [!M::Titre!]
                                                        </a>
                                                [/IF]
                                            </li>
                                        [/STORPROC]
                                </ul>
                        </div><!-- /.navbar-collapse -->
                </nav>
        </div>
</div>
