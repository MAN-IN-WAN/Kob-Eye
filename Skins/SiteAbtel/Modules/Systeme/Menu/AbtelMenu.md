//Menu spécifique à Abtel
[OBJ Systeme|Site|Sit]
[!CurSite:=[!Sit::getCurrentSite()!]!]
[!EntiteSite:=[!CurSite::getOneChild(Entite)!]!]

[STORPROC [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|M|0|20]
        [!Entite:=[!EntiteSite!]!] 
        [!EntiteMenu:=[!M::getOneChild(Entite)!]!]
        [IF [!EntiteMenu!]]
                [!Entite:=[!EntiteMenu!]!] 
        [/IF]
        [IF [!Entite!]]
                <div class="mainMenuLink lienEntite_[!Entite::CodeGestion!] [!M::ClassCss!]">
                        [IF [!EntiteMenu!]]
                                [!Grp:=0!]
                                [STORPROC Systeme/Site/Entite/[!Entite::Id!]|Sitouille|0|1|Id|ASC]
                                    [!link:=https://[!Sitouille::Domaine!]!]
                                    [IF [!link!]!=][ELSE][!link:=[!M::Url!]!][/IF]
                                    [STORPROC Systeme/User/Site/[!Sitouille::Id!]|Usr|0|1][/STORPROC]
                                    [STORPROC Systeme/Group/User/[!Usr::Id!]|Grp|0|1][/STORPROC]
                                [/STORPROC]

                                <a href="[!link!]" target="_blank" style="background-color:[!Entite::CodeCouleur!];" class="abtelMainMenuLink">
                                        <div class="mainMenuWrap">
                                                //<h2>[!Entite::Nom!]</h2>
                                                <img src="[!Entite::Logo!]" />
                                                //<div>[!Entite::Description!]</div>
                                        </div>        
                                </a>
                                <div class="abtelMainSubMenu">
                                    [IF [!Grp!]!=0]
                                        [STORPROC Systeme/Group/[!Grp::Id!]/Menu/MenuSpecial=1|Mnu|0|3]
                                        <a href="[IF [!Mnu::Url!]~www||[!Mnu::Url!]~http][ELSE][!link!]/[/IF][!Mnu::Url!]" [IF [!link!]!=]target="_blank"[/IF] class="abtelMainSubMenuLink">[!Mnu::Titre!] <span class="sub">[!Mnu::SousTitre!]</span></a><br/>
                                            [NORESULT]
                                                <div>[!Entite::Description!]</div>
                                            [/NORESULT]
                                        [/STORPROC]
                                    [ELSE]
                                        <div>[!Entite::Description!]</div>
                                    [/IF]
                                </div>
                        [ELSE]
                                [IF [!M::Url!]~http:/]
                                        <a href="[!M::Url!]" target="_blank" [IF [!M::BackgroundImage!]]style="background-image:url([!M::BackgroundImage!]);"[/IF]  class="abtelMainMenuLink">
                                                <div class="mainMenuWrap">
                                                        <h2>[!M::Titre!]</h2>
                                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                                        [IF [!M::MenuDescription!]!=]<p>[!M::MenuDescription!]</p>[/IF]
                                                </div>       
                                        </a>
                                [ELSE]
                                        <a href="/[!M::Url!]" [IF [!M::BackgroundImage!]]style="background-image:url([!M::BackgroundImage!]);background-size:cover;"[/IF]  class="abtelMainMenuLink">
                                                <div class="mainMenuWrap">
                                                        <h2>[!M::Titre!]</h2>
                                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                                        [IF [!M::MenuDescription!]!=]<p>[!M::MenuDescription!]</p>[/IF]
                                                </div>        
                                        </a>
                                [/IF]
                        [/IF]
                </div>
        [ELSE]
                <div class="mainMenuLink lienClassic">
                [IF [!M::Url!]~http:/]
                        <a href="[!M::Url!]" target="_blank"  class="abtelMainMenuLink">
                                <div class="mainMenuWrap">
                                        <h2>[!M::Titre!]</h2>
                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                </div>
                        </a>
                [ELSE]
                        <a href="/[!M::Url!]" >
                                <div class="mainMenuWrap"  class="abtelMainMenuLink">
                                        <h2>[!M::Titre!]</h2>
                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                </div>
                        </a>
                [/IF]
                </div>
        [/IF]
[/STORPROC]

