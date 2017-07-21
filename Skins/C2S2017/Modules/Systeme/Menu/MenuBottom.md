
<nav id="menuBottom" class="row" role="navigation">
        [STORPROC [!Systeme::Menus!]/Affiche=1&MenuBas=1|M]
                <div class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] col-md-3 col-sm-3" >
                        [IF [!M::Url!]~http]
                                <a href="[!M::Url!]" target="_blank" class="row menuCol">[IF [!M::BackgroundImage!]]<img class="menuBack" src="/[!M::BackgroundImage!]"/>[/IF]<div class="menuTxt">[!M::Titre!]</div></a>
                        [ELSE]
                                <a href="/[!M::Url!]" class="row menuCol">[IF [!M::BackgroundImage!]]<img class="menuBack" src="/[!M::BackgroundImage!]"/>[/IF]<div class="menuTxt">[!M::Titre!]</div></a>
                        [/IF]
                </div>
        [/STORPROC]
</nav>


