<div id="newsDroite">  
        <div class="boxhoraire">
                        [STORPROC 6]
                        <div class="row blocjours">
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 jours">
                        [IF [!Pos!]=1]Lun[/IF]
                        [IF [!Pos!]=2]Mar[/IF]
                        [IF [!Pos!]=3]Mer[/IF]
                        [IF [!Pos!]=4]Jeu[/IF]
                        [IF [!Pos!]=5]Ven[/IF]
                        [IF [!Pos!]=6]Sam[/IF]
                    </div>
                    <div class="col-lg-10 col-md-9 col-sm-9 col-xs-9" style="text-align: center;">
                            08h00 - 12h00 / 14h00 -19h00
                        </div>
                </div>
               [/STORPROC]
            <div class="boutonCentre"><a href="/contact">Prendre rendez-vous</a></div>
        </div>
        <div class="boxnum">
                <p><img src="/Skins/Vetoccitan2/Fonts/TELEPHONE_ICON.png" class="logoContact"> 05.53.67.89.90 <img src="/Skins/Vetoccitan2/Fonts/TELEPHONE_ICON.png" class="logoContact"></p>
        </div>
    [IF [!Lien!]!=services&&[!Lien!]!=conseils&&[!Lien!]!=news]
        <div class="boxnews hidden-xs">
            <h2 class="news">Dernières Actus</h2>
                [STORPROC 5]
                <div class="blocnews">
                    <p>Lorem ipsum dolor sit amet, sapien etiam, nunc amet dolor ac odio mauris justo. Luctus arcu, urna praesent at id quisque ac. </p>
                </div>
                [/STORPROC]
                <div class="boutonCentre"><a href="/news">Voir toutes les news</a></div>
        </div>
    [/IF]
</div>



