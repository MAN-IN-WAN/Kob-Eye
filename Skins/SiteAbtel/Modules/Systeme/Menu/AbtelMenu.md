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
                                <a href="[!M::Url!]" target="_blank" style="background-color:[!Entite::CodeCouleur!];" >
                                        <div class="mainMenuWrap">
                                                //<h2>[!Entite::Nom!]</h2>
                                                <img src="[!Entite::Logo!]" >
                                                <div>[!Entite::Description!]</div>
                                        </div>        
                                </a>
                        [ELSE]
                                [IF [!M::Url!]~http:/]
                                        <a href="[!M::Url!]" target="_blank" [IF [!M::BackgroundImage!]]style="background-image:url([!M::BackgroundImage!]);"[/IF]>
                                                <div class="mainMenuWrap">
                                                        <h2>[!M::Titre!]</h2>
                                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                                        [IF [!M::MenuDescription!]!=]<p>[!M::MenuDescription!]</p>[/IF]
                                                </div>       
                                        </a>
                                [ELSE]
                                        <a href="/[!M::Url!]" [IF [!M::BackgroundImage!]]style="background-image:url([!M::BackgroundImage!]);background-size:cover;"[/IF]>
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
                        <a href="[!M::Url!]" target="_blank" >
                                <div class="mainMenuWrap">
                                        <h2>[!M::Titre!]</h2>
                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                </div>
                        </a>
                [ELSE]
                        <a href="/[!M::Url!]" >
                                <div class="mainMenuWrap">
                                        <h2>[!M::Titre!]</h2>
                                        [IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]
                                </div>
                        </a>
                [/IF]
                </div>
        [/IF]
[/STORPROC]

