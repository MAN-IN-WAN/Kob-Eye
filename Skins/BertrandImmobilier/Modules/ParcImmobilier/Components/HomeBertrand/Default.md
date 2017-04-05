[TITLE]Bertrand Immobilier, Agence immoblière à Palavas-Les-Flots[/TITLE]
[DESCRIPTION]Agence immobilière à Palavas-Les-Flots (34 - Hérault) vous présente ses biens immobiliers : location, vente, locations saisonnières et gestion de syndic.[/DESCRIPTION]
<div class="row">
    <div class="span9">
        <h2 class="page-header">Annonces immobilières à la une !</h2>
        <div class="properties-grid">
            <div class="row">
                [!ReqVenteUne:=[!Query!]ParcImmobilier/Residence/AlaUne=1|Limit=1!]
                [!compteur:=0!]
                [STORPROC [!ReqVenteUne!]|ResidenceVenteUne]
                    <div class="property span3">
                        <div class="image">
                            <div class="content">
                                <a href="/vente/[!ResidenceVenteUne::Url!]"></a>
                                <img src="[!ResidenceVenteUne::Photo1!].mini.270x270.jpg" alt="">
                            </div><!-- /.content -->
                                [IF [!ResidenceVenteUne::Type!]==Location]<div class="price">[!ResidenceVenteUne::Prix!] €</div>[/IF]
                                [IF [!ResidenceVenteUne::Type!]==Vente]<div class="price">[!Utils::getFormatedPrice([!ResidenceVenteUne::Prix!])!] €</div>[/IF]
                                [IF [!ResidenceVenteUne::Type!]==Location saisonniére]<div class="price">à partir de [!ResidenceVenteUne::BasseSaison!] €</div>[/IF]
                                [IF [!ResidenceVenteUne::Type!]==Location saisonnière]<div class="price">à partir de [!ResidenceVenteUne::BasseSaison!] €</div>[/IF]
                            <!-- /.price -->
                            <div class="reduced">
                                [IF [!ResidenceVenteUne::Type!]==Location saisonniére]Location saisonniére[/IF]
                                [IF [!ResidenceVenteUne::Type!]==Location saisonnière]Location saisonniére[/IF]
                                [IF [!ResidenceVenteUne::Type!]==Location]Location[/IF]
                                [IF [!ResidenceVenteUne::Type!]==Vente]Vente[/IF]
                            </div><!-- /.reduced -->
                        </div><!-- /.image -->

                        <div class="title">
                            <h2><a href="/vente/[!ResidenceVenteUne::Url!]">[!ResidenceVenteUne::Titre!]</a></h2>
                        </div><!-- /.title -->

                        <div class="location">[STORPROC [!ResidenceVenteUne::getParents(Ville)!]|Ville][!Ville::Nom!][/STORPROC]</div><!-- /.location -->
                        <div class="area">
                            <!--<span class="key">Vue dégagée</span> /.value -->
                        </div><!-- /.area -->
                        <div class="bedrooms"><div class="content">[!ResidenceVenteUne::Chambres!]</div></div><!-- /.bedrooms -->
                        <div class="bathrooms"><div class="content">[!ResidenceVenteUne::SallesDeBains!]</div></div><!-- /.bathrooms -->
                    </div><!-- /.property -->
                    [!compteur+=1!]
                    [IF [!compteur!]==3]</div><div class="row" style="margin-top:25px">[/IF]
                [/STORPROC]
            </div><!-- /.row -->
        </div><!-- /.properties-grid -->
    </div>
    <div class="sidebar span3">
        <div class="widget our-agents">
            <div class="title">
                <h2>Nous trouver</h2>
            </div><!-- /.title -->

            <div class="content">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d11570.869698002607!2d3.935074!3d43.529085!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x1dc4f5dcc9e6fc32!2sBertrand+Immobilier!5e0!3m2!1sfr!2sfr!4v1424255986690" width="270" height="226" frameborder="0" style="border:0"></iframe>
                <div class="agent">
                    <h5>Horaires d'ouverture</h5>
                    <p>
                        De juin à septembre du lundi au samedi 9h-12h et 14h30-17h30
                        <br>
                        D’octobre à mai du lundi au vendredi 9h-12h et 14h30-17h30
                    </p>
                </div>
            </div><!-- /.content -->
        </div><!-- /.our-agents -->
    </div>
    <div class="span3">
        <div class="property-filter no-print">
            <div class="content">
                <div class="title">
                    <h2 style="color:#fff;margin-top:0px">Connexion client</h2>
                </div>
                <form method="post" action="http://bertrandimmobilier.crypto-extranet.com/extranet/connexion/login" id="loginForm"  name="loginForm">
                    <input type="hidden" id="secureID" name="secureID" value="16a9b6adf6fa0baddf623a62b3f95a02" />
                    <div class="location control-group">
                        <div class="controls">
                            <input type="text" name="login" id="login" value="" placeholder="Votre identifiant">
                        </div><!-- /.controls -->
                    </div>
                    <div class="location control-group">
                        <div class="controls">
                            <input type="text" name="password" id="password" value="" placeholder="Votre mot de passe">
                        </div><!-- /.controls -->
                    </div>
                    <div class="form-actions">
                        <input type="submit" value="Connexion" class="btn btn-primary btn-large">
                    </div>
                </form>
            </div><!-- /.content -->
        </div><!-- /.property-filter -->
    </div>
</div>

[!Req:=[!Query!]ParcImmobilier/Residence!]

<div class="carousel">
    <h2 class="page-header">Nos dernières annonces</h2>

    <div class="content">
        <a class="carousel-prev" href="#">Previous</a>
        <a class="carousel-next" href="#">Next</a>
        <ul>
            [STORPROC [!Req!]|residence]
            <li style="max-width: 350px">
                <div class="image">
                    <a href="/vente/[!residence::Url!]" title="[!residence::Titre!]"></a>
                    <img src="[!residence::Photo1!].mini.350x200.jpg" alt="[!residence::Titre!]">
                </div><!-- /.image -->
                <div class="title" style="margin-left:10px">
                    <strong><a href="/vente/[!residence::Url!]" title="[!residence::Titre!]">[!residence::Titre!]</a></strong>
                </div><!-- /.title -->
                <div class="location">[STORPROC [!residence::getParents(Ville)!]|Ville][!Ville::Nom!][/STORPROC]</div><!-- /.location-->
                <div class="price">
                    [IF[!residence::Prix!]!=0][!residence::Prix!][/IF]
                    [IF[!residence::Loyer!]!=0][!residence::Loyer!][/IF]
                </div><!-- .price -->
                <div class="area">
                    <span class="key">Superficie :</span>
                    <span class="value">[!residence::SuperficieCarrez!]m<sup>2</sup></span>
                </div><!-- /.area -->
                <div class="bathrooms"><div class="inner">[!residence::SallesDeBains!]</div></div><!-- /.bathrooms -->
                <div class="bedrooms"><div class="inner">[!residence::Chambres!]</div></div><!-- /.bedrooms -->
            </li>
            [/STORPROC]
        </ul>
    </div>
</div>
