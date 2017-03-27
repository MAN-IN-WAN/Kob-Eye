[STORPROC [!Query!]|residence|0|1]

[STORPROC [!residence::getParents(Ville)!]|Ville][/STORPROC]

[TITLE]
    [IF [!residence::Type!]==Location saisonnière]Location de vacances [!residence::TypeBien!] [!residence::Couchages!] couchages à [!Ville::Nom!][/IF]
    [IF [!residence::Type!]==Location]Location [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!][/IF]
    [IF [!residence::Type!]==Vente]Vente [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!][/IF]
[/TITLE]
[DESCRIPTION]
    [IF [!residence::Type!]==Location saisonnière]Location de vacances [!residence::Titre!] avec [!residence::Couchages!] couchages à [!Ville::Nom!][/IF]
    [IF [!residence::Type!]==Location]Location [!residence::Titre!] - [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!][/IF]
    [IF [!residence::Type!]==Vente]Vente  [!residence::Titre!] - [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!][/IF]
[/DESCRIPTION]

[HEADER]
[IF [!residence::Type!]==Location saisonnière]<meta property="og:title" content="Location de vacances [!residence::TypeBien!] [!residence::Couchages!] couchages à [!Ville::Nom!]"/>[/IF]
[IF [!residence::Type!]==Location]<meta property="og:title" content="Location [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!]"/>[/IF]
[IF [!residence::Type!]==Vente]<meta property="og:title" content="Vente [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!]"/>[/IF]
<meta property="og:type" content="estate" />
<meta property="og:url" content="http://www.bertrandimmobilier.fr/[!Lien!]" />
<meta property="og:image" content="http://www.bertrandimmobilier.fr/Home/[!residence::Photo1!].mini.870x435.jpg" />
[IF [!residence::Type!]==Location saisonnière]<meta property="og:description" content="Location de vacances [!residence::Titre!] avec [!residence::Couchages!] couchages à [!Ville::Nom!]" />[/IF]
[IF [!residence::Type!]==Location]<meta property="og:description" content="Location [!residence::Titre!] - [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!]" />[/IF]
[IF [!residence::Type!]==Vente]<meta property="og:description" content="Vente  [!residence::Titre!] - [!residence::TypeBien!] [!residence::SuperficieCarrez!] m2 à [!Ville::Nom!]" />[/IF]
[/HEADER]


                    <h1 class="page-header">[!residence::Titre!]</h1>
                    [IF [!residence::ImagePano!]]
                        [IF [!residence::ImagePano!]!=]
                            <div id="panoResid">
                                <img src="/[!residence::ImagePano!].limit.870x435.jpg" alt="">
                            </div>
                        [/IF]
                    [/IF]
                    <div class="carousel property" id="carousel">
                        <div class="preview" style="text-align: center">
                            <img src="/[!residence::Photo1!].limit.870x435.jpg" alt="">
                        </div><!-- /.preview -->

                        <div class="content no-print">

                            <a class="carousel-prev" href="#">Previous</a>
                            <a class="carousel-next" href="#">Next</a>
                            <ul class="fiche-imgs">
                                [IF [!residence::Photo1!]]
                                    [IF [!residence::Photo1!]!=]
                                        <li class="active">
                                            <img src="/[!residence::Photo1!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo2!]]
                                    [IF [!residence::Photo2!]!=]
                                        <li>
                                            <img src="/[!residence::Photo2!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo3!]]
                                    [IF [!residence::Photo3!]!=]
                                        <li>
                                            <img src="/[!residence::Photo3!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo4!]]
                                    [IF [!residence::Photo4]!=]
                                        <li>
                                            <img src="/[!residence::Photo4!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo5!]]
                                    [IF [!residence::Photo5!]!=]
                                        <li>
                                            <img src="/[!residence::Photo5!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo6!]]
                                    [IF [!residence::Photo6!]!=]
                                        <li>
                                            <img src="/[!residence::Photo6!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo7!]]
                                    [IF [!residence::Photo7!]!=]
                                        <li>
                                            <img src="/[!residence::Photo7!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo8!]]
                                    [IF [!residence::Photo8!]!=]
                                        <li>
                                            <img src="/[!residence::Photo8!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo9!]]
                                    [IF [!residence::Photo9!]!=]
                                        <li>
                                            <img src="/[!residence::Photo9!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo10!]]
                                    [IF [!residence::Photo10!]!=]
                                        <li>
                                            <img src="/[!residence::Photo10!].limit.870x435.jpg" alt="">
                                        </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo11!]]
                                    [IF [!residence::Photo11!]!=]
                                    <li>
                                        <img src="/[!residence::Photo11!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo12!]]
                                    [IF [!residence::Photo12!]!=]
                                    <li>
                                        <img src="/[!residence::Photo12!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo13!]]
                                    [IF [!residence::Photo13!]!=]
                                    <li>
                                        <img src="/[!residence::Photo13!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo14!]]
                                    [IF [!residence::Photo14!]!=]
                                    <li>
                                        <img src="/[!residence::Photo14!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo15!]]
                                    [IF [!residence::Photo15!]!=]
                                    <li>
                                        <img src="/[!residence::Photo15!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo16!]]
                                    [IF [!residence::Photo16!]!=]
                                    <li>
                                        <img src="/[!residence::Photo16!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo17!]]
                                    [IF [!residence::Photo17!]!=]
                                    <li>
                                        <img src="/[!residence::Photo17!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo18!]]
                                    [IF [!residence::Photo18!]!=]
                                    <li>
                                        <img src="/[!residence::Photo18!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                                [IF [!residence::Photo19!]]
                                    [IF [!residence::Photo19!]!=]
                                    <li>
                                        <img src="/[!residence::Photo19!].limit.870x435.jpg" alt="">
                                    </li>
                                    [/IF]
                                [/IF]
                            </ul>
                        </div>
                        <!-- /.content -->
                    </div>
                    <!-- /.carousel -->

                    <div class="property-detail">
                        <div class="pull-left overview">
                            <div class="row">
                                <div class="span3">
                                    <h2 class="no-print">Fiche du bien : [!residence::Type!]</h2>
                                    [IF [!residence::Type!]==Location Saisonnière]
                                        <h2 class="only-print" style="margin:0 0 10px 0;padding:0">Location saisonnière</h2>
                                    [/IF]
                                    [IF [!residence::Type!]==Location]
                                        <h2 class="only-print" style="margin:0 0 10px 0;padding:0">Location</h2>
                                    [/IF]
                                    [IF [!residence::Type!]==Vente]
                                        <h2 class="only-print" style="margin:0 0 10px 0;padding:0">Vente</h2>
                                    [/IF]
                                    <table>
                                        [IF [!residence::Reference!]]
                                            <tr>
                                                <th>Référence :</th>
                                                <td>[!residence::Reference!]</td>
                                            </tr>
                                        [/IF][IF [!residence::Type!]==Vente]
                                            <tr>
                                                <th>Prix de vente :</th>
                                                <td>[!residence::Prix!] €</td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::Type!]==Location]
                                            <tr>
                                                <th>Loyer :</th>
                                                <td>[!residence::Loyer!] €</td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::TypeBien!]]
                                            <tr>
                                                <th>Type de bien :</th>
                                                <td>[!residence::TypeBien!]</td>
                                            </tr>
                                        [/IF]
                                        [STORPROC [!residence::getParents(Ville)!]|Ville][/STORPROC]
                                        [IF [!Ville!]]
                                            <tr>
                                                <th>Localisation :</th>
                                                <td>[!Ville::Nom!]</td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::SallesDeBains!]]
                                            <tr>
                                                <th>Salles de bain :</th>
                                                <td>[!residence::SallesDeBains!]</td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::Chambres!]]
                                            <tr>
                                                <th>Chambre :</th>
                                                <td>[!residence::Chambres!]</td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::Type!]==Location saisonnière]
                                            <tr>
                                                <th>Couchages :</th>
                                                <td>[!residence::Couchages!]</td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::SuperficieCarrez!]]
                                            <tr>
                                                <th>Surface habitable (Carrez) :</th>
                                                <td>[!residence::SuperficieCarrez!] m<sup>2</sup></td>
                                            </tr>
                                        [/IF]
                                        [IF [!residence::SuperficieTerrain!]]
                                            <tr>
                                                <th>Surface terrain :</th>
                                                <td>[!residence::SuperficieTerrain!] m<sup>2</sup></td>
                                            </tr>
                                        [/IF]
                                    </table>
                                </div>
                                <!-- /.span2 -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <p class="justify">
                            [!residence::Descriptif!]
                        </p>
                        [IF [!residence::DPE!]]
                        <h2 style="clear:left" class="no-print">Diagnostics énergétiques</h2>
                        [/IF]
                        [IF [!residence::DPE!]>0]
                            [IF [!residence::DPE!]<51]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_A.png);">
                                    <span style="position: relative; top: 35px; left: 265px; font-weight: 600">[!residence::DPE!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::DPE!]>50]
                            [IF [!residence::DPE!]<91]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_B.png);">
                                    <span style="position: relative; top: 70px; left: 265px; font-weight: 600">[!residence::DPE!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::DPE!]>90]
                            [IF [!residence::DPE!]<151]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_C.png);">
                                    <span style="position: relative; top: 105px; left: 260px; font-weight: 600">[!residence::DPE!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::DPE!]>150]
                            [IF [!residence::DPE!]<231]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_D.png);">
                                    <span style="position: relative; top: 140px; left: 260px; font-weight: 600">[!residence::DPE!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::DPE!]>230]
                            [IF [!residence::DPE!]<331]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_E.png);">
                                    <span style="position: relative; top: 175px; left: 260px; font-weight: 600">[!residence::DPE!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::DPE!]>330]
                            [IF [!residence::DPE!]<451]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_F.png);">
                                    <span style="position: relative; top: 210px; left: 260px; font-weight: 600">[!residence::DPE!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::DPE!]>450]
                            <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/DPE/dpe_G.png);">
                                <span style="position: relative; top: 245px; left: 257px; font-weight: 600">[!residence::DPE!]</span>
                            </div>
                        [/IF]


                        [IF [!residence::GES!]>0]
                            [IF [!residence::GES!]<6]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_A.png);">
                                    <span style="position: relative; top: 35px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::GES!]>5]
                            [IF [!residence::GES!]<11]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_B.png);">
                                    <span style="position: relative; top: 70px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::GES!]>10]
                            [IF [!residence::GES!]<21]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_C.png);">
                                    <span style="position: relative; top: 105px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::GES!]>20]
                            [IF [!residence::GES!]<36]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_D.png);">
                                    <span style="position: relative; top: 140px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::GES!]>35]
                            [IF [!residence::GES!]<56]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_E.png);">
                                    <span style="position: relative; top: 175px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::GES!]>55]
                            [IF [!residence::GES!]<81]
                                <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_F.png);">
                                    <span style="position: relative; top: 210px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                                </div>
                            [/IF]
                        [/IF]
                        [IF [!residence::GES!]>80]
                            <div style="height: 300px; width: 300px; display: inline-block; background-image: url(http://www.bertrandimmobilier.fr/Home/GES/ges_G.png);">
                                <span style="position: relative; top: 245px; left: 265px; font-weight: 600">[!residence::GES!]</span>
                            </div>
                        [/IF]

                        <h2 style="clear:left" class="no-print">Documents à télécharger</h2>

                        <div class="row no-print">
                            <div class="span12">
                                [IF [!residence::Plan!]]<a href="/[!residence::Plan!]" alt="Plan du bien immobilier" download="Plan-[!residence::Url!]">Plan du bien immobilier</a><br>[/IF]
                                [IF [!residence::PlanMasse!]]<a href="/[!residence::PlanMasse!]" alt="Plan de masse" download="Plan-de-masse-[!residence::Url!]">Plan de masse</a><br>[/IF]
                                [IF [!residence::DiagnosticEnergie!]]<a href="/[!residence::DiagnosticEnergie!]" alt="Diagnostic énergétique" download="Diagnostic-energetique-[!residence::Url!]">Diagnostic énergétique</a><br>[/IF]
                                [IF [!residence::DiagnosticAmiante!]]<a href="/[!residence::DiagnosticAmiante!]" alt="Diagnostic amiante" download="Diagnostic-amiante-[!residence::Url!]">Diagnostic amiante</a><br>[/IF]
                                [IF [!residence::DiagnosticParasite!]]<a href="/[!residence::DiagnosticParasite!]" alt="Diagnostic parasites" download="Diagnostic-parasite-[!residence::Url!]">Diagnostic parasite</a><br>[/IF]
                                [IF [!residence::DiagnosticPlomb!]]<a href="/[!residence::DiagnosticPlomb!]" alt="Diagnostic plomb" download="Diagnostic-plomb-[!residence::Url!]">Diagnostic plomb</a><br>[/IF]
                            </div>
                        </div>

                        <h2 style="clear:left">Critères supplémentaires, équipements et aménagements</h2>

                        <div class="row">
                            [!compteur:=0!]
                            [IF [!residence::Climatisation!]]<div class="span2 checkItem">Climatisation</div>[/IF]
                            [IF [!residence::Meublé!]]<div class="span2 checkItem">Meublé</div>[/IF]
                            [IF [!residence::Piscine!]]<div class="span2 checkItem">Piscine</div>[/IF]
                            [IF [!residence::Balcon!]]<div class="span2 checkItem">Balcon</div>[/IF]
                            [IF [!residence::Terrasse!]]<div class="span2 checkItem">Terrasse</div>[/IF]
                            [IF [!residence::Parking!]]<div class="span2 checkItem">Parking</div>[/IF]
                            [IF [!residence::ProcheMaternelle!]]<div class="span2 checkItem">Proche maternelle</div>[/IF]
                            [IF [!residence::ProchePrimaire!]]<div class="span2 checkItem">Proche primaire</div>[/IF]
                            [IF [!residence::ProcheCollege!]]<div class="span2 checkItem">Proche collège</div>[/IF]
                            [IF [!residence::ProcheLycee!]]<div class="span2 checkItem">Proch lycée</div>[/IF]
                            [IF [!residence::MachineLaver!]]<div class="span2 checkItem">Machine à laver</div>[/IF]
                            [IF [!residence::LaveVaiselle!]]<div class="span2 checkItem">Lave-vaisselle</div>[/IF]
                            [IF [!residence::Cour!]]<div class="span2 checkItem">Cour</div>[/IF]
                            [IF [!residence::WiFi!]]<div class="span2 checkItem">WiFi</div>[/IF]
                            [IF [!residence::Animaux!]]<div class="span2 checkItem">Animaux acceptés</div>[/IF]
                            [IF [!residence::Garage!]]<div class="span2 checkItem">Garage</div>[/IF]
                            [IF [!residence::Cellier!]]<div class="span2 checkItem">Cellier</div>[/IF]
                            [IF [!residence::Alarme!]]<div class="span2 checkItem">Alarme</div>[/IF]
                            [IF [!residence::Cheminee!]]<div class="span2 checkItem">Cheminée</div>[/IF]
                            [IF [!residence::Jardin!]]<div class="span2 checkItem">Jardin</div>[/IF]
                            [IF [!residence::Cour!]]<div class="span2 checkItem">Cour</div>[/IF]
                            [IF [!residence::Securise!]]<div class="span2 checkItem">Sécurisé</div>[/IF]
                            [IF [!residence::WCindependant!]]<div class="span2 checkItem">WC indépendant</div>[/IF]
                            [IF [!residence::Radiateur!]]<div class="span2 checkItem">Radiateur</div>[/IF]
                            [IF [!residence::Convecteur!]]<div class="span2 checkItem">Convecteur électrique</div>[/IF]
                            [IF [!residence::Box!]]<div class="span2 checkItem">Box</div>[/IF]
                            [IF [!residence::Interphone!]]<div class="span2 checkItem">Interphone</div>[/IF]
                            [IF [!residence::CuisineAmenagee!]]<div class="span2 checkItem">Cuisine amenagée</div>[/IF]
                            [IF [!residence::Cuisineequipee!]]<div class="span2 checkItem">Cuisine équipée</div>[/IF]

                        </div>
                    </div>
                    [IF [!residence::Type!]==Location Saisonnière||[!residence::Type!]==Location saisonnière]
                        <div class="property-detail no-print" style="margin-top: 25px">
                            <h2 class="no-print">Commentaires des précédents locataires</h2>

                            [STORPROC [!Query!]/Commentaire/Valide=1|Com]
                            <div class="com-bloc">
                                <div class="com-bloc-note">
                                    [IF [!Com::Note!] > 0]<span class="com-note">★</span>[ELSE]<span class="com-note">☆</span>[/IF]
                                    [IF [!Com::Note!] > 1]<span class="com-note">★</span>[ELSE]<span class="com-note">☆</span>[/IF]
                                    [IF [!Com::Note!] > 2]<span class="com-note">★</span>[ELSE]<span class="com-note">☆</span>[/IF]
                                    [IF [!Com::Note!] > 3]<span class="com-note">★</span>[ELSE]<span class="com-note">☆</span>[/IF]
                                    [IF [!Com::Note!] > 4]<span class="com-note">★</span>[ELSE]<span class="com-note">☆</span>[/IF]
                                </div>
                                <h4>[!Com::Prenom!]</h4>
                                <div class="com-com">
                                    [!Com::Commentaire!]
                                </div>
                                <span class="com-date">Le [!Utils::getDate(d/m/Y,[!Com::tmsCreate!])!]</span>
                                <div style="clear:both"></div>
                            </div>
                            [/STORPROC]




                        </div>

                        <div class="property-detail no-print" style="margin-top: 25px">
                            <h2 class="no-print">Déposer un commentaire</h2>
                            <p>Vous avez séjourné dans cette résidence et souhaitez partager votre expérience. Il vous suffit de remplir le formulaire ci-dessous. Après vérification Bertrand Immobilier publiera votre commentaire sur le site internet.</p>
                            <form method="get" action="javascript:;" id="formCommentaire" class="contact-form">
                                <div class="name control-group" id="groupNom">
                                    <label class="control-label" for="inputNom">
                                        Nom
                                        <span class="form-required" title="Ce champs est obligatoire.">*</span>
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="inputNom" id="inputNom" required="">
                                    </div><!-- /.controls -->
                                </div><!-- /.control-group -->

                                <div class="email control-group" id="groupPrenom">
                                    <label class="control-label" for="inputPrenom">
                                        Prénom
                                        <span class="form-required" title="Ce champs est obligatoire.">*</span>
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="inputPrenom" required="" id="inputPrenom">
                                    </div><!-- /.controls -->
                                </div><!-- /.control-group -->

                                <div class="email control-group" id="groupEmail">
                                    <label class="control-label" for="inputEmail">
                                        Email
                                        <span class="form-required" title="Ce champs est obligatoire.">*</span>
                                    </label>
                                    <div class="controls">
                                        <input type="email" name="inputEmail" required="" id="inputEmail">
                                    </div><!-- /.controls -->
                                </div><!-- /.control-group -->

                                <div class="name control-group" id="groupTelephone">
                                    <label class="control-label" for="inputTelephone">
                                        Téléphone
                                        <span class="form-required" title="Ce champs est obligatoire.">*</span>
                                    </label>
                                    <div class="controls">
                                        <input type="text" name="inputTelephone" id="inputTelephone" required="">
                                    </div><!-- /.controls -->
                                </div><!-- /.control-group -->


                                <div class="control-group"  id="groupCommentaire">
                                    <div class="controls">
                                        <textarea id="inputCommentaire" name="inputCommentaire" placeholder="Saisissez votre commentaire ici"></textarea>
                                    </div><!-- /.controls -->
                                </div><!-- /.control-group -->

                                <input type="hidden" name="inputNote" id="inputNote" value="0">

                                <div class="commentaire-note">
                                    <div class="com-note">Votre note  <span class="form-required" title="Ce champs est obligatoire.">*</span> : </div>
                                    <div class="rating">
                                        <span id="note5" onclick="setNote(5)">☆</span><span onclick="setNote(4)" id="note4">☆</span><span onclick="setNote(3)" id="note3">☆</span><span onclick="setNote(2)" id="note2">☆</span><span onclick="setNote(1)" id="note1">☆</span>
                                    </div>
                                </div>
                                <div style="height:10px;clear:both"></div>
                                <div class="form-error" style="background-color: #BB2A2A;display: none;padding:10px;text-align: center;margin-bottom: 10px"></div>

                                <div class="form-actions">
                                    <input type="button" id="submitCommentaire" value="Envoyer votre commentaire" class="btn btn-primary btn-large">
                                </div><!-- /.form-actions -->
                            </form>
                            <div class="form-success" style="background-color: #27ad0f;display: none;padding:10px;text-align: center;margin-bottom: 10px;color:#fff">
                                <p><strong>Merci !</strong></p>
                                <p>Votre commentaire nous a été transmis. Après vérification, il sera ajouté au site internet.</p>
                            </div>
                        </div>
                    [/IF]
    <script>

        $('document').ready(function(){


  


            setNote = function (note){

                for(i = 1; i <= 5; i++) $("#note" + i).removeClass("golden");
                if(note > 0 ) $("#note1").addClass("golden");
                if(note > 1) $("#note2").addClass("golden");
                if(note > 2) $("#note3").addClass("golden");
                if(note > 3) $("#note4").addClass("golden");
                if(note > 4) $("#note5").addClass("golden");

                $("#inputNote").val(note);
            }

            $("#submitCommentaire").click(function(){

                $.ajax({
                    url: '/[!Lien!]/setCommentaire.json',
                    data: {
                        nom         : $("#inputNom").val(),
                        prenom      : $("#inputPrenom").val(),
                        telephone   : $("#inputTelephone").val(),
                        email       : $("#inputEmail").val(),
                        commentaire : $("#inputCommentaire").val(),
                        note        : encodeURIComponent($("#inputNote").val()),
                        id          : [!residence::Id!]
                    }
                }).done(function( data ) {

                    if(data.error)
                    {
                        var formMsg = '';

                        if(data.errorNom){
                            $("#groupNom").addClass("error");
                            formMsg += 'Votre nom doit être renseigné.<br>';
                        }else $("#groupNom").removeClass("error");

                        if(data.errorPrenom){
                            $("#groupPrenom").addClass("error");
                            formMsg += 'Votre prénom doit être renseigné. <br>';
                        }else $("#groupPrenom").removeClass("error");

                        if(data.errorTelephone){
                            $("#groupTelephone").addClass("error");
                            formMsg += 'Votre numéro de téléphone doit être renseigné. <br>';
                        }else $("#groupTelephone").removeClass("error");

                        if(data.errorEmail){
                            $("#groupEmail").addClass("error");
                            formMsg += 'Votre email doit être correctement renseigné. <br>';
                        }else $("#groupEmail").removeClass("error");

                        if(data.errorCommentaire){
                            $("#groupCommentaire").addClass("error");
                            formMsg += 'Votre commentaire doit contenir au minimum 50 caractères.<br>';
                        }else $("#groupCommentaire").removeClass("error");

                        if(data.errorNote){
                            formMsg += 'Vous devez laisser une note à cette résidence.<br>';
                        }

                        $(".form-error").html(data.msg + '<br>' + formMsg);
                        $(".form-error").show('slow');

                    }else {
                        $(".form-error").hide('slow', function() {
                            $("#formCommentaire").hide();
                            $("#submitCommentaire").hide();
                            $('.form-success').show('slow');
                        });
                    }
                });
            });

        });




    </script>
[/STORPROC]

